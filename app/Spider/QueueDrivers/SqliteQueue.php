<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 2020-03-08
 * Time: 03:58
 */

namespace App\Spider\QueueDrivers;


use App\Spider\CrawlUrl;
use App\Spider\Enum\ProcessStatus;
use App\Spider\Enum\ProcessType;
use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use PDO;

class SqliteQueue implements CrawlQueueInterface {
    
    protected $site_id;
    protected $connection;
    
    public function __construct($db_file, $site_id) {
        
        $this->site_id = $site_id;
    
        if(!file_exists( $db_file )){
            touch( $db_file );
        }
        
        $this->connection = self::makeConnection( $db_file, $site_id);
        $this->initIfNotExists();
        
    }
    
    protected static function makeConnection($db, $site_id){
        $connection_name = str_replace( ".", "_", basename( $db )) . "_" . $site_id;
        
        $capsule = new Manager();
        
        $capsule->addConnection([
            'driver'    => 'sqlite',
            'host'      => 'localhost',
            'database'  => $db,
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'foreign_key_constraints' => true,
        ], $connection_name);
        
        return $capsule->getConnection($connection_name);
    }
    
    protected function initIfNotExists(){
        if(!$this->connection->getSchemaBuilder()->hasTable( $this->getTableName())){
            $this->connection->getSchemaBuilder()->create($this->getTableName(), function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('parent_url_hash')->index()->default('')->comment( "Hash of parent url");
                $table->string('site_id')->index()->default('')->comment( "Site name or id");
                $table->string('step')->index()->comment( "Step of page");
                $table->mediumText('url')->comment( "Full Url as text");
                $table->string('url_hash')->index()->comment( "Hash of url");
                $table->integer('js')->index()->default( 0)->comment( "0: no, 1: need js");
                $table->integer('visited')->index()->default( 0 )->comment( "visited times");
                $table->timestamp('last_visited')->index()->default( CrawlUrl::NULL_DATETIME )->comment( "last visited time");
                $table->integer( 'process_link_status')->index()->default( 0)->index()->comment( "See App\Spider\Enum\ProcessStatus");
                $table->integer( 'process_data_status')->index()->default( 0)->index()->comment( "See App\Spider\Enum\ProcessStatus");
            });
        }
    }
    
    public function reset(){
        $this->connection->getSchemaBuilder()->dropIfExists( $this->getTableName());
        $this->initIfNotExists();
    }
    
    public function resume(string $type, $before = 0){
        if($before){
            $time = Carbon::now()->subMinutes($before);
        }
        $query = $this->connection->table( $this->getTableName())
                         ->where('process_' . $type . '_status', ProcessStatus::PROCESSING);
        if($before){
            $query->where( 'last_visited', '<', $time);
        }
        return $query->update( ['process_' . $type . '_status' => ProcessStatus::INIT]);
    }
    
    protected function getTableName(){
        return "stack" . ($this->site_id ? "_" . str_replace( ".", "__", $this->site_id) : "");
    }
    
    public function add( CrawlUrl $url ) {
        $url->setSiteId( $this->site_id );
        if($this->has( $url )){
            return false;
        }
    
        $inserted = $this->connection->table( $this->getTableName())->insertGetId($url->toArray());
    
        if($inserted){
            $url->setId( $inserted );
        }
    
        return $url;
    }
    
    public function has( CrawlUrl $crawlUrl ): bool {
        return $this->connection->table( $this->getTableName() )
                                ->where( 'site_id', $this->site_id )
                                ->where( 'url_hash', $crawlUrl->getUrlHash() )
                                ->exists();
    }
    
    public function hasPendingUrls(string $type): bool {
        return $this->connection->table( $this->getTableName() )
                                ->where( 'process_' . $type . '_status', ProcessStatus::INIT )
                                ->exists();
    }
    
    public function getUrlById( $id ): CrawlUrl {
        $first = $this->connection->table( $this->getTableName() )
                                  ->where( 'id', $id )
                                  ->first();
        if($first){
            return CrawlUrl::fromObject($first);
        }else{
            throw new \Exception("#{$id} crawl url not found in collection");
        }
    }
    
    /**
     * Get pending url and mark as processing
     *
     * @param string $type
     *
     * @return mixed
     */
    public function getFirstPendingUrl(string $type): ?CrawlUrl {
        $first = $this->connection->table( $this->getTableName() )
                                  ->lock($this->getLockForPopping())
                                  ->where( 'site_id', $this->site_id)
                                  ->where( 'process_' . $type . '_status', ProcessStatus::INIT )
                                  ->first();
        if($first){
            $crawlUrl = CrawlUrl::fromObject($first);
            $crawlUrl->setId( $first->id );
            $this->changeProcessStatus( $crawlUrl, $type, ProcessStatus::PROCESSING);
            return $crawlUrl;
        }else{
            return null;
        }
    }
    
    /**
     * Get the lock required for popping the next job.
     *
     * @return string|bool
     */
    protected function getLockForPopping()
    {
        $databaseEngine = $this->connection->getPdo()->getAttribute(PDO::ATTR_DRIVER_NAME);
        $databaseVersion = $this->connection->getPdo()->getAttribute(PDO::ATTR_SERVER_VERSION);
        
        if ($databaseEngine == 'mysql' && ! strpos($databaseVersion, 'MariaDB') && version_compare($databaseVersion, '8.0.1', '>=') ||
            $databaseEngine == 'pgsql' && version_compare($databaseVersion, '9.5', '>=')) {
            return 'FOR UPDATE SKIP LOCKED';
        }
        
        return true;
    }
    
    /**
     *
     *
     * @param CrawlUrl $url
     *
     * @return bool
     */
    public function hasAlreadyBeenProcessed( CrawlUrl $url, string $type ): bool {
        return $this->connection->table( $this->getTableName() )
                                ->where( 'id', $url->getId() )
                                ->where( 'process_' . $type . '_status', ProcessStatus::SUCCESS )
                                ->exists();
    }
    
    /**
     * @param CrawlUrl $crawlUrl
     * @param string $type
     * @param $status
     *
     * @return mixed
     * @see ProcessStatus
     */
    public function changeProcessStatus( CrawlUrl $crawlUrl, string $type, $status ) {
        $data = [ 'process_' . $type . '_status' => $status ];
        if ( $type == ProcessType::LINK && $status == ProcessStatus::PROCESSING ) {
            $data ['visited'] = DB::raw( 'visited + 1' );
            $data['last_visited'] = Carbon::now();
        }
        return $this->connection->table( $this->getTableName() )
                                ->where( 'id', $crawlUrl->getId() )
                                ->update( $data );
    }
    
    public function paginate(string $type = '', $status = null, $page = 1, $limit = 20, $order_by = "id", $order_direction = "desc"){
        $builder = $this->connection->table( $this->getTableName());
        if($status !== null && $type){
            $builder->where('process_' . $type . '_status', $status);
        }
        $builder->orderBy( $order_by, $order_direction);
        return $builder->simplePaginate($limit, ['*'], 'page', $page);
    }
    
}