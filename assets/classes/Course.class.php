<?php

class Course {
    
    private $code;
    private $title;
    private $au;
    private $courseType;
    private $su;
    private $gerType;
    private $index;
    private $status;
    private $choice;
    private $classType;
    private $group;
    private $day;
    private $startDate;
    private $startTime;
    private $endTime;
    private $place;
    private $remark;
    private $weeksCount = 14;

    public function setWeeksCount($weeks) {
        $this->weeksCount = $weeks;
    }

    public function setCode($code) {
        $code = trim($code);
        if ($code != "")
            $this->code = $code;
    }

    public function setTitle($title) {
        // to fix the bug where the first letter after a bracket is not capitalised
        $title = str_replace('( ', '(', ucwords(strtolower(str_replace('(', '( ', trim($title)))));
        if ($title != "")
            $this->title = $title;
    }

    public function setAu($au) {
        $au = ucwords(strtolower(trim($au)));
        if ($au != "")
            $this->au = $au;
    }

    public function setCourseType($type) {
        $type = ucwords(strtolower(trim($type)));
        if ($type != "")
            $this->courseType = $type;
    }

    public function setSu($su) {
        $su = ucwords(strtolower(trim($su)));
        if ($su != "")
            $this->su = $su;
    }

    public function setGerType($type) {
        $type = ucwords(strtolower(trim($type)));
        if ($type != "")
            $this->gerType = $type;
    }

    public function setIndex($index) {
        $index = ucwords(strtolower(trim($index)));
        if ($index != "")
            $this->index = $index;
    }

    public function setStatus($status) {
        $status = ucwords(strtolower(trim($status)));
        if ($status != "")
            $this->status = $status;
    }


    public function setChoice($choice) {
        $choice = ucwords(strtolower(trim($choice)));
        if ($choice != "")
            $this->choice = $choice;
    }

    public function setClassType($type) {
        $type = trim($type);
        if ($type == "")
            return;

        switch ($type) {
            case 'TUT':
                $this->classType = ClassType::Tutorial();
                break;

            case 'LEC/STUDIO':
                $this->classType = ClassType::Lecture();
                break;

            case 'LAB':
                $this->classType = ClassType::Lab();
                break;
        }
    }

    public function setGroup($group) {
        $group = trim($group);
        if ($group != "")
            $this->group = $group;
    }

    public function setDay($day) {
        $day = trim($day);
        if ($day == "")
            return;

        switch ($day) {
            case 'M':
                $day = DaysOfWeek::Monday();
                $date = date('Y-m-d', strtotime("monday", strtotime(Dates::getTermStart())));
                break;

            case 'T':
                $day = DaysOfWeek::Tuesday();
                $date = date('Y-m-d', strtotime("tuesday", strtotime(Dates::getTermStart())));
                break;

            case 'W';
                $day = DaysOfWeek::Wednesday();
                $date = date('Y-m-d', strtotime("wednesday", strtotime(Dates::getTermStart())));
                break;

            case 'TH':
                $day = DaysOfWeek::Thursday();
                $date = date('Y-m-d', strtotime("thursday", strtotime(Dates::getTermStart())));
                break;

            case 'F':
                $day = DaysOfWeek::Friday();
                $date = date('Y-m-d', strtotime("friday", strtotime(Dates::getTermStart())));
                break;

            default:
                $day = NULL;
                $date = NULL;
        }

        if (date('Y-m-d', strtotime($date.' + 13 weeks')) > Dates::getTermEnd())
            $this->weeksCount -= 1;

        $this->day = $day;
        $this->startDate = $date;
    }

    public function setTime($time) {
        $time = trim($time);
        if ($time == "" || strpos($time, '-') === false)
            return;

        $time = explode('-', $time);
        $this->startTime = $time[0];
        $this->endTime = $time[1];
    }

    public function setPlace($place) {
        $place = trim($place);
        if ($place != "")
            $this->place = $place;
    }

    public function setRemark($remark) {
        $remark = trim($remark);
        if ($remark != "")
            $remark = str_replace("Teaching Wk", "", $remark);

        if (strpos($remark, '-') !== false) {
            $this->remark = TeachingWeeks::All();
            return;
        }

        $remark = explode(",", $remark);
        $even = 0;
        foreach ($remark as $r) {
            if (! $r%2)
                $even++;
        }
        if ($even == sizeof($remark))
            $this->remark = TeachingWeeks::Even();
        else
            $this->remark = TeachingWeeks::Odd();
        
        $this->weeksCount = sizeof($remark);

        // start lesson x weeks later
        $this->startDate = date('Y-m-d', strtotime($this->startDate."+ ".($remark[0]-1)." weeks"));
    }

    public function getCode() {
        return $this->code;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getAu() {
        return $this->au = $au;
    }

    public function getCourseType() {
        return $this->courseType = $type;
    }

    public function getSu() {
        return $this->su;
    }

    public function getGerType() {
        return $this->gerType;
    }

    public function getIndex() {
        return $this->index;
    }

    public function getStatus() {
        return $this->status;
    }

    public function getChoice() {
        return $this->choice;
    }

    public function getClassType() {
        return $this->classType;
    }

    public function getGroup() {
        return $this->group;
    }

    public function getDay() {
        return $this->day;
    }
    
    public function getStartDate() {
        return date('Y-m-d\TH:i:s', strtotime($this->startDate . ' ' . $this->startTime));
    }

    public function getStartTime() {
        return $this->startTime;
    }
    
    public function getEndDate() {
        return date('Y-m-d\TH:i:s', strtotime($this->startDate . ' ' . $this->endTime));
    }

    public function getEndTime() {
        return $this->endTime;
    }

    public function getPlace() {
        
        if (NTUPlace::getPlace($this->place)) {
            return NTUPlace::getPlace($this->place);
        }
        
        $string = 'http://maps.ntu.edu.sg/a/search?q=' . $this->place;
        $query = json_decode(file_get_contents($string), true);
        
        if (isset($query['what']['businesses'][0]['unit_number']) && $query['what']['businesses'][0]['unit_number']) {
            
            // location found
            $title = $this->place . ' (' . $query['what']['businesses'][0]['unit_number'] . ')';
            NTUPlace::addPlace($this->place, $title);
            return $title;
        
        } else {
            
            // isset($query['what']['suggestions']) && ! $query['what']['suggestions']
            // location not found
            NTUPlace::addPlace($this->place);
            return $this->place;
        }
        
    }

    public function getRemark() {
        return $this->remark;
    }

    public function getWeeksCount() {
        return $this->weeksCount;
    }
    
    public function getInterval() {
        return ($this->remark == TeachingWeeks::All()) ? 1 : 2;
    }
}

?>