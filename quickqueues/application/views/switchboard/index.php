<div id="switchboard">
    <div class="card border-top-primary border-primary border-top-3">
        <div class="card-header">{{ lang['switchboard'] }}</div>
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
        <div class="card-body">
            <div class="extension-states">
                <div v-for="state in extension_states" :key="state[0]" class="extension-state">
                    <div class="card" :class="state_class_map[state[1].Status]">
                        <div class="card-body">
                            <h4 class="card-title">{{ state[0] }}</span></h4>
                            <p class="card-text">
                                <span v-if="state[1].Status === 2" class="alert-icon">!</span>
                                {{ devices[state[0]] || 'N/A' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


  
  <style>
#switchboard .card {
    width: 100%;
    margin: 5px auto;
}

.extension-states {
    display: grid;
    grid-template-columns: repeat(15, 1fr);
    row-gap: 0px;
    column-gap: 10px;
}

.extension-state {
    margin: 0;
    padding: 0;
    overflow: hidden;
   
}

.extension-state .card {
    min-width: 0;
    word-wrap: break-word;
    margin: 0; /* Remove margin from card */
    padding: 0; /* Remove padding from card */
}

.extension-state .card-body {
    text-align: center;
    padding: 5px;
}


.extension-state .card-text {
    font-size: 12px; /* Adjust the font size as needed */
    white-space: nowrap; /* Prevents the text from wrapping to the next line */
    overflow: hidden; /* Hides text that overflows the element's box */
    text-overflow: ellipsis; /* Adds an ellipsis (...) to truncated text */
    max-width: 90%; /* Set a maximum width to prevent overflowing the card */
    margin: 0 auto; /* Center the text within the card */
}

</style>
