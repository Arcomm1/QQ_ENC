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
            <div class="container" id="signin">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="card-group d-block d-md-flex row">
                            <div class="card col-md-7 p-4 mb-0">
                                <?php echo form_open(); ?>
                                <div class="card-body">
                                    <p class="text-medium-emphasis"><?php echo lang('sign_in'); ?></p>
                                    <?php if ($auth_errors) { ?>
                                    <div class="alert alert-danger">
                                        <?php echo $auth_errors; ?>
                                    </div>
                                    <?php } ?>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text">
                                            <svg class="icon">
                                                <use xlink:href="<?php echo base_url('assets/v6/vendors/@coreui/icons/svg/free.svg#cil-user'); ?>"></use>
                                            </svg>
                                        </span>
                                        <input v-model="username" name="username" id="username" class="form-control" type="text" placeholder="<?php echo lang('username'); ?>">
                                    </div>
                                    <div class="input-group mb-4">
                                        <span class="input-group-text">
                                            <svg class="icon">
                                                <use xlink:href="<?php echo base_url('assets/v6/vendors/@coreui/icons/svg/free.svg#cil-lock-locked'); ?>"></use>
                                            </svg>
                                        </span>
                                        <input v-model="password" name="password" id="password" class="form-control" type="password" placeholder="<?php echo lang('password'); ?>">
                                    </div>
                                    <div class="input-group mb-4">
                                        <span class="input-group-text">
                                            <svg class="icon">
                                                <use xlink:href="<?php echo base_url('assets/v6/vendors/@coreui/icons/svg/free.svg#cil-chat-bubble'); ?>"></use>
                                            </svg>
                                        </span>
                                        <select id="app_language" name="app_language" class="form-control">
                                            <?php foreach (get_languages() as $lang) {
                                                if ($lang == 'georgian') {
                                                    echo "<option selected value='".$lang."'>".lang($lang)."</option>";
                                                } else {
                                                    echo "<option value='".$lang."'>".lang($lang)."</option>";
                                                }
                                            } ?>
                                        </select>
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            <button disabled id="signin_submit" class="btn btn-primary px-4" type="submit"><?php echo lang('sign_in'); ?></button>
                                        </div>
                                        <div class="col-6 text-end">
                                            <a href="<?php echo base_url('index.php/auth/reset_password') ?>">
                                                <button class="btn btn-link px-0" type="button"><?php echo lang('forgot_password'); ?></button>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <?php echo form_close(); ?>
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

        <script src="<?php echo base_url('assets/js/components/auth/signin.js'); ?>"></script>

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
