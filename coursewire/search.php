<?php
/**
 * Displays form, searches courses and displays results
 * Uses many helper functions from search-helper.php
 */
 include('header.php');
 ?>
<div id="search"> <!-- start search form -->
<legend>Search Wellesley Courses</legend>
<form id="searchForm" method="GET" action="<?php echo $path ?>search/q?">
  <!-- something with these fields is changing the container size -->
  <table id="dropdowns" style="width: 100%;">
  <thead>
  <tr>
    <th class="pull-left"><label>Term</label></th>            
    <th class="pull-right"><label>Department/Subject</label></th>
  </tr>
  </thead>
  <tbody>
  <tr>
  <td class="pull-left">
    <select class="span2" name="term_code">
    <?php options($termList); ?>
    <option value="" <?php if(!empty($_GET) and !isset($_GET['term_code']))     echo 'selected=""';?>>All Terms</option>
    </select>
  </td>
  <td class="pull-right">
    <select name="subject_code">
    <option value="">All Subjects</option>
    <?php options($subjectList); ?>
    </select>
  </td>
  </tr>
  </tbody>
  </table><br>

  <span class="form-inline">
  <label style="width: 130px;">Course Number</label>               
  <input type="search" name="course_number">
  </span>
  <span class="help-block">e.g. CS 111, C* 1*, * 1*, CS+PHIL 1*+2*</span>

  <span class="form-inline" >
  <label style="width: 130px;">Instructor(s)</label>
  <input style="margin-bottom: 20px;" type="search" name="instructors">
  </span>

  <span class="form-inline">
  <label style="width: 130px;">Keyword/Title</label>
  <input style="margin-bottom: 20px;" type="search" name="keyword">
  </span>

  <label>Days</label>
  <span class="help-block">Select the days you would like the course to meet on (e.g. marking all days shows courses that meet on MTWThF)</span>
  <label class="checkbox inline">
  <input type="checkbox" id="allDays" onClick="javascript:uncheckDays(this);"> Any
  </label>
  <?php 
  $attr = '<label class="checkbox inline">
  <input type="checkbox" class="dayOption" name="days[]" ';
  checkboxes($attr,$daysList);        
  ?>
  <br><br>

  <label>Times</label>
  <select class="span2" name="start1">
  <option value="" <?php if(empty($_GET)) echo 'selected=""';?>>8 am</option>
  <?php options($start1List); ?></select>
  to <select class="span2" name="end1"><?php options($end1List); ?>
  <option value="" <?php if(!isset($_GET['end1'])) echo 'selected=""';?>>10 pm</option>
  </select>
  <br>

  <label>Timeslot</label>
  <select class="span4" name="startend1">
  <option value="" <?php if(empty($_GET)) echo 'selected=""';?>></option>
  <?php options($startend1List); ?></select>
  </select>
  <br>

  <label>Distributions</label>
  <?php 
  $attr = '<label class="checkbox">
  <input type="checkbox" name="distrib[]" ';?>
  <table id="distrib" style="width: 100%;">
  <tbody>
  <tr>
    <td><?php checkboxes($attr,array_slice($distribList,0,3)); ?></td>
    <td><?php checkboxes($attr,array_slice($distribList,3,3)); ?></td>
    <td><?php checkboxes($attr,array_slice($distribList,6,2)); ?></td>
    <td><?php checkboxes($attr,array_slice($distribList,8,1)); ?>
    <label class="checkbox">
    <input type="checkbox" name="hasLab" value="with lab" <?php if(isset($_GET['hasLab'])) echo 'checked="checked"';?>> Lab
    </label></td>
  </tr>
  </tbody>
  </table><br>

  <span class="form-inline">
  <label style="width: 60px;">Sort by</label>
  <select name="sort_by">
  <?php options($sortList); ?>
  </select>
  </span>

  <p><center><button type="submit" class="btn btn-primary">Search</button><a class="btn" href="<?php echo $path ?>search/">Reset</a></center>
  </form>

  <div id="search_history"> <!-- start search history -->
  <?php get_search_history(); ?>
  </div> <!-- end search history -->
</div> <!-- end search form -->

<div id="results"> <!-- start results -->
<div id="error" class="alert fade in">
<strong>Please try again.</strong> Remember to include a space between the department and the course number. 
</div>
<?php if (!empty($_GET)) getSearchResults($_GET); ?>        
</div> <!-- end results -->
<?php include('footer.php'); ?>

<?php store_session(); // store current search ?>

<script language="javascript" type="text/javascript">    
$(document).ready(function(){
  <?php
  // if there is a current search, prefill the form
  if (!empty($_GET) and isset($_SESSION['query'])) {
    foreach ($_SESSION['query'] as $key => $value) {
      if (!is_array($value)) echo "$('[name=".$key."]').val('".$value."');";
    }
  }            
  ?>

  // not working at the moment
  // if "Any" is checked for days, uncheck all other days
  $('#anyDay').click (function () {
    alert('changed');
    if (this.checked) {
      var days = document.getElementsByClassName("dayOption");
      for (var i=0; i<days.length; i++) {
        days[i].checked = false;
      }
    }
  });
});

// change results div to show a specific course's details
function getDetail(term_code,crn) {
  $.ajax({
  type: "GET",
  url: '<?php echo $path ?>get-details.php',
  data: "term_code="+term_code+"&crn="+crn,
  success: function(data) {
    $('#results').html(data);
    // add link back to search
  <?php 
  if (isset($_SESSION['past_query'])) echo <<< EOF
  $('#results').append('<a href="{$_SESSION['past_query'][0]['url']}">&laquo; Back to search</a>');
EOF;
  ?>
  }
  });
}

// prevent default form submission to check for errors and clean url
$('#searchForm').submit(function (event) {
  // these are the fields that show in url even when empty
  var fields = new Array('term_code','subject_code','course_number','instructors','keyword','startend1','start1','end1');
  // check and remove name of each field to prevent it showing in url when empty
  for (var i=0; i<fields.length; i++) {
    if ($("#searchForm").find('[name='+fields[i]+']').val() == '') {
      $("#searchForm").find('[name='+fields[i]+']').attr('name','');
    }
  }
  // check course number for correct format
  var courseInput = $("#searchForm").find('[name=course_number]');
  if (courseInput.length > 0) {
    var regEx = /.\s./;
    courseInput = ($.trim(courseInput.val())).replace(/\s\s+/g,' ');
    var courses = courseInput.split(',');
    for (var i=0; i<courses.length; i++) {
      // complain if there is no space between subject code and number
      if (!regEx.test(courses[i])) {
        $('.alert').fadeIn();
        return false;
      }
    }
  }
});

// if Any is checked for Days field, uncheck all other days
function uncheckDays(obj) {
  if (obj.checked) {
    var days = document.getElementsByClassName("dayOption");
    for (var i=0; i<days.length; i++) {
      days[i].checked = false;
    }
  }
}
</script>