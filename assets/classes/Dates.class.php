<?php

class Dates {
    
    private static $start = '2015-08-11';
    private static $end = '2015-11-13';
    private static $recess = '2015-09-28';
    private static $initialized = false;

    public static function setTermStart($start) {
        self::$start = $start;
    }

    public static function setTermEnd($end) {
        self::$end = $end;
    }

    public static function setTermRecess($recess) {
        self::$recess = $recess;
    }

    public static function getTermStart() {
        return self::$start;
    }

    public static function getTermEnd() {
        return self::$end;
    }

    public static function getTermRecess() {
        return self::$recess;
    }
    
    public static function getStartTermRecess() {
        return date('Y-m-d\TH:i:sP', strtotime(self::$recess));
    }
    
    public static function getEndTermRecess() {
        return date('Y-m-d\TH:i:sP', strtotime(self::$recess . '+ 7 days'));
    }
}

?>