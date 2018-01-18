<?php

namespace qashops\demo\business;

use \GuzzleHttp\Client;
use \Smalot\PdfParser\Parser;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Middleware;

const MAX_RETRIES = 2;
const TEMP_ROUTE = 'C:\\xampp\\htdocs\\refactorization\\tmp\\';
const TEXT_FILES_ROUTE = 'C:\\xampp\\htdocs\\refactorization\\text\\';

/**
 * BormeDownloader class
 */
class BormeDownloader
{
    /**
     * downloadBorme
     * @param string $url
     *
     * @return boolean
     */
    public function downloadBorme($url)
    {
        //lo normal es tomar estos parametros de un fichero de configuración. Fuera del scope de la prueba
        $filename       = uniqid();
        $fullPdfRoute   = TEMP_ROUTE.$filename.'.pdf';
        //comprueba la url
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            throw(new \Exception("Invalid URL"));
        }

        //descarga el pdf

        $handlerStack = HandlerStack::create(new CurlHandler());
        $handlerStack->push(Middleware::retry(self::retryDecider(), self::retryDelay()));
        $client = new Client(array('handler' => $handlerStack));

        $resource = fopen($fullPdfRoute, 'w');
        $client->request('GET', $url, ['sink' => $resource]);
        try {
            //obten el texto
            $parser = new Parser();
            $pdf    = $parser->parseFile($fullPdfRoute);
            $text   = $pdf->getText();
            //imprime el contenido en el fichero de texto
            file_put_contents(TEXT_FILES_ROUTE.$filename.'.txt', $text);
            //borra el fichero original
            unlink($fullPdfRoute);
        } catch (\Exception $e) {
            //toma aqui cualquier accion adicional y dispara la exception
            throw($e);
        }

        return true;
    }

    private function retryDecider()
    {
        return function (
            $retries,
            Request $request,
            Response $response = null,
            RequestException $exception = null
        ) {
            // 2 intentos
            if ($retries >= MAX_RETRIES) {
                return false;
            }

            // lista de exceptions que activan el retry
            if ($exception instanceof ConnectException) {
                return true;
            }

            if ($response) {
                //cualquier error 5XX dispara el reintento
                if ($response->getStatusCode() >= 500) {
                    return true;
                }

                //error 404 dispara el reintento
                if ($response->getStatusCode() >= 404) {
                    return true;
                }
            }

            return false;
        };
    }

    private function retryDelay()
    {
        return function ($numberOfRetries) {
            return 1000 * $numberOfRetries;
        };
    }
}
