<?php
session_start();
include '../config/db.php';

// Ambil semua tempat parkir
$tempat_stmt = $conn->query("SELECT * FROM tempat_parkir");
$tempat_list = $tempat_stmt->fetchAll(PDO::FETCH_ASSOC);

// Ambil lokasi berdasarkan jenis, bukan prefix
$lokasi_motor = $conn->query("SELECT * FROM parkir_slots WHERE jenis = 'motor'")->fetchAll(PDO::FETCH_ASSOC);
$lokasi_mobil = $conn->query("SELECT * FROM parkir_slots WHERE jenis = 'mobil'")->fetchAll(PDO::FETCH_ASSOC);
$lokasi_vip   = $conn->query("SELECT * FROM parkir_slots WHERE jenis = 'vip'")->fetchAll(PDO::FETCH_ASSOC);

// Fungsi untuk menampilkan dropdown jam
function renderJamSelect($name, $start = 7, $end = 23) {
    echo "<select name='$name' class='form-control' required>";
    echo "<option value=''>Pilih Jam</option>";
    for ($i = $start; $i <= $end; $i++) {
        $jam = sprintf('%02d:00', $i);
        echo "<option value='$jam'>$jam</option>";
    }
    echo "</select>";
}

// Fungsi untuk memilih tanggal (input date)
function renderTanggalInput($name) {
    echo "<input type='date' name='$name' class='form-control' required>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<title>PARKIFY</title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="description" content="Travelix Project">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" type="text/css" href="styles/bootstrap4/bootstrap.min.css">
<link href="plugins/font-awesome-4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="plugins/OwlCarousel2-2.2.1/owl.carousel.css">
<link rel="stylesheet" type="text/css" href="plugins/OwlCarousel2-2.2.1/owl.theme.default.css">
<link rel="stylesheet" type="text/css" href="plugins/OwlCarousel2-2.2.1/animate.css">
<link rel="stylesheet" type="text/css" href="styles/main_styles.css">
<link rel="stylesheet" type="text/css" href="styles/responsive.css">
</head>

<body>

<div class="super_container">
	
	<!-- Header -->
<!-- navbar start -->
	<?php include '../travelix/include/navbar.php'; ?>
<!-- navbar end -->

	<div class="menu trans_500">
		<div class="menu_content d-flex flex-column align-items-center justify-content-center text-center">
			<div class="menu_close_container"><div class="menu_close"></div></div>
			<div class="logo menu_logo"><a href="#"><img src="images/logo.png" alt=""></a></div>
			<ul>
				<li class="menu_item"><a href="#">home</a></li>
				<li class="menu_item"><a href="about.html">about us</a></li>
				<li class="menu_item"><a href="offers.html">offers</a></li>
				<li class="menu_item"><a href="blog.html">news</a></li>
				<li class="menu_item"><a href="contact.html">contact</a></li>
			</ul>
		</div>
	</div>

	<!-- Home -->

	<div class="home">
		
		<!-- Home Slider -->

		<div class="home_slider_container">
			<div class="owl-carousel owl-theme home_slider">
				<!-- Slider Item -->
				<div class="owl-item home_slider_item">
					<!-- Image by https://unsplash.com/@anikindimitry -->
					<div class="home_slider_background" style="background-image:url(images/home_slider.jpg)"></div>

					<div class="home_slider_content text-center">
						<div class="home_slider_content_inner" data-animation-in="flipInX" data-animation-out="animate-out fadeOut">
							<h1>drive</h1>
							<h1>find your space</h1>
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>

	<!-- Search -->

	<div class="search">
		

		<!-- Search Contents -->
		
		<div class="container fill_height">
			<div class="row fill_height">
				<div class="col fill_height">

					<!-- Search Tabs -->

					<div class="search_tabs_container">
						<div class="search_tabs d-flex flex-lg-row flex-column align-items-lg-center align-items-start justify-content-lg-between justify-content-start">
							<div class="search_tab active d-flex flex-row align-items-center justify-content-lg-center justify-content-start">Motor</div>
							<div class="search_tab d-flex flex-row align-items-center justify-content-lg-center justify-content-start">Mobil</div>
							<div class="search_tab d-flex flex-row align-items-center justify-content-lg-center justify-content-start">Mobil VIP</div>
						</div>		
					</div>

				    <!-- Search Panel Motor -->
					<div class="search_panel active">
						<form action="search_result.php" method="POST" class="search_panel_content d-flex flex-lg-row flex-column align-items-lg-center align-items-start justify-content-lg-between justify-content-start">
							<input type="hidden" name="jenis" value="motor">
							<div class="search_item col-md-3 mb-3">
								<div>Tempat</div>
								<select name="tempat" id="tempat_motor" class="form-control" required>
									<option value="">Pilih Tempat</option>
									<?php foreach ($tempat_list as $t): ?>
										<option value="<?= $t['tempat_id'] ?>"><?= $t['nama_tempat'] ?></option>
									<?php endforeach; ?>
								</select>
							</div>

							<div class="search_item col-md-3 mb-3">
								<div>Lokasi Parkir</div>
								<select name="lokasi" id="lokasi_motor" class="form-control" required>
									<option value="">Pilih Lokasi</option>
								</select>
							</div>

							<div class="search_item col-md-2 mb-3">
								<div>Jam Masuk</div>
								<input type="datetime-local" name="jam_masuk" class="form-control" required>
							</div>

							<div class="search_item col-md-2 mb-3">
								<div>Jam Keluar</div>
								<input type="datetime-local" name="jam_keluar" class="form-control" required>
							</div>
							<div class="search_item col-md-2 mb-3">
							<button type="button" class="button search_button btn-cari" data-jenis="motor">search</button>
							</div>
						</form>
					</div>
					<!-- Search Panel Mobil -->
					<div class="search_panel">
						<form action="search_result.php" method="POST" class="search_panel_content d-flex flex-lg-row flex-column align-items-lg-center align-items-start justify-content-lg-between justify-content-start">
							<input type="hidden" name="jenis" value="mobil">

							<div class="search_item col-md-3 mb-3">
								<div>Tempat</div>
								<select name="tempat" id="tempat_mobil" class="form-control" required>
									<option value="">Pilih Tempat</option>
									<?php foreach ($tempat_list as $t): ?>
										<option value="<?= $t['tempat_id'] ?>"><?= $t['nama_tempat'] ?></option>
									<?php endforeach; ?>
								</select>
							</div>

							<div class="search_item col-md-3 mb-3">
								<div>Lokasi Parkir</div>
								<select name="lokasi" id="lokasi_mobil" class="form-control" required>
									<option value="">Pilih Lokasi</option>
								</select>
							</div>

							<div class="search_item col-md-2 mb-3">
								<div>Jam Masuk</div>
								<input type="datetime-local" name="jam_masuk" class="form-control" required>
							</div>

							<div class="search_item col-md-2 mb-3">
								<div>Jam Keluar</div>
								<input type="datetime-local" name="jam_keluar" class="form-control" required>
							</div>
							<div class="search_item col-md-2 mb-3">
							<button type="button" class="button search_button btn-cari" data-jenis="mobil">search</button>
							</div>
						</form>
					</div>
					<!-- Search Panel Mobil VIP -->
					<div class="search_panel">
						<form action="search_result.php" method="POST" class="search_panel_content d-flex flex-lg-row flex-column align-items-lg-center align-items-start justify-content-lg-between justify-content-start">
							<input type="hidden" name="jenis" value="vip">

							<div class="search_item col-md-3 mb-3">
								<div>Tempat</div>
								<select name="tempat" id="tempat_vip" class="form-control" required>
									<option value="">Pilih Tempat</option>
									<?php foreach ($tempat_list as $t): ?>
										<option value="<?= $t['tempat_id'] ?>"><?= $t['nama_tempat'] ?></option>
									<?php endforeach; ?>
								</select>
							</div>

							<div class="search_item col-md-3 mb-3">
								<div>Lokasi Parkir</div>
							<select name="lokasi" id="lokasi_vip" class="form-control" required>
								<option value="">Pilih Lokasi</option>
							</select>
							</div>

							<div class="search_item col-md-2 mb-3">
								<div>Jam Masuk</div>
								<input type="datetime-local" name="jam_masuk" class="form-control" required>
							</div>

							<div class="search_item col-md-2 mb-3">
								<div>Jam Keluar</div>
								<input type="datetime-local" name="jam_keluar" class="form-control" required>
							</div>
							<div class="search_item col-md-2 mb-3">
								<button type="button" class="button search_button btn-cari" data-jenis="vip">search</button>
							</div>
						</div>
						</form>
					</div>
				</div>
			</div>
		</div>		
	</div>
		<!-- Tambahkan section baru di sini -->
					<section id="hasil_motor" class="search_results" style="display: none;">
					<div class="container">
						<div class="row intro_items"></div>
					</div>
					</section>

					<section id="hasil_mobil" class="search_results" style="display: none;">
					<div class="container">
						<div class="row intro_items"></div>
					</div>
					</section>

					<section id="hasil_vip" class="search_results" style="display: none;">
					<div class="container">
						<div class="row intro_items"></div>
					</div>
					</section>
	<!-- Intro -->
	
	<div class="intro">
		<div class="container">
			<div class="row">
				<div class="col">
					<h2 class="intro_title text-center">Parkir Mudah, Cepat, dan Aman</h2>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-10 offset-lg-1">
					<div class="intro_text text-center">
						<p>Parkify menyediakan layanan pencarian dan booking slot parkir secara real-time di berbagai lokasi strategis di kota. Temukan tempat parkir terbaik hanya dalam beberapa klik.</p>
					</div>
				</div>
			</div>

			<!-- Satu baris, 4 kolom -->
			<div class="row intro_items">

				<!-- Tunjungan Plaza -->
				<div class="col-lg-3 intro_col">
					<div class="intro_item">
						<div class="intro_item_overlay"></div>
						<div class="intro_item_background" style="background-image:url(images/tunjungan.jpg)"></div>
						<div class="intro_item_content d-flex flex-column align-items-center justify-content-center">
							<div class="intro_date">Tersedia Setiap Hari</div>
							<div class="intro_center text-center">
								<h3 style="color:#fa9e1c;font-weight: 700;">Tunjungan Plaza</h3>
								<div class="intro_price">Parkir Aman & Nyaman</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Grand City -->
				<div class="col-lg-3 intro_col">
					<div class="intro_item">
						<div class="intro_item_overlay"></div>
						<div class="intro_item_background" style="background-image:url(images/gc.jpg)"></div>
						<div class="intro_item_content d-flex flex-column align-items-center justify-content-center">
							<div class="intro_date">Tersedia Setiap Hari</div>
							<div class="intro_center text-center">
								<h3 style="color:#fa9e1c;font-weight: 700;">Grand City</h3>
								<div class="intro_price">Lokasi Strategis</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Mayjen Sungkono -->
				<div class="col-lg-3 intro_col">
					<div class="intro_item">
						<div class="intro_item_overlay"></div>
						<div class="intro_item_background" style="background-image:url(images/mayjen.jpg)"></div>
						<div class="intro_item_content d-flex flex-column align-items-center justify-content-center">
							<div class="intro_date">Tersedia Setiap Hari</div>
							<div class="intro_center text-center">
								<h3 style="color:#fa9e1c;font-weight: 700;">Mayjen Sungkono</h3>
								<div class="intro_price">Park & Ride Area</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Adityawarman -->
				<div class="col-lg-3 intro_col">
					<div class="intro_item">
						<div class="intro_item_overlay"></div>
						<div class="intro_item_background" style="background-image:url(images/aditya.jpg)"></div>
						<div class="intro_item_content d-flex flex-column align-items-center justify-content-center">
							<div class="intro_date">Tersedia Setiap Hari</div>
							<div class="intro_center text-center">
								<h3 style="color:#fa9e1c;font-weight: 700;">Adityawarman</h3>
								<div class="intro_price">Mudah Diakses</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Footer -->

	<?php include '../travelix/include/footer.php'; ?>

</div>

<script src="js/jquery-3.2.1.min.js"></script>
<script src="styles/bootstrap4/popper.js"></script>
<script src="styles/bootstrap4/bootstrap.min.js"></script>
<script src="plugins/OwlCarousel2-2.2.1/owl.carousel.js"></script>
<script src="plugins/easing/easing.js"></script>
<script src="js/custom.js"></script>
<script>
$(document).ready(function() {
    $('#tempat_motor').on('change', function() {
        var tempat_id = $(this).val();
        if (tempat_id) {
            $.ajax({
                url: 'get_lokasi.php',
                type: 'POST',
                data: { tempat_id: tempat_id, jenis: 'motor' },
                success: function(response) {
                    $('#lokasi_motor').html(response);
                }
            });
        } else {
            $('#lokasi_motor').html('<option value="">Pilih Lokasi</option>');
        }
    });
	// Dropdown dinamis untuk mobil
$('#tempat_mobil').on('change', function() {
    var tempat_id = $(this).val();
    if (tempat_id) {
        $.ajax({
            url: 'get_lokasi.php',
            type: 'POST',
            data: { tempat_id: tempat_id, jenis: 'mobil' },
            success: function(response) {
                $('#lokasi_mobil').html(response);
            }
        });
    } else {
        $('#lokasi_mobil').html('<option value="">Pilih Lokasi</option>');
    }
});

// Dropdown dinamis untuk mobil VIP
$('#tempat_vip').on('change', function() {
    var tempat_id = $(this).val();
    if (tempat_id) {
        $.ajax({
            url: 'get_lokasi.php',
            type: 'POST',
            data: { tempat_id: tempat_id, jenis: 'vip' },
            success: function(response) {
                $('#lokasi_vip').html(response);
            }
        });
    } else {
        $('#lokasi_vip').html('<option value="">Pilih Lokasi</option>');
    }
});
});
$('#btn_search_motor').on('click', function () {
    var dataForm = $('#form_motor').serialize();
    $.ajax({
        url: 'search_result.php',
        type: 'POST',
        data: dataForm,
        success: function (response) {
            $('#hasil_pencarian_motor').html(response);
        }
    });
});
$('.btn-cari').on('click', function () {
    var jenis = $(this).data('jenis');
    var form = $(this).closest('form');
    var dataForm = form.serialize();

    $.ajax({
        url: 'search_result.php',
        type: 'POST',
        data: dataForm,
        success: function (response) {
            if (jenis === 'motor') {
                $('#hasil_motor .intro_items').html(response);
                $('#hasil_motor').show(); // tampilkan hasil jika ada
            } else if (jenis === 'mobil') {
                $('#hasil_mobil .intro_items').html(response);
                $('#hasil_mobil').show();
            } else if (jenis === 'vip') {
                $('#hasil_vip .intro_items').html(response);
                $('#hasil_vip').show();
            }
			$('html, body').animate({
				scrollTop: $('#' + 'hasil_' + jenis).offset().top
			}, 800);
        }
		
    });
});


</script>

</body>

</html>