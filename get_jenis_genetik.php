<?php
$koneksi = mysqli_connect("localhost","root","","fatma");
 
// Check connection
if (mysqli_connect_errno()){
	echo "Koneksi database gagal : " . mysqli_connect_error();
}

// Ambil nilai tinggi badan, berat badan, dan umur yang dikirim dari permintaan AJAX
$tinggiBadan = $_POST['tinggi_badan'];
$beratBadan = $_POST['berat_badan'];
$umur = $_POST['umur'];

// Di sini, Anda perlu melakukan query ke database berdasarkan nilai tinggi badan, berat badan, dan umur untuk mendapatkan jenis genetik yang sesuai
// Misalnya, jika Anda menggunakan MySQL, query bisa terlihat seperti ini (pastikan Anda sudah terhubung ke database sebelumnya):
// $koneksi = mysqli_connect('nama_host', 'nama_pengguna', 'kata_sandi', 'nama_database');
// $jenisGenetikQuery = mysqli_query($koneksi, "SELECT id, nama FROM kriteria WHERE tinggi_badan = '$tinggiBadan' AND berat_badan = '$beratBadan' AND umur = '$umur'");
// while ($jenisGenetik = mysqli_fetch_array($jenisGenetikQuery)) {
//   echo '<option value="' . $jenisGenetik['id'] . '">' . $jenisGenetik['nama'] . '</option>';
// }



// Untuk contoh sederhana ini, kita akan mengembalikan pilihan jenis genetik berdasarkan tinggi badan, berat badan, dan umur secara statis
$jenisGenetik = [
  ['id' => 1, 'nama' => 'Ectomorph'],
  ['id' => 2, 'nama' => 'Mesomorph'],
  ['id' => 3, 'nama' => 'Endomorph']
];

if($tinggiBadan !='' && $beratBadan!=''){
$jenisGenetikQuery = "SELECT CASE WHEN bobot_berat  = 1 OR (bobot_berat = 2 AND bobot_tinggi > 2) THEN 1
WHEN (bobot_tinggi < 3 AND bobot_berat > 2) OR (bobot_tinggi = 3 AND bobot_tinggi = 5) THEN 3
ELSE 2 END AS result FROM ( 
SELECT MAX(bobot_tinggi) AS bobot_tinggi, MAX(bobot_berat) AS bobot_berat, MAX(bobot_tinggi)*MAX(bobot_berat) AS total FROM (
SELECT CASE WHEN kode_kriteria ='C1' AND id_sub_kriteria ='".$tinggiBadan."' THEN nilai END AS bobot_tinggi,
CASE WHEN kode_kriteria ='C2' AND nilai ='".$beratBadan."' THEN nilai END AS bobot_berat
FROM kriteria a INNER JOIN sub_kriteria AS b
ON a.id_kriteria = b.id_kriteria) a ) b";
$result = mysqli_query($koneksi, $jenisGenetikQuery);
$value = $result->fetch_row()[0];
}


foreach ($jenisGenetik as $genetik) {
  echo '<option value="' . $genetik['id'] . '" ';
  if($tinggiBadan != '' && $beratBadan !='' && $genetik['id'] == $value){ echo " selected "; }
  echo '>' . $genetik['nama'] . '</option>';
}
?>
