<?php require_once('includes/init.php'); ?>
<?php cek_login($role = array(2)); ?>

<?php
$ada_error = false;
$result = '';

$id_user = (isset($_GET['id_user'])) ? trim($_GET['id_user']) : '';
$waktu = (isset($_GET['waktu'])) ? trim($_GET['waktu']) : '';
if(!$id_user) {
	$ada_error = 'Maaf, data tidak dapat diproses.';
}elseif(!$waktu) {
	$ada_error = 'Maaf, data tidak dapat diproses.';
} else {
	$query = mysqli_query($koneksi,"SELECT * FROM hasil WHERE waktu='$waktu' AND id_user = '$id_user';");
	$cek = mysqli_num_rows($query);
	
	if($cek <= 0) {
		$ada_error = 'Maaf, data tidak dapat diproses.';
	} else {
		mysqli_query($koneksi,"DELETE FROM hasil WHERE waktu='$waktu' AND id_user = '$id_user';");
		redirect_to('riwayat-user.php?status=sukses-hapus');
	}
}
?>

<?php
$page = "Riwayat";
require_once('template/header.php');
?>
	<?php if($ada_error): ?>
		<?php echo '<div class="alert alert-danger">'.$ada_error.'</div>'; ?>	
	<?php endif; ?>
<?php
require_once('template/footer.php');