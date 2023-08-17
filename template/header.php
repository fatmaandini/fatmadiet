<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Sistem Pendukung Keputusan Metode TOPSIS</title>

  <!-- Custom fonts for this template-->
  <link href="assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <!-- Custom styles for this template-->
  <link href="assets/css/sb-admin-2.min.css" rel="stylesheet">
  <link href="assets/css/sb-admin-2 copy.css" rel="stylesheet">
  <link href="assets/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
  <script src="assets/vendor/jquery/jquery.min.js"></script>
  <link rel="shortcut icon" href="assets/img/favicon.ico" type="image/x-icon">
  <link rel="icon" href="assets/img/favicon.ico" type="image/x-icon">

</head>
<body id="page-top">

  <!-- Page Wrapper -->
  <div id="wrapper">

    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-success sidebar sidebar-dark accordion" id="accordionSidebar">

      <!-- Sidebar - Brand -->
      <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
        <div class="sidebar-brand-icon rotate-n-30">
        <img src="assets/img/dietsehat.png" width="150" height="55">
        </div>
      </a>

      <!-- Divider -->
      <hr class="sidebar-divider my-0">

      <!-- Nav Item - Dashboard -->
      <li class="nav-item <?php if($page == "Dashboard"){echo "active";} ?>">
        <a class="nav-link" href="index.php">
          <i class="fas fa-fw fa-home"></i>
          <span>Dashboard</span></a>
      </li>
	  
	  <?php
	  $user_role = get_role();
	  if($user_role == 'admin') {
	  ?>
	  <!-- Divider -->
      <hr class="sidebar-divider">

      <!-- Heading -->
      <div class="sidebar-heading">
        Master Data
      </div>

	  
	  <li class="nav-item <?php if($page == "Kriteria"){echo "active";} ?>">
        <a class="nav-link" href="list-kriteria.php">
          <i class="fas fa-fw fa-cube"></i>
          <span>Data Kriteria</span></a>
      </li>

      <!-- <li class="nav-item <?php if($page == "Kriteria"){echo "active";} ?>">
        <a class="nav-link" href="list-kriteria-input.php">
          <i class="fas fa-fw fa-cube"></i>
          <span>Data Kriteria [Input]</span></a>
      </li> -->
	  
	  <li class="nav-item <?php if($page == "Sub Kriteria"){echo "active";} ?>">
        <a class="nav-link" href="list-sub-kriteria.php">
          <i class="fas fa-fw fa-cubes"></i>
          <span>Data Sub Kriteria</span></a>
      </li>
	  
	  <li class="nav-item <?php if($page == "Alternatif"){echo "active";} ?>">
        <a class="nav-link" href="list-alternatif.php">
          <i class="fas fa-fw fa-users"></i>
          <span>Data Alternatif</span></a>
      </li>
	  
	  <li class="nav-item <?php if($page == "Riwayat"){echo "active";} ?>">
        <a class="nav-link" href="riwayat.php">
          <i class="fas fa-fw fa-history"></i>
          <span>Data Riwayat</span></a>
      </li>	
	  
	  <!-- Divider -->
      <hr class="sidebar-divider">
	  
	  <!-- Heading -->
      <div class="sidebar-heading">
        Master User
      </div>

	  
	  <li class="nav-item <?php if($page == "Admin"){echo "active";} ?>">
        <a class="nav-link" href="list-admin.php">
          <i class="fas fa-fw fa-user-shield"></i>
          <span>Data Admin</span></a>
      </li>
	  
	  <li class="nav-item <?php if($page == "User"){echo "active";} ?>">
        <a class="nav-link" href="list-user.php">
          <i class="fas fa-fw fa-users-cog"></i>
          <span>Data User</span></a>
      </li>	  
	  
	  <li class="nav-item <?php if($page == "Profile"){echo "active";} ?>">
        <a class="nav-link" href="list-profile.php">
          <i class="fas fa-fw fa-user"></i>
          <span>Data Profile</span></a>
      </li>	
	  
	  <?php
	  }elseif($user_role == 'user') {
	  ?>
	  
	  <!-- Divider -->
      <hr class="sidebar-divider">

      <!-- Heading -->
      <div class="sidebar-heading">
        Master Data
      </div>
	  
	  <li class="nav-item <?php if($page == "Konsultasi"){echo "active";} ?>">
        <a class="nav-link" href="konsultasi.php">
          <i class="fas fa-fw fa-user-md"></i>
          <span>Konsultasi</span></a>
      </li>	

      <!-- <li class="nav-item <?php if($page == "konsultasi-input.php"){echo "active";} ?>">
        <a class="nav-link" href="konsultasi-input.php">
          <i class="fas fa-fw fa-history"></i>
          <span>Konsultasi Input</span></a>
      </li>	

      <li class="nav-item <?php if($page == "perhitungan.php"){echo "active";} ?>">
        <a class="nav-link" href="perhitungan.php">
          <i class="fas fa-fw fa-history"></i>
          <span>Data Perhitungan [input]</span></a>
      </li>	 -->
	  
	  <li class="nav-item <?php if($page == "Riwayat"){echo "active";} ?>">
        <a class="nav-link" href="riwayat-user.php">
          <i class="fas fa-fw fa-history"></i>
          <span>Data Riwayat</span></a>
      </li>	
	  
	  <!-- Divider -->
      <hr class="sidebar-divider">
	  
	  <!-- Heading -->
      <div class="sidebar-heading">
        Master User
      </div>
	  
	  <li class="nav-item <?php if($page == "Profile"){echo "active";} ?>">
        <a class="nav-link" href="list-profile.php">
          <i class="fas fa-fw fa-user"></i>
          <span>Data Profile</span></a>
      </li>	
	  
	  <?php
	  }
	  ?>

      <!-- Divider -->
      <hr class="sidebar-divider d-none d-md-block">

      <!-- Sidebar Toggler (Sidebar) -->
      <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
      </div>

    </ul>
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

      <!-- Main Content -->
      <div id="content">

        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

          <!-- Sidebar Toggle (Topbar) -->
		  <button id="sidebarToggleTop" class="btn text-success d-md-none rounded-circle mr-3">
            <i class="fa fa-bars"></i>
          </button>

          <!-- Topbar Navbar -->
          <ul class="navbar-nav ml-auto">

            <!-- Nav Item - User Information -->
            <li class="nav-item dropdown no-arrow">
              <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="text-uppercase mr-2 d-none d-lg-inline text-gray-600 small">
					<?php
					echo $_SESSION['nama'];
					?>
				</span>
                <img class="img-profile rounded-circle" src="assets/img/user.png">
              </a>
              <!-- Dropdown - User Information -->
              <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="list-profile.php">
                  <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                  Profile
                </a>
				<div class="dropdown-divider"></div>
				<a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                  <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                  Logout
                </a>
              </div>
            </li>

          </ul>

        </nav>
        <!-- End of Topbar -->
		
		<div class="container-fluid">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
