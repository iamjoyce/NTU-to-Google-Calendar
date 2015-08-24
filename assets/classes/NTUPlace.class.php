<?php

class NTUPlace {
    
    private static $places = array();
    
    public static function addPlace($place, $title = '') {
        
        if (! isset(self::$places[$place])) {
            
            self::$places[$place] = $title;
        }
    }
    
    public static function getPlace($place) {
        
        return (isset(self::$places[$place]) ? self::$places[$place] : '');
    }
}

?>