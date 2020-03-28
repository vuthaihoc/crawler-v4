<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 2019-10-12
 * Time: 02:20
 */

namespace App\Spider\Browsers\Phantomjs;


use Symfony\Component\Process\Process;

class RenderWithJs {
    public static $bin = "phantomjs";
    public static $js = __DIR__ . "/get_html.js";
    public static $timeout = 69;// 69 seconds
    /** @var Process */
    protected $process;
    protected $command;
    
    /**
     * RenderWithJs constructor.
     */
    public function __construct($bin = null, $js = null) {
    
    }
    
    protected function getProcess($command, $timeout){
        $process = new Process( $command );
        $process->setTimeout( $timeout );
        return $process;
    }
    
    public function getHtml($url){
        $command = [self::$bin, self::$js, $url];
        $this->run( $command );
        $output = $this->output();
        $result = \GuzzleHttp\json_decode( $output, true);
        return $result;
    }
    
    public static function render($url){
        $renderer = new self();
        return $renderer->getHtml( $url);
    }
    
    protected function run($command)
    {
        $this->command = $command; //escapeshellcmd($command);
        $this->process = $this->getProcess( $this->command, self::$timeout);
        $this->process->run();
        $this->validateRun();
        
        return $this;
    }
    
    protected function validateRun()
    {
        $status = $this->process->getExitCode();
        $error  = $this->process->getErrorOutput();
        
        if ($status !== 0 and $error !== '') {
            throw new \RuntimeException(
                sprintf(
                    "The exit status code %s says something went wrong:\n stderr: %s\n stdout: %s\ncommand: %s.",
                    $status,
                    $error,
                    $this->process->getOutput(),
                    $this->process->getCommandLine()
                )
            );
        }
    }
    
    protected function output()
    {
        return $this->process->getOutput();
    }
}