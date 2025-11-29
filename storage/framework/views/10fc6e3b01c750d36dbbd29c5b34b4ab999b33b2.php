<!DOCTYPE html>
<?php
    $logo=asset(Storage::url('uploads/logo/'));
    $company_favicon=Utility::getValByName('company_favicon');
?>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Nobs Micro Credit">
    <meta name="author" content="Stephen Aidoo">
    <title><?php echo $__env->yieldContent('page-title'); ?> - <?php echo e((Utility::getValByName('header_text')) ? Utility::getValByName('header_text') : config('app.name', 'Nobs Micro Credit')); ?></title>
    <link rel="icon" href="<?php echo e($logo.'/'.(isset($company_favicon) && !empty($company_favicon)?$company_favicon:'favicon.png')); ?>" type="image" sizes="16x16">

    <link rel="stylesheet" href="<?php echo e(asset('css/materialize.css')); ?>">
	<link rel="stylesheet" href="<?php echo e(asset('css/loader.css')); ?>">
	<link rel="stylesheet" href="<?php echo e(asset('css/fontawesome.min.css')); ?>">
	<link rel="stylesheet" href="<?php echo e(asset('css/owl.carousel.min.css')); ?>">
	<link rel="stylesheet" href="<?php echo e(asset('css/owl.theme.default.min.css')); ?>">
	<link rel="stylesheet" href="<?php echo e(asset('css/lightbox.css')); ?>">
	<link rel="stylesheet" href="<?php echo e(asset('css/style.css')); ?>">
