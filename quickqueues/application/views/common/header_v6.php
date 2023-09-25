<!-- Updated 19.02.2023 -->
<!DOCTYPE html>
<html>
    <head>
        <base href="./">
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
        <meta name="author" content="ARComm, LTD">
        <title><?php echo $page_title; ?> | QuickQueues</title>
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage" content="<?php echo base_url('assets/v6/assets/favicon/ms-icon-144x144.png'); ?>">
        <meta name="theme-color" content="#ffffff">

        <link rel="stylesheet" href="<?php echo base_url('assets/v6/vendors/simplebar/css/simplebar.css'); ?>">
        <link rel="stylesheet" href="<?php echo base_url('assets/v6/css/vendors/simplebar.css'); ?>">
        <link rel="stylesheet" href="<?php echo base_url('assets/css/jquery.datetimepicker.min.css'); ?>">
        <script type="text/javascript" src="<?php echo base_url('assets/js/Chart.bundle.min.js'); ?>"></script>

        <link rel="stylesheet" href="<?php echo base_url('assets/v6/vendors/@coreui/chartjs/css/coreui-chartjs.css'); ?>">

        <link href="<?php echo base_url('assets/v6/css/style.css'); ?>" rel="stylesheet">
        <link href="<?php echo base_url('assets/v6/assets/icons/css/all.min.css'); ?>" rel="stylesheet">

        <script type="text/javascript" src="<?php echo base_url('assets/js/jquery.min.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo base_url('assets/js/bootstrap.bundle.min.js'); ?>"></script>
		<script type="text/javascript">
			//check the button which is clicked
			 var darkClick = localStorage.getItem('darkBtnClicked'),
				 normalClick =
				 localStorage.getItem('normalBtnClicked');

			 if (darkClick == "true") {
				 console.log('clicked on dark');
				 $("html, h3, a, body").addClass("dark-theme");
			 }
			 if (normalClick == "true") {
				 $("html, h3, a, body").removeClass("dark-theme");
			 }

			function handleThemeChange(src) {
				var event = document.createEvent('Event');
				event.initEvent('themeChange', true, true);

				if (src.value === 'dark') {
					//on click of the button add the class we need a nd set the cookies
					//Adding class to all the elements you need in just one line.
					$("html, h3, a, body").addClass("dark-theme");
					//setting cookies for the button click
					localStorage.setItem('darkBtnClicked', true);
					localStorage.setItem('normalBtnClicked', false);
				}

				 if (src.value === 'light') {
					$("html, h3, a, body").removeClass("dark-theme");
					//setting cookies for the button click
					localStorage.setItem('normalBtnClicked', true);
					localStorage.setItem('darkBtnClicked', false);
				 }

				 document.body.dispatchEvent(event);
			}

		</script>

    </head>
    <body>
        <div class="wrapper d-flex flex-column min-vh-100 bg-light dark:bg-transparent"> <!-- full content -->
        <!-- header -->
        <header class="header header-sticky-mb-4">
            <div class="container-fluid">
                <!-- navigation menu entries -->
                <ul class="header-nav d-none d-md-flex">
                    <li class="nav-item">
                        <a id="nav_start" class="nav-link" href="<?php echo site_url('start'); ?>">Quickqueues</a>
                    </li>
                    <?php if (isset($logged_in_user->associated_agent_id)) { ?>
                        <li class="nav-item" id="nav_workspace">
                            <a class="nav-link" href="<?php echo site_url('workspace'); ?>">
                                <?php echo lang('workspace'); ?>
                            </a>
                        </li>
                    <?php } ?>
                    <li class="nav-item">
                        <a id="nav_recordings" class="nav-link" href="<?php echo site_url('recordings'); ?>">
                            <?php echo lang('recordings'); ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a id="nav_queues" class="nav-link" href="<?php echo site_url('queues'); ?>">
                            <?php echo lang('queues'); ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a id="nav_agents" class="nav-link" href="<?php echo site_url('agents'); ?>">
                            <?php echo lang('agents'); ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a id="nav_monitoring" class="nav-link" href="<?php echo site_url('monitoring'); ?>">
                            <?php echo lang('monitoring'); ?>
                        </a>
                    </li>
                    <?php if (isset($inject_nav_item)) { ?>
                    <li class="nav-item">
                        <a id="<?php echo $inject_nav_item[0]; ?>" class="nav-link" href="<?php echo site_url($inject_nav_item[1]); ?>">
                            <?php echo lang($inject_nav_item[2]); ?>
                        </a>
                    </li>
                    <?php } ?>
                    <?php if (isset($logged_in_user) && $logged_in_user->role == 'admin') { ?>
                    <li class="nav-item dropdown d-flex align-items-center">
                        <a class="nav-link py-0" data-coreui-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                            <?php echo lang('manage'); ?>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end pt-0">
                            <a class="dropdown-item" href="<?php echo site_url('users'); ?>">
                                <?php echo lang('users'); ?>
                            </a>
                            <?php if ($config->app_campaigns == 'yes') { ?>
                            <a class="dropdown-item" href="<?php echo site_url('campaigns'); ?>">
                                <?php echo lang('campaigns'); ?>
                            </a>
                            <?php } ?>
                        </div>
                    </li>
                    <?php } ?>
                </ul>
                <!--End Of navigation menu entries -->

                <!-- dark mode switcher -->
                <nav class="header-nav ms-auto me-4">
                    <div class="btn-group" role="group" aria-label="Basic checkbox toggle button group">
                        <input class="btn-check" id="btn-light-theme" type="radio" name="theme-switch"
                               autocomplete="off" value="light" onchange="handleThemeChange(this)">
                        <label class="btn btn-primary" for="btn-light-theme">
                            <svg class="icon">
                                <use xlink:href="<?php echo base_url('assets/v6/vendors/@coreui/icons/svg/free.svg#cil-sun'); ?>"></use>
                            </svg>
                        </label>
                        <input class="btn-check" id="btn-dark-theme" type="radio" name="theme-switch"
                               autocomplete="off" value="dark" onchange="handleThemeChange(this)">
                        <label class="btn btn-primary" for="btn-dark-theme">
                            <svg class="icon">
                                <use xlink:href="<?php echo base_url('assets/v6/vendors/@coreui/icons/svg/free.svg#cil-moon'); ?>"></use>
                            </svg>
                        </label>
                    </div>
                </nav>
                <!-- End Of dark mode switcher -->

                <!-- user menu -->
                <ul class="header-nav me-4">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo site_url('Settings'); ?>">
                        <svg class="icon">
                            <use xlink:href="<?php echo base_url('assets/v6/vendors/@coreui/icons/svg/free.svg#cil-settings'); ?>"></use>
                        </svg>
                    </a>
                </li>
                    <li class="nav-item dropdown d-flex align-items-center">
                        <a class="nav-link py-0" data-coreui-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                            <div class="avatar avatar-md">
                                <svg class="icon">
                                    <use xlink:href="<?php echo base_url('assets/v6/vendors/@coreui/icons/svg/free.svg#cil-user'); ?>"></use>
                                </svg>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end pt-0">
                            <a class="dropdown-item" href="<?php echo site_url('auth/signout'); ?>">
                                <svg class="icon me-2">
                                    <use xlink:href="<?php echo base_url('assets/v6/vendors/@coreui/icons/svg/free.svg#cil-account-logout'); ?>"></use>
                                </svg> <?php echo lang('sign_out'); ?>
                            </a>
                        </div>
                    </li>
                </ul>
                <!-- user menu -->

            </div>
        </header>
        <!-- End Of header -->

        <!-- content -->
        <div class="body flex-grow-1 px-3 mb-4">


