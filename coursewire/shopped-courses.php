<?php
/**
 * Shown in Settings page in settings.php
 * Lists courses user is shopping
 */
include('path.php'); ?>
<!-- placeholder div for success messages when a course is successfully removed -->
<div id="success" class="alert alert-success fade in">
</div>
<!-- need to also include term_code to add, removing this feature for now    
<form id='courses-form' class='form-inline' method='POST'>
    <table id="info">
      <tbody>
        <tr>
          <td width="80px"><label>CRN</label></td>
          <td width="120px"><input class="input-small" type="text" name="crn"></td>
          <td><button id="courses-submit" type="submit" class="btn btn-primary">Quick add</button></td>
        </tr>
      </tbody>
    </table>
</form>
-->
<!-- placeholder to list all courses user has shopped -->
<div id="courses_shopped">
</div>
<script language="javascript" type="text/javascript">
$(document).ready(function() {
  $('.alert').fadeOut(); // fade out all alerts by default
  update_courses(); // create list of courses shopped
});

// need to attach to document as courses will be dynamically added
 $(document).on('click','.remove', function () {
  $('.alert').fadeOut(); // fade out all alerts if any displayed
  $.ajax({
    cache: false,
    type: 'POST',              
    url: '<?php echo $path ?>remove-shopped-course.php',
    data: 'term_code='+$(this).attr('term')+'&crn='+$(this).attr('crn'),
    success: function () {      
      update_courses(); // reload list of taken courses
      // alert user on success
      $('#success').html('Course removed!');
      $('#success').fadeIn();
    },
    error: function (response) {
      alert("error: " + response.responseText);
    }
  });
}); 
// generates the list of shopped courses
function update_courses() {
  $.ajax({
    cache: false,
    type: 'POST',              
    url: '<?php echo $path ?>get-courses-shopped.php',
    dataType: 'json',
    success: function (data) {
      $('#courses_shopped').html(data.list);
    },
    error: function (response) {
      alert("error: " + response.responseText);
    }
  });
}
/* currently not being used    
$('#courses-form').submit(function (event) {
  event.preventDefault();
  // fade out in case it is already displaying
  $('.alert').fadeOut();    
	var data = $(this).serialize();
	$.ajax({
	cache: false,
	type: 'POST',              
	url: '<?php echo $path ?>add-shopped-course.php',
	data: data,
	success: function (data) {
		// reload list of taken courses
		update_courses();
		
		$('#success').html('Course added!');
		$('#success').fadeIn();
	},
	error: function (response) {
		alert("error: " + response.responseText);
	}
	});
});*/
</script>