<?php
include '../db_connect.php';
?>
<div class="container-fluid">
	<form action="" id="manage-restriction">
		<div class="row">
			<div class="col-md-4 border-right">
				<input type="hidden" name="academic_id" value="<?php echo isset($_GET['id']) ? $_GET['id'] : '' ?>">
				<div id="msg" class="form-group"></div>
				<div class="form-group">
					<label for="" class="control-label">Faculty</label>
					<select name="faculty_id" id="faculty_id" class="form-control form-control-sm select2" required>
						<option value=""></option>
						<?php
						$faculty = $conn->query("SELECT *,concat(firstname,' ',lastname) as name FROM faculty_list order by concat(firstname,' ',lastname) asc");
						$f_arr = array();
						while ($row = $faculty->fetch_assoc()) :
							$f_arr[$row['id']] = $row;
						?>
							<option value="<?php echo $row['id'] ?>" <?php echo isset($faculty_id) && $faculty_id == $row['id'] ? "selected" : "" ?>><?php echo ucwords($row['name']) ?></option>
						<?php endwhile; ?>
					</select>
				</div>
				<div class="form-group">
					<label for="" class="control-label">Class</label>
					<select name="class_id" id="class_id" class="form-control form-control-sm select2" required>
						<option value=""></option>
						<?php
						$classes = $conn->query("SELECT id,concat(curriculum,' ',level,' - ',section) as class FROM class_list ORDER BY id asc");
						$c_arr = array();
						while ($row = $classes->fetch_assoc()) :
							$c_arr[$row['id']] = $row;
						?>
							<option value="<?php echo $row['id'] ?>" <?php echo isset($class_id) && $class_id == $row['id'] ? "selected" : "" ?>><?php echo $row['class'] ?></option>
						<?php endwhile; ?>
					</select>
				</div>
				<div class="form-group">
					<label for="" class="control-label">Batch</label>
					<select name="batch_id" id="batch_id" class="form-control form-control-sm select2" multiple="multiple" required>
						<option value=""></option>
						<?php
						$batches = $conn->query("SELECT id, CONCAT(bname) as batch FROM batch_list WHERE id > 0");
						$b_arr = array();
						while ($row = $batches->fetch_assoc()) :
							$b_arr[$row['id']] = $row;
						?>
							<option value="<?php echo $row['id'] ?>" <?php echo isset($batch_id) && $batch_id == $row['id'] ? "selected" : "" ?>><?php echo $row['batch'] ?></option>
						<?php endwhile; ?>
					</select>
				</div>
				<div class="form-group">
					<label for="" class="control-label">Subject</label>
					<select name="subject_id" id="subject_id" class="form-control form-control-sm select2" required>
						<option value=""></option>
						<?php
						$subject = $conn->query("SELECT id,concat(code,' - ',subject) as subj FROM subject_list");
						$s_arr = array();
						while ($row = $subject->fetch_assoc()) :
							$s_arr[$row['id']] = $row;
						?>
							<option value="<?php echo $row['id'] ?>" <?php echo isset($subject_id) && $subject_id == $row['id'] ? "selected" : "" ?>><?php echo $row['subj'] ?></option>
						<?php endwhile; ?>
					</select>
				</div>
				<div class="form-group">
					<div class="d-flex w-100 justify-content-center">
						<button class="btn btn-sm btn-flat btn-primary bg-gradient-primary" id="add_to_list" type="button">Add to List</button>
					</div>
				</div>
			</div>
			<div class="col-md-8">
				<table class="table table-condensed" id="r-list">
					<thead>
						<tr>
							<th>Faculty</th>
							<th>Class</th>
							<th>Batch</th>
							<th>Subject</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$restriction = $conn->query("SELECT * FROM restriction_list where academic_id = {$_GET['id']} order by id asc");
						while ($row = $restriction->fetch_assoc()) :
						?>
							<tr>
								<td>
									<b><?php echo isset($f_arr[$row['faculty_id']]) ? $f_arr[$row['faculty_id']]['name'] : '' ?></b>
									<input type="hidden" name="rid[]" value="<?php echo $row['id'] ?>">
									<input type="hidden" name="faculty_id[]" value="<?php echo $row['faculty_id'] ?>">
								</td>
								<td>
									<b><?php echo isset($c_arr[$row['class_id']]) ? $c_arr[$row['class_id']]['class'] : '' ?></b>
									<input type="hidden" name="class_id[]" value="<?php echo $row['class_id'] ?>">
								</td>
								<td>
									<b><?php echo isset($b_arr[$row['batch_id']]) ? $b_arr[$row['batch_id']]['batch'] : 'Whole Class' ?></b>
									<input type="hidden" name="batch_id[]" value="<?php echo $row['batch_id'] ?>">
								</td>
								<td>
									<b><?php echo isset($s_arr[$row['subject_id']]) ? $s_arr[$row['subject_id']]['subj'] : '' ?></b>
									<input type="hidden" name="subject_id[]" value="<?php echo $row['subject_id'] ?>">
								</td>

								<td class="text-center">
									<button class="btn btn-sm btn-outline-danger" onclick="$(this).closest('tr').remove()" type="button"><i class="fa fa-trash"></i></button>
								</td>
							</tr>
						<?php endwhile; ?>
					</tbody>
				</table>
			</div>
		</div>
	</form>
