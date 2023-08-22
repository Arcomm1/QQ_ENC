<div id="switchboard">
    <div class="card border-primary">
        <div class="card-header">{{ lang['switchboard'] }}</div>
        <div class="card-body">
            <div class="row">
                <div v-for="state in extension_states" class="col-2 mb-2">
                    <div v-bind:class="['card', state_class_map[state[1].Status]]">
                        <center>
                            <h3>{{ state[0] }} <span v-if="state[1].Status == 2">!</span></h3>
                            {{ devices[state[0]] }}
                        </center>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


