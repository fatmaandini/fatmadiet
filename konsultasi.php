<?php
require_once('includes/init.php');

$user_role = get_role();
if($user_role == 'user') {

$page = "Konsultasi";
require_once('template/header.php');
// error_reporting(E_ERROR | E_PARSE);
?>

<script type="text/javascript">
$(document).ready(function() {
	$("#select_5").prop('disabled',true);
	$('form').bind('submit', function () {
   	 	$(this).find(':input').prop('disabled', false);
  	});

});
</script>


<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-fw fa-user-md"></i> Konsultasi</h1>
</div>


<div class="card shadow mb-4">
	<!-- /.card-header -->
	<div class="card-header py-3">
		<h6 class="m-0 font-weight-bold text-success"><i class="fa fa-table"></i> Pilih Sesuai Keadaan Sebenarnya</h6>
	</div>

	<form action="" method="POST">
		<div class="card-body">
			<div class="row">
				<?php
				$q2 = mysqli_query($koneksi,"SELECT * FROM kriteria ORDER BY kode_kriteria ASC");			
				$i = 0;
				while($d = mysqli_fetch_array($q2)){
				?>
				<input type="text" name="id_kriteria[]" value="<?= $d['id_kriteria'] ?>" hidden>
				<div class="form-group col-md-6">
					<label class="font-weight-bold"><?= $d['nama'] ?></label>
					<select name="nilai[]" class="form-control" id="select_<?= $d['id_kriteria'] ?>" required>
						<option value="">--Pilih--</option>
						<?php
						$id_kriteria = $d['id_kriteria'];
						$q3 = mysqli_query($koneksi,"SELECT * FROM sub_kriteria WHERE id_kriteria = '$id_kriteria' ORDER BY nilai ASC");			
						while($d3 = mysqli_fetch_array($q3)){
						?>
						<option value="<?= $d3['nilai'] ?>" <?php if (isset($_POST['hitung'])) {if($_POST['nilai'][$i] == $d3['nilai']) {echo "selected";}}?>><?= $d3['nama'] ?> </option>
						<?php } ?>
					</select>
				</div>
				<?php $i++; } ?>
			</div>
		</div>
		
		<div class="card-footer text-center">
			<button name="hitung" type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Proses Hitung</button>
		</div>
	</form>
</div>



<?php
if (isset($_POST['hitung'])) {
	
	$id_kriteria = $_POST['id_kriteria'];
	$nilai = $_POST['nilai'];
	$i = 0;
	$nilai_bobot = array();
	foreach ($id_kriteria as $key) {
		$nilai_bobot[$key]= $nilai[$i];
		$i++;
	}
	
	$kriterias = mysqli_query($koneksi,"SELECT * FROM kriteria ORDER BY kode_kriteria ASC");			
	$alternatifs = mysqli_query($koneksi,"SELECT * FROM alternatif");

	//Matrix Keputusan (X)
	$matriks_x = array();
	$rating = array();
	foreach($kriterias as $kriteria):
		foreach($alternatifs as $alternatif):
			
			$id_alternatif = $alternatif['id_alternatif'];
			$id_kriteria = $kriteria['id_kriteria'];
			$q4 = mysqli_query($koneksi,"SELECT sub_kriteria.nilai, sub_kriteria.nama FROM penilaian JOIN sub_kriteria WHERE penilaian.nilai=sub_kriteria.id_sub_kriteria AND penilaian.id_alternatif='$alternatif[id_alternatif]' AND penilaian.id_kriteria='$kriteria[id_kriteria]'");
			$data = mysqli_fetch_array($q4);
			$nilai = $data['nilai'];
			$nama = $data['nama'];
			$matriks_x[$id_kriteria][$id_alternatif] = $nilai;
			$rating[$id_kriteria][$id_alternatif] = $nama;
		endforeach;
	endforeach;

	//Matriks Ternormalisasi (R)
	$matriks_r = array();
	foreach($matriks_x as $id_kriteria => $penilaians):
		
		$jumlah_kuadrat = 0;
		foreach($penilaians as $penilaian):
			$jumlah_kuadrat += pow($penilaian, 2);
		endforeach;
		$akar_kuadrat = sqrt($jumlah_kuadrat);
		
		foreach($penilaians as $id_alternatif => $penilaian):
			$matriks_r[$id_kriteria][$id_alternatif] = $penilaian / $akar_kuadrat;
		endforeach;
		
	endforeach;

	 //Matriks Y
	$matriks_y = array();
	foreach($kriterias as $kriteria):
		foreach($alternatifs as $alternatif):
			
			$id_alternatif = $alternatif['id_alternatif'];
			$id_kriteria = $kriteria['id_kriteria'];
			$bobot = $nilai_bobot[$id_kriteria];
			
			$nilai_r = $matriks_r[$id_kriteria][$id_alternatif];
			$matriks_y[$id_kriteria][$id_alternatif] = $bobot * $nilai_r;

		endforeach;
	endforeach;

	//Solusi Ideal Positif & Negarif
	$solusi_ideal_positif = array();
	$solusi_ideal_negatif = array();
	foreach($kriterias as $kriteria):

		$id_kriteria = $kriteria['id_kriteria'];
		$type_kriteria = $kriteria['type'];
		
		$nilai_max = @(max($matriks_y[$id_kriteria]));
		$nilai_min = @(min($matriks_y[$id_kriteria]));
		
		if($type_kriteria == 'Benefit'):
			$s_i_p = $nilai_max;
			$s_i_n = $nilai_min;
		elseif($type_kriteria == 'Cost'):
			$s_i_p = $nilai_min;
			$s_i_n = $nilai_max;
		endif;
		
		$solusi_ideal_positif[$id_kriteria] = $s_i_p;
		$solusi_ideal_negatif[$id_kriteria] = $s_i_n;

	endforeach;

	//Jarak Ideal Positif & Negatif
	$jarak_ideal_positif = array();
	$jarak_ideal_negatif = array();
	foreach($alternatifs as $alternatif):

		$id_alternatif = $alternatif['id_alternatif'];		
		$jumlah_kuadrat_jip = 0;
		$jumlah_kuadrat_jin = 0;
		
		// Mencari penjumlahan kuadrat
		foreach($matriks_y as $id_kriteria => $penilaians):
			
			$hsl_pengurangan_jip = $penilaians[$id_alternatif] - $solusi_ideal_positif[$id_kriteria];
			$hsl_pengurangan_jin = $penilaians[$id_alternatif] - $solusi_ideal_negatif[$id_kriteria];
			
			$jumlah_kuadrat_jip += pow($hsl_pengurangan_jip, 2);
			$jumlah_kuadrat_jin += pow($hsl_pengurangan_jin, 2);
		
		endforeach;
		
		// Mengakarkan hasil penjumlahan kuadrat
		$akar_kuadrat_jip = sqrt($jumlah_kuadrat_jip);
		$akar_kuadrat_jin = sqrt($jumlah_kuadrat_jin);
		
		// Memasukkan ke array matriks jip & jin
		$jarak_ideal_positif[$id_alternatif] = $akar_kuadrat_jip;
		$jarak_ideal_negatif[$id_alternatif] = $akar_kuadrat_jin;
		
	endforeach;

	//Kedekatan Relatif Terhadap Solusi Ideal (V)
	$kedekatan_relatif = array();
	foreach($alternatifs as $alternatif):

		$s_negatif = $jarak_ideal_negatif[$alternatif['id_alternatif']];
		$s_positif = $jarak_ideal_positif[$alternatif['id_alternatif']];	
		
		$nilai_v = @($s_negatif / ($s_positif + $s_negatif));
		
		$kedekatan_relatif[$alternatif['id_alternatif']]['id_alternatif'] = $alternatif['id_alternatif'];
		$kedekatan_relatif[$alternatif['id_alternatif']]['nama'] = $alternatif['nama'];
		$kedekatan_relatif[$alternatif['id_alternatif']]['nilai'] = $nilai_v;
		
	endforeach;
	
	$sorted_ranks = $kedekatan_relatif;
	if(function_exists('array_multisort')):
		foreach ($sorted_ranks as $key => $row) {
			$nilai[$key] = $row['nilai'];
		}
		array_multisort( array_column($sorted_ranks, 'nilai' ), SORT_DESC, $sorted_ranks);
	endif;
?>

<div class="card shadow mb-4">
	<!-- /.card-header -->
	<div class="card-header py-3">
		<h6 class="m-0 font-weight-bold text-success"><i class="fa fa-table"></i> Hasil Proses Perhitungan</h6>
	</div>

	<div class="card-body">
		<div class="table-responsive">
			<table class="table table-bordered" width="100%" cellspacing="0">
				<thead class="bg-success text-white">
					<tr align="center">
						<th width="5%">No</th>
						<th>Nama Alternatif</th>
						<th width="30%">Nilai</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$no=1;
					foreach($sorted_ranks as $alternatif ): ?>
						<tr align="center">
							<td><?php echo $no; ?></td>
							<td align="left"><?php echo $alternatif['nama']; ?></td>
							<td><?php echo $alternatif['nilai']; ?></td>											
						</tr>
					<?php 
					$no++;
					endforeach; ?>
				</tbody>
			</table>
		</div>
		
		<div class="alert alert-info">
			<?php
			$no=1;
			foreach($sorted_ranks as $alternatif ): 
			if($no == "1"){
			?>
					Didapatkan diet yang terbaik adalah <b><?php echo $alternatif['nama']; ?></b> dengan nilai sebesar <b><?php echo $alternatif['nilai']; ?></b> atau persentase sebesar <b><?php echo number_format($alternatif['nilai']*100,2); ?>%</b>.
			<?php 
			}
			$no++;
			endforeach; ?>
		</div>
	</div>
</div>
	
<div class="text-center mb-5">
  <a data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
    Lihat Perhitungannya Lengkapnya...
  </a>
</div>

<div class="collapse" id="collapseExample">
	<div class="card shadow mb-4">
		<!-- /.card-header -->
		<div class="card-header py-3">
			<h6 class="m-0 font-weight-bold text-success"><i class="fa fa-table"></i> Rating Kecocokan</h6>
		</div>

		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-bordered" width="100%" cellspacing="0">
					<thead class="bg-success text-white">
						<tr align="center">
							<th width="5%" rowspan="2">No</th>
							<th>Nama Alternatif</th>
							<?php foreach ($kriterias as $kriteria): ?>
								<th><?= $kriteria['nama'] ?></th>
							<?php endforeach ?>
						</tr>
					</thead>
					<tbody>
						<?php 
							$no=1;
							foreach ($alternatifs as $alternatif): ?>
						<tr align="center">
							<td><?= $no; ?></td>
							<td align="left"><?= $alternatif['nama'] ?></td>
							<?php
							foreach ($kriterias as $kriteria):
								$id_alternatif = $alternatif['id_alternatif'];
								$id_kriteria = $kriteria['id_kriteria'];
								echo '<td>';
								echo $rating[$id_kriteria][$id_alternatif];
								echo '</td>';
							endforeach
							?>
						</tr>
						<?php
							$no++;
							endforeach
						?>
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<div class="card shadow mb-4">
		<!-- /.card-header -->
		<div class="card-header py-3">
			<h6 class="m-0 font-weight-bold text-success"><i class="fa fa-table"></i> Matrix Keputusan (X)</h6>
		</div>

		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-bordered" width="100%" cellspacing="0">
					<thead class="bg-success text-white">
						<tr align="center">
							<th width="5%" rowspan="2">No</th>
							<th>Nama Alternatif</th>
							<?php foreach ($kriterias as $kriteria): ?>
								<th><?= $kriteria['nama'] ?></th>
							<?php endforeach ?>
						</tr>
					</thead>
					<tbody>
						<?php 
							$no=1;
							foreach ($alternatifs as $alternatif): ?>
						<tr align="center">
							<td><?= $no; ?></td>
							<td align="left"><?= $alternatif['nama'] ?></td>
							<?php
							foreach ($kriterias as $kriteria):
								$id_alternatif = $alternatif['id_alternatif'];
								$id_kriteria = $kriteria['id_kriteria'];
								echo '<td>';
								echo $matriks_x[$id_kriteria][$id_alternatif];
								echo '</td>';
							endforeach
							?>
						</tr>
						<?php
							$no++;
							endforeach
						?>
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<div class="card shadow mb-4">
		<!-- /.card-header -->
		<div class="card-header py-3">
			<h6 class="m-0 font-weight-bold text-success"><i class="fa fa-table"></i> Bobot Preferensi (W)</h6>
		</div>

		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-bordered" width="100%" cellspacing="0">
					<thead class="bg-success text-white">
						<tr align="center">
							<?php foreach ($kriterias as $kriteria): ?>
							<th><?= $kriteria['nama'] ?> (<?= $kriteria['type'] ?>)</th>
							<?php endforeach ?>
						</tr>
					</thead>
					<tbody>
						<tr align="center">
							<?php foreach ($kriterias as $kriteria):
							$id_kriteria = $kriteria['id_kriteria'];
							?>
							<td>
							<?php 
							echo $nilai_bobot[$id_kriteria];;
							?>
							</td>
							<?php endforeach ?>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<div class="card shadow mb-4">
		<!-- /.card-header -->
		<div class="card-header py-3">
			<h6 class="m-0 font-weight-bold text-success"><i class="fa fa-table"></i> Matriks Ternormalisasi (R)</h6>
		</div>

		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-bordered" width="100%" cellspacing="0">
					<thead class="bg-success text-white">
						<tr align="center">
							<th width="5%" rowspan="2">No</th>
							<th>Nama Alternatif</th>
							<?php foreach ($kriterias as $kriteria): ?>
								<th><?= $kriteria['nama'] ?></th>
							<?php endforeach ?>
						</tr>
					</thead>
					<tbody>
						<?php 
							$no=1;
							foreach ($alternatifs as $alternatif): ?>
						<tr align="center">
							<td><?= $no; ?></td>
							<td align="left"><?= $alternatif['nama'] ?></td>
							<?php						
							foreach($kriterias as $kriteria):
								$id_alternatif = $alternatif['id_alternatif'];
								$id_kriteria = $kriteria['id_kriteria'];
								echo '<td>';
								echo $matriks_r[$id_kriteria][$id_alternatif];
								echo '</td>';
							endforeach;
							?>
						</tr>
						<?php
							$no++;
							endforeach
						?>
					</tbody>
				</table>
			</div>
		</div>
	</div>


	<div class="card shadow mb-4">
		<!-- /.card-header -->
		<div class="card-header py-3">
			<h6 class="m-0 font-weight-bold text-success"><i class="fa fa-table"></i> Matriks Y</h6>
		</div>

		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-bordered" width="100%" cellspacing="0">
					<thead class="bg-success text-white">
						<tr align="center">
							<th width="5%" rowspan="2">No</th>
							<th>Nama Alternatif</th>
							<?php foreach ($kriterias as $kriteria): ?>
								<th><?= $kriteria['nama'] ?></th>
							<?php endforeach ?>
						</tr>
					</thead>
					<tbody>
						<?php 
							$no=1;
							foreach ($alternatifs as $alternatif): ?>
						<tr align="center">
							<td><?= $no; ?></td>
							<td align="left"><?= $alternatif['nama'] ?></td>
							<?php						
							foreach($kriterias as $kriteria):
								$id_alternatif = $alternatif['id_alternatif'];
								$id_kriteria = $kriteria['id_kriteria'];
								echo '<td>';
								echo $matriks_y[$id_kriteria][$id_alternatif];
								echo '</td>';
							endforeach;
							?>
						</tr>
						<?php
							$no++;
							endforeach
						?>
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<div class="card shadow mb-4">
		<!-- /.card-header -->
		<div class="card-header py-3">
			<h6 class="m-0 font-weight-bold text-success"><i class="fa fa-table"></i> Solusi Ideal Positif (A+)</h6>
		</div>

		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-bordered" width="100%" cellspacing="0">
					<thead class="bg-success text-white">
						<tr align="center">
							<?php foreach($kriterias as $kriteria ): ?>
								<th><?php echo $kriteria['nama']; ?></th>
							<?php endforeach; ?>
						</tr>
					</thead>
					<tbody>
						<tr align="center">
						<?php foreach($kriterias as $kriteria ): ?>
							<td>
								<?php
								$id_kriteria = $kriteria['id_kriteria'];							
								echo $solusi_ideal_positif[$id_kriteria];
								?>
							</td>
						<?php endforeach; ?>
						</tr>					
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<div class="card shadow mb-4">
		<!-- /.card-header -->
		<div class="card-header py-3">
			<h6 class="m-0 font-weight-bold text-success"><i class="fa fa-table"></i> Solusi Ideal Negatif (A-)</h6>
		</div>

		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-bordered" width="100%" cellspacing="0">
					<thead class="bg-success text-white">
						<tr align="center">
							<?php foreach($kriterias as $kriteria ): ?>
								<th><?php echo $kriteria['nama']; ?></th>
							<?php endforeach; ?>
						</tr>
					</thead>
					<tbody>
						<tr align="center">
						<?php foreach($kriterias as $kriteria ): ?>
							<td>
								<?php
								$id_kriteria = $kriteria['id_kriteria'];							
								echo $solusi_ideal_negatif[$id_kriteria];
								?>
							</td>
						<?php endforeach; ?>
						</tr>					
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<div class="card shadow mb-4">
		<!-- /.card-header -->
		<div class="card-header py-3">
			<h6 class="m-0 font-weight-bold text-success"><i class="fa fa-table"></i> Jarak Ideal Positif (S<sub>i</sub>+)</h6>
		</div>

		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-bordered" width="100%" cellspacing="0">
					<thead class="bg-success text-white">
						<tr align="center">
							<th width="5%">No</th>
							<th>Nama Alternatif</th>
							<th width="30%">Jarak Ideal Positif</th>
						</tr>
					</thead>
					<tbody>
					<?php 
					$no=1;
					foreach($alternatifs as $alternatif ): ?>
						<tr align="center">
							<td><?php echo $no; ?></td>
							<td align="left"><?php echo $alternatif['nama']; ?></td>
							<td>
								<?php								
								$id_alternatif = $alternatif['id_alternatif'];
								echo $jarak_ideal_positif[$id_alternatif];
								?>
							</td>						
						</tr>
					<?php 
					$no++;
					endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<div class="card shadow mb-4">
		<!-- /.card-header -->
		<div class="card-header py-3">
			<h6 class="m-0 font-weight-bold text-success"><i class="fa fa-table"></i> Jarak Ideal Negatif (S<sub>i</sub>-)</h6>
		</div>

		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-bordered" width="100%" cellspacing="0">
					<thead class="bg-success text-white">
						<tr align="center">
							<th width="5%">No</th>
							<th>Nama Alternatif</th>
							<th width="30%">Jarak Ideal Negatif</th>
						</tr>
					</thead>
					<tbody>
					<?php 
					$no=1;
					foreach($alternatifs as $alternatif ): ?>
						<tr align="center">
							<td><?php echo $no; ?></td>
							<td align="left"><?php echo $alternatif['nama']; ?></td>
							<td>
								<?php								
								$id_alternatif = $alternatif['id_alternatif'];
								echo $jarak_ideal_negatif[$id_alternatif];
								?>
							</td>						
						</tr>
					<?php 
					$no++;
					endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<div class="card shadow mb-4">
		<!-- /.card-header -->
		<div class="card-header py-3">
			<h6 class="m-0 font-weight-bold text-success"><i class="fa fa-table"></i> Kedekatan Relatif Terhadap Solusi Ideal (V)</h6>
		</div>

		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-bordered" width="100%" cellspacing="0">
					<thead class="bg-success text-white">
						<tr align="center">
							<th width="5%">No</th>
							<th>Nama Alternatif</th>
							<th width="30%">Nilai</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$no=1;
						foreach($kedekatan_relatif as $alternatif ): ?>
							<tr align="center">
								<td><?php echo $no; ?></td>
								<td align="left"><?php echo $alternatif['nama']; ?></td>
								<td><?php echo $alternatif['nilai']; ?></td>											
							</tr>
						<?php 
						$no++;
						endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	

	<div class="card shadow mb-4">
		<!-- /.card-header -->
		<div class="card-header py-3">
			<h6 class="m-0 font-weight-bold text-success"><i class="fa fa-table"></i> Ranking</h6>
		</div>

		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-bordered" width="100%" cellspacing="0">
					<thead class="bg-success text-white">
						<tr align="center">
							<th width="5%">No</th>
							<th>Nama Alternatif</th>
							<th width="30%">Nilai</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$no=1;
						$id_user = $_SESSION['user_id'];
						date_default_timezone_set('Asia/Jakarta');
						$waktu = date('Y-m-d H:i:s');
						foreach($sorted_ranks as $alternatif ): ?>
							<tr align="center">
								<td><?php echo $no; ?></td>
								<td align="left"><?php echo $alternatif['nama']; ?></td>
								<td><?php echo $alternatif['nilai']; ?></td>											
							</tr>
						<?php 
						$no++;
						mysqli_query($koneksi,"INSERT INTO hasil (id_hasil, id_user, id_alternatif, waktu, nilai) VALUES ('', '$id_user', '$alternatif[id_alternatif]', '$waktu','$alternatif[nilai]')");
						endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<?php
}
require_once('template/footer.php');
}
else {
	header('Location: login.php');
}
?>