<?php
require_once('includes/init.php');

$user_role = get_role();
if($user_role == 'admin') {

$page = "Riwayat";
require_once('template/header.php');
$waktu = (isset($_GET['waktu'])) ? trim($_GET['waktu']) : '';
$id_user = (isset($_GET['id_user'])) ? trim($_GET['id_user']) : '';
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-fw fa-history"></i> Data Riwayat</h1>
	
	<a href="riwayat-detail.php?id_user=<?=$id_user?>" class="btn btn-secondary btn-icon-split"><span class="icon text-white-50"><i class="fas fa-arrow-left"></i></span>
		<span class="text">Kembali</span>
	</a>
</div>

<div class="card shadow mb-4">
    <!-- /.card-header -->
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-success"><i class="fa fa-table"></i> Detail Data Riwayat Konsultasi</h6>
    </div>

    <div class="card-body">
		<div class="table-responsive">
			<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
				<thead class="bg-success text-white">
					<tr align="center">
						<th width="5%">No</th>
						<th>Tanggal Konsultasi</th>
						<th>Jam</th>
						<th>Hasil</th>
						<th>Nilai</th>
				</thead>
				<tbody>
					<?php 
						$no=0;
						$query = mysqli_query($koneksi,"SELECT id_hasil, id_alternatif, nilai, waktu, DATE_FORMAT(waktu, '%Y-%m-%d') as tanggal, DATE_FORMAT(waktu, '%H:%i:%s') as jam FROM hasil WHERE id_user='$id_user' AND waktu='$waktu';");
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
							$id_alternatif = $data['id_alternatif'];
							$q1 = mysqli_query($koneksi,"SELECT * FROM hasil JOIN alternatif ON hasil.id_alternatif=alternatif.id_alternatif WHERE hasil.id_alternatif='$id_alternatif' AND hasil.waktu='$waktu';");
							$d1 = mysqli_fetch_array($q1);
							echo $d1['nama'];
						?>
						</td>
						<td><?= $data['nilai']; ?></td>
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