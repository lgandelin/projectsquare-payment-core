<?php

namespace Webaccess\ProjectSquarePayment\Requests;

class Request
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
