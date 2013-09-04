<?php
/**
 * Called by planner.php
 * Returns a list of shopped courses with checkbox to toggle display and event data to be displayed on the calendar
 */
session_start();
require_once('MDB2.php');
require_once('/home/cs304/public_html/php/MDB2-functions.php');
require_once('search-helper.php');
require_once('coursewire-dsn.inc');
$dbh = db_connect($coursewire_dsn);
  
// get all taken courses
$query = 'SELECT s.crn,s.term_code,concat(c.subject_code," ",c.course_number) as course,c.course_title as title,c.days1,c.start1,c.end1,loc1,c.days2,c.start2,c.end2,c.loc2,c.days3,c.start3,c.end3,c.loc3 FROM classes_shopped s INNER JOIN courses c ON (s.crn=c.crn AND s.term_code=c.term_code) WHERE s.student_id = ? AND s.term_code = ?';
$resultset = prepared_query($dbh,$query,array($_SESSION['user'],$_POST['term_code']));

$data = array(); // JSON array to be returned
if ($resultset->numRows() > 0) {    
  $data['course_list'] = array(); // list of courses shopped with checkboxes to toggle display
  $data['course_data'] = array(); // course data to be displayed on calendar

  while ($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
    // create checkbox for course
    $course = <<< EOF
    <label class="checkbox">
    <input type="checkbox" name="{$row['course']}" id={$row['term_code']}-{$row['crn']} checked="checked"> {$row['course']}
    </label>
EOF;
    array_push($data['course_list'],$course);
    
    // put together course information for display
    $course_data = array('id'=>$row['term_code'].'-'.$row['crn'],'term_code'=>$row['term_code'],'crn'=>$row['crn'],'course'=>$row['course']);
    
    // create list of meeting dates and times for calendar display
    $course_data['meetings'] = create_events($row);
    
    array_push($data['course_data'],$course_data);
  }
} else {
  $data['course_list'] = 'You don\'t have any shopped courses for '.$termList[$_POST['term_code']].'.';
}

echo json_encode($data);

/*
 * Create calendar event for each meeting time of course
 *
 * @param $row, row of course information from query
 */
function create_events($row) {
  // FullCalendar plugin specifies days as date, must convert to specific date for week used in calendar
  $date_for_day = array('M'=>'2013-05-06','T'=>'2013-05-07','W'=>'2013-05-08','Th'=>'2013-05-09','F'=>'2013-05-10');
  
  $events_data = array();
  
  // split days list into array
  // splits by anticipating capital letter, so must remove empty first entry        
  $days1 = array_slice(preg_split('/(?=[A-Z])/',$row['days1']),1);
  
  foreach ($days1 as $value) {
    $event = array();
    $event['id'] = $row['term_code'].'-'.$row['crn'];
    $event['title'] = $row['course'].': '.$row['title'];
    $event['start'] = $date_for_day[$value].' '.$row['start1'];
    $event['end'] = $date_for_day[$value].' '.$row['end1'];
    array_push($events_data,$event);
  }

  $days2 = array_slice(preg_split('/(?=[A-Z])/',$row['days2']),1);

  foreach ($days2 as $value) {
    $event = array();
    $event['id'] = $row['term_code'].'-'.$row['crn'];
    $event['title'] = $row['course'].': '.$row['title'];
    $event['start'] = $date_for_day[$value].' '.$row['start2'];
    $event['end'] = $date_for_day[$value].' '.$row['end2'];
    array_push($events_data,$event);
  }

  $days3 = array_slice(preg_split('/(?=[A-Z])/',$row['days3']),1);

  foreach ($days3 as $value) {
    $event = array();
    $event['id'] = $row['term_code'].'-'.$row['crn'];
    $event['title'] = $row['course'].': '.$row['title'];
    $event['start'] = $date_for_day[$value].' '.$row['start3'];
    $event['end'] = $date_for_day[$value].' '.$row['end3'];
    array_push($events_data,$event);
  }
  
  return $events_data;
}
?>