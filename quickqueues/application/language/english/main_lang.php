<?php
//For settings page
$lang['settings']                                                = "Settings";
$lang['overload']                                                = "Overload";
$lang['sms_text']                                                = "SMS Text";
$lang['sms_key']                                                 = "SMS Key";
$lang['all_queues']                                              = "All Queues";
//For start page
$lang['start_menu_calls_answered']                               = "Answered calls";
$lang['start_menu_sla_less_than_or_equal_to_10']                 = "SLA < 10 seconds"; 
$lang['start_menu_sla_greater_than_10_less_then_or_equal_to_20'] = "SLA > 10 and SLA < 20 seconds";
$lang['start_menu_sla_greater_than_20']                          = "SLA > 20 seconds";
$lang['start_menu_calls_unanswered']                             = "Unanswered calls";
$lang['callback_request']                                        = "Callback requests";
$lang['ata']                                                     = "ATA (average)";
$lang['answered_aoutcall']                                       = "Answered (outcall)";
$lang['incoming_talk_time_sum']                                  = "Incoming talk time (sum)";
$lang['incoming_talk_time_avg']                                  = "Incoming talk time (average)";
$lang['incoming_talk_time_max']                                  = "Incoming talk time (max)";
$lang['outgoing_talk_time_sum']                                  = "Outgoing talk time (sum)";
$lang['outgoing_talk_time_avg']                                  = "Outgoing talk time (average)";
$lang['outgoing_talk_time_max']                                  = "Outgoing talk time (max)";
$lang['start_menu_calls_offwork']                                = "Off work calls";
$lang['start_menu_calls_waiting']                                = "Calls waiting";
$lang['local_calls']                                             = "Local calls";

$lang['recordings']         = "Recordings";
$lang['queues']             = "Queues";
$lang['agents']             = "Agents";
$lang['manage']             = "Manage";
$lang['profile']            = "Profile";
$lang['users']              = "Users";
$lang['preferences']        = "Preferences";
$lang['realtime']           = "Realtime";
$lang['overall']             = "Overall";
$lang['stats']              = "Stats";
$lang['sign_out']           = "Log out";
$lang['sign_in']            = "Log in";
$lang['agent']              = "Agent";
$lang['current_call']       = "Current call";
$lang['last']               = "Last";
$lang['last_call']          = "Last call";
$lang['actions']            = "Actions";

$lang['calls_answered']     = "Answered calls";
$lang['calls_unanswered']   = "Unanswered calls";
$lang['calls_ongoing']      = "Ongoing calls";
$lang['calls_waiting']      = "Waiting calls";
$lang['calls_total']        = "Total calls";
$lang['calls_unique']       = "Unique calls";
$lang['calls_outgoing']     = "Outgoing calls";
$lang['calls']              = "Calls";
$lang['call']               = "Calls";

$lang['calls_unique_in']    = "Unique incoming calls";
$lang['calls_unique_users'] = "Unique Users";

$lang['callers']            = "Callers";

$lang['call_time']          = "Call time";
$lang['hold_time']          = "Hold time";
$lang['ring_time']          = "Ring time";

$lang['position']           = "Position at disconnect";
$lang['orig_position']      = "Starting position";

$lang['loading']            = "Loading";

$lang['incorrect_username'] = "Incorrect username";
$lang['incorrect_password'] = "Incorrect password";
$lang['username']           = "Username";
$lang['password']           = "Password";

$lang['system_overview']    = "System overview";

$lang['call_distrib_by_queue'] = "Call distribution by queue";
$lang['call_distrib_by_agent'] = "Call distribution by agent";

$lang['manage_config']      = "Manage application settings";
$lang['app_settings']       = "Application settings";
$lang['desc_app_settings']  = "Change system-wide settings and global parameteres";
$lang['queue_settings']     = "Global queue settings";
$lang['desc_queue_settings'] = "Manage global parameters affecting all queues in the system";
$lang['adv_settings']       = "Advanced settings";
$lang['desc_adv_settings']  = "Change advanced system parameters";

