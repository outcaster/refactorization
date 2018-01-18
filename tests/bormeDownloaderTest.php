<?php

include '/business/BormeDownloader.php';

use PHPUnit\Framework\TestCase;
use qashops\demo\business\BormeDownloader;

/**
 * BormeDownloaderTest
 */
class BormeDownloaderTest extends TestCase
{
    /**
     * testDownload
     */
    public function testDownload()
    {
        $url = 'http://www.boe.es/borme/dias/2017/01/10/pdfs/BORME-A-2017-6-41.pdf';
        $bormeDownloader = new BormeDownloader();
        $result = $bormeDownloader->downloadBorme($url);

        $this->assertEquals(true, $result);
    }

    /**
     * testInvalidFormatUrl
     */
    public function testInvalidFormatUrl()
    {
        $this->expectException(\Exception::class);
        $url = 'xxxxxxxxx';
        $bormeDownloader = new BormeDownloader();
        $bormeDownloader->downloadBorme($url);
    }

    /**
     * testNotFoundUrl
     */
    public function testNotFoundUrl()
    {
        $this->expectException(\Exception::class);
        $url = 'http://www.boe.es/borme/dias/2017/01/10/pdfs/xxxx.pdf';
        $bormeDownloader = new BormeDownloader();
        $bormeDownloader->downloadBorme($url);
    }
}
