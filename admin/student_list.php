<?php include 'db_connect.php' ?>
<div class="col-lg-12">
	<div class="card card-outline card-success">
		<div class="card-header">
			<div class="card-tools">
				<a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="./index.php?page=new_student"><i class="fa fa-plus"></i> Add New Student</a>
			</div>
		</div>
		<div class="card-body">
			<table class="table tabe-hover table-bordered" id="list">
				<colgroup>
					<col width="4%">
					<col width="15%">
					<col width="25%">
					<col width="25%">
					<col width="15%">
					<col width="10%">
					<col width="5%">
				</colgroup>
				<thead>
					<tr>
						<th class="text-center">Sr.no.</th>
						<th class="text-center">University ID</th>
						<th class="text-center">Name</th>
						<th class="text-center">Email</th>
						<th class="text-center">Class</th>
						<th class="text-center">Batch</th>
						<th class="text-center">Action</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$i = 1;
					$class = array();
					$classes = $conn->query("SELECT id, CONCAT(curriculum, ' ', level,' - ',section) AS class FROM class_list");
					while ($row = $classes->fetch_assoc()) {
						$class[$row['id']] = $row['class'];
					}
					$batches = $conn->query("SELECT id, CONCAT(bname) AS batch FROM batch_list");
					while ($row = $batches->fetch_assoc()) {
						$batch[$row['id']] = $row['batch'];
					}

					$qry = $conn->query("SELECT *,concat(firstname,' ',lastname) as name FROM student_list order by concat ('id') asc");
					while ($row = $qry->fetch_assoc()) :
					?>
						<tr>
							<th class="text-center"><?php echo $i++ ?></th>
							<td class="text-center"><b><?php echo $row['school_id'] ?></b></td>
							<td><b><?php echo ucwords($row['name']) ?></b></td>
							<td><b><?php echo $row['email'] ?></b></td>
							<td class="text-center"><b><?php echo isset($class[$row['class_id']]) ? $class[$row['class_id']] : "N/A" ?></b></td>
							<td class="text-center"><b><?php echo isset($batch[$row['batch_id']]) ? $batch[$row['batch_id']] : "N/A" ?></b></td>
							<td class="text-center">
								<button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
									Action
								</button>
								<div class="dropdown-menu" style="">
									<a class="dropdown-item view_student" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">View</a>
									<div class="dropdown-divider"></div>
									<a class="dropdown-item" href="./index.php?page=edit_student&id=<?php echo $row['id'] ?>">Edit</a>
									<div class="dropdown-divider"></div>
									<a class="dropdown-item delete_student" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">Delete</a>
								</div>
							</td>
						</tr>
					<?php endwhile; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<script>
	$(document).ready(function() {
		$('.view_student').click(function() {
			uni_modal("<i class='fa fa-id-card'></i> student Details", "<?php echo $_SESSION['login_view_folder'] ?>view_student.php?id=" + $(this).attr('data-id'))
		})
		$('.delete_student').click(function() {
			_conf("Are you sure to delete this student?", "delete_student", [$(this).attr('data-id')])
		})
		$('#list').dataTable()
	})

	function delete_student($id) {
		start_load()
		$.ajax({
			url: 'ajax.php?action=delete_student',
			method: 'POST',
			data: {
				id: $id
			},
			success: function(resp) {
				if (resp == 1) {
					alert_toast("Data successfully deleted", 'success')
					setTimeout(function() {
						location.reload()
					}, 1500)

				}
			}
		})
	}
</script>