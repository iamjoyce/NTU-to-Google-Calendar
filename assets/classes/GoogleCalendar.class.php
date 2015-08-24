<?php

define('TIMEZONE', 'Asia/Singapore');

class GoogleCalendar extends GoogleAuth {
    
    /** Create a new secondary calendar **/
    function createCalendar($calendarName) {

        try {
            
            $calendar = new Google_Service_Calendar_Calendar();
            $calendar->setSummary($calendarName);
            $calendar->setTimeZone(TIMEZONE);
            $createdCalendar = self::$service->calendars->insert($calendar);

            if (self::$batch != null) {
                $batchName = time() + rand();
                self::$batch->add($createdCalendar, $batchName);
                return 'response-' . $batchName;
            } else {
                return $createdCalendar->getId();
            }
            
        } catch (Google_Auth_Exception $e) {
            
            // token expired
            $this->expiredToken();
            
        } catch (Google_Service_Exception $e) {
            
            $this->exceptionCode($e);
        }
    }
    
    /** Delete a secondary calendar **/
    function deleteCalendar($calendarId) {

        try {
            
            $deletedCalendar = self::$service->calendars->delete($calendarId);

            if (self::$batch != null) {
                $this->batch();
                $batchName = time() + rand();
                self::$batch->add($deletedCalendar, $batchName);
                $this->execute();
                return 'response-' . $batchName;
            } else {
                return $deletedCalendar->getId();
            }
            
        } catch (Google_Auth_Exception $e) {
            
            // token expired
            $this->expiredToken();
            
        } catch (Google_Service_Exception $e) {
            
            $this->exceptionCode($e);
        }
    }
    
    /** Create event
     * @eventParams start       yyyy-mm-dd
     * @eventParams end         yyyy-mm-dd
     *
     */
    function createEvent($calendarId, $eventParams = array()) {
            
        try {
            
            $event = new Google_Service_Calendar_Event();
            $event->setSummary($eventParams['summary']);

            $start = new Google_Service_Calendar_EventDateTime();
            $start->setDate($eventParams['start']);
            $start->setTimeZone(TIMEZONE);
            $event->setStart($start);

            $end = new Google_Service_Calendar_EventDateTime();
            $end->setDate($eventParams['end']);
            $end->setTimeZone(TIMEZONE);
            $event->setEnd($end);
            
            $newEvent = self::$service->events->insert($calendarId, $event);

            if (self::$batch != null) {
                $batchName = time() + rand();
                self::$batch->add($newEvent, $batchName);
                return 'response-' . $batchName;
            } else {
                return $newEvent->getId();
            }
            
        } catch (Google_Auth_Exception $e) {
            
            // token expired
            $this->expiredToken();
            
        } catch (Google_Service_Exception $e) {
            
            $this->exceptionCode($e);
        }
    }

    /** Create recurring event **/
    /**
     * @params summary          Name of event
     * @params description      Description of event
     * @params location         Location of event
     * @params start            Start datetime of event
     * @params end              End datetime of event
     * @params frequency        WEEKLY, DAILY, HOURLY, MONTHLY, YEARLY
     *                          (default) WEEKLY
     * @params until            Stop datetime of recurrence (inclusive)
     * @params count            Number of times to repeat recurrence
     * @params interval         Number of repetitions
     * @params repeatDay
     **/
    function createRecurringEvent($calendarId, $eventParams = array()) {
        
        try {
            
            $params = array('frequency' => 'WEEKLY');
            $params = array_merge($params, $eventParams);

            $event = new Google_Service_Calendar_Event();
            $event->setSummary($params['summary']);
            $event->setLocation((isset($params['location']) ? $params['location'] : ''));

            $start = new Google_Service_Calendar_EventDateTime();
            $start->setDateTime($params['start']);
            $start->setTimeZone(TIMEZONE);
            $event->setStart($start);

            $end = new Google_Service_Calendar_EventDateTime();
            $end->setDateTime($params['end']);
            $end->setTimeZone(TIMEZONE);
            $event->setEnd($end);

            $rule = 'RRULE:';
            $rule .= (isset($params['frequency'])) ? 'FREQ=' . $params['frequency'] . ';' : '';
            $rule .= (isset($params['until'])) ? 'UNTIL=' . $params['until'] . ';' : '';
            $rule .= (isset($params['count'])) ? 'COUNT=' . $params['count'] . ';' : '';
            $rule .= (isset($params['interval'])) ? 'INTERVAL=' . $params['interval'] . ';' : '';
            $rule .= (isset($params['repeatDay'])) ? 'BYDAY=' . $params['repeatDay'] . ';' : '';
            $event->setRecurrence(array($rule));

            $recurringEvent = self::$service->events->insert($calendarId, $event);

            if (self::$batch != null) {
                $batchName = time() + rand();
                self::$batch->add($recurringEvent, $batchName);
                return 'response-' . $batchName;
            } else {
                return $recurringEvent->getId();
            }
            
        } catch (Google_Auth_Exception $e) {
            
            // token expired
            $this->expiredToken();
            
        } catch (Google_Service_Exception $e) {
            
            $this->exceptionCode($e);
        }
    }

