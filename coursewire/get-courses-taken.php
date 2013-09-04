<?php
/**
 * Called by course-history.php
 * Returns a list of formatted courses and an array of courses taken
 */
session_start();
require_once('MDB2.php');
require_once('/home/cs304/public_html/php/MDB2-functions.php');
require_once('search-helper.php');
require_once('coursewire-dsn.inc');
$dbh = db_connect($coursewire_dsn);
  
// get all taken courses taken by given user
$query = 'SELECT subject_code,course_number,course_title FROM classes WHERE student_id = ?';
$resultset = prepared_query($dbh,$query,array($_SESSION['user']));

$courses_taken = array();
$courses_info = array();

if ($resultset->numRows() > 0) {    
  while ($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {       
    $course_data = $row['subject_code'].' '.$row['course_number'];
    array_push($courses_taken,$course_data); // first store course in array
    // add title and remove button
    if ($row['course_title'] != '') $course_data .= ': '.$row['course_title'];
    $course_data .= ' <a href="javascript:void(0);" class="remove" id='.$row['subject_code'].'-'.$row['course_number'].'>remove</a>';
    array_push($courses_info,$course_data);
  }
}

$data = array(); // JSON array to be returned
$course_list = implode('<br>',$courses_info);
$data['courses'] = $courses_taken;
$data['list'] = $course_list;

echo json_encode($data);
?>