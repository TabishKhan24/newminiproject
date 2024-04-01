<?php
include '../db_connect.php';
if (isset($_GET['id'])) {
	$qry = $conn->query("SELECT * FROM subject_list where id={$_GET['id']}")->fetch_array();
	foreach ($qry as $k => $v) {
		$$k = $v;
	}
}
?>
<div class="container-fluid">
	<form action="" id="manage-subject">
		<input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
		<div id="msg" class="form-group"></div>
		<div class="form-group">
			<label for="code" class="control-label">Subject Code</label>
			<input type="text" class="form-control form-control-sm" name="code" id="code" value="<?php echo isset($code) ? $code : '' ?>" required>
		</div>
		<div class="form-group">
			<label for="subject" class="control-label">Subject Name</label>
			<input type="text" class="form-control form-control-sm" name="subject" id="subject" value="<?php echo isset($subject) ? $subject : '' ?>" required>
		</div>

		<div class="form-group">
    <label for="course_type">Course Type</label>
    <select class="form-control" id="course_type" name="course_type" required>
        <option value="">Please select here</option>
        <?php
        // Define an array containing the course names
        $courses = array("Basic Science Course (BSC)", "Engineering Science Course (ESC)", "Humanities and Social Science including Management Courses (HSSMC)", "Professional Core Course (PCC)", "Professional Elective Course (PEC)", "Open Elective Course (OEC)", "Emerging Courses");

        // Loop through the array to generate dropdown options
        foreach ($courses as $course) {
            // Check if the current course matches the stored course type
            $selected = isset($course_type) && $course_type == $course ? 'selected' : '';
            echo "<option value='{$course}' $selected>{$course}</option>";
        }
        ?>
    </select>
</div>

	</form>
</div>


</form>
</div>
<script>
	$(document).ready(function() {
		$('#manage-subject').submit(function(e) {
			e.preventDefault();
			start_load()
			$('#msg').html('')
			$.ajax({
				url: 'ajax.php?action=save_subject',
				method: 'POST',
				data: $(this).serialize(),
				success: function(resp) {
					if (resp == 1) {
						alert_toast("Data successfully saved.", "success");
						setTimeout(function() {
							location.reload()
						}, 1750)
					} else if (resp == 2) {
						$('#msg').html('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Subject Code already exist.</div>')
						end_load()
					}
				}
			})
		})
	})
</script>