<?php require_once('includes/init.php'); ?>
<?php cek_login($role = array(1)); ?>

<?php
$errors = array();
$sukses = false;

$id_alternatif = (isset($_POST['id_alternatif'])) ? trim($_POST['id_alternatif']) : '';
$nama = (isset($_POST['nama'])) ? trim($_POST['nama']) : '';

if(isset($_POST['submit'])):	
	
	// Validasi
	if(!$nama) {
		$errors[] = 'Nama tidak boleh kosong';
	}
	
	$id_kriteria = $_POST['id_kriteria'];
	$nilai = $_POST['nilai'];

	if(!$id_kriteria) {
		$errors[] = 'ID kriteria tidak boleh kosong';
	}
	if(!$id_alternatif) {
		$errors[] = 'ID Alternatif kriteria tidak boleh kosong';
	}		
	if(!$nilai) {
		$errors[] = 'Nilai kriteria tidak boleh kosong';
	}	
	
	// Jika lolos validasi lakukan hal di bawah ini
	if(empty($errors)):
		$simpan = mysqli_query($koneksi,"INSERT INTO alternatif (id_alternatif, nama) VALUES ('$id_alternatif', '$nama')");
		$i = 0;
		foreach ($nilai as $key) {
			$simpan = mysqli_query($koneksi,"INSERT INTO penilaian (id_penilaian, id_alternatif, id_kriteria, nilai) VALUES ('', '$id_alternatif', '$id_kriteria[$i]', '$key')");
			$i++;
		}
		
		if($simpan) {
			redirect_to('list-alternatif.php?status=sukses-baru');
		}else{
			$errors[] = 'Data gagal disimpan';
		}
	endif;
	
	
	
	

endif;

$page = "Alternatif";
require_once('template/header.php');
?>


<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-fw fa-users"></i> Data Alternatif</h1>

	<a href="list-alternatif.php" class="btn btn-secondary btn-icon-split"><span class="icon text-white-50"><i class="fas fa-arrow-left"></i></span>
		<span class="text">Kembali</span>
	</a>
</div>
			
<?php if(!empty($errors)): ?>
	<div class="alert alert-info">
		<?php foreach($errors as $error): ?>
			<?php echo $error; ?>
		<?php endforeach; ?>
	</div>
<?php endif; ?>	


<?php
$query = mysqli_query($koneksi, "SELECT max(id_alternatif) as id FROM alternatif");
$data = mysqli_fetch_array($query);
$idalt = $data['id'];
$urutan = (int) substr($idalt, -4, 4);
$urutan++;
$altid = sprintf("%04s", $urutan);
?>		
			
<form action="tambah-alternatif.php" method="post">
	<div class="card shadow mb-4">
		<div class="card-header py-3">
			<h6 class="m-0 font-weight-bold text-success"><i class="fas fa-fw fa-plus"></i> Tambah Data Alternatif</h6>
		</div>
		<div class="card-body">
			<div class="row">				
				<div class="form-group col-md-6">
					<label class="font-weight-bold">Nama</label>
					<input type="text" name="id_alternatif" value="<?= $altid ?>" hidden>
					<input autocomplete="off" type="text" name="nama" required value="<?php echo $nama; ?>" class="form-control"/>
				</div>
				
				<?php
				$q2 = mysqli_query($koneksi,"SELECT * FROM kriteria ORDER BY kode_kriteria ASC");			
				while($d = mysqli_fetch_array($q2)){
				?>
				<input type="text" name="id_kriteria[]" value="<?= $d['id_kriteria'] ?>" hidden>
				<div class="form-group col-md-6">
					<label class="font-weight-bold">(<?= $d['kode_kriteria'] ?>) <?= $d['nama'] ?></label>
					<select name="nilai[]" class="form-control" required>
						<option value="">--Pilih--</option>
						<?php
						$id_kriteria = $d['id_kriteria'];
						$q3 = mysqli_query($koneksi,"SELECT * FROM sub_kriteria WHERE id_kriteria = '$id_kriteria' ORDER BY nilai ASC");			
						while($d3 = mysqli_fetch_array($q3)){
						?>
						<option value="<?= $d3['id_sub_kriteria'] ?>"><?= $d3['nama'] ?> </option>
						<?php } ?>
					</select>
				</div>
				<?php } ?>
			</div>
		</div>
		<div class="card-footer text-right">
            <button name="submit" value="submit" type="submit" class="btn btn-success"><i class="fa fa-save"></i> Simpan</button>
            <button type="reset" class="btn btn-info"><i class="fa fa-sync-alt"></i> Reset</button>
        </div>
	</div>
</form>

<?php
require_once('template/footer.php');
?>