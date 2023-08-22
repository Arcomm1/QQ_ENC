<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title><?php echo $page_title; ?> | Quickqueues</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />

        <link rel="icon" href="<?php echo base_url('assets/img/favicon.ico'); ?>" type="image/png">

        <link rel="stylesheet" href="<?php echo base_url('assets/css/bootstrap.min.css'); ?>" media="screen">
        <link rel="stylesheet" href="<?php echo base_url('assets/css/jquery-ui.min.css'); ?>">
        <link rel="stylesheet" href="<?php echo base_url('assets/css/custom.css'); ?>">
        <link rel="stylesheet" href="<?php echo base_url('assets/css/font-awesome-all.min.css'); ?>">
        <link rel="stylesheet" href="<?php echo base_url('assets/css/datatables.min.css'); ?>">
        <link rel="stylesheet" href="<?php echo base_url('assets/css/jquery.datetimepicker.min.css'); ?>">
        <!-- <link rel="stylesheet" href="<?php echo base_url('assets/css/blue.monday/css/jplayer.blue.monday.min.css'); ?>"> -->
        <link rel="stylesheet" href="<?php echo base_url('assets/css/jplayer-flat/jplayer-flat-audio-theme.css'); ?>">
        <link rel="stylesheet" href="<?php echo base_url('assets/css/introjs.min.css'); ?>">
        <link rel="stylesheet" href="<?php echo base_url('assets/css/call_subjects.css'); ?>">

        <script type="text/javascript" src="<?php echo base_url('assets/js/jquery.min.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo base_url('assets/js/jquery-ui.min.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo base_url('assets/js/bootstrap.bundle.min.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo base_url('assets/js/datatables.min.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo base_url('assets/js/moment-locales.min.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo base_url('assets/js/jquery.datetimepicker.full.min.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo base_url('assets/js/Chart.bundle.min.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo base_url('assets/js/vue.min.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo base_url('assets/js/axios.min.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo base_url('assets/js/bootstrap-notify.min.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo base_url('assets/js/jquery.jplayer.min.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo base_url('assets/js/intro.min.js'); ?>"></script>

        <script type="text/javascript" src="<?php echo base_url('assets/js/raphael-2.1.4.min.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo base_url('assets/js/justgage.js'); ?>"></script>

        <script type="text/javascript" src="<?php echo base_url('assets/js/components/common.js'); ?>"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

        <script type="text/javascript">
            var api_url = "<?php echo site_url('api'.'/');?>"
            var app_url = "<?php echo site_url();?>"
        </script>
    </head>

    <body id="body">

        <div class="navbar navbar-expand-lg fixed-top navbar-dark bg-primary">
            <div class="container" data-step="1" data-intro="<?php echo lang('intro_nav'); ?>"  data-position='left'>
                <a class="navbar-brand" href="<?php echo site_url('start'); ?>">
                    <img src="<?php echo base_url('assets/img/qq_brand_notext.svg'); ?>" width="30" height="30" alt="">
                    Quickqueues
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#qq_navbar" aria-controls="qq_navbar" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="qq_navbar">
                    <ul class="navbar-nav">

                        <?php if ($logged_in_user->associated_agent_id) { ?>
                            <li class="nav-item" id="nav_workspace">
                                <a class="nav-link" href="<?php echo site_url('workspace'); ?>"><?php echo lang('workspace'); ?></a>
                            </li>
                        <?php } ?>
                        <li class="nav-item" id="nav_recordings">
                            <a class="nav-link" href="<?php echo site_url('recordings'); ?>"><?php echo lang('recordings'); ?></a>
                        </li>
                        <?php if ($config->app_service_module == 'yes') { ?>
                        <li class="nav-item" id="nav_service">
                            <a class="nav-link" href="<?php echo site_url('service_stats'); ?>"><?php echo lang('service'); ?></a>
                        </li>
                        <?php } ?>
                        <li class="nav-item" id="nav_queues">
                            <a class="nav-link" href="<?php echo site_url('queues'); ?>" id="nav_queues"><?php echo lang('queues'); ?></a>
                        </li>
                        <li class="nav-item" id="nav_agents">
                            <a class="nav-link" href="<?php echo site_url('agents') ;?>"><?php echo lang('agents'); ?></a>
                        </li>
                        <?php if ($config->app_ticket_module == 'yes') { ?>
                            <li class="nav-item" id="nav_tickets">
                                <a class="nav-link" href="<?php echo site_url('tickets') ;?>"><?php echo lang('tickets'); ?></a>
                            </li>
                        <?php } ?>
                        <li class="nav-item" id="nav_monitoring">
                            <a class="nav-link" href="<?php echo site_url('monitoring') ;?>"><?php echo lang('monitoring'); ?></a>
                        </li>
                        <?php if ($config->app_enable_switchboard == 'yes') { ?>
                            <li class="nav-item" id="nav_switchboard">
                                <a class="nav-link" i href="<?php echo site_url('switchboard') ;?>"><?php echo lang('switchboard'); ?></a>
                            </li>
                        <?php } ?>
                        <?php if ($config->app_cdr_lookup == 'yes') { ?>
                            <li class="nav-item" id="nav_switchboard">
                                <a class="nav-link" href="#" data-toggle="modal" data-target="#cdr_lookup"><i class="fa fa-search"></i></a>
                            </li>
                        <?php } ?>
                        <?php if ($config->app_news_module == 'yes') { ?>
                            <li class="nav-item" id="nav_news">
                                <a class="nav-link" i href="<?php echo site_url('news') ;?>"><?php echo lang('news'); ?></a>
                            </li>
                        <?php } ?>
                        <?php if ($logged_in_user->role == 'admin') { ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" id="nav_manage"><?php echo lang('manage'); ?><span class="caret"></span></a>
                            <div class="dropdown-menu" aria-labelledby="nav_manage">
                                <a class="dropdown-item" href="<?php echo site_url('config/profile'); ?>"><?php echo lang('profile'); ?></a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="<?php echo site_url('users'); ?>"><?php echo lang('users'); ?></a>
                                <?php if ($config->app_call_categories == 'yes') { ?>
                                <a class="dropdown-item" href="<?php echo site_url('config/call_categories'); ?>"><?php echo lang('call_categories'); ?></a>
                                <?php } ?>
                                <?php if ($config->app_call_tags == 'yes') { ?>
                                <a class="dropdown-item" href="<?php echo site_url('config/call_tags'); ?>"><?php echo lang('call_tags'); ?></a>
                                <?php } ?>
                                <a class="dropdown-item" href="<?php echo site_url('broadcast_notifications'); ?>"><?php echo lang('broadcast_notifs'); ?></a>
                                <?php if ($config->app_ticket_module == 'yes') { ?>
                                <a class="dropdown-item" href="<?php echo site_url('config/ticket_departments'); ?>"><?php echo lang('tickets'); ?></a>
                                <?php } ?>
                                <a class="dropdown-item" href="<?php echo site_url('call_subjects'); ?>"><?php echo lang('manage_subjects'); ?></a>
                            </div>
                        </li>
                        <?php } ?>
                    </ul>

                    <ul class="nav navbar-nav ml-auto">
                        <li class="nav-item">
                            <?php if ($config->app_notifications == 'yes') { ?>
                                <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" id="notification_menu">
                                    <?php if (count($notifications) > 0) { ?>
                                        <i class="fa fa-bell text-danger"></i>
                                    <?php } else { ?>
                                        <i class="fa fa-bell"></i>
                                    <?php } ?>

                                    <span class="caret"></span>
                                </a>
                            <?php } ?>
                            <div class="dropdown-menu" aria-labelledby="notification_menu">
                                <?php foreach ($notifications as $n) { ?>
                                    <a class="dropdown-item" href="<?php echo $n->url; ?>"><?php echo lang($n->content); ?></a>
                                <?php } ?>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="javascript:void(0);" onclick="javascript:introJs().start();"><i class="fa fa-question-circle"></i> </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo site_url('auth/signout'); ?>"><?php echo lang('sign_out'); ?></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="container">

<?php if ($config->app_cdr_lookup == 'yes') { ?>
<div class="modal" id="cdr_lookup" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo lang('cdr_lookup'); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row mb-2">
                    <div class="col">
                        <div class="input-group mb-3">
                            <input type="text" id="cdr_lookup_number" name="cdr_lookup_number" class="form-control" placeholder="<?php echo lang('number'); ?>">
                            <select id="cdr_lookup_hour" name="cdr_lookup_hour" class="form-control">
                                <option value="8">8 <?php echo lang('hour'); ?></option>
                                <option value="12">12 <?php echo lang('hour'); ?></option>
                                <option value="24">24 <?php echo lang('hour'); ?></option>
                                <option value="48">48 <?php echo lang('hour'); ?></option>
                            </select>
                            <div class="input-group-append">
                                <button onClick="cdr_lookup.perform_cdr_lookup()" id="cdr_lookup_search" class="btn btn-primary"><?php echo lang('search'); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div id="cdr_lookup_result"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><?php echo lang('close'); ?></button>
            </div>
        </div>
    </div>
</div>
<?php } ?>
