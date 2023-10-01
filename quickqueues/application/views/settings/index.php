<div class="settings-page-container">
    <form action="<?php echo site_url('settings/update_settings'); ?>" method="post">
        <div class="form-group row">
            <label for="overload" class="col-sm-2 col-form-label"><?php echo lang('overload'); ?></label>
            <div class="col-sm-10">
                <input type="number" name="overload" class="form-control" value="<?php echo isset($this->data->settings['call_overload']) ? $this->data->settings['call_overload'] : ''; ?>">
            </div>
        </div>
        <hr>
        <div class="form-group row">
            <label for="SMS" class="col-sm-2 col-form-label"><?php echo lang('sms_text'); ?></label>
            <div class="col-sm-10">
                <input type="text" name="sms_text" class="form-control" value="<?php echo isset($this->data->settings['sms_content']) ? $this->data->settings['sms_content'] : ''; ?>">
            </div>
        </div>
        <hr>
        <input type="submit" value="Submit" class="btn btn-primary pull-right">
    </form>
</div>