$lang['app_log_path']       = "Log path";
$lang['desc_app_log_path']  = "Absolute path where Quickqueues will log its activities";
$lang['app_last_parsed_event'] = "Last parsed event";
$lang['desc_app_last_parsed_event'] = "Timestamp of last parsed event. Only change this if you know what are you doing";
$lang['app_language']       = "Application language";
$lang['desc_app_language']  = "Interface language for Quickqueues. changing this will change language for all users";
$lang['app_track_ringnoanswer'] = "Collect missed calls for agents";
$lang['desc_app_track_ringnoanswer'] = "Settings this to any other value then 'No' will result in agents having 'missed calls' value. Settings this to 'More then 10' will collect only events where phone rang for more then 10 seconds, setting this to 'Unique' will only collect one event per call, meaning multiple RINGNOANSWER events for agent for same calls will be counted as 1.";
$lang['app_track_outgoing'] = "Collect outgoing calls";
$lang['desc_app_track_outgoing'] = "If selected yes, Quickqueues will try to collect all outgoing made by agents. They will be associated with agents primary queue";
$lang['ringnoanswer']            = "Agent-missed calls";

$lang['save']               = "Save";
$lang['restore']            = "Restore";
$lang['show']               = "Show";
$lang['no']                 = "No";
$lang['yes']                = "Yes";
$lang['unique']             = "Unique";
$lang['10sec']              = "Minimum 10 seconds";
$lang['10secunique']        = "Minimum 10 seconds and unique";
$lang['conf_update_ok']     = "Configuration updated successfully";
$lang['conf_update_fail']   = "Configuration could not be updated";

$lang['english']            = "English";
$lang['georgian']           = "ქართული";

$lang['start_date']         = "Start date";
$lang['end_date']           = "End date";
$lang['today']              = "Today";
$lang['yesterday']          = "Yesterday";
$lang['this_week']          = "This week";
$lang['this_month']         = "This month";
$lang['last_7_days']        = "Last 7 days";
$lang['last_14_days']       = "Last 14 days";
$lang['last_30_days']       = "Last 30 days";
$lang['refresh']            = "Refresh";

$lang['status']             = "Status";
$lang['calls_missed']       = "Missed calls";
$lang['call_distrib_by_hour']   = "Call distribution by hour";
$lang['call_distrib_by_day']    = "Call distribution by day";
$lang['not_implemented']    = "Not implemented";
$lang['call_distrib']       = "Call distribution";
$lang['call_distrib_by_time']   = "Call distribution by time";
$lang['hour']               = "Hour";
$lang['time']               = "Time";
$lang['day']                = "Day";
$lang['call_distrib_by_call_time']  = "Call distribution by call time";
$lang['call_distrib_by_hold_time']  = "Call distribution by hold time";
$lang['found']              = "Found";
$lang['src']                = "Source";
$lang['dst']                = "Destination";
$lang['duration']           = "Duration";
$lang['cause']              = "Cause";
$lang['date']               = "Date";
$lang['filter_recordings']  = "Filter recordings";
$lang['search']             = "Search";
$lang['reset']              = "Reset";
$lang['answered']           = "Answered";
$lang['unanswered']         = "Unanswered";
$lang['back_to_top']        = "Back to top";
$lang['about']              = "About";
$lang['help']               = "Help";
$lang['company']            = "Company";
$lang['blog']               = "Blog";
$lang['developed_by']       = "Developed by";
$lang['code_license']       = "Quickqueues is licensed under";
$lang['graphs']             = "Graphs";
$lang['details']            = "Details";

$lang['close']              = "Close";
$lang['call_details']       = "Call details";
$lang['event']              = "Event";

$lang['time_distrib']       = "Time distribution";
$lang['queue']              = "Queue";

