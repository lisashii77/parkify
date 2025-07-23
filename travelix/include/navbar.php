<header class="header">

		<!-- Top Bar -->

		<div class="top_bar">
			<div class="container">
				<div class="row">
					<div class="col d-flex flex-row">
						<div class="user_box ml-auto">
							<div class="navbar-right" style="margin-left:auto; display: flex; align-items: center; gap: 15px; color:white;">
								<?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'user'): ?>
									<span>Hi, <?= htmlspecialchars($_SESSION['user']['username']) ?></span> |
									<a href="/parkir-main/auth/logout.php" style="color: #f44336;">Logout</a>
								<?php else: ?>
									<a href="/parkir-main/auth/login.php" style="color: #fa9e1c;">Login</a>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
			</div>		
		</div>

		<!-- Main Navigation -->

		<nav class="main_nav">
			<div class="container">
				<div class="row">
					<div class="col main_nav_col d-flex flex-row align-items-center justify-content-start">
						<div class="logo_container">
							<div class="logo"><a href="#">PARKIFY</a></div>
						</div>
						<div class="main_nav_container ml-auto">
							<ul class="main_nav_list">
								<li class="main_nav_item"><a href="index.php">Beranda</a></li>
								<li class="main_nav_item"><a href="riwayat_pembayaran.php">Riwayat Pembayaran</a></li>
							</ul>
						</div>
						<form id="search_form" class="search_form bez_1">
							<input type="search" class="search_content_input bez_1">
						</form>

						<div class="hamburger">
							<i class="fa fa-bars trans_200"></i>
						</div>
					</div>
				</div>
			</div>	
		</nav>

	</header>