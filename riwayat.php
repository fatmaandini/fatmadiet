<?php
require_once('includes/init.php');

$user_role = get_role();
if($user_role == 'admin') {

$page = "Riwayat";
require_once('template/header.php');
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-fw fa-history"></i> Data Riwayat</h1>
</div>

<div class="card shadow mb-4">
    <!-- /.card-header -->
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-success"><i class="fa fa-table"></i> Data Riwayat</h6>
    </div>

    <div class="card-body">
		<div class="table-responsive">
			<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
				<thead class="bg-success text-white">
					<tr align="center">
						<th width="5%">No</th>
						<th>Nama User</th>
						<th width="15%">Aksi</th>
				</thead>
				<tbody>
					<?php 
						$no=0;
						$query = mysqli_query($koneksi,"SELECT * FROM user WHERE role='2';");
						while($data = mysqli_fetch_array($query)){
						$no++;
					?>
					<tr align="center">
						<td><?= $no; ?></td>
						<td align="left"><?= $data['nama'] ?></td>
						<td>
							<a href="riwayat-detail.php?id_user=<?php echo $data['id_user']; ?>" class="btn btn-success btn-sm"><i class="fa fa-eye"></i> Detail</a>
						</td>
					</tr>
					<?php
						}
					?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<?php
require_once('template/footer.php');
}
else {
	header('Location: login.php');
}
?>