</head>
<body class="application application-offset">
    <div class="sidebar-panel">
		<ul id="slide-out" class="collapsible side-nav">
			<li class="list-top">
				<a href="index.html"><i class="fas fa-home"></i>Home</a>
			</li>
			<li>
				<div class="collapsible-header">
					<i class="fas fa-building"></i>Properties<span><i class="fas fa-angle-right right"></i></span>
				</div>
				<div class="collapsible-body">
					<ul>
						<li><a href="properties.html">Properties</a></li>
						<li><a href="properties-details.html">Properties Details</a></li>
					</ul>
				</div>
			</li>
			<li>
				<div class="collapsible-header">
					<i class="fas fa-user"></i>Agent<span><i class="fas fa-angle-right right"></i></span>
				</div>
				<div class="collapsible-body">
					<ul>
						<li><a href="agent.html">Agent</a></li>
						<li><a href="agent-details.html">Agent Details</a></li>
					</ul>
				</div>
			</li>
			<li>
				<div class="collapsible-header">
					<i class="fas fa-rss"></i>Blog<span><i class="fas fa-angle-right right"></i></span>
				</div>
				<div class="collapsible-body">
					<ul>
						<li><a href="blog.html">Blog</a></li>
						<li><a href="blog-single.html">Blog Single</a></li>
					</ul>
				</div>
			</li>
			<li>
				<div class="collapsible-header">
					<i class="fas fa-user"></i>Account<span><i class="fas fa-angle-right right"></i></span>
				</div>
				<div class="collapsible-body">
					<ul>
						<li><a href="profile.html">Profile</a></li>
						<li><a href="login.html">Login</a></li>
						<li><a href="register.html">Register</a></li>
						<li><a href="forgot-password.html">Forgot Password</a></li>
						<li><a href="settings.html">Settings</a></li>
					</ul>
				</div>
			</li>
			<li>
				<div class="collapsible-header">
					<i class="fas fa-clone"></i>Pages<span><i class="fas fa-angle-right right"></i></span>
				</div>
				<div class="collapsible-body">
					<ul>
						<li><a href="about.html">About</a></li>
						<li><a href="features.html">Features</a></li>
						<li><a href="services.html">Services</a></li>
						<li><a href="gallery.html">Gallery</a></li>
						<li><a href="portfolio.html">Portfolio</a></li>
						<li><a href="coming-soon.html">Coming Soon</a></li>
						<li><a href="page-not-found.html">Page Not Found</a></li>
						<li><a href="faq.html">Faq</a></li>
						<li><a href="testimonial.html">Testimonial</a></li>
					</ul>
				</div>
			</li>
			<li><a href="coming-soon.html"><i class="fas fa-clock"></i>Coming Soon</a></li>
			<li><a href="faq.html"><i class="fas fa-question"></i>Faq</a></li>
			<li><a href="contact.html"><i class="fas fa-envelope"></i>Contact</a></li>
			<li><a href="login.html"><i class="fas fa-sign-in-alt"></i>Login</a></li>
			<li><a href="register.html"><i class="fas fa-user-plus"></i>Register</a></li>
			<li class="bg-specific"><a href="submit-properties.html"><i class="fas fa-folder-plus"></i>Submit Properties</a></li>
			<li><a href="index.html"><i class="fas fa-sign-out-alt"></i>Log Out</a></li>
		</ul>
	</div>


    <div class="profile-modal">
		<div class="container">
			<div class="wrappers">
				<div class="wrap-modal">
					<div id="modal" class="modal modal-service" style="z-index: 1003;">
						<div class="modal-content">
							<div class="m-header">
								<div class="icon-close">
									<a href="#!" class="modal-close"><i class="fas fa-times"></i></a>
								</div>
							</div>
							<div class="m-avatar">
								<div class="image">
									<img src="images/agent1.jpg" alt="">
								</div>
								<h4>Jonathan</h4>
								<span>Agent</span>
							</div>
							<div class="m-info">
								<ul>
									<li>Name <span>Jonathan Paulo</span></li>
									<li>Age <span>29</span></li>
									<li>Address <span>United States</span></li>
								</ul>
							</div>
							<div class="m-gallery">
								<div class="row">
									<div class="col s6">
										<a href="images/gallery1.jpg" data-lightbox="galleryProfile"><img src="images/gallery1.jpg" alt=""></a>
									</div>
									<div class="col s6">
										<a href="images/gallery2.jpg" data-lightbox="galleryProfile"><img src="images/gallery2.jpg" alt=""></a>
									</div>
								</div>
								<div class="row">
									<div class="col s6">
										<a href="images/gallery3.jpg" data-lightbox="galleryProfile"><img src="images/gallery3.jpg" alt=""></a>
									</div>
									<div class="col s6">
										<a href="images/gallery4.jpg" data-lightbox="galleryProfile"><img src="images/gallery4.jpg" alt=""></a>
									</div>
								</div>
								<div class="row">
									<div class="col s6">
										<a href="images/gallery5.jpg" data-lightbox="galleryProfile"><img src="images/gallery5.jpg" alt=""></a>
									</div>
									<div class="col s6">
										<a href="images/gallery6.jpg" data-lightbox="galleryProfile"><img src="images/gallery6.jpg" alt=""></a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>


   







<div class="container-fluid container-application">
    <div class="main-content position-relative">
        <div class="page-content">
            <div class="min-vh-100 py-5 d-flex align-items-center">
                <?php echo $__env->yieldContent('content'); ?>
            </div>
        </div>
    </div>
</div>

</body>
<script src="<?php echo e(asset('assets/js/jquery.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/materialize.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/lightbox.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/owl.carousel.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/main.js')); ?>"></script>

<script src="<?php echo e(asset('assets/js/site.core.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/site.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/demo.js')); ?>"></script>

<div class="hiddendiv common"></div>

<div id="lightboxOverlay" class="lightboxOverlay" style="display: none;"></div>

<div id="lightbox" class="lightbox" style="display: none;"><div class="lb-outerContainer"><div class="lb-container"><img class="lb-image" src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw=="><div class="lb-nav"><a class="lb-prev" href=""></a><a class="lb-next" href=""></a></div><div class="lb-loader"><a class="lb-cancel"></a></div></div></div><div class="lb-dataContainer"><div class="lb-data"><div class="lb-details"><span class="lb-caption"></span><span class="lb-number"></span></div><div class="lb-closeContainer"><a class="lb-close"></a></div></div></div></div>


</html>
<?php /**PATH /home/banqgego/public_html/nobsbackend/resources/views/layouts/auth.blade.php ENDPATH**/ ?>