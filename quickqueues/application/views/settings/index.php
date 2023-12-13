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
                    
                <?php foreach ($this->data->settings['queues'] as $queue): ?>
                    <div style="display:flex; width:100%; justify-content:space-between;">
                        <div class="title"><?php echo $queue['display_name']; ?></div>
                        <div class="value">
                            <input type="checkbox"
                                    value="<?php echo $queue['id']; ?>" 
                                    onchange="updateQueueID(event,<?php echo $queue['id']; ?>)" 
                                    name="selected_queues[]"
                                    <?php
           $selectedQueueIds = explode(',', $this->data->settings['queue_id']);
           echo in_array($queue['id'], $selectedQueueIds) ? 'checked' : '';
       ?>>
                        </div>
                    </div>
                <?php endforeach; ?>
  
            <!-- Hidden input to store the selected queue_id -->
            <input type="hidden" name="selected_queue_id" id="selected-queue-id" value="<?php echo implode(',', (array)$this->data->settings['queue_id']); ?>">
                </div>
            </div>

            </section>
        </div>
        <hr>
        <input type="submit" value="Submit" class="btn btn-primary">
    </form>
</div>

<script>
function updateQueueID(event, id) {
    let checkboxes = $('input[name="selected_queues[]"]:checked');
    let arr = checkboxes.map(function () {
        return this.value;
    }).get();

    let checked = event.target.checked;
    let index = arr.indexOf(id.toString());

    if (checked) {
        if (index === -1) {
            arr.push(id.toString());
        }
    } else {
        if (index > -1) {
            arr.splice(index, 1);
        }
    }

    // Update the hidden input value
    $('#selected-queue-id').val(arr.join(','));
    console.log("Selected queues:", arr);
}

$(document).ready(function () {
    $(".sms-row").click(function () {
        $("#sms-settings").slideToggle();
        const toggleIcon = document.getElementById("toggle-icon");
        toggleIcon.classList.toggle("up-arrow");
    });

    $("#queue-select").change(function () {
        const selectedQueueIdArr = $(this).val();

        // Update the hidden input value
        $("#selected-queue-id").val(selectedQueueIdArr);
        console.log("selected queue id:", selectedQueueIdArr);

        // Update the initial checked state
        $('input[name="selected_queues[]"]').each(function () {
            const queueId = $(this).val();
            $(this).prop('checked', selectedQueueIdArr.includes(queueId));
        });
    });
});

</script>

