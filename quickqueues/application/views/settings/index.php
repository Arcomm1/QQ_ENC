<div class="settings-page-container">
    <form action="<?php echo site_url('settings/update_settings'); ?>" method="post">
        <div class="form-group row">
            <label for="overload" class="col-sm-2 col-form-label"><?php echo lang('overload'); ?></label>
            <div class="col-sm-10">
                <input type="number" name="overload" class="form-control" value="<?php echo isset($this->data->settings['call_overload']) ? $this->data->settings['call_overload'] : ''; ?>">
            </div>
        </div>
        <hr>
        <div class="row sms-row">
            <div class="col-sm-12 d-flex flex-row align-items-center justify-content-between">
                <p class="sms-label">SMS</p>
                <div class="toggle-icon" id="toggle-icon">&#9660;</div>
            </div>
        </div>
        <div id="sms-settings" class="custom-collapse">
            <section class="sms-settings bg-light p-3 mb-4">
                <div class="form-group row">
                    <label for="SMS" class="col-sm-2 col-form-label"><?php echo lang('sms_text'); ?></label>
                    <div class="col-sm-10">
                        <input type="text" name="sms_text" class="form-control" value="<?php echo isset($this->data->settings['sms_content']) ? $this->data->settings['sms_content'] : ''; ?>">
                    </div>
                </div>
                <hr>
                <div class="form-group row">
                    <label for="sms_key" class="col-sm-2 col-form-label"><?php echo lang('sms_key'); ?></label>
                    <div class="col-sm-10">
                        <input type="text" name="sms_key" class="form-control" value="<?php echo isset($this->data->settings['sms_token']) ? $this->data->settings['sms_token'] : ''; ?>">
                    </div>
                </div>
                <hr>
                <div class="form-group row">
                    <label for="sms_type" class="col-sm-2 col-form-label" ><?php echo lang('type'); ?></label>
                    <div class="col-sm-10">
                        <select name="sms_type" class="form-control">
                            <option value="1" <?php echo isset($this->data->settings['sms_type']) && $this->data->settings['sms_type'] == '1' ? "selected" : ""; ?>>Unanswered Call</option>
                            <option value="2" <?php echo isset($this->data->settings['sms_type']) && $this->data->settings['sms_type'] == '2' ? "selected" : ""; ?>>Complete Call</option>
                        </select>
                    </div>
                </div>
                <hr>
                <div class="form-group row">
                    <label for="sms_type" class="col-sm-2 col-form-label"><?php echo lang('queue'); ?></label>
                    <div class="col-sm-10">
                    <select name="queue_id" class="form-control" id="queue-select">
                        <option value="" <?php echo empty($this->data->settings['queue_id']) ? "selected" : ""; ?>>
                            <?php echo lang('select_queue'); ?>
                        </option>
                        <option value="all" <?php echo ($this->data->settings['queue_id'] == 'all') ? "selected" : ""; ?>>
                            <?php echo lang('all_queues'); ?>
                        </option>
                        <?php foreach ($this->data->settings['queues'] as $queue): ?>
                            <option value="<?php echo $queue['id']; ?>" <?php echo ($this->data->settings['queue_id'] == $queue['id']) ? "selected" : ""; ?>>
                                <?php echo $queue['display_name']; ?>
                            </option>
                        <?php endforeach; ?>
        </select>
        <!-- Hidden input to store the selected queue_id -->
        <input type="hidden" name="selected_queue_id" id="selected-queue-id" value="<?php echo $this->data->settings['queue_id']; ?>">
    </div>
</div>

            </section>
        </div>
        <hr>
        <input type="submit" value="Submit" class="btn btn-primary">
    </form>
</div>

<script>
    $(document).ready(function () 
    {
        $(".sms-row").click(function () 
        {
            $("#sms-settings").slideToggle();
            const toggleIcon = document.getElementById("toggle-icon");
            toggleIcon.classList.toggle("up-arrow");
        });

        $("#queue-select").change(function () {
            const selectedQueueId = $(this).val();
           
            // Update the hidden input value

           $("#selected-queue-id").val(selectedQueueId);
           console.log("selected queue id:", selectedQueueId);
          
        });
    });
</script>

