<?php
namespace qashops\demo\controller;

require 'vendor/autoload.php';
include 'business/BormeDownloader.php';
use qashops\demo\business\BormeDownloader;

//Controlador
$url = 'http://www.boe.es/borme/dias/2017/01/10/pdfs/BORME-A-2017-6-41.pdf';

$bormeDownloader = new BormeDownloader();

$bormeDownloader->downloadBorme($url);

echo 'Jobs done!';
