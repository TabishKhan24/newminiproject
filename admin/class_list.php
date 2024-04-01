<?php include 'db_connect.php' ?>
<div class="col-lg-12">
	<div class="card card-outline card-primary">
		<div class="card-header">
			<div class="card-tools">
				<a class="btn btn-block btn-sm btn-default btn-flat border-primary new_class" href="javascript:void(0)"><i class="fa fa-plus"></i> Add New Class</a>
			</div>
		</div>
		<div class="card-body">
			<table class="table tabe-hover table-bordered" id="list">
				<colgroup>
					<col width="5%">
					<col width="20%">
					<col width="20%">
					<col width="20%">
				</colgroup>
				<thead>
					<tr>
						<th class="text-center">Sr.no.</th>
						<th class="text-center">Class</th>
						<th class="text-center">Section</th>
						<th class="text-center">Action</th>
					</tr>
				</thead>
				<?php
				$i = 1;
				$qry = $conn->query("SELECT *, CONCAT(curriculum,' ', level) AS `class` FROM class_list ORDER BY class ASC");
				while ($row = $qry->fetch_assoc()) :
				?>
					<tr>
						<th class="text-center"><?php echo $i++ ?></th>
						<td class="text-center"><b><?php echo $row['class'] ?></b></td>
						<td class="text-center"><b><?php echo $row['section'] ?></b></td>
						<td class="text-center">
							<div class="btn-group">
								<a href="javascript:void(0)" data-id='<?php echo $row['id'] ?>' class="btn btn-primary btn-flat manage_class">
									<i class="fas fa-edit"></i>
								</a>
								<button type="button" class="btn btn-danger btn-flat delete_class" data-id="<?php echo $row['id'] ?>">
									<i class="fas fa-trash"></i>
								</button>
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
		$('#list').dataTable()
		$('.new_class').click(function() {
			uni_modal("New Class", "<?php echo $_SESSION['login_view_folder'] ?>manage_class.php")
		})
		$('.manage_class').click(function() {
			uni_modal("Manage class", "<?php echo $_SESSION['login_view_folder'] ?>manage_class.php?id=" + $(this).attr('data-id'))
		})
		$('.delete_class').click(function() {
			_conf("Are you sure to delete this class?", "delete_class", [$(this).attr('data-id')])
		})
	})
	function delete_class($id) {
		start_load()
		$.ajax({
			url: 'ajax.php?action=delete_class',
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