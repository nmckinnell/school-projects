<?php
/**
 * Shown in Settings page in settings.php
 * Lists courses user has already taken with a form to add more courses
 */
include('path.php'); // creates $path with current directory
?>

<!-- placeholder div for success messages when a course is successfully added -->
<div id="success" class="alert alert-success fade in">
</div>
<!-- placeholder div for warning messages when a course was attempted to be added a second time -->
<div id="warning" class="alert alert-warning fade in">
</div>
<!-- form to add a new course to course history -->
<form id='courses-form' class='form-inline' method='POST'>
  <table id="info">
  <tbody>
  <tr>
    <td width="80px"><label>Course</label></td>
    <td width="120px"><input class="input-small" type="text" name="course"></td>
    <td><button id="courses-submit" type="submit" class="btn btn-primary">Add</button></td>
  </tr>
  </tbody>
  </table>
</form>
<!-- placeholder to list all courses previously taken -->
<div id="courses_taken">
</div>

<script language="javascript" type="text/javascript">
var courses_taken = new Array();

$(document).ready(function() {
  $('.alert').fadeOut(); // fade out all alerts by default
  update_courses(); // create list of courses taken
});

// removes given course from course history
// need to attach to document as courses will be dynamically added
 $(document).on('click','.remove', function () {  
  $('.alert').fadeOut(); // fade out all alerts if any displayed  
  $.ajax({
    cache: false,
    type: 'POST',              
    url: '<?php echo $path ?>remove-course.php',
    data: 'course='+$(this).attr('id'),
    success: function () {
      // reload list of taken courses
      update_courses();
      // alert user of success
      $('#success').html('Course removed!');
      $('#success').fadeIn();
    },
    error: function (response) {
      alert("error: " + response.responseText);
    }
  });
}); 

// generates the list of already added courses
function update_courses() {
  $.ajax({
    cache: false,
    type: 'POST',              
    url: '<?php echo $path ?>get-courses-taken.php',
    dataType: 'json',
    success: function (data) {
      $('#courses_taken').html(data.list);
      courses_taken = data.courses; // used to check if adding already added course
    },
    error: function (response) {
      alert("error: " + response.responseText);
    }
  });
}
    
$('#courses-form').submit(function (event) {
  event.preventDefault();  
  $('.alert').fadeOut(); // fade out all alerts if displaying
  
  // check course number for correct format
  var courseInput = $("#courses-form").find('[name=course]');
  if (courseInput.length > 0) {
    var regEx = /.\s./;
    courseInput = ($.trim(courseInput.val())).replace(/\s\s+/g,' ');
    if (!regEx.test(courseInput)) {
      $('#warning').html('Please include a space between the subject code and the course number.');
      $('#warning').fadeIn();
      return false;
    }
  }

  var input = $('[name=course]').val().toUpperCase(); // convert to uppercase for vanity
  
  // check if course was already added
  if ($.inArray(input,courses_taken) != -1) {
    // if yes, display warning and do not add course
    $('#warning').html('You have already added this course!');
    $('#warning').fadeIn();
  } else {
    var data = $(this).serialize(); // get user input from form
    $.ajax({
    cache: false,
    type: 'POST',              
    url: '<?php echo $path ?>add-course.php',
    data: data,
    success: function (data) {
      // reload list of taken courses
      update_courses();
      // alert user on successful add
      $('#success').html('Course added!');
      $('#success').fadeIn();
    },
    error: function (response) {
      alert("error: " + response.responseText);
    }
    });
  }
});
</script>