$lang['create_user']        = "Create new user";
$lang['back']               = "Back";
$lang['desc_username']      = "Unique login name for this user";
$lang['enter_username']     = "Enter username";
$lang['display_name']       = "Display name";
$lang['desc_display_name']  = "Real name for this user";
$lang['enter_display_name'] = "Enter display name";
$lang['email']              = "Email address";
$lang['desc_email']         = "Email address for this user. It will be used to to send reports to and recorver passwords";
$lang['enter_email']        = "Enter email";
$lang['enter_password']     = "Enter password";
$lang['desc_password']      = "Password for new user. Minimum 6 characters. Ideally should contain symbols, numbers and alphabetic characters";
$lang['confirm_password']   = "Confirm password";
$lang['desc_confirm_password']  = "Confirm password";
$lang['role']               = "Role";
$lang['desc_role']          = "Select permission level for this user.";
$lang['admin']              = "Administrator";
$lang['manager']            = "Manager";
$lang['guest']              = "Guest";
$lang['username_short']     = "Username must be at least 4 characters";
$lang['user_exists']        = "User with same name already exists in database";
$lang['display_name_short'] = "Display name is too short";
$lang['email_short']        = "This email is too short";
$lang['email_invalid']      = "This is not valid email address";
$lang['password_short']     = "This password is too short";
$lang['password_mismatch']  = "password do not match";
$lang['select_role']        = "Please select permission level";
$lang['user_create_success'] = "User created succesfully";
$lang['edit_user']          = "Edit user";
$lang['deactivate_user']    = "Deactivate user";
$lang['activate_user']      = "Activate user";
$lang['delete_user']        = "Delete user";
$lang['manage_queues']      = "Manage queues";
$lang['manage_agents']      = "Manage agents";
$lang['select_queue']       = "Select queue";
$lang['select']             = "Select";
$lang['assigned_queues']    = "Assigned queues";
$lang['assign']             = "Assign";
$lang['unassign']           = "Unassign";
$lang['user_queue_assign_success'] = "Assigned queue to user";
$lang['user_queue_assign_fail'] = "Could not assign queue to user";
$lang['user_queue_unassign_success'] = "Unassigned queue from user";
$lang['user_queue_unassign_fail'] = "Could not unassign queue from user";
$lang['are_you_sure_delete_user'] = "Are you sure you want to delete this user? This action can not be reversed";
$lang['are_you_sure_deactivate_user'] = "Are you sure you want to change user status?";
$lang['user_edit_success']  = "User information updated succesfully";
$lang['user_edit_fail']     = "Could not update user information";
$lang['user_delete_success'] = "User deleted succesfully";
$lang['user_delete_fail']   = "Could not delete user";
$lang['copyright']          = "Copyright";
$lang['select_agent']       = "Select agent";
$lang['assigned_agent']     = "Assigned agent";
$lang['user_agent_unassign_success'] = "Unassigned agent from user";
$lang['user_agent_unassign_fail'] = "Could not unassign agent from user";
$lang['user_agent_assign_success'] = "Assigned agent from user";
$lang['user_agent_assign_fail'] = "Could not assign agent from user";
$lang['user_id_deactivated']    = "User is deactivated";
$lang['user_no_agents']     = "Your user is not properly configured, please contact application administrators";
$lang['user_no_queues']     = "Your user is not properly configured, please contact application administrators";
$lang['app_track_called_back_calls'] = "Track returned calls";
$lang['desc_app_track_called_back_calls'] = "If selected, unanaswered calls with be marked as 'Not returned' and users will have to manually set for each unanswered call that it has been returned. Statistical information will be generated.";
$lang['called_back']        = "Returned";
$lang['call_status_update_success'] = "Call status updated succesfully";
$lang['call_status_update_fail'] = "Could not update call status";
$lang['time_range']         = "Select time range";
$lang['calltime_gt']        = "Call time >";
$lang['calltime_lt']        = "Call time <";
$lang['outgoing']           = "Outgoing";

