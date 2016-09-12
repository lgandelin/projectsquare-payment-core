<?php

namespace Webaccess\ProjectSquarePayment\Responses;

class Response
{
    public function __construct($params = array())
    {
        foreach ($params as $key => $value) {
            if (property_exists($this, $key) && $value != null) {
                $this->$key = $value;
            }
        }
    }
}
