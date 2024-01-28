<div id="switchboard">
  <div class="card border-top-primary border-primary border-top-3">
    <div class="card-header">{{ lang['switchboard'] }}</div>
    <div class="card-body">
      <div class="extension-states">
        <div v-for="state in extension_states" :key="state[0]" class="extension-state">
          <div class="card" :class="state_class_map[state[1].Status]">
            <div class="card-body">
              <h4 class="card-title">{{ state[0] }}</h4>
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
.extension-states {
  display: flex;
  flex-wrap: wrap;
  gap: 10px; /* Adjust as needed */
}

.extension-state {
  width: calc(33.33% - 10px); /* Three states per row with a gap of 10px */
  max-width: 300px; /* Adjust the maximum width as needed */
}

/* Add your existing state_class_map styles here */
</style>