$lang['overview']           = "Overview";
$lang['no_current_call']    = "No ongoing calls";
$lang['number']             = "Number";
$lang['download']           = "Download";

$lang['manage_users']       = "Manage users";

$lang['agent_settings']     = "Global agent settings";
$lang['desc_agent_settings'] = "Manage global parameters affecting all agents in the system";

$lang['app_call_categories']     = "Call categories";
$lang['desc_app_call_categories'] = "If selected, Call category management will be enabled and calls can be assigned to specific categories";
$lang['call_categories']    = "Call categories";
$lang['name']               = "Name";
$lang['color']              = "Color";
$lang['create']             = "Create new";
$lang['cat_create_success'] = "Category created succesfully";
$lang['cat_create_fail']    = "Could not create category";
$lang['create_new_cat']     = "Create new category";
$lang['enter_name']         = "Enter name";
$lang['delete']             = "Delete";
$lang['cat_delete_success'] = "Category deleted succesfully";
$lang['call_category']      = "Call category";
$lang['add_comment']        = "Add comment";
$lang['comment']            = "Comment";
$lang['desc_add_comment']   = "Add comment to this call";
$lang['add_category']       = "Add category";
$lang['call_update_success'] = "Call information updated succesfully";
$lang['export']             = "Export";
$lang['random']             = "Random";

$lang['max_calltime']       = "Max. call time";
$lang['max_holdtime']       = "Max. hold time";
$lang['max_origposition']   = "Max. starting position";
$lang['current_calls']      = "Current calls";

$lang['sla_calltime']       = "Call time SLA";
$lang['desc_sla_calltime']  = "Amount of time in seconds, in which call duration is considered to be withing SLA. If call exceeds value set here, it will be highlighted in dashboard and other parts of applications";
$lang['sla_holdtime']       = "Hold time SLA";
$lang['desc_sla_holdtime']  = "Amount of time waiting, in seconds, in which call hold time is considered to be withing SLA. If waiting time exceeds value set here, it will be highlighted in dashboard and other parts of applications";
$lang['sla_overflow']       = "Overflow number";
$lang['desc_sla_overflow']  = "When number of callers reach value set here, queue will be considered to be overflow, and appropriate notifications will be set for administrators and agents to be notified";
$lang['queue_conf_update_fail'] = "Could not update queue configuration";
$lang['queue_conf_update_ok'] = "Queue configuration updated succesfully";
$lang['desc_queue_display_name'] = "Descriptive name for this queue. Will be visible in reports and throughout of application.";
$lang['desc_agent_display_name'] = "Descriptive name for this agent. Will be visible in reports and throughout of application.";
$lang['agent_conf_update_fail'] = "Could not update agent configuration";
$lang['agent_conf_update_ok'] = "Agent configuration updated succesfully";
$lang['total']              = "Total";
$lang['avg']                = "Average";
$lang['max']                = "Max";

$lang['something_wrong']    = "Something went wrong";
$lang['file_not_found']     = "File not found";
$lang['license_info']       = "License information";
$lang['item']               = "Item";
$lang['value']              = "Value";

$lang['ABANDON']            = "Caller hang up";
$lang['EXITEMPTY']          = "Exit empty queue";
$lang['EXITWITHTIMEOUT']    = "Exit after timeout";
$lang['EXITWITHKEY']        = "Exit with key";

$lang['p_position']         = "Position";

$lang['app_track_transfers'] = "Collect transferred calls";
$lang['desc_app_track_transfers'] = "If selected yes, Quickqueues will try to collect all transferred calls.";
$lang['calls_transferred']  = "Transferred";
$lang['transferred']        = "Transferred";
$lang['transferred_to']     = "Transferred to";
$lang['no_callers']         = "There are no callers waiting in the queue";
$lang['calls_answered_within_10s'] = "Answered within SLA";

