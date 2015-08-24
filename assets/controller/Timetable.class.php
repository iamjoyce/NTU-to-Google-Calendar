<?php

use Sunra\PhpSimple\HtmlDomParser;

class Timetable {
    
    // full class list that contains information of all courses
    private static $classList = array();
    
    function __construct($formData) {
        
        if (isset($formData['startTerm']) && $formData['startTerm']) {
            Dates::setTermStart($formData['startTerm']);
        }
        
        if (isset($formData['endTerm']) && $formData['endTerm']) {
            Dates::setTermEnd($formData['endTerm']);
        }
        
        if (isset($formData['recess']) && $formData['recess']) {
            Dates::setTermRecess($formData['recess']);
        }
        
        if (! isset($formData['source']) || ! $formData['source']) {
            return;
        }
        
        $calName = array();
        if (isset($formData['lectureCal']) && $formData['lectureCal']) {
            $calName['lecture']['name'] = $formData['lectureCal'];
        }
        
        if (isset($formData['tutorialCal']) && $formData['tutorialCal']) {
            $calName['tutorial']['name'] = $formData['tutorialCal'];
        }
        
        if (isset($formData['labCal']) && $formData['labCal']) {
            $calName['lab']['name'] = $formData['labCal'];
        }
        
        if (isset($formData['week']) && $formData['week']) {
            $this->createWeekNo();
        }
        
        $this->sourceToArray($formData['source']);
        $this->createTimetable($calName);
    }
    
    function createWeekNo($calendarName = 'NTU Week') {
        
        $startOfWeek = $this->getNearestDay(Dates::getTermStart());
        
        $calendar = new GoogleCalendar();
        $calendar->enableBatch(false);
        $calendarId = $calendar->createCalendar($calendarName);
        
        $daysOffset = 0;
        $weekOffset = 0;
        for ($i = 1; $i <= 14; $i++) {
            
            $date = date('Y-m-d', strtotime($startOfWeek . '+ ' . $daysOffset . ' days'));
            
            $daysOffset += 7;
            
            if (strtotime($date) == strtotime(Dates::getTermRecess())) {
                // no offset required before recess week
                $weekOffset = 1;
                continue;
            }
            
            $weekNo = $i - $weekOffset;
            $params = array('summary' => 'Week ' . $weekNo,
                            'start' => $date,
                            'end' => $date);
            
            $id = $calendar->createEvent($calendarId, $params);
        }
    }
    
    function sourceToArray($source) {
        
        // skips the header row
        $skip = TRUE;
        $html = HtmlDomParser::str_get_html($source);
        
        // each row represent one course
        $course = new Course();
        foreach($html->find('tr') as $row) {
            
            if ($skip) { 
                $skip = FALSE;
                continue;
            }

            $colNum = 0;
            foreach ($row->find('td') as $col) {

                $text = trim($col->innertext);
                switch ($colNum++) {
                    case 0:
                        $course->setCode($text);
                        break;

                    case 1:
                        $course->setTitle($text);
                        break;

                    case 2:
                        $course->setAu($text);
                        break;

                    case 3:
                        $course->setCourseType($text);
                        break;

                    case 4:
                        $course->setSu($text);
                        break;

                    case 5:
                        $course->setGerType($text);
                        break;

                    case 6:
                        $course->setIndex($text);
                        break;

                    case 7:
                        $course->setStatus($text);
                        break;

                    case 8:
                        $course->setChoice($text);
                        break;

                    case 9:
                        $course->setClassType($text);
                        break;

                    case 10:
                        $course->setGroup($text);
                        break;

                    case 11:
                        $course->setDay($text);
                        break;

                    case 12:
                        $course->setTime($text);
                        break;

                    case 13:
                        $course->setPlace($text);
                        break;

                    case 14:
                        $course->setRemark($text);
                        break;
                }
            }
            
            array_push(self::$classList, clone $course);
        }
    }
    
    function createTimetable($calendarNames = array()) {
        
        // set calendar names
        $calNames = array('lecture' => array('name' => 'NTU Lecture'),
                          'tutorial' => array('name' => 'NTU Tutorial'),
                          'lab' => array('name' => 'NTU Lab'));
        $calNames = array_merge($calNames, $calendarNames);
        
        
        
        $calendar = new GoogleCalendar();
        
        // create secondary calendar
        $calendar->batch();
        foreach ($calNames as $key => $value) {
            $calNames[$key]['responseId'] = $calendar->createCalendar($value['name']);
        }
        $results = $calendar->execute();
        
        foreach ($calNames as $key => $value) {
            $responseId = $calNames[$key]['responseId'];
            $calNames[$key]['calendarId'] = $results[$responseId]['id'];
        }
        
        
        // create events for courses
        $calendar_request = array();
        foreach (self::$classList as $course) {
            
            $calendarId = '';
            switch ($course->getClassType()) {
                
                case ClassType::Lecture():
                    $calendarId = $calNames['lecture']['calendarId'];
                    $calNames['lecture']['used'] = true;
                    break;
                
                case ClassType::Tutorial():
                    $calendarId = $calNames['tutorial']['calendarId'];
                    $calNames['tutorial']['used'] = true;
                    break;
                
                case ClassType::Lab():
                    $calendarId = $calNames['lab']['calendarId'];
                    $calNames['lab']['used'] = true;
                    break;
            }
            
            $eventParams = array('summary' => $course->getCode() . ' ' . $course->getTitle(),
                                 'description' => $course->getGroup(),
                                 'location' => $course->getPlace(),
                                 'start' => $course->getStartDate(),
                                 'end' => $course->getEndDate(),
                                 'count' => $course->getWeeksCount(),
                                 'interval' => $course->getInterval(),
                                 'repeatDay' => $course->getDay());
            
            // cannot create recurring event here as the lib cannot perform ops for different calendars in the same batch
            if (! isset($calendar_request[$calendarId])) {
                $calendar_request[$calendarId] = array();
            }
            array_push($calendar_request[$calendarId], $eventParams);
        }
        
        foreach ($calendar_request as $calendarId => $classes) {
            
            $calendar->batch();
            foreach ($classes as $class) {
                $calendar->createRecurringEvent($calendarId, $class);
            }
            $results = $calendar->execute();
        }
        
        
        $eventParams = array('timeMin' => Dates::getStartTermRecess(),
                             'timeMax' => Dates::getEndTermRecess());
        
        foreach ($calNames as $name) {
            
            $calendarId = $name['calendarId'];
            
            if (! $name['used']) {
                
                // delete calendar that has no events
                $calendar->deleteCalendar($calendarId);
                $name['used'] = 'deleted';
                
            } else {
            
                // delete classes that happen in recess week
                $calendar->deleteEvents($calendarId, $eventParams);
                
            }
        }
    }
    
    function getNearestDay($date, $day = 'Monday') {
        
        $last = strtotime('last ' . $day, strtotime($date));
        $next = strtotime('next ' . $day, strtotime($date));
        
        $diff_last = abs(strtotime($date) - $last);
        $diff_next = abs(strtotime($date) - $next);
        
        if ($diff_last <= $diff_next) {
            return date('Y-m-d', $last);
        } else {
            return date('Y-m-d', $next);
        }
    }
}

?>