<?php include 'db_connect.php' ?>
<div class="col-lg-12">
	<div class="card card-outline card-primary">
		<div class="card-header">
			<div class="card-tools">
				<a class="btn btn-block btn-sm btn-default btn-flat border-primary new_batch" href="javascript:void(0)"><i class="fa fa-plus"></i> Add New Batch</a>
			</div>
		</div>
		<div class="card-body">
			<table class="table tabe-hover table-bordered" id="list">
				<colgroup>
					<col width="5%">
					<col width="20%">
					<col width="20%">
				</colgroup>
				<thead>
					<tr>
						<th class="text-center">Sr.no.</th>
						<th class="text-center">Batch</th>
						<th class="text-center">Action</th>
					</tr>
				</thead>
				<?php
				$i = 1;
				$qry = $conn->query("SELECT *, CONCAT(bname) AS `batch` FROM batch_list ORDER BY batch ASC");
				while ($row = $qry->fetch_assoc()) :
				?>
					<tr>
						<th class="text-center"><?php echo $i++ ?></th>
						<td class="text-center"><b><?php echo $row['batch'] ?></b></td>
						<td class="text-center">
							<div class="btn-group">
								<a href="javascript:void(0)" data-id='<?php echo $row['id'] ?>' class="btn btn-primary btn-flat manage_batch">
									<i class="fas fa-edit"></i>
								</a>
								<button type="button" class="btn btn-danger btn-flat delete_batch" data-id="<?php echo $row['id'] ?>">
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
		$('.new_batch').click(function() {
			uni_modal("New Batch", "<?php echo $_SESSION['login_view_folder'] ?>manage_batch.php")
		})
		$('.manage_batch').click(function() {
			uni_modal("Manage Batch", "<?php echo $_SESSION['login_view_folder'] ?>manage_batch.php?id=" + $(this).attr('data-id'))
		})
		$('.delete_batch').click(function() {
			_conf("Are you sure to delete this batch?", "delete_batch", [$(this).attr('data-id')])
		})
	})
	function delete_batch($id) {
		start_load()
		$.ajax({
			url: 'ajax.php?action=delete_batch',
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