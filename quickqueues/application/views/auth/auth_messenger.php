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

        <link href="<?php echo base_url('assets/v6/css/style.css'); ?>" rel="stylesheet">
    </head>
    <body>
        <div class="bg-light min-vh-100 d-flex flex-row align-items-center dark:bg-transparent">
            <div class="container" id="reset_password">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="card-group d-block d-md-flex row">
                            <div class="card col-md-7 p-4 mb-0">
                                <div class="card-body">
                                <?php
                                if(!$user_msg){
                                    $user_msg='Invalid';
                                }
                                    echo $user_msg;
                                ?>
                                <br/>
                                </div>
                            </div>
                            <div class="card col-md-5 text-white bg-primary py-5">
                                <div class="card-body text-center">
                                    <div>
                                        <h2>QuickQueues</h2>
                                        <p>{{ lang[help_topic] }} </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script type="text/javascript">
            var api_url = "<?php echo site_url('api'.'/');?>"
            var app_url = "<?php echo site_url();?>"
            <?php echo "var lang = ".json_encode($this->lang->language);  ?>

        </script>

        <script src="<?php echo base_url('assets/v6/vendors/@coreui/coreui-pro/js/coreui.bundle.min.js'); ?>"></script>
        <script src="<?php echo base_url('assets/v6/vendors/simplebar/js/simplebar.min.js'); ?>"></script>
        <script src="<?php echo base_url('assets/js/vue.min.js'); ?>"></script>
        <script src="<?php echo base_url('assets/js/axios.min.js'); ?>"></script>

        <script src="<?php echo base_url('assets/js/components/auth/reset_password.js'); ?>"></script>

        <script>
            if (document.body.classList.contains('dark-theme')) {
                var element = document.getElementById('btn-dark-theme');
                if (typeof(element) != 'undefined' && element != null) {
                    document.getElementById('btn-dark-theme').checked = true;
                }
            } else {
                var element = document.getElementById('btn-light-theme');
                if (typeof(element) != 'undefined' && element != null) {
                    document.getElementById('btn-light-theme').checked = true;
                }
            }

            function handleThemeChange(src) {
                var event = document.createEvent('Event');
                event.initEvent('themeChange', true, true);

                if (src.value === 'light') {
                    document.body.classList.remove('dark-theme');
                }
                if (src.value === 'dark') {
                    document.body.classList.add('dark-theme');
                }
                document.body.dispatchEvent(event);
            }
        </script>
    </body>
</html>
