<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 2020-03-08
 * Time: 04:00
 */

namespace App\Spider\StorageDrivers;


use App\Sites\Simple\SiteInfo;
use App\Spider\CrawlUrl;
use App\Spider\DataStorageInterface;
use App\Spider\Exceptions\DataCanNotBeWritten;
use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem;

class FileStorage implements DataStorageInterface {
    
    /**
     * @var Filesystem
     */
    protected $filesystem;
    
    /**
     * FileStorage constructor.
     *
     * @param Filesystem $filesystem
     */
    public function __construct( Filesystem $filesystem ) {
        $this->filesystem = $filesystem;
    }
    
    /**
     * @param array $data
     * @param CrawlUrl $crawlUrl
     * @param SiteInfo|string $mode
     * @param SiteInfo|null $siteInfo
     *
     * @return bool
     * @throws DataCanNotBeWritten
     */
    public function store( array $data, CrawlUrl $crawlUrl, $mode = DataStorageInterface::SAVE_MERGE,SiteInfo $siteInfo = null ): bool {
    
        if($mode != DataStorageInterface::SAVE_MERGE){
            $path = $this->makePath( $crawlUrl );
            try{
                if($mode == DataStorageInterface::SAVE_NORMAL){
                    return $this->filesystem->write( $path, json_encode( $data, JSON_PRETTY_PRINT ) );
                }else{
                    return $this->filesystem->put( $path, json_encode( $data, JSON_PRETTY_PRINT ) );
                }
            }catch (FileExistsException $ex){
                throw new DataCanNotBeWritten($ex->getMessage());
            }
        }
        
        $existed = $this->get( $crawlUrl, $siteInfo);
        $data = array_merge( $existed, $data );
        $path = $this->makePath( $crawlUrl );
        return $this->filesystem->put( $path, json_encode( $data, JSON_PRETTY_PRINT ) );
        
    }
    
    public function get( CrawlUrl $crawlUrl, SiteInfo $siteInfo = null ): array {
        $path = $this->makePath( $crawlUrl );
        try{
            $data = $this->filesystem->read( $path );
            return json_decode( $data, true );
        }catch (FileNotFoundException $ex){
            return [];
        }
    }
    
    protected function makePath(CrawlUrl $crawlUrl){
        return $crawlUrl->getUrlHash() . ".json";
    }
    
}