$lang['blacklist']          = "Blacklist";
$lang['description']        = "Description";
$lang['enter_number']       = "Enter number";
$lang['enter_description']  = "Enter description";
$lang['blacklist_add_success']  = "Number added to blacklist";
$lang['blacklist_del_success']  = "Number removed from blacklist";


$lang['on_call']            = "On call";
$lang['free']               = "Free";
$lang['busy']               = "Busy";
$lang['paused']             = "Paused";


$lang['connect_silently']   = "Connect silently";
$lang['connect_barge']      = "Connect with agent";

$lang['extension']          = "Extension";
$lang['desc_extension']     = "User extension";
$lang['enter_extension']    = "Enter extension";

$lang['chanspy_user_no_ext'] = "Can not listen on this call. Your user has no extension.";
$lang['chanspy_init']       = "Please wait for the call on your extension...";


$lang['play_recording']     = "Play recording";
$lang['app_mark_answered_elsewhere'] = "Mark calls as answered elsewhere";
$lang['desc_app_mark_answered_elsewhere'] = "Answered elsewhere interval in minutes. If set, calls that were abaonded or left unanaswered in someway, will be marked as answered elsewhere if same caler will call again and this will be answered within specifid amount of time. Setting this to 0 disables this option.";

$lang['period']             = "Period";
$lang['Monday']             = "Monday";
$lang['Tuesday']            = "Tuesday";
$lang['Wednesday']          = "Wednesday";
$lang['Thursday']           = "Thursday";
$lang['Friday']             = "Friday";
$lang['Saturday']           = "Saturday";
$lang['Sunday']             = "Sunday";

$lang['pause_time']         = "Pause time";
$lang['forgot_password']    = "Forgot password";
$lang['send_email']         = "Send email";
$lang['user_email_not_found'] = "User with such email was not found on the system";
$lang['email_with_password_reset_sent'] = "Email with password reset link has been sent";
$lang['set_new_password']   = "Please choose new password";
$lang['password_change_success'] = "Passwords updated succesfully";
$lang['password_reset_process'] = "Password reset process";
$lang['password_reset_body'] = "Hi,\n\n You requested to reset the password on your account.\n Please click the link below to set new passwords:\n\n";

$lang['cb_nah']             = "Not needed";
$lang['cb_nop']             = "Failed";
$lang['cb_yes']             = "Yes";
$lang['cb_no']              = "No";

$lang['missed']             = "Missed";
$lang['my_calls']           = "My calls";

$lang['agent_call_restrictions'] = "Call restrictions for agents";
$lang['desc_agent_call_restrictions'] = "This parameter controls which to which call agent can have access. Setting this to agent 'agent calls', will result agents' user being able to listen to search for their calls, 'queue calls' will gve them acces to call from their queue, and 'all calls' will give them access to all calls throughout the system";

$lang['own_calls']          = "Agent calls";
$lang['queue_calls']        = "Queue calls";
$lang['all']                = "All";

$lang['date_gt']            = "Date from";
$lang['date_lt']            = "Date to";

$lang['app_track_agent_pause_time'] = "Track agent pause timers";
$lang['desc_app_track_agent_pause_time'] = "If activated, Quickqueues will collect information about agent pause times";

$lang['app_track_agent_session_time'] = "Track agent sessions";
$lang['desc_app_track_agent_session_time'] = "If activated, Quickqueues will collect information about agent sessions/shifts";

$lang['app_track_duplicate_calls']  = "Mark calls duplicate";
$lang['desc_app_track_duplicate_calls'] = "If caller with same caller ID calls within specified amount of minutes, calls will be marked as duplicate. Setting this to zero, disables this functionality";

$lang['duplicate_calls']    = "Duplicate calls";
$lang['duplicate']          = "Duplicate";

$lang['pause']              = "Pause";
$lang['session']            = "Session";

$lang['move']               = "Move";

