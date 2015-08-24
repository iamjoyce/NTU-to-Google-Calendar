<?php

require_once(dirname(dirname(__FILE__)) . '/vendor/autoload.php');

// enums
require_once(dirname(__FILE__) . '/classes/ClassType.class.php');
require_once(dirname(__FILE__) . '/classes/DaysOfWeek.class.php');
require_once(dirname(__FILE__) . '/classes/TeachingWeeks.class.php');
require_once(dirname(__FILE__) . '/classes/NTUPlace.class.php');

// classes
require_once(dirname(__FILE__) . '/classes/Course.class.php');
require_once(dirname(__FILE__) . '/classes/Dates.class.php');
require_once(dirname(__FILE__) . '/classes/GoogleAuth.class.php');
require_once(dirname(__FILE__) . '/classes/GoogleCalendar.class.php');

require_once(dirname(__FILE__) . '/controller/Timetable.class.php');

?>