    /** Delete all events within a timeframe **/
    function deleteEvents($calendarId, $eventParams = array()) {

        try {
            
            $this->batch();
            $responseId = $this->listEvents($calendarId);
            $response = $this->execute();
            
            if ($response[$responseId]->getItems()) {
                
                $responseIdList = array();
                
                $this->batch();
                foreach ($response[$responseId]->getItems() as $event) {
                    $responseId = $this->listRecurringInstance($calendarId, $event->getId(), $eventParams);
                    array_push($responseIdList, $responseId);
                }
                $response = $this->execute();
                
                $this->batch();
                foreach ($responseIdList as $responseId) {
                    
                    foreach ($response[$responseId]->getItems() as $event) {
                        
                        $this->deleteEvent($calendarId, $event->getId());
                    }
                }
                $this->execute();
            }
            
        } catch (Google_Auth_Exception $e) {
            
            // token expired
            $this->expiredToken();
            
        } catch (Google_Service_Exception $e) {
            
            $this->exceptionCode($e);
        }
    }
    
    function listRecurringInstance($calendarId, $eventId, $eventParams = array()) {
        
        try {
            
            $events = self::$service->events->instances($calendarId, $eventId, $eventParams);

            if (self::$batch != null) {
                $batchName = time() + rand();
                self::$batch->add($events, $batchName);
                return 'response-' . $batchName;
            } else {
                return $events->getId();
            }
            
        } catch (Google_Auth_Exception $e) {
            
            // token expired
            $this->expiredToken();
            
        } catch (Google_Service_Exception $e) {
            
            $this->exceptionCode($e);
        }
    }
    
    function listEvents($calendarId, $eventParams = array()) {
        
        try {
            
            $events = self::$service->events->listEvents($calendarId, $eventParams);

            if (self::$batch != null) {
                $batchName = time() + rand();
                self::$batch->add($events, $batchName);
                return 'response-' . $batchName;
            } else {
                return $events->getId();
            }
            
        } catch (Google_Auth_Exception $e) {
            
            // token expired
            $this->expiredToken();
            
        } catch (Google_Service_Exception $e) {
            
            $this->exceptionCode($e);
        }
    }
    
    /** Delete an event **/
    function deleteEvent($calendarId, $eventId) {
        
        try {
            
            $deletedEvent = self::$service->events->delete($calendarId, $eventId);

            if (self::$batch != null) {
                $batchName = time() + rand();
                self::$batch->add($deletedEvent, $batchName);
                return 'response-' . $batchName;
            } else {
                return $deletdEvent->getId();
            }
            
        } catch (Google_Auth_Exception $e) {
            
            // token expired
            $this->expiredToken();
            
        } catch (Google_Service_Exception $e) {
            
            $this->exceptionCode($e);
        }
    }
    
    function enableBatch($enable = true) {
        self::$client->setUseBatch($enable);
    }
    
    function batch() {
        self::$batch = new Google_Http_Batch(self::$client);
    }
    
    /** Execute existing batch commands **/
    function execute() {
        
        if (self::$batch != null) {
            return self::$batch->execute();
        }
    }
    
    function redirect($query) {
        
        $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '?' . $query;
        header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
    }
    
    function expiredToken() {
        
        $_SESSION['access_token'] = '';
        $this->redirect();
    }
    
    function exceptionCode($e) {
            
        if ($e->getCode() == 403) {

            // limit reached
            $this->redirect('limit_reached');
            
        } else {
            
            echo $e;
        }
    }
}

?>