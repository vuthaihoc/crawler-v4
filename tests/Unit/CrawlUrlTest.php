<?php

namespace Tests\Unit;

use App\Spider\CrawlUrl;
use PHPUnit\Framework\TestCase;

class CrawlUrlTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testJoinFunctionTest()
    {
        $base = "http://www.example.com/path/to/the/sky/";
        $relative = "abc.txt";
        $new = "http://www.example.com/path/to/the/sky/abc.txt";
        $this->assertTrue( $new == CrawlUrl::join( $base, $relative) );
    }
}
