<?php
/**
 * Front page
 * If not logged in, displays marking stuff to entice viewers to use the site
 * If logged in, displays course recommendations
 */
include('header.php');

// if logged in, display marketing spiel
if(!$loggedIn) { ?>
<div class="hero-unit">
  <h2>Coursewire helps you explore Wellesley courses.</h2>
  <p>Registration can be one of the most stressful times in the semester. It doesn't have to be. Sign in with your Wellesley domain account to start tracking your courses and get personal recommendations.</p>
  <p class="text-right"><a href="?loginCAS" class="btn btn-primary btn-large">Get started</a></p>
</div>
<div class="row">
<div class="span4">
  <h3>Advanced search</h3>
  <p>Ever wanted to find the perfect class to fill that gap in your schedule? Now you can search to your heart's desire. Search by course number, instructor, keyword, day of the week, time of day... even distribution requirements!</p>
  <p><a class="btn" href="<?php echo $path ?>search/">Try it now &raquo;</a></p>
</div>
<div class="span4">
  <h3>Plan your schedule</h3>
  <p>Put away the pen and paper. Want to visualize your prospective courses? All you have to do is click on a button for a course to show up on the calendar. Toggle them on and off to build the schedule of your dreams. You're welcome.</p>
  <p><a class="btn" href="?loginCAS">Sign in &raquo;</a></p>
</div>
<div class="span4">
  <h3>Recommendations</h3>
  <p>Sometimes it's hard to choose. Let us help! We will use all information you give us to give you useful course recommendations. Ever wonder what the most popular courses are at Wellesley? They might surprise you (or not).</p>
  <p><a class="btn" href="?loginCAS">Sign in &raquo;</a></p>
</div>
</div>  
<?php
} else {
  // logged in, display recommended courses
  echo '<div id="recommended">';
  echo "<legend>Course recommendations</legend>";
  get_popular_courses();
  if (isset($_SESSION['major1']) and $_SESSION['major1'] != '') { echo "<br>"; get_major_courses($_SESSION['major1']); }
  if (isset($_SESSION['major2']) and $_SESSION['major2'] != '') { echo "<br>"; get_major_courses($_SESSION['major2']); }
  echo '<br><br><small>Seeing courses you\'ve already taken? Want more recommendations? <a href="settings/">Update your course history!</a></small>';
}    
?></div>
<?php
include('footer.php'); 

/*
 * Generates and formats top 10 courses taken by students with given major
 *
 * @param $major, a major code
 */
function get_major_courses($major) {
  global $dbh;
  
  // get top 10 courses taken by students other than user with major not yet taken by user
  $query = 'SELECT subject_code,course_number,count(*) as freq FROM classes
  WHERE student_id in (SELECT student_id FROM students WHERE (major1=? or major2=?) and student_id<>?)
  and concat(subject_code,course_number) not in (SELECT concat(subject_code,course_number) FROM classes WHERE student_id=?)
  GROUP BY concat(subject_code,course_number) ORDER BY freq DESC LIMIT 10';
  $resultset = prepared_query($dbh,$query,array($major,$major,$_SESSION['user'],$_SESSION['user']));

  if ($resultset->numRows() > 0) {
    $major_courses = array();
    while ($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
      // format course with link to search query
      $course_data = <<< EOF
      <a href="search/q?course_number={$row['subject_code']}+{$row['course_number']}">{$row['subject_code']} {$row['course_number']}</a>
EOF;
      array_push($major_courses,$course_data);
    }
  }
  echo "<h5>Courses taken by other ".$major." majors</h5>";
  echo implode(', ',$major_courses); // create list of courses
}

/*
 * Generates and formats top 10 courses taken by all students
 */
function get_popular_courses() {
  global $dbh;
  
  // get top 10 courses taken by all students not yet taken by user
  $query = 'SELECT subject_code,course_number,count(*) as freq FROM classes
  WHERE concat(subject_code,course_number) not in (SELECT concat(subject_code,course_number) FROM classes WHERE student_id=?)
  GROUP BY concat(subject_code,course_number) ORDER BY freq DESC LIMIT 10';
  $resultset = prepared_query($dbh,$query,array($_SESSION['user']));

  if ($resultset->numRows() > 0) {
    $courses = array();
    while ($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
      // format course with link to search query
      $course_data = <<< EOF
      <a href="search/q?course_number={$row['subject_code']}+{$row['course_number']}">{$row['subject_code']} {$row['course_number']}</a>
EOF;
      array_push($courses,$course_data);
    }
  }
  echo "<h5>Popular courses</h5>";
  echo implode(', ',$courses); // create list of courses
}
?>