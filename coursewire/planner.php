<?php
/**
 * Shows shopped courses by term and displays them on a calendar
 */
include('header.php');
require_once('search-helper.php');

// check if logged in
if(!$loggedIn) {
  echo '<a href="?loginCAS">Please log in with CAS to see your prospective schedule.</a>';
} else {
?>
<!-- required files for FullCalendar plugin -->
<link href='<?php echo $path ?>calendar/fullcalendar.css' rel='stylesheet' />
<link href='<?php echo $path ?>calendar/fullcalendar.print.css' rel='stylesheet' media='print' />
<script src='<?php echo $path ?>calendar/fullcalendar.min.js'></script>
<script>
$(document).ready(function() {
  courses = new Object(); // used to store event source objects for event toggling
  
  // calendar settings
  $('#calendar').fullCalendar({
    header: false,
    height: 700,
    defaultView: 'agendaWeek',
    columnFormat: { week: 'dddd' },
    // need to set week to avoid calculating days of the week when adding courses to calendar
    year: 2013,
    month: 4, // month is 0-based for whatever reason
    date: 6,
    slotMinutes: 15,
    weekends: false,
    minTime: 8,
    maxTime: 22,
    allDaySlot: false,
    editable: false,
    eventClick: function(calEvent, jsEvent, view) {
      // when event is clicked, get term code and crn from id
      var info = (calEvent.id).split('-');
      // display course details
      show_course_info(calEvent.title,info[0],info[1]);
    }
  });
  
  // update calendar when term code is changed
  $('#term_code').change( function () {
    // remove all events currently displayed
    $('#calendar').fullCalendar('removeEvents');
    // update displayed courses
    update_shopped();
  });
  
  // display courses on calendar
  update_shopped();  
});

// get and display course information for given term
function update_shopped() {
  $.ajax({
    cache: false,
    type: 'POST',
    async: false,
    url: '<?php echo $path ?>get-planner-courses.php',
    dataType: 'json',
    data: 'term_code='+$('#term_code').val(),
    success: function (data) {
      // show list of courses on sidebar
      $('#shopped-courses').html(data.course_list);
      // add array of course data to global for toggling
      var course_data = data.course_data;
      //alert(JSON.stringify(course_data));
      $.each(course_data, function() {
        // create event source for course
        var source = make_course_source(this);
        // add event source to global variable
        courses[this['id']] = source;
        // display on calendar
        $('#calendar').fullCalendar('addEventSource',source);            
      });
    },
    error: function (response) {
      alert("error: " + response.responseText);
    }
  });
}

// create event source using course data
function make_course_source(course_info) {
  var courseMeetings = new Array(); // array of all event sources for course
  var meetings = course_info['meetings']; // info for meetings of course
  
  // $.each () did not work here for some reason
  for (var i=0; i<meetings.length; i++) {
    // create event for calendar
    var courseEvent = new Object();
    courseEvent.id = meetings[i]['id'];
    courseEvent.title = meetings[i]['title'];
    courseEvent.start = meetings[i]['start'];
    courseEvent.end = meetings[i]['end'];
    courseEvent.allDay = false;        
    courseMeetings.push(courseEvent);
  }
  // event source object for all meetings of given course
  var source = new Object(); 
  // add meetings to events in source object
  source.events = courseMeetings; 
  // randomly generate color for event background
  source.color = '#'+(0x1000000+(Math.random())*0xffffff).toString(16).substr(1,6);  
  return source;
}

// display course details for given course
function show_course_info(title,term_code,crn) {
  $.ajax({
   type: "GET",
   url: '<?php echo $path ?>get-details.php',
   data: "term_code="+term_code+"&crn="+crn+"&sections=false",
   success: function(data) {
      // add button to close modal
      var close_button = '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>';
      // add html to modal
      $('.modal-header').html(close_button+'<h4>Viewing details for '+title+'</h4>');
      $('.modal-body').html(data);
      // show modal
      $('.modal').modal('show');
    }
  });
}

// need to attach click handler to document as buttons are dynamically added
$(document).on('click','input[type=checkbox]', function () {
  if ($(this).is(":checked")) { 
    // add events for course to calendar when box is checked
    $('#calendar').fullCalendar('addEventSource',courses[this.id]);
  } else {
    // remove events for course from calendar when box is unchecked
    $('#calendar').fullCalendar('removeEventSource',courses[this.id]);
  }
});
</script>
<div id="course-list">
<select id="term_code" class="span2">
<?php options($termList); ?>
</select>
<div id="shopped-courses"></div>
<small>Click on a course on the calendar to view its details.</small>
</div>
<div id="calendar"></div>
<div class="modal hide fade">
  <div class="modal-header">
  </div>
  <div class="modal-body">
  </div>
</div>
<?php } // ending else
include('footer.php');
?>