$lang['agent_session_start_ok'] = "Agent session started succesfully";
$lang['agent_session_end_ok'] = "Agent session ended succesfully";
$lang['agent_pause_ok'] = "Agent paused sucessfully";
$lang['agent_unpause_ok'] = "Agent unpaused sucessfully";

$lang['Mon']                = "Monday";
$lang['Tue']                = "Tueday";
$lang['Wed']                = "Wednesday";
$lang['Thu']                = "Thursday";
$lang['Fri']                = "Friday";
$lang['Sat']                = "Saturday";
$lang['Sun']                = "Sunday";

$lang['call_distrib_by_weekday']    = "Call distribution by week day";

$lang['session_start_time'] = "Session start time";
$lang['desc_session_start_time'] = "Time when agent is supposed to start their session";
$lang['session_end_time']   = "Session end time";
$lang['desc_session_end_time'] = "Time when agent is supposed to finish their session";
$lang['max_pause_time']     = "Allowed pause duration";
$lang['desc_max_pause_time'] = "Allowed pause time for agent, in minutes, per day";

$lang['create_agent']       = "Create new agent";
$lang['agent_create_success'] = "Agent created succesfully";

$lang['select_queue']       = "Select queue";
$lang['name_short']         = "Name is too short";
$lang['extension_short']    = "Extension is too short";
$lang['desc_agent_name']    = "Agent name";
$lang['desc_agent_queue']   = "Queue, to which this agent belongs";

$lang['agent_not_found']    = "Agent not found";
$lang['queue_not_found']    = "Queue not found";

$lang['app_crm_mode']       = "CRM mode";
$lang['desc_app_crm_mode']  = "Enabling this feature will activate additional call related flags, like call curators, call statuses, and call priorities";

$lang['todo']               = "Todo";
$lang['callback_queue']     = "Callback";
$lang['workspace']          = "Workspace";

$lang['monitoring']         = "Monitoring";

$lang['app_call_statuses']  = "Manage call statuses";
$lang['desc_app_call_statuses'] = "If activated calls can be assigned to different statuses";
$lang['open']               = "Open";
$lang['ongoing']            = "Ongoing";
$lang['closed']             = "Closed";

$lang['answered_elsewhere'] = "Answered in another call";

$lang['recording']          = "Recording";

$lang['cancel']             = "Cancel";

$lang['app_call_priorities'] = "Manage call priorities";
$lang['desc_app_call_priorities'] = "If activated calls can be assigned to different priorities";
$lang['low']                = "Low";
$lang['normal']             = "Normal";
$lang['high']               = "High";
$lang['urgent']             = "Urgent";

$lang['priority']           = "Priority";

$lang['app_call_curators']  = "Manage call curators";
$lang['desc_app_call_curators'] = "If activated, calls can be assigned to 'curators'. Those curators will be able to track calls assigned to them as if they were their calls";

$lang['curator']            = "Curator";

$lang['app_call_tags']      = "Manage call tags";
$lang['desc_app_call_tags'] = "If activated calls can be assigned to different tags";

$lang['call_tags']          = "Call tags";

$lang['tag_create_success'] = "Tag created succesfully";
$lang['tag_create_fail']    = "Could not create tag";
$lang['tag_delete_success'] = "Tag deleted succesfully";

$lang['call_tag']           = "Call tag";

$lang['broadcast_notifs']   = "Broadcast";
$lang['broadcast_notif']    = "Broadcast";
$lang['bcast_create_success'] = "Broadcast created succesfully";
$lang['bcast_create_fail']    = "Could not create broadcast";
$lang['bcast_delete_success'] = "Broadcast deleted succesfully";

$lang['enter_text']         = "Enter text...";

$lang['agent_pauses']       = "Agent pauses";

$lang['app_enable_switchboard'] = "Enable siwtchboard";
$lang['desc_app_enable_switchboard'] = "If enabled, users will be able to see other, non-agent extensions statuses";

