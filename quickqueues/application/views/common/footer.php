            <footer id="footer">
                <div class="row">
                    <div class="col-lg-12">
                        <hr>
                        <ul class="list-unstyled">
                            <li class="float-lg-right"><a href="#top"><?php echo lang('back_to_top'); ?></a></li>
                            <li><a href="http://arcomm.ge/next/?page_id=415"><?php echo lang('blog'); ?></a></li>
                            <li><a href="http://arcomm.ge"><?php echo lang('company'); ?></a></li>
                            <li><a href="http://quickqueues.com/wiki"><?php echo lang('help'); ?></a></li>
                            <li><a href="http://quickqueues.com/about"><?php echo lang('about'); ?></a></li>
                        </ul>
                        <p><?php echo lang('developed_by'); ?> <a href="http://arcomm.ge"> ARComm</a>. <?php echo lang('copyright')." &copy 2016-".date('Y'); ?></p>
                        <p><?php echo lang('code_license'); ?><a href="<?php echo site_url('misc/license'); ?>"> ARComm Public License</a>.</p>
                        <p>Version v<?php echo get_qq_version(); ?></p>
                    </div>
                </div>

            </footer>
        </div> <!-- container -->

    </body>

<script type="text/javascript">
    <?php echo "var lang = ".json_encode($this->lang->language);  ?>
</script>

<?php if (isset($js_vars) and is_array($js_vars)) { ?>
<script type="text/javascript">
    <?php foreach ($js_vars as $var => $val) {
        echo "var ".$var." = '".$val."';\n";
    } ?>
</script>
<?php } ?>

<?php if (isset($js_include) and is_string($js_include)) { ?>
<script type="text/javascript" src="<?php echo $js_include; ?>"></script>
<?php } ?>
<?php if (isset($js_include) and is_array($js_include)) { foreach ($js_include as $js) { ?>
<script type="text/javascript" src="<?php echo $js; ?>"></script>
<?php } } ?>

<?php if ($this->session->flashdata('msg_style')) { ?>
<script type="text/javascript">send_notif("<?php echo $this->session->flashdata('msg_body'); ?>", "<?php echo $this->session->flashdata('msg_style'); ?>");</script>

<?php } ?>

<script type="text/javascript">
    $('#nav_'+window.location.href.split('/')[5].replace('#', '')).addClass('active');
</script>

</html>
