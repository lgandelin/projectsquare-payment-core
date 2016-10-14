<?php

namespace Webaccess\ProjectSquarePayment\Contracts;

use Webaccess\ProjectSquarePayment\Requests\Request;
use Webaccess\ProjectSquarePayment\Responses\Response;

interface Logger
{
    public function info($message, $parameters = []);

    public function error($message, $parameters = [], $file = null, $line = null);

    public function logRequest($class, Request $request = null);

    public function logResponse($class, Response $response = null);
}