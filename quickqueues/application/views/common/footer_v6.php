        </div>
        <!-- Updated At 19.02.2023 -->
        <!-- content -->
        <footer class="footer"> <!-- footer -->
            <div style="width: 50%; margin: 0 auto; text-align: center;">
                <a href="https://arcomm.ge">ARComm | Quickqueues</a> <?php echo qq_get_version(); ?> © 2016-<?php echo date('Y'); ?>
            </div>
            <!-- <div class="ms-auto">Powered by&nbsp;
                <a href="https://coreui.io/bootstrap/ui-components/">CoreUI PRO UI Components</a>
            </div> -->
        </footer> <!-- footer -->

        </div> <!-- full content -->
        <script type="text/javascript">
            var api_url = "<?php echo site_url('api'.'/');?>"
            var app_url = "<?php echo site_url();?>"
            <?php echo "var lang = ".json_encode($this->lang->language);  ?>
        </script>

        <?php if (isset($js_vars) and is_array($js_vars)) { ?>
        <script type="text/javascript">
            <?php foreach ($js_vars as $var => $val) {
                echo "var ".$var." = '".$val."';\n";
            } ?>
        </script>
        <?php } ?>

        <script src="<?php echo base_url('assets/js/jquery.min.js'); ?>"></script>

        <script src="<?php echo base_url('assets/v6/vendors/@coreui/coreui-pro/js/coreui.bundle.min.js'); ?>"></script>
        <script src="<?php echo base_url('assets/v6/vendors/@coreui/chartjs/js/coreui-chartjs.js'); ?>"></script>

        <script src="<?php echo base_url('assets/v6/vendors/simplebar/js/simplebar.min.js'); ?>"></script>
        <script src="<?php echo base_url('assets/js/vue.min.js'); ?>"></script>
        <script src="<?php echo base_url('assets/js/axios.min.js'); ?>"></script>
        <script src="<?php echo base_url('assets/js/jquery.datetimepicker.full.min.js'); ?>"></script>
        <script src="<?php echo base_url('assets/js/moment-locales.min.js'); ?>"></script>

        <script src="<?php echo base_url('assets/js/components/common.js'); ?>"></script>


        <?php if (isset($js_include) and is_string($js_include)) { ?>
        <script type="text/javascript" src="<?php echo $js_include; ?>"></script>
        <?php } ?>
    </body>
</html>
