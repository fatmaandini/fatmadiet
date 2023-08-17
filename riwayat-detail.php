<?php
require_once('includes/init.php');

$user_role = get_role();
if($user_role == 'admin') {

$page = "Riwayat";
require_once('template/header.php');
$id_user = (isset($_GET['id_user'])) ? trim($_GET['id_user']) : '';
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-fw fa-history"></i> Data Riwayat</h1>
	
	<a href="riwayat.php" class="btn btn-secondary btn-icon-split"><span class="icon text-white-50"><i class="fas fa-arrow-left"></i></span>
		<span class="text">Kembali</span>
	</a>
</div>

	
<?php
$status = isset($_GET['status']) ? $_GET['status'] : '';
$msg = '';
switch($status):
	case 'sukses-hapus':
		$msg = 'Data behasil dihapus';
		break;
endswitch;

if($msg):
	echo '<div class="alert alert-info">'.$msg.'</div>';
endif;
?>


<div class="card shadow mb-4">
    <!-- /.card-header -->
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-success"><i class="fa fa-table"></i> Data Riwayat Konsultasi</h6>
    </div>

    <div class="card-body">
		<div class="table-responsive">
			<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
				<thead class="bg-success text-white">
					<tr align="center">
						<th width="5%">No</th>
						<th>Tanggal Konsultasi</th>
						<th>Jam</th>
						<th>Hasil Terbaik</th>
						<th>Nilai</th>
						<th width="15%">Aksi</th>
				</thead>
				<tbody>
					<?php 
						$no=0;
						$query = mysqli_query($koneksi,"SELECT id_hasil, waktu, DATE_FORMAT(waktu, '%Y-%m-%d') as tanggal, DATE_FORMAT(waktu, '%H:%i:%s') as jam FROM hasil WHERE id_user='$id_user' GROUP BY waktu ORDER BY waktu DESC");
						while($data = mysqli_fetch_array($query)){
						$no++;
					?>
					<tr align="center">
						<td><?= $no; ?></td>
						<td><?= tgl_indo($data['tanggal']); ?></td>
						<td><?= $data['jam']; ?></td>
						<td>
						<?php
							$waktu = $data['waktu'];
							$q1 = mysqli_query($koneksi,"SELECT * FROM hasil JOIN alternatif ON hasil.id_alternatif=alternatif.id_alternatif WHERE hasil.waktu='$waktu' ORDER BY hasil.nilai DESC LIMIT 1");
							$d1 = mysqli_fetch_array($q1);
							echo $d1['nama'];
						?>
						</td>
						<td><?= $d1['nilai'];?></td>
						<td>
							<a href="riwayat-admin-detail.php?waktu=<?php echo $data['waktu']; ?>&id_user=<?= $id_user; ?>" class="btn btn-success btn-sm"><i class="fa fa-eye"></i> Detail</a>
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