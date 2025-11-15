<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?php echo html( $this->description ); ?>">
    <meta name="author" content="Sunag Technologies / https://www.sunag.com.br/">
	<meta name="robots" content="noindex">
	<link rel="shortcut icon" href="../favicon.ico" />
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo url("favicon.png"); ?>">
    <title><?php echo html( $this->title ); ?></title>
    <!-- Bootstrap Core CSS -->
	<?php $this->css('admin/template/assets/plugins/bootstrap/css/bootstrap.min.css'); ?>
    <!-- Custom CSS -->
	<?php $this->css('admin/template/css/style.css'); ?>
    <!-- You can change the theme colors from here -->
	<?php $this->css('admin/template/css/colors/blue.css'); ?>
	<!-- Template -->
	<?php $this->css('admin/css/style.css'); ?>
	<!-- jquery (special) -->
	<?php $this->script('admin/js/jquery-3.3.1.min.js'); ?>
	<!-- custom font -->
	<?php $this->css('admin/font/stylesheet.css'); ?>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
	<![endif]-->
	<!-- Plugin -->
	<?php $this->plugins_format('css'); ?>
</head>
<body class="fix-header card-no-border logo-center">
    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->
    <!--<div class="preloader">
        <svg class="circular" viewBox="25 25 50 50">
            <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" />
		</svg>
    </div>-->
    <!-- ============================================================== -->
    <!-- Main wrapper - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <div id="main-wrapper">
