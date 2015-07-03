$(document).ready(function() {

    $("input[name=\"start\"]").datepicker({
        format: 'dd M yyyy',
        weekStart: 1,
        startDate: new Date(),
        daysOfWeekDisabled: [6,0]
    });
    
    $("input[name=\"end\"]").datepicker({
        format: 'dd M yyyy',
        weekStart: 1,
        startDate: new Date(),
        daysOfWeekDisabled: [6,0]
    });
    
    $("input[name=\"recess\"]").datepicker({
        format: 'dd M yyyy',
        weekStart: 1,
        startDate: new Date(),
        daysOfWeekDisabled: [2,3,4,5,6,0]
    });
    
});