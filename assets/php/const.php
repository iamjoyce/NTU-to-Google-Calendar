<?php
require('assets/php/classes/BasicEnum.php');

abstract class DaysOfWeek extends BasicEnum {
    const Monday = 1;
    const Tuesday = 2;
    const Wednesday = 3;
    const Thursday = 4;
    const Friday = 5;
}

abstract class ClassType extends BasicEnum {
    const Tutorial = 1;
    const Lecture = 2;
    const Lab = 3;
    const Seminar = 4;
}

abstract class TeachingWeeks extends BasicEnum {
    const All = 1;
    const Even = 2;
    const Odd = 3;
}

class Dates {

    private function __construct() {}
    private static $start = '2015-08-11';
    private static $end = '2015-11-13';
    private static $recess = '2015-09-28';
    private static $initialized = false;

    private static function initialize()
    {
        if (self::$initialized)
            return;

        self::$initialized = true;
    }

    public static function setTermStart($s) {
        self::initialize();
        self::$start = $s;
    }

    public static function setTermEnd($e) {
        self::initialize();
        self::$end = $e;
    }

    public static function setTermRecess($r) {
        self::initialize();
        self::$recess = $r;
    }

    public static function TermStart() {
        self::initialize();
        return self::$start;
    }

    public static function TermEnd() {
        self::initialize();
        return self::$end;
    }

    public static function TermRecess() {
        self::initialize();
        return self::$recess;
    }
}
?>