$(document).ready(function() {
  // Ambil nilai dari dropdown tinggi badan, berat badan, dan umur ketika halaman siap
  const tinggiBadanDropdown = $('select[name="nilai[]"]').eq(0);
  const beratBadanDropdown = $('select[name="nilai[]"]').eq(1);
  const umurDropdown = $('select[name="nilai[]"]').eq(2);

  // Fungsi untuk mengubah pilihan dropdown jenis genetik berdasarkan nilai tinggi badan, berat badan, dan umur
  function updateJenisGenetikDropdown() {
    const tinggiBadan = tinggiBadanDropdown.val();
    const beratBadan = beratBadanDropdown.val();
    const umur = umurDropdown.val();


  	$("#select_5").prop('disabled',false); // open disable just in case old browser

    // Kirim permintaan AJAX ke server untuk mendapatkan jenis genetik berdasarkan nilai yang dipilih
    $.ajax({
      url: 'get_jenis_genetik.php',
      method: 'POST',
      data: { tinggi_badan: tinggiBadan, berat_badan: beratBadan, umur: umur },
      success: function(data) {
        // Ubah isi dropdown jenis genetik berdasarkan data yang diterima dari server
        const jenisGenetikDropdown = $('select[name="nilai[]"]').eq(4);
        jenisGenetikDropdown.html(data);
      },
      error: function() {
        alert('Terjadi kesalahan saat mengambil data jenis genetik!');
      }
    });

    $("#select_5").prop('disabled',true); //back to disable
  }

  // Panggil fungsi untuk pertama kali ketika halaman siap
 updateJenisGenetikDropdown();

  // Panggil fungsi lagi setiap kali ada perubahan pada dropdown tinggi badan, berat badan, atau umur
  $('select[name="nilai[]"]').on('change', function() {
    updateJenisGenetikDropdown();
  });
});
</script>
