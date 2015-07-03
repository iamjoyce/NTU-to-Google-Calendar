<?php
require('assets/php/require.php');
require('assets/php/lib/2.7.15/simple_html_dom.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>https://github.com/iamjoyce :: NTU timetable-Google calendar</title>
<!-- bootstrap scripts -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
<link rel="stylesheet" href="assets/css/bootstrap/datepicker/2.7.15/bootstrap-datepicker.min.css">
<!-- custom scripts -->
<link rel="stylesheet" href="assets/css/custom.css">
<link href='http://fonts.googleapis.com/css?family=Lato:300,400%7CRaleway:100,400,300,500,600,700%7COpen+Sans:400,500,600' rel='stylesheet' type='text/css'>

<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
</head>
<body>
    <?php
    if (! isset($_POST) || empty($_POST) || ! isset($_POST['text']) || empty($_POST['text'])) {
        
        $error = 'Source code required';

    } else {

        /** 1. get course information from html table **/
        if (isset($_POST['start']))
            Dates::setTermStart($_POST['start']);
        if (isset($_POST['end']))
            Dates::setTermEnd($_POST['end']);
        if (isset($_POST['recess']))
            Dates::setTermRecess($_POST['recess']);

        $course = new Course();
        $class = array();

        $skip = TRUE; // skip first row
        $html = str_get_html($_POST["text"]);
        foreach($html->find('tr') as $row) {

            if ($skip) { 
                $skip = FALSE;
                continue;
            }

            $num = 0;
            $course->setWeeksCount(14);
            foreach ($row->find('td') as $col) {

                $text = trim($col->innertext);
                switch ($num++) {
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

            $temp = clone $course;
            array_push($class, $temp);
        }   
    }
    ?>
    <div class="container">
        <div class="row" style="display: none" id="authorize-div">
            <div class="col-md-8 col-md-offset-2">
                <button id="authorize-button" class="btn btn-danger btn-lg btn-block" onclick="handleAuthClick(event)">Require authorisation to Google Calendar</button>
            </div>
        </div>
        <!-- ./ row -->
        <div class="row" style="display: none" id="form-div">
            <div class="col-md-8 col-md-offset-2">
                <?php if (isset($error) && ! empty($_POST)) { ?>
                <div class="alert alert-danger" role="alert">
                    <p>
                        <strong>Error: </strong><?=$error;?>
                    </p>
                </div>
                <?php } ?>
                <div class="alert alert-success" role="alert">
                    <p>
                        <strong>Steps:</strong><br>
                        <ol>
                            <li><a href="https://sso.wis.ntu.edu.sg/webexe88/owa/sso_redirect.asp?t=1&app=https://wish.wis.ntu.edu.sg/pls/webexe/aus_stars_check.check_subject_web2">Login to NTU website</a></li>
                            <li>Go to the term you wish to generate calendar (e.g. 2015-2016 Semester 1)</li>
                            <li>Right-click the page</li>
                            <li>Click on "View Page Source"</li>
                            <li>Copy the contents of the page source</li>
                            <li>Paste into the textbox below</li>
                        </ol>
                    </p>
                    <p class="text-danger">
                        <strong>Note:</strong> Check your calendar before submitting another request<br>
                        There are no success or failure messages at the moment.
                    </p>
                </div>
                <form action="index.php" method="post">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="text">Source Code</label>
                            <textarea rows="10" class="form-control" name="text" id="text" placeholder="Enter source code"></textarea>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="start">Start Term Date</label>
                            <input type="text" class="form-control" name="start" id="start" value="<?=date('d M Y', strtotime(Dates::TermStart()));?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="end">End Term Date</label>
                        <input type="text" class="form-control" name="end" id="end" value="<?=date('d M Y', strtotime(Dates::TermEnd()));?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="end">Recess Week Date (Monday)</label>
                        <input type="text" class="form-control" name="recess" id="recess" value="<?=date('d M Y', strtotime(Dates::TermRecess()));?>">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="calendar">Google Calendar</label>
                            <input type="text" class="form-control" name="calendar" id="calendar" placeholder="Name of calendar to save timetable">
                            <span id="helpBlock" class="help-block">If none indicated, the default name is "NTU GOOGLE CAL"</span>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <input type="submit" class="btn btn-warning btn-lg btn-block" value="Submit" />
                    </div>
                </form>
            </div>
            <!-- ./ col-md-8 -->
        </div>
        <!-- ./ row -->
    </div>
    <!-- ./ container -->
    <script type="text/javascript">
        var CLIENT_ID = '469136601136-8bct92qfdit373n94qalof6fh7kjtlf1.apps.googleusercontent.com';
        var SCOPES = ["https://www.googleapis.com/auth/calendar"];

        /**
        * Check if current user has authorized this application.
        */
        function checkAuth() {
            gapi.auth.authorize({
                'client_id': CLIENT_ID,
                'scope': SCOPES,
                'immediate': true
            }, handleAuthResult);
        }


        /**
        * Handle response from authorization server.
        * @param {Object} authResult Authorization result.
        */
        function handleAuthResult(authResult) {
            var authorizeDiv = document.getElementById('authorize-div');
            var formDiv = document.getElementById('form-div');
            if (authResult && !authResult.error) {
                // Hide auth UI, then load client library.
                authorizeDiv.style.display = 'none';
                formDiv.style.display = 'inline';
                loadCalendarApi();
            } else {
                // Show auth UI, allowing the user to initiate authorization by
                // clicking authorize button.
                authorizeDiv.style.display = 'inline';
            }
        }

        /**
        * Initiate auth flow in response to user clicking authorize button.
        * @param {Event} event Button click event.
        */
        function handleAuthClick(event) {
            gapi.auth.authorize({
                client_id: CLIENT_ID,
                scope: SCOPES,
                immediate: false
            }, handleAuthResult);
            return false;
        }

        /**
        * Load Google Calendar client library
        */
        function loadCalendarApi() {
            gapi.client.load('calendar', 'v3', main);
        }

        function main() {
            <?php if (! empty($class)) { ?>
            createEvents(<?=$_POST['calendar'];?>);
            <?php } ?>
//            deleteFutureEvents();
//            populateCalendar();
        }

        function populateCalendar() {
            var batch = gapi.client.newHttpBatch();
            batch.add(getCalendarList(), { id: 'calendarList' });
    //            batch.add(createCalendar("NEW NEW"), {id: 'derp' });
            batch.execute(function(resp, raw) {
                var calendarList = resp.calendarList.result.items;
            var batch2 = gapi.client.newHttpBatch();
                if (calendarList.length > 0) {
                    for (i = 0; i < calendarList.length; i++) {
                        var summary = calendarList[i].summary;
                        var value = calendarList[i].id;

                        if (calendarList[i].summary.toLowerCase() == 'new new') {
                            console.log(value);
                            batch2.add(deleteCalendar(value));
                        }

                        var addNewOption = function(summary, value) {
                            var select = document.getElementById('calendar');
                            var opt = document.createElement('option');
                            opt.innerHTML = summary;
                            opt.value = value;
                            select.appendChild(opt);
                        }
                        addNewOption(summary, value);
                    }
                 }

                batch2.execute(function(resp, raw) {
                    console.log(resp);
                });
            });
        }

        <?php if (! empty($class)) { ?>
        function createEvents(calendarId) {
            var addBatch = gapi.client.newHttpBatch();
            addBatch.add(createCalendar(calendarId), { id: 'newCalendar' });

            <?php
            /** 2. create calendar information **/
            foreach ($class as $course) {
                $start = date('Y-m-d\TH:i:s', strtotime($course->startDate.' '.$course->startTime));
                $end = date('Y-m-d\TH:i:s', strtotime($course->startDate.' '.$course->endTime));
                $interval = ($course->remark == TeachingWeeks::All) ? 1 : 2;

                $event = array();
                $event['summary'] = $course->code.' '.$course->title;
                $event['location'] = $course->place;
                $event['description'] = $course->group;
                $event['start'] = array('dateTime' => $start,
                                        'timeZone' => TIMEZONE);
                $event['end'] = array('dateTime' => $end,
                                      'timeZone' => TIMEZONE);
                $event['recurrence'] = array('RRULE:FREQ=WEEKLY;COUNT='.$course->weeksCount.';INTERVAL='.$interval);

                $js_array = json_encode($event);
                echo "var event = ". $js_array . ";\n";
            }
            ?>
            addBatch.add(addEvent(event,calendarId));
            var timeMin = "<?php echo date('Y-m-d\TH:i:s\Z', strtotime(Dates::TermRecess())); ?>";
            var timeMax = "<?php echo date('Y-m-d\TH:i:s\Z', strtotime(Dates::TermRecess()."+ 7 days")); ?>";
            var params = {
                'timeMin' : timeMin,
                'timeMax' : timeMax,
                'calendarId' : calendarId
            };
            addBatch.add(viewEvent(params), { id: 'recessView' });

            addBatch.execute(function(resp, raw) {
                console.log("Added classes to calendar");
                var deleteBatch = gapi.client.newHttpBatch(); // delete recess week
                var events = resp.recessView.result.items;
                if (events.length > 0) {
                    for (i = 0; i < events.length; i++) {
                        deleteBatch.add(deleteEvent(events[i].id), { id: i });
                    }
                }
                deleteBatch.execute(function(resp, raw) {
                    console.log("Deleted recess week classes");
                    for (i = 0; i < Object.size(resp); i++) {
                        console.log((resp[i].result == "") ? 'Success' : 'Failed');
                    };
                });
            });
        }
        <?php } // check if $class is empty ?>

        // delete future events
        function deleteFutureEvents() {
            var batch = gapi.client.newHttpBatch();

            var timeMin = (new Date()).toISOString();
            var timeMax = (new Date(new Date().getTime()+(300*24*60*60*1000))).toISOString();
            var maxResults = 200;
            var params = {
                'timeMin' : timeMin,
                'timeMax' : timeMax,
                'maxResults' : maxResults
            };
            batch.add(viewEvent(params), { id: 'view' });
            batch.execute(function(resp, raw) {
                var events = resp.view.result.items;
                if (events.length > 0) {
                    var batch2 = gapi.client.newHttpBatch();
                    for (i = 0; i < events.length; i++) {
                        batch2.add(deleteEvent(events[i].id), { id: i });
                    }

                    batch2.execute(function(resp, raw) {
                        for (i = 0; i < Object.size(resp); i++) {
                            console.log((resp[i].result == "") ? 'Success' : 'Failed');
                        };
                    });
                }
            });
        }

        var addEvent = function(event, calendarId) {
            calendarId = typeof calendarId !== 'undefined' ? calendarId : 'primary';
            return gapi.client.calendar.events.insert({
                'calendarId': calendarId,
                'resource': event
            });
        }

        var deleteEvent = function(eventId, calendarId) {
            calendarId = typeof calendarId !== 'undefined' ? calendarId : 'primary';
            return gapi.client.calendar.events.delete({
                 'calendarId': calendarId,
                 'eventId': eventId
            });
        }

        var viewEvent = function(params) {
            var defaults = {
                'calendarId'    : 'primary',
                'maxResults'    : 20,
                'timeMin'       : (new Date(2011,1,1)).toISOString(),
                'timeMax'       : (new Date(new Date().getTime()+(7*24*60*60*1000))).toISOString(),
                'showDeleted'   : false,
                'singleEvents'  : true,
                'orderBy'       : 'startTime'
            };
            params = MergeRecursive(defaults, params);
            return gapi.client.calendar.events.list(params);
        }

        var getCalendarList = function() {
            return gapi.client.calendar.calendarList.list();
        }

        var createCalendar = function(summary) {
            summary = typeof summary !== 'undefined' ? summary : 'NTU GOOGLE CAL';
            return gapi.client.calendar.calendars.insert({
                'summary' : summary
            });
        }

        var deleteCalendar = function(calendarId) {
            calendarId = typeof calendarId !== 'undefined' ? calendarId : 'primary';
            return gapi.client.calendar.calendars.delete({
                'calendarId' : calendarId
            });
        }
    </script>
    
    <!-- bootstrap scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script src="assets/js/bootstrap/datepicker/2.7.15/bootstrap-datepicker.min.js"></script>
    <!-- custom scripts -->
    <script src="assets/js/custom/utils.js"></script>
    <script src="assets/js/custom/cal.js"></script>
    <script src="https://apis.google.com/js/client.js?onload=checkAuth"></script>
</body>
</html>