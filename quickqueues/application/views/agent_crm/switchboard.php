<div class="container">
<div id="switchboard">
    <div class="row mb-2">
        <div class="col d-flex justify-content-center">
            <div class="btn-group btn-group">
                <button id="exts_all" class="btn btn-outline-info" @click="show_exts('all')"><?php echo lang('all'); ?></button>
                <button id="exts_free" class="btn btn-outline-info" @click="show_exts('free')"><?php echo lang('free'); ?></button>
                <button id="exts_on_call" class="btn btn-outline-info" @click="show_exts('on_call')"><?php echo lang('on_call'); ?></button>
                <button id="exts_busy" class="btn btn-outline-info" @click="show_exts('busy')"><?php echo lang('busy'); ?></button>
                <button id="exts_unavailable" class="btn btn-outline-info" @click="show_exts('unavailable')"><?php echo lang('unavailable'); ?></button>
            </div>
        </div>
    </div>
    <div class="row">
        <div v-for="state in extension_states" class="col-2 mb-2">
            <div v-bind:class="['card', state_class_map[state[1].Status]]">
                <center>
                    <h3>{{ state[1].Exten }}</h3>
                    {{ devices[state[1].Exten] }}
                </center>
            </div>
        </div>
    </div>
</div>


