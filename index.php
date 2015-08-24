<?php

require_once(dirname(__FILE__) . '/assets/loader.php');

session_start();

$authenticated_user = false;
if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
    $authenticated_user = true;
}

if (isset($_POST['auth'])) {

    new GoogleAuth();
}

if ($authenticated_user && isset($_POST['timetable'])) {
    
    new GoogleAuth();
    $timetable = new Timetable($_POST);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>https://github.com/iamjoyce/ntu-gcal || NTU to Google Calendar</title>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
<link rel="stylesheet" href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.15.35/css/bootstrap-datetimepicker.css" />
<link rel="stylesheet" href="assets/css/custom.css">

<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->

</head>

<body>
<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <a class="navbar-brand" href="https://github.com/iamjoyce/ntu-gcal">ntu-gcal</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-right">
                <li><a href="destroy.php">Click Here to Destroy Session (You will have to re-authorise again)</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <div class="col-md-8 col-md-offset-2">
        <?php if (! $authenticated_user): ?>
        <div class="row">
            <div class="jumbotron">
            <h2>We need authorisation!</h2>
            <p>
                As this web application inserts your NTU timetable into Google Calendar, it will require access to your Google Calendar.<br>
            </p>
            <p>
                Please click the button below to provide required permissions.
            </p>
            <p>
                <form method="post" action="index.php">
                    <input type="hidden" name="auth">
                    <button type="submit" class="btn btn-lg btn-primary">Click here to authorise</button>
                </form>
            </p>
            </div>
        </div>
        <?php else: ?>
        <div class="row">
            <form class="text-left" method="post" action="index.php">
                <div class="col-md-12">
                    <h3>Calendar Names</h3>
                </div>
                <div class="form-group col-md-4">
                    <label for="lectname">Lectures</label>
                    <input type="text" class="form-control" id="lectname" name="lectureCal" value="NTU Lecture">
                    <span class="help-block"><small>Default: NTU Lecture</small></span>
                </div>
                <div class="form-group col-md-4">
                    <label for="tutname">Tutorials</label>
                    <input type="text" class="form-control" id="tutname" name="tutorialCal" value="NTU Tutorial">
                    <span class="help-block"><small>Default: NTU Tutorial</small></span>
                </div>
                <div class="form-group col-md-4">
                    <label for="labname">Labs/Seminars</label>
                    <input type="text" class="form-control" id="labname" name="labCal" value="NTU Lab">
                    <span class="help-block"><small>Default: NTU Lab</small></span>
                </div>
                <div class="col-md-12">
                    <label><h3>Dates</h3></label>
                </div>
                <div class="form-group col-md-4">
                    <label for="startTerm">Start Term</label>
                    <input type="text" class="form-control" id="startTerm" name="startTerm" value="<?=Dates::getTermStart();?>">
                    <span class="help-block"><small>Default: AY 2015-16 Sem 1</small></span>
                </div>
                <div class="form-group col-md-4">
                    <label for="endTerm">End Term</label>
                    <input type="text" class="form-control" id="endTerm" name="endTerm" value="<?=Dates::getTermEnd();?>">
                    <span class="help-block"><small>Refer to <a href="http://www.ntu.edu.sg/Students/Undergraduate/AcademicServices/AcademicCalendar/Pages/AY2015-16.aspx">NTU's Academic Calendar</a></small></span>
                </div>
                <div class="form-group col-md-4">
                    <label for="recess">Recess Week</label>
                    <input type="text" class="form-control" id="recess" name="recess" value="<?=Dates::getTermRecess();?>">
                </div>
                <div class="form-group col-md-12">
                    <label for="source"><h3>Source Code</h3></label>
                    <textarea class="form-control" id="source" name="source" rows="6" value="<?=$_POST['source'];?>"></textarea>
                    <span class="help-block">
                        <small>
                            <a href="sample-source.txt" target="_blank">Click here for sample source code. <strong>Copy and paste</strong> the source code into the textbox.</a><br>
                            <a href="https://sso.wis.ntu.edu.sg/webexe88/owa/sso_redirect.asp?t=1&app=https://wish.wis.ntu.edu.sg/pls/webexe/aus_stars_check.check_subject_web2" target="_blank">Click here to get source code for your <strong>own timetable</strong>.</a>
                        </small>
                    </span>
                </div>
                <div class="col-md-12">
                    <button type="submit" class="btn btn-default" name="timetable" value="true">Submit</button>
                    &nbsp;&nbsp;
                    <label>
                        <input type="checkbox" name="week"> Check the box if you wish to have a separate calendar for week numbers as well
                    </label>
                </div>
            </form>
        </div>
        <?php endif; ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="text-muted">
            Source code available at <a href="https://github.com/iamjoyce/ntu-gcal">https://github.com/iamjoyce/ntu-gcal</a>
        </p>
    </div>
</footer>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.15.35/js/bootstrap-datetimepicker.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/js/ie10-viewport-bug-workaround.js"></script>
<script type="application/javascript">
    $("#startTerm").datetimepicker({
        format: "YYYY-MM-DD"
    }).on("dp.change", function (e) {
        $("#endTerm").data("DateTimePicker").minDate(e.date);
    });
    
    $("#endTerm").datetimepicker({
        format: "YYYY-MM-DD"
    }).on("dp.change", function (e) {
        $("#startTerm").data("DateTimePicker").maxDate(e.date);
    });
    
    $("#recess").datetimepicker({
        format: "YYYY-MM-DD"
    });
</script>
</body>
</html>
