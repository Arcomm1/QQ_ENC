<div id="monitoring" class="container-fluid">
    <div class="row mb-2">
        <div class="col">
            <div class="card border-primary">
                <div class="row">
                    <div id="answered" class="col" style="height:15vw"></div>
                    <div id="unanswered" class="col" style="height:15vw"></div>
                    <div id="outgoing" class="col" style="height:15vw"></div>
                    <div id="waiting" class="col" style="height:15vw"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-7 pr-1">
            <div class="card border-info">
                <div class="row">
                    <div class="col-3 pr-0">
                        <div class="card text-white bg-danger ml-1 mb-3 mr-1 mt-1">
                            <div class="card-header pt-0 pb-0 pl-0 pr-0">
                                <center>
                                    <p class="pb-0 mb-0"><small><?php echo lang('inactive'); ?></small></p>
                                    <p class="pb-0 mb-0" style="font-size:3.8vw">{{ agents_busy }}</p>
                                </center>
                            </div>
                        </div>
                    </div>
                    <div class="col-3 pl-0 pr-0">
                        <div class="card text-white bg-success mb-3 mr-1 mt-1">
                            <div class="card-header pt-0 pb-0 pl-0 pr-0">
                                <center>
                                    <p class="pb-0 mb-0"><small><?php echo lang('free'); ?></small></p>
                                    <p class="pb-0 mb-0" style="font-size:3.8vw">{{ agents_free }}</p>
                                </center>
                            </div>
                        </div>
                    </div>
                    <div class="col-3 pl-0 pr-0">
                        <div class="card text-white bg-info mb-3 mr-1 mt-1">
                            <div class="card-header pt-0 pb-0 pl-0 pr-0">
                                <center>
                                    <p class="pb-0 mb-0"><small><?php echo lang('on_call'); ?></small></p>
                                    <p class="pb-0 mb-0" style="font-size:3.8vw">{{ agents_on_call }}</p>
                                </center>
                            </div>
                        </div>
                    </div>
                    <div class="col-3 pl-0">
                        <div class="card text-white bg-warning mb-3 mr-1 mt-1">
                            <div class="card-header pt-0 pb-0 pl-0 pr-0">
                                <center>
                                    <p class="pb-0 mb-0"><small><?php echo lang('on_call'); ?></small></p>
                                    <p v-if="Object.keys(queue_realtime).length > 0 && Object.keys(queue_realtime.callers).length > 0" class="pb-0 mb-0" style="font-size:3.8vw">{{ sec_to_min(queue_realtime.callers[0].Wait) }}</p>
                                    <p v-else class="pb-0 mb-0" style="font-size:3.8vw">00:00</p>
                                </center>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <table id="agents_overview" class="table table-hover table-striped table-sm">
                            <thead>
                            <tr>
                                <th scope="col"><?php echo lang('agent'); ?><i class="fas fa-user ml-2 text-danger"></i></th>
                                <th scope="col"></th>
                                <th scope="col"><?php echo lang('calls'); ?><i class="far fa-check-circle ml-2 text-success"></i></th>
                                <th scope="col"><?php echo lang('calls'); ?><i class="fas fa-minus-circle ml-2 text-danger"></i></th>
                                <th scope="col"><?php echo lang('calls'); ?><i class="fas fa-chevron-up ml-2 text-info"></i></th>
                                <th scope="col"><?php echo lang('current_call'); ?><i class="fas fa-user-clock ml-2 text-success"></i></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="a in agent_extens">
                                <td v-if="a">
                                    <a v-bind:href="app_url+'/agents/stats/'+a[1].data.id" class="text-info">{{a[1].data.extension+' - '+a[1].data.display_name}}</a>
                                </td>
                                <td v-if="a">
                                    <span v-bind:class="'badge mr-2 badge-pill badge-'+agent_status_colors(a[1].realtime.Status)">
                                        &nbsp
                                    </span>
                                </td>
                                <td v-if="a">
                                    {{a[1].calls_answered}}
                                </td>
                                <td v-if="a">
                                    {{a[1].calls_missed}}
                                </td>
                                <td v-if="a">
                                    {{a[1].calls_outgoing}}
                                </td>
                                <td v-if="a">
                                    <span v-if="a[1].realtime.Status == 1 && a[1].current_calls[0]">
                                        <span>
                                            <span v-if="!dids.includes(a[1].current_calls[0].ConnectedLineNum)">
                                                <span v-if="a[1].current_calls[0].Application.includes('AppQueue')">
                                                    <i class="fas fa-arrow-down mr-1 text-info"></i>
                                                </span>
                                                <span v-else>
                                                    <i class="fas fa-arrow-up mr-1 text-success"></i>
                                                </span>
                                                {{a[1].current_calls[0].ConnectedLineNum+' ('+sec_to_time(a[1].current_calls[0].Seconds)+')'}}
                                            </span>
                                        </span>
                                        <span v-else>
                                            <span>
                                                <span v-if="a[1].current_calls[0].Application.includes('AppQueue')">
                                                    <i class="fas fa-arrow-down mr-1 text-info"></i>
                                                </span>
                                                <span v-else>
                                                    <i class="fas fa-arrow-up mr-1 text-success"></i>
                                                </span>
                                                {{a[1].current_calls[0].CallerIDNum+' ('+sec_to_time(a[1].current_calls[0].Seconds)+')'}}
                                            </span>
                                        </span>
                                    </span>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col pl-1">
            <div class="card border-primary">
                <div class="card-header">{{ lang['callers'] }}</div>
                <div class="card-body">
                    <table class="table table-sm table-striped">
                        <tr>
                            <th scope="col"><?php echo lang('p_position'); ?></th>
                            <th scope="col"><?php echo lang('src'); ?></th>
                            <th scope="col"><?php echo lang('hold_time'); ?></th>
                        </tr>
                        <tr v-for='caller in queue_realtime.callers'>
                            <td>{{ caller.Position }}</td>
                            <td>{{ caller.CallerIDNum }}</td>
                            <td>{{ sec_to_time(caller.Wait) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