$lang['switchboard']        = "Switchboard";

$lang['available']          = "Available";
$lang['unavailable']        = "Unavailable";
$lang['logged_out']         = "Logged out";

$lang['change_password']    = "Change password";

$lang['current_password']   = "Current password";
$lang['new_password']       = "New password";


$lang['select_color']       = "Select color";

$lang['unique_callers']     = "Unique callers";

$lang['archive']            = "Archive";
$lang['author']             = "Author";

$lang['active']             = 'Active';

$lang['bcast_restore_success'] = "Broadcast notification restored succesfully";
$lang['edit']               = "Edit";

$lang['app_ignore_abandon'] = "Ignore unanswered calls with specific wait time";
$lang['app_ignore_abandon_desc'] = "Unanswered calls below specified amount of seconds will be counted separately from other unanswered calls. Specifying 0 will disable this feature";

$lang['seconds']            = "sec.";

$lang['calls_without_service'] = "Calls without service";

$lang['bcast_edit_success'] = "Broadcast notification successfully updated";

$lang['external']           = "External";
$lang['internal']           = "Internal";

$lang['COMPLETECALLER']     = "Anwered (caller hang up)";
$lang['COMPLETEAGENT']      = "Anwered (agent hang up)";

$lang['work_days']          = "Work days";

$lang['total_stats']        = "Total stats";
$lang['avg_stats']          = "Average stats";

$lang['app_track_top_agents'] = "Trck top agents";
$lang['desc_app_track_top_agents'] = "If configured, system will track top agents for specified amount of hours";

$lang['top_agents']         = "Top agents";

$lang['call_distrib_by_category'] = "Call distribution by category";

$lang['app_auto_mark_called_back'] = "Automatically mark calls as called back";
$lang['desc_app_auto_mark_called_back'] = "If activated, unanswered calls - within specified amount of minutes from numbers to which outgoing call is made will be marked as called back. Setting this value to 0 deactivates the feature";

$lang['first_name']         = "First name";
$lang['last_name']          = "Last name";
$lang['id']                 = "ID";
$lang['login_name']         = "Login name";

$lang['agent_show_in_dashboard']  = "Show in dashboard";
$lang['desc_agent_show_in_dashboard'] = "iIf disabled, agent realtime data will not be visible in dashboards and realtime views, but can be accessed in statistics pages. This can be usefull when specific agent does not work or does not receive calls anymore";

$lang['contact']            = "Contact";

$lang['inactive']           = "Inactive";

$lang['product_information']    = "Product information";

$lang['queue_name']       = "Queue name";

$lang['app_call_subcategories']     = "Call subcategories";
$lang['desc_app_call_subcategories'] = "If selected, Call category management will be enabled and calls can be assigned to specific subcategories";
$lang['call_subcategories']    = "Call subcategories";

$lang['create_new_subcat']  = "Create new subcategory";

$lang['subcat_create_success'] = "Subcategory created succesfully";

$lang['call_subcategory']   = "Subcategory";

$lang['service']            = "Service";
$lang['product']            = "Product";
$lang['service_type']       = "Service type";
$lang['service_subtype']    = "Service subtype";

$lang['cdr_lookup']         = "CDR Lookup";
$lang['desc_cdr_lookup']    = "If enabled, users will be able to quick lookup numbers history from navigation menu";

$lang['result_not_found']   = "Results not found";

$lang['calls_offwork']      = "Call in off work hours";

$lang['intro_nav']          = "From navigation menu, you can search and find various statistical information about system";
$lang['intro_start']        = "This page show general information about the system, like number of answered and unanswered calls, distribution of calls by queue and agents, and so on.";
$lang['intro_datepicker']   = "Bu default system displays stats for current day, but time periods can be changed using date pickers and dropdowns.";
$lang['intro_recordings']   = "On this page you can search and find all the  calls registered in the system.";
$lang['intro_rec_filter']   = "You can filter calls by source destination call date and other fields.";
$lang['intro_rec_results']  = "Search results display various call information.";