</div>
<script>
	$(document).ready(function() {
		$('.select2').select2({
			placeholder: "Please select here",
			width: "100%"
		});
		$('#manage-restriction').submit(function(e) {
			e.preventDefault();
			start_load()
			$('#msg').html('')
			$.ajax({
				url: 'ajax.php?action=save_restriction',
				method: 'POST',
				data: $(this).serialize(),
				success: function(resp) {
					if (resp == 1) {
						alert_toast("Data successfully saved.", "success");
						setTimeout(function() {
							location.reload()
						}, 1750)
					} else if (resp == 2) {
						$('#msg').html('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Class already exists.</div>')
						end_load()
					}
				}
			})
		})

		$('#add_to_list').click(function() {
			start_load();
			var frm = $('#manage-restriction');
			var cid = frm.find('#class_id').val();
			var fid = frm.find('#faculty_id').val();
			var bids = frm.find('#batch_id').val(); // Modify to get an array of batch IDs

			var sid = frm.find('#subject_id').val();
			var f_arr = <?php echo json_encode($f_arr) ?>;
			var c_arr = <?php echo json_encode($c_arr) ?>;
			var b_arr = <?php echo json_encode($b_arr) ?>;
			var s_arr = <?php echo json_encode($s_arr) ?>;


			if (bids.length === 0) {
				// Handle the case when bids array is empty
				var title = $('#select2-class_id-container').attr('title');

				// Get the last character of the title attribute

				if (title) {
					var lastCharacter = title.charAt(title.length - 1);

					console.log(lastCharacter);
					var lastCharacter = title.charAt(title.length - 1);

					// If section is 'A', add A1 to A5 to the bids array
					// if (lastCharacter == 'A') {
					// 	bids = ['A1', 'A2', 'A3', 'A4', 'A5'];
					// } else if (lastCharacter == 'B') {
					// 	// If section is 'B', add B1 to B5 to the bids array
					// 	bids = ['B1', 'B2', 'B3', 'B4', 'B5'];
					// }

					// Inside the AJAX success callback
					if (lastCharacter == 'A') {
						var tr = $("<tr></tr>")
						tr.append('<td><b>' + f_arr[fid].name + '</b><input type="hidden" name="rid[]" value=""><input type="hidden" name="faculty_id[]" value="' + fid + '"></td>')
						tr.append('<td><b>' + c_arr[cid].class + '</b><input type="hidden" name="class_id[]" value="' + cid + '"></td>')
						tr.append('<td><b>Whole Class</b><input type="hidden" name="batch_id[]" value="' + (-1) + '"></td>')
						tr.append('<td><b>' + s_arr[sid].subj + '</b><input type="hidden" name="subject_id[]" value="' + sid + '"></td>')
						tr.append('<td class="text-center"><span class="btn btn-sm btn-outline-danger" onclick="$(this).closest(\'tr\').remove()" type="button"><i class="fa fa-trash"></i></span></td>')
						$('#r-list tbody').append(tr);


						// 	// Make an AJAX request to get batch IDs based on section
						// 	$.ajax({
						// 		url: 'admin/get_batch_ids.php',
						// 		method: 'GET',
						// 		data: {
						// 			section: lastCharacter
						// 		},
						// 		dataType: 'json',
						// 		success: function(response) {
						// 			if (response && response.length > 0) {
						// 				bids = response;

						// 				$.each(bids, function(index, bid) {
						// 					var tr = $("<tr></tr>")
						// 					tr.append('<td><b>' + f_arr[fid].name + '</b><input type="hidden" name="rid[]" value=""><input type="hidden" name="faculty_id[]" value="' + fid + '"></td>')
						// 					tr.append('<td><b>' + c_arr[cid].class + '</b><input type="hidden" name="class_id[]" value="' + cid + '"></td>')
						// 					tr.append('<td><b>' + lastCharacter + (index + 1) + '</b><input type="hidden" name="batch_id[]" value="' + bid + '"></td>')
						// 					tr.append('<td><b>' + s_arr[sid].subj + '</b><input type="hidden" name="subject_id[]" value="' + sid + '"></td>')
						// 					tr.append('<td class="text-center"><span class="btn btn-sm btn-outline-danger" onclick="$(this).closest(\'tr\').remove()" type="button"><i class="fa fa-trash"></i></span></td>')
						// 					$('#r-list tbody').append(tr);
						// 				});
						// 			} else {
						// 				console.error('Failed to fetch batch IDs');
						// 			}
						// 		},
						// 		error: function(xhr, status, error) {
						// 			console.error('AJAX error:', error);
						// 		}
						// 	});
					} else if (lastCharacter == 'B') {
						var tr = $("<tr></tr>")
						tr.append('<td><b>' + f_arr[fid].name + '</b><input type="hidden" name="rid[]" value=""><input type="hidden" name="faculty_id[]" value="' + fid + '"></td>')
						tr.append('<td><b>' + c_arr[cid].class + '</b><input type="hidden" name="class_id[]" value="' + cid + '"></td>')
						tr.append('<td><b>Whole Class</b><input type="hidden" name="batch_id[]" value="' + (-1) + '"></td>')
						tr.append('<td><b>' + s_arr[sid].subj + '</b><input type="hidden" name="subject_id[]" value="' + sid + '"></td>')
						tr.append('<td class="text-center"><span class="btn btn-sm btn-outline-danger" onclick="$(this).closest(\'tr\').remove()" type="button"><i class="fa fa-trash"></i></span></td>')
						$('#r-list tbody').append(tr);
					}

				}
			} else {
				$.each(bids, function(index, bid) {
					var tr = $("<tr></tr>")
					tr.append('<td><b>' + f_arr[fid].name + '</b><input type="hidden" name="rid[]" value=""><input type="hidden" name="faculty_id[]" value="' + fid + '"></td>')
					tr.append('<td><b>' + c_arr[cid].class + '</b><input type="hidden" name="class_id[]" value="' + cid + '"></td>')
					tr.append('<td><b>' + b_arr[bid].batch + '</b><input type="hidden" name="batch_id[]" value="' + bid + '"></td>')
					tr.append('<td><b>' + s_arr[sid].subj + '</b><input type="hidden" name="subject_id[]" value="' + sid + '"></td>')
					tr.append('<td class="text-center"><span class="btn btn-sm btn-outline-danger" onclick="$(this).closest(\'tr\').remove()" type="button"><i class="fa fa-trash"></i></span></td>')
					$('#r-list tbody').append(tr);
				});

			}


			frm.find('#class_id').val(null).trigger('change');
			frm.find('#faculty_id').val(null).trigger('change');
			frm.find('#batch_id').val(null).trigger('change'); // Reset batch selection
			frm.find('#subject_id').val(null).trigger('change');
			end_load();
		});
	});
</script>