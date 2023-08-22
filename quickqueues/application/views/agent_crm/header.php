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
        <link rel="stylesheet" href="<?php echo base_url('assets/css/jplayer-flat/jplayer-flat-audio-theme.css'); ?>">

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

        <script type="text/javascript" src="<?php echo base_url('assets/js/raphael-2.1.4.min.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo base_url('assets/js/justgage.js'); ?>"></script>

        <script type="text/javascript" src="<?php echo base_url('assets/js/components/common.js'); ?>"></script>

        <script type="text/javascript">
            var api_url = "<?php echo site_url('api'.'/');?>"
            var app_url = "<?php echo site_url();?>"
        </script>
    </head>

    <body>
        <div id="agent_crm">
        <div class="navbar navbar-expand-lg fixed-top navbar-dark bg-primary">
            <div class="container-fluid">
                <a class="navbar-brand" href="<?php echo site_url('start'); ?>">
                    <img src="<?php echo base_url('assets/img/qq_brand_notext.svg'); ?>" width="30" height="30" alt="">
                    Quickqueues
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#qq_navbar" aria-controls="qq_navbar" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="qq_navbar">
                    <ul class="navbar-nav">
                        <li class="nav-item" id="nav_workspace">
                            <a class="nav-link" href="<?php echo site_url('agent_crm/workspace'); ?>">
                                <?php echo lang('workspace'); ?>
                                <span v-if="current_call[0] && realtime_status.Status == 1" class="badge badge-pill badge-danger">!</span>
                            </a>
                        </li>
                        <li class="nav-item" id="nav_recordings">
                            <a class="nav-link" href="<?php echo site_url('agent_crm/recordings'); ?>"><?php echo lang('recordings'); ?></a>
                        </li>
                        <?php if ($config->app_track_called_back_calls == 'yes') { ?>
                        <li class="nav-item" id="nav_callback_queue">
                            <a class="nav-link" href="<?php echo site_url('agent_crm/recordings?event_type=UNANSWERED&calls_without_service=yes'); ?>"><?php echo lang('callback_queue'); ?></a>
                        </li>
                        <?php } ?>
                        <?php if ($config->app_service_module == 'yes') { ?>
                        <li class="nav-item" id="nav_service">
                            <a class="nav-link" href="<?php echo site_url('agent_crm/service_stats'); ?>"><?php echo lang('service'); ?></a>
                        </li>
                        <?php } ?>
                        <?php if ($config->app_call_curators == 'yes') { ?>
                        <li class="nav-item" id="nav_todo">
                            <a class="nav-link" href="<?php echo site_url('agent_crm/todo'); ?>"><?php echo lang('todo'); ?></a>
                        </li>
                        <?php } ?>
                        <li class="nav-item" id="nav_stats">
                            <a class="nav-link" href="<?php echo site_url('agent_crm/stats'); ?>"><?php echo lang('stats'); ?></a>
                        </li>

                        <?php if ($config->app_enable_switchboard == 'yes') { ?>
                            <li class="nav-item" id="nav_switchboard">
                                <a class="nav-link" i href="<?php echo site_url('agent_crm/switchboard') ;?>"><?php echo lang('switchboard'); ?></a>
                            </li>
                        <?php } ?>
                        <?php if ($config->app_cdr_lookup == 'yes') { ?>
                            <li class="nav-item" id="nav_switchboard">
                                <a class="nav-link" href="#" data-toggle="modal" data-target="#cdr_lookup"><i class="fa fa-search"></i></a>
                            </li>
                        <?php } ?>
                        <li class="nav-item" id="nav_overview">
                            <a class="nav-link" href="<?php echo site_url('agent_crm/overview'); ?>"><?php echo lang('overview'); ?></a>
                        </li>
                        <?php if (count($user_queues) > 1) { ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" id="nav_queue_dashboards"><i class="fa fa-th-list"></i><span class="caret"></span></a>
                            <div class="dropdown-menu" aria-labelledby="nav_queue_dashboards">
                                <?php foreach ($user_queues as $q) { ?>
                                    <a class="dropdown-item" href="<?php echo site_url('agent_crm/overview/'.$q->id); ?>"><?php echo $q->display_name; ?></a>
                                <?php } ?>
                            </div>
                        </li>
                        <?php } ?>
                    </ul>

                    <ul class="nav navbar-nav ml-auto dropdown">
                        <li class="nav-item">
                            <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" id="agent_menu">
                                <span v-bind:class="'badge mr-2 badge-pill badge-'+agent_status_colors(realtime_status.Status)">
                                    &nbsp
                                </span>
                                {{ agent.display_name }}
                                <span class="caret"></span>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="agent_menu">
                                <?php if ($config->app_track_agent_pause_time == 'yes') { ?>
                                <form>
                                    <ul class="list-group list-group-flush mb-2">
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>
                                                <?php echo lang('pause'); ?>
                                            </span>
                                            <span>
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input" id="agent_pause_toggle" checked="">
                                                    <label class="custom-control-label" for="agent_pause_toggle"></label>
                                                </div>
                                            </span>
                                        </li>

                                        <?php if ($config->app_track_agent_session_time == 'yes') { ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>
                                                <?php echo lang('session'); ?>
                                            </span>
                                            <span>
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input" id="agent_session_toggle" checked="">
                                                    <label class="custom-control-label" for="agent_session_toggle"></label>
                                                </div>
                                            </span>
                                        </li>
                                        <?php } ?>
                                    </ul>
                                </form>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#change_password_modal"><?php echo lang('change_password'); ?></a>
                                <div class="dropdown-divider"></div>
                                <?php } ?>
                                <a class="dropdown-item" href="<?php echo site_url('auth/signout'); ?>"><?php echo lang('sign_out'); ?></a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>


<div id="change_password_modal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo lang('change_password'); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <v-change-password></v-change-password>
        </div>
    </div>
</div>


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