$lang['custom_1']           = "Location";
$lang['custom_2']           = "Note";
$lang['custom_3']           = "Product";
$lang['custom_4']           = "Showroom";

$lang['user']               = "User";

$lang['INCOMINGOFFWORK']    = "Call offwork";
$lang['OUT_ANSWERED']       = "Outgoing answered";
$lang['OUT_NOANSWER']       = "Outgoing no answer";
$lang['OUT_BUSY']           = "Outgoing busy";
$lang['OUT_FAILED']         = "Outgoing failed";
$lang['INC_ANSWERED']       = "Incoming answered";
$lang['INC_NOANSWER']       = "Incoming no answer";
$lang['INC_BUSY']           = "Incoming busy";
$lang['INC_FAILED']         = "Incoming failed";
$lang['IVRABANDON']         = "Abandoned in IVR";

$lang['order_id']           = "Order ID";
$lang['payed']              = "Payed";
$lang['payment_method']     = "Payment method";
$lang['shipping_date']      = "Shipping date";

$lang['survey_result']      = "Survey score";

$lang['compare']            = "Compare";

$lang['agent_historical_stats_monthly'] = "Monthly historical stats";
$lang['agent_historical_stats_daily'] = "Daily historical stats";

$lang['tickets']            = "Tickets";
$lang['ticket_departments'] = "Departments";
$lang['ticket_categories']  = "Categories";
$lang['ticket_subcategories']  = "Subcategories";

$lang['department']         = "Department";

$lang['created_at_gt']      = "Created after";
$lang['created_at_lt']      = "Created before";
$lang['due_at_gt']          = "Due after";
$lang['due_at_lt']          = "Due before";
$lang['create_ticket']      = "Create ticket";

$lang['due_at']             = "Due";

$lang['edit_ticket']        = "Edit ticket";
$lang['add_comment_success'] = "Comment added succesfully";

$lang['call_addeed_to_ticket_succcess'] = "Call added to ticket succesfully";

$lang['comments']           = "Comments";

$lang['call_removed_from_ticket_succcess'] = "Call was removed from ticket succesfully";

$lang['news']               = "News";
$lang['add_to_ticket']      = "Add to ticket";

$lang['call_status_finished_success'] = 'Finished succesfully';
$lang['call_status_on_hold'] = 'on hold';
$lang['call_status_finished_fail'] = 'Finished unsuccesfully';
$lang['call_status_finished_ok'] = 'Finished';

$lang['start_session']      = "Start Session";
$lang['end_session']        = "End Session";

$lang['start_pause']        = "Start pause";
$lang['end_pause']          = "End pause";
$lang['timetable']          = "Timetable";

$lang['SESSION']            = "Session";
$lang['PAUSE']              = "Pause";

$lang['announcement']       = 'News';
$lang['document']           = 'Document';
$lang['type']               = "Type";

$lang['new_article_in_news_directory'] = "New article in news directory";

$lang['your_call_has_new_comment'] = "Your call has new comment";

$lang['calls_outgoing_answered'] = "Outgoing calls (answered)";
$lang['calls_outgoing_failed'] = "Outgoing calls (failed)";

$lang['edit_customer_name'] = "Edit customer name";

$lang['campaigns'] = "Campaigns";
$lang['new_campaign'] = "New capaign";

$lang['category_export']       = 'Category Export';
$lang['category_refresh']       = 'Category Refresh';

$lang['days']               = "Days";
$lang['hours']              = "Hours";
$lang['categories']         = "Categories";
$lang['category_stats']     = "Call stats by categories and sub-categories";

/* ----------- For System Overview --------- */

$lang['incoming_talk_time_sum_overview'] =  "Incoming talk time (sum)";
$lang['outgoing_talk_time_sum_overview'] = "Outgoing talk time (sum)";;