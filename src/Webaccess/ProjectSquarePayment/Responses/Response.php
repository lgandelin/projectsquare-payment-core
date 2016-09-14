<?php

namespace Webaccess\ProjectSquarePayment\Responses;

class Response
{
    public function __construct($params = [])
    {
        foreach ($params as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}
