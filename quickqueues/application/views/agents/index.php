<div class="container-lg mt-3" id="agent_overview">
    <div class="row">
        <div class="col">
            <div class="card border-top-primary border-primary border-top-3">
                <div class="card-body">
                    <h4 class="card-title mb-3"><?php echo lang('agents'); ?></h4>
                    <div class="row mb-2">
                        <div class="col-3 col-lg-3">
                            <div class="card overflow-hidden">
                                <div class="card-body p-0 d-flex align-items-center">
                                    <div class="bg-success text-white text-strong py-4 px-5 me-3">
                                        <svg class="icon icon-xxl">
                                            <use xlink:href="<?php echo base_url('assets/v6/vendors/@coreui/icons/svg/free.svg#cil-user'); ?>"></use>
                                        </svg>
                                    </div>
                                    <div>
                                        <div v-cloak v-if="!agent_statuses_loading" class="fs-2 fw-semibold text-success">
                                            <span>
                                                {{ agents_free }}
                                            </span>
                                            <span v-else>
                                                <div class="spinner-border text-success" role="status">
                                                    <span class="visually-hidden"></span>
                                                </div>
                                            </span>
                                        </div>
                                        <div class="text-medium-emphasis text-uppercase fw-semibold small"><?php echo lang('free'); ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-3 col-lg-3">
                            <div class="card overflow-hidden">
                                <div class="card-body p-0 d-flex align-items-center">
                                    <div class="bg-info text-white text-strong py-4 px-5 me-3">
                                        <svg class="icon icon-xxl">
                                            <use xlink:href="<?php echo base_url('assets/v6/vendors/@coreui/icons/svg/free.svg#cil-user'); ?>"></use>
                                        </svg>
                                    </div>
                                    <div>
                                        <div v-cloak v-if="!agent_statuses_loading" class="fs-2 fw-semibold text-info">
                                            <span>
                                                {{ agents_on_call }}
                                            </span>
                                            <span v-else>
                                                <div class="spinner-border text-info" role="status">
                                                    <span class="visually-hidden"></span>
                                                </div>
                                            </span>
                                        </div>
                                        <div class="text-medium-emphasis text-uppercase fw-semibold small"><?php echo lang('on_call'); ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-3 col-lg-3">
                            <div class="card overflow-hidden">
                                <div class="card-body p-0 d-flex align-items-center">
                                    <div class="bg-secondary text-white text-strong py-4 px-5 me-3">
                                        <svg class="icon icon-xxl">
                                            <use xlink:href="<?php echo base_url('assets/v6/vendors/@coreui/icons/svg/free.svg#cil-user'); ?>"></use>
                                        </svg>
                                    </div>
                                    <div>
                                        <div v-cloak v-if="!agent_statuses_loading" class="fs-2 fw-semibold text-secondary">
                                            <span>
                                                {{ agents_unavailable }}
                                            </span>
                                            <span v-else>
                                                <div class="spinner-border text-secondary" role="status">
                                                    <span class="visually-hidden"></span>
                                                </div>
                                            </span>
                                        </div>
                                        <div class="text-medium-emphasis text-uppercase fw-semibold small"><?php echo lang('unavailable'); ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-3 col-lg-3">
                            <div class="card overflow-hidden">
                                <div class="card-body p-0 d-flex align-items-center">
                                    <div class="bg-danger text-white text-strong py-4 px-5 me-3">
                                        <svg class="icon icon-xxl">
                                            <use xlink:href="<?php echo base_url('assets/v6/vendors/@coreui/icons/svg/free.svg#cil-user'); ?>"></use>
                                        </svg>
                                    </div>
                                    <div>
                                        <div v-cloak v-if="!agent_statuses_loading" class="fs-2 fw-semibold text-danger">
                                            <span>
                                                {{ agents_busy }}
                                            </span>
                                            <span v-else>
                                                <div class="spinner-border text-danger" role="status">
                                                    <span class="visually-hidden"></span>
                                                </div>
                                            </span>
                                        </div>
                                        <div class="text-medium-emphasis text-uppercase fw-semibold small"><?php echo lang('busy'); ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Ordering -->
                    <table class="table agents_table">
                        <thead class="table-light fw-semibold">
                            <tr>
                                <th scope="col"><?php echo lang('agent'); ?></th>
                                <th scope="col"><?php echo lang('calls_answered'); ?></th>
                                <th scope="col"><?php echo lang('incoming_talk_time_sum'); ?></th>
                                <th scope="col"><?php echo lang('ringnoanswer'); ?></th>
                                <th scope="col"><?php echo lang('calls_outgoing_answered'); ?></th>
                                <th scope="col"><?php echo lang('outgoing_talk_time_sum'); ?></th>
                                <th scope="col"><?php echo lang('calls_outgoing_failed'); ?></th>
                                <th scope="col"><?php echo lang('dnd'); ?></th>
                                <!-- <th scope="col"></th> -->
                                <th scope="col"><?php echo lang('actions'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                          <tr v-for="agent in agents" v-if="agent && agent.display_name">
                                <td v-bind:id="'agent_status_'+agent.agent_id"
                                    v-if="agent_statuses[agent.extension]">
                                    <div>
                                        <span>
                                            <i v-if="agent_statuses[agent.extension]" v-bind:class="'cil-phone mr-3 text-white bg-' + agent_statuses[agent.extension].status_color"></i>
                                            <i v-else class="cil-phone mr-3 text-dark"></i>
                                        </span>
                                        <a v-bind:href="'agents/stats/'+agent.agent_id" class="ml-3 link-dark">{{ agent.display_name }}</a>
                                        <span v-if="agent_current_calls[agent.extension]">
                                            <i v-bind:class="'cil-chevron-double-'+agent_current_calls[agent.extension].direction+' mr-3 text-primary'"></i>
                                            {{ agent_current_calls[agent.extension].second_party }}
                                        </span>
                                        <span v-else></span>
                                     </div>
                                    <div class="small text-medium-emphasis">
                                        <span>
                                            <span>{{ agent.extension }}</span>
                                        </span>
                                        {{ " | "+agent.last_call }}
                                    </div>
                                </td>
                                <td v-if="agent_statuses[agent.extension]">
                                    {{ agent.calls_answered }}
                                </td>
                                <td v-if="agent_statuses[agent.extension]">
                                    {{ sec_to_time(agent.incoming_total_calltime) }}
                                </td>
                                <td v-if="agent_statuses[agent.extension]">
                                    {{ agent.calls_missed }}
                                </td>
                                <td v-if="agent_statuses[agent.extension]">
                                    {{ agent.calls_outgoing_answered }}
                                </td>
                                <td v-if="agent_statuses[agent.extension]">
                                    {{ sec_to_time(agent.outgoing_total_calltime) }}
                                </td>
                                <td v-if="agent_statuses[agent.extension]">
                                    {{ agent.calls_outgoing_unanswered }}
                                </td>
                                <td v-if="agent_statuses[agent.extension]">
                                    {{ sec_to_time(agent.total_pausetime) }}
                                <td>
                                <!-- <td>
                                    <span v-if="agent.dnd_status_pushed == 'on'" style="color:red">
                                        {{ agent.dnd_status_pushed }} - {{ agent.dnd_subject_title_pushed }}
                                        <div>
                                            {{ agent.dnd_duration_pushed }}
                                        </div>
                                    </span>
                                    <span v-else>
                                        {{ agent.dnd_status_pushed}}
                                    </span>
                                </td> -->
                                <td>
                                    <div class="btn-group" role="group">
                                        <a class="btn btn-ghost-success" v-bind:href="'agents/dndperagent/'+agent.agent_id"><i class="cil-media-pause"></i></a>
                                        <a class="btn btn-ghost-info" v-bind:href="'agents/stats/'+agent.agent_id"><i class="cil-bar-chart"></i></a>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <!--End Of Ordering-->
                </div>
            </div>
        </div>
    </div>
</div>
