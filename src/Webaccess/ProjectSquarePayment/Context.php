<?php

namespace Webaccess\ProjectSquarePayment;

class Context
{
    private static $month;
    private static $year;

    public static function getMonth()
    {
        return (self::$month) ? self::$month : date('m');
    }

    public static function setMonth($month)
    {
        self::$month = $month;
    }

    public static function getYear()
    {
        return (self::$year) ? self::$year : date('Y');
    }

    public static function setYear($year)
    {
        self::$year = $year;
    }
}