<?php if ($this->logged) { ?>
		<!-- ============================================================== -->
        <!-- Topbar header - style you can find in pages.scss -->
        <!-- ============================================================== -->
        <header class="topbar">
            <nav class="navbar top-navbar navbar-expand-md navbar-light">
                <!-- ============================================================== -->
                <!-- Logo -->
                <!-- ============================================================== -->
                <div class="navbar-header">
                    <a class="navbar-brand" href="<?php echo url("admin/"); ?>">
                       <img  src="<?php echo url("admin/img/R_RENAULT_EMBLEM_RGB_Positive_v1.svg"); ?>" alt="homepage" class="logo" />
					</a>
                </div>
                <!-- ============================================================== -->
                <!-- End Logo -->
                <!-- ============================================================== -->
                <div class="navbar-collapse">
					<!-- ============================================================== -->
                    <!-- toggle and nav items -->
                    <!-- ============================================================== -->
                    <ul class="navbar-nav mr-auto mt-md-0">
						<!-- This is  -->
                        <li class="nav-item">
							<a class="nav-link nav-toggler hidden-md-up text-muted waves-effect waves-dark" href="javascript:void(0)"><i class="mdi mdi-menu"></i></a>
						</li>
                    </ul>
					<!-- ============================================================== -->
                    <!-- User profile and search -->
                    <!-- ============================================================== -->
                    <ul class="navbar-nav my-lg-0">
                        <!-- ============================================================== -->
                        <!-- Profile -->
                        <!-- ============================================================== -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-muted waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<!--<img src="<?php echo url("admin/template/assets/images/users/1.jpg"); ?>" alt="user" class="profile-pic" />-->
								<i class="mdi mdi-account profile-pic"></i>
							</a>
                            <div class="dropdown-menu dropdown-menu-right scale-up">
                                <ul class="dropdown-user">
                                    <li><a href="signin.php?logout"><i class="fa fa-power-off"></i> Logout</a></li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        <!-- ============================================================== -->
        <!-- End Topbar header -->
        <!-- ============================================================== -->
		<!-- ============================================================== -->
        <!-- Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <aside class="left-sidebar text-center">
            <!-- Sidebar scroll-->
            <div class="scroll-sidebar">
                <!-- Sidebar navigation-->
                <nav class="sidebar-nav">
                    <ul id="sidebarnav">
<?php if ($useradm->is_level(LEVEL_COMMON)) { ?>
	<?php if ($useradm->is_level(LEVEL_MASTER)) { ?>
						<li>
                            <a class="has-arrow" href="managers.php" aria-expanded="false">
								<i class="mdi mdi-account-multiple"></i><span class="hide-menu">Gerentes</span>
							</a>
                        </li>
	<?php } ?>
						<li>
                            <a class="has-arrow" href="users.php" aria-expanded="false">
								<i class="mdi mdi-account-multiple"></i><span class="hide-menu">Usuários</span>
							</a>
                        </li>
	<?php if ($useradm->is_level(LEVEL_MASTER)) { ?>
						<li>
							<a class="has-arrow" href="posts.php" aria-expanded="false">
								<i class="mdi mdi-format-list-bulleted"></i><span class="hide-menu">Socials</span>
							</a>
						</li>
						<li>
							<a class="has-arrow" href="news.php" aria-expanded="false">
								<i class="mdi mdi-format-list-bulleted"></i><span class="hide-menu">Notícias</span>
							</a>
						</li>
						<li>
							<a class="has-arrow" href="communicateds.php" aria-expanded="false">
								<i class="mdi mdi-format-list-bulleted"></i><span class="hide-menu">Comunicados</span>
							</a>
						</li>
						<li>
							<a class="has-arrow" href="whatsapp_galeries.php" aria-expanded="false">
								<i class="mdi mdi-whatsapp"></i><span class="hide-menu">Galerias WhatsApp</span>
							</a>
						</li>
						<li>
							<a class="has-arrow" href="campaigns.php" aria-expanded="false">
								<i class="mdi mdi-format-page-break"></i><span class="hide-menu">Campanhas</span>
							</a>
						</li>
						<li>
							<a class="has-arrow" href="vehicles.php" aria-expanded="false">
								<i class="mdi mdi-car"></i><span class="hide-menu">Veículos</span>
							</a>
						</li>
						<li>
							<a class="has-arrow" href="templates.php" aria-expanded="false">
								<i class="mdi mdi-presentation"></i><span class="hide-menu">Templates</span>
							</a>
						</li>
						<li>
							<a class="has-arrow" href="surveys.php" aria-expanded="false">
								<i class="mdi mdi-playlist-check"></i><span class="hide-menu">Enquetes</span>
							</a>
						</li>
						<li>
							<a class="has-arrow" href="tutorials.php" aria-expanded="false">
								<i class="mdi mdi-file-video"></i><span class="hide-menu">Tutoriais</span>
							</a>
						</li>
						<li>
							<a class="has-arrow" href="faq_groups.php" aria-expanded="false">
								<i class="mdi mdi-comment-question-outline"></i><span class="hide-menu">FAQ</span>
							</a>
						</li>
						<li>
							<a class="has-arrow" href="renault_universe.php" aria-expanded="false">
								<i class="mdi mdi-comment-question-outline"></i><span class="hide-menu">Universo Renault</span>
							</a>
						</li>
						<li>
							<a class="has-arrow" href="renault_care_services.php" aria-expanded="false">
								<i class="mdi mdi-security-home"></i><span class="hide-menu">Renault Care Services</span>
							</a>
						</li>
						<li>
							<a class="has-arrow" href="renault_academy.php" aria-expanded="false">
								<i class="mdi mdi-school"></i><span class="hide-menu">Renault Academy</span>
							</a>
						</li>
						<li>
							<a class="has-arrow" href="quick_views.php" aria-expanded="false">
								<i class="mdi mdi-note-outline"></i><span class="hide-menu">Fichas de Modelos</span>
							</a>
						</li>

	<?php } ?>
<?php } ?>
                    </ul>
                </nav>
                <!-- End Sidebar navigation -->
            </div>
            <!-- End Sidebar scroll-->
        </aside>
        <!-- ============================================================== -->
        <!-- End Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
		<!-- ============================================================== -->
        <!-- Page wrapper  -->
        <!-- ============================================================== -->
        <div class="page-wrapper">
            <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <div class="container-fluid">
<?php } ?>