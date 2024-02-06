<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Export extends MY_Controller {


    public function __construct()
    {

        parent::__construct();

        $this->data->queue_ids = array();
        foreach ($this->data->user_queues as $q) {
            $this->data->queue_ids[] = $q->id;
        }
        include_once(APPPATH.'third_party/xlsxwriter.class.php');
       
    }


    public function _prepare_headers($file_name = 'export')
    {
        header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($file_name).'"');
        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
    }


    public function _write_xlsx($rows = false)
    {
        $writer = new XLSXWriter();
        $writer->setAuthor('Quickqueues');
        foreach($rows as $row) {
            $writer->writeSheetRow('Sheet1', $row);
        }
        $writer->writeToStdOut();
        exit(0);
    }

    public function recordings()
    {
        $category_export_permission= $this->data->config->app_call_categories;
        $rows = array();

        $headers = array(
            lang('date'),
            lang('queue'),
            lang('agent'),
            lang('src'),
            lang('dst'),
            lang('cause'),
            lang('call_time'),
            lang('hold_time'),
            lang('comment'),
            $category_export_permission == 'yes' ? lang('call_category') : '',
            $category_export_permission == 'yes' ? lang('call_tag').' -1': '',
            $category_export_permission == 'yes' ? lang('call_tag').' -2': '',
            $category_export_permission == 'yes' ? lang('call_tag').' -3': '',
        );

        if ($this->data->config->app_call_tags == 'yes') 
        {
            array_push($headers, lang('call_tag'));
            $ttags = array();
            foreach ($this->Call_tag_model->get_all() as $ct) 
            {
                $ttags[$ct->id] = $ct->name;
            }
        }

        if ($this->data->config->app_call_statuses == 'yes') 
        {
            array_push($headers, lang('status'));
        }

        $rows[]  = $headers;
        $tqueues = array();
        $tagents = array();

        $tqueues = [];

        foreach ($this->data->user_queues as $q) 
        {
            if (stripos($q->display_name, 'callback') === false) 
            {
                $tqueues[$q->id] = $q->display_name;
            }
        }


        foreach ($this->data->user_agents as $a) 
        {
            $tagents[$a->id] = $a->display_name;
        }

       $queue_ids = array();

       $queue_ids = [];

        foreach ($this->data->user_queues as $q) 
        {
            if (stripos($q->display_name, 'callback') === false) 
            {
                $queue_ids[] = $q->id; 
            }   
        }

        $where = array();
        $like = array();


        $where['calltime >'] = $this->input->get('calltime_gt') ? $this->input->get('calltime_gt') : false;
        $where['calltime <'] = $this->input->get('calltime_lt') ? $this->input->get('calltime_lt') : false;

        $where['date >'] = $this->input->get('date_gt') ? $this->input->get('date_gt') : QQ_TODAY_START;
        $where['date <'] = $this->input->get('date_lt') ? $this->input->get('date_lt') : QQ_TODAY_END;

        if ($this->input->get('queue_id')) {
            $where['queue_id'] = $this->input->get('queue_id');
        } else {
            $where['queue_id'] =  $queue_ids;
        }

        $where['agent_id'] = $this->input->get('agent_id');

        $where['called_back'] = $this->input->get('called_back');
        $where['category_id'] = $this->input->get('category_id');
        $where['transferred'] = $this->input->get('transferred');
        $where['duplicate']   = $this->input->get('duplicate');

        if ($this->input->get('event_type') == 'ANSWERED') {
            $where['event_type'] = array('COMPLETECALLER', 'COMPLETEAGENT');
        } elseif ($this->input->get('event_type') == 'UNANSWERED') {
            $where['event_type'] = array('ABANDON', 'EXITWITHKEY', 'EXITWITHTIMEOUT', 'EXITEMPTY');
        } elseif ($this->input->get('event_type') == 'calls_without_service') {
            $where['event_type'] = array('ABANDON', 'EXITWITHKEY', 'EXITWITHTIMEOUT', 'EXITEMPTY');
            $where['called_back'] = 'no';
        } elseif ($this->input->get('event_type') == 'OUTGOING') {
            $where['event_type'] = array('OUT_FAILED', 'OUT_ANSWERED', 'OUT_NOANSWER', 'OUT_BUSY');
        } else {
            $where['event_type'] = $this->input->get('event_type');
        }

        if ($this->data->config->app_service_module == 'yes') {
            $where['service_id'] = $this->input->get('service_id');
            $where['service_product_id'] = $this->input->get('service_product_id');
            $where['service_product_type_id'] = $this->input->get('service_product_type_id');
            $where['service_product_subtype_id'] = $this->input->get('service_product_subtype_id');
        }

        $like['src'] = $this->input->get('src');
        $like['dst'] = $this->input->get('dst');

        $calls = $this->Call_model->search($where, $like);

    

         /* ------- Get Formatted Categories and Subcategories For Export ------- */
        if($category_export_permission == 'yes') {
            $main_subject = array();
            $child_1_result = array();
            $child_2_result = array();
            $child_3_result = array();

            foreach ($this->Call_subjects_model->get_main_subjects() as $row) {
                $main_subject[$row['id']] = $row['title'];
            }
            foreach ($this->Call_subjects_model->get_child_1_subjects() as $row) {
                $child_1_result[$row['id']] = $row['title'];
            }

            foreach ($this->Call_subjects_model->get_child_2_subjects() as $row) {
                $child_2_result[$row['id']] = $row['title'];
            }

            foreach ($this->Call_subjects_model->get_child_3_subjects() as $row) {
                $child_3_result[$row['id']] = $row['title'];
            }
        }

            foreach ($calls as $c) 
            {
                //$category_export_permission= $this->data->config->app_call_categories;
                if($category_export_permission == 'yes') 
                {
                    if (strlen($c->subject_family) > 0) {
                        $empty_subject_family = ['', '', '', ''];
                        $subject_family_array = explode('|', $c->subject_family);

                        if (isset($subject_family_array[0])) {
                            if (strlen($subject_family_array[0]) > 0) {
                                if (strpos($subject_family_array[0], 'undefined') == 'true') {
                                    $empty_subject_family[0] = '';
                                } else {
                                    $empty_subject_family[0] = $main_subject[$subject_family_array[0]];
                                }
                            }
                        } else {
                            $empty_subject_family[0] = '';
                        }

                        if (isset($subject_family_array[1])) {
                            if (strlen($subject_family_array[1]) > 0) {
                                if (strpos($subject_family_array[1], 'undefined') == 'true') {
                                    $empty_subject_family[1] = '';
                                } else {
                                    $empty_subject_family[1] = $child_1_result[$subject_family_array[1]];
                                }
                            }
                        } else {
                            $empty_subject_family[1] = '';
                        }

                        if (isset($subject_family_array[2])) {
                            if (strlen($subject_family_array[2]) > 0) {
                                if (strpos($subject_family_array[2], 'undefined') == 'true') {
                                    $empty_subject_family[2] = '';
                                } else {
                                    $empty_subject_family[2] = $child_2_result[$subject_family_array[2]];
                                }
                            }
                        } else {
                            $empty_subject_family[2] = '';
                        }

                        if (isset($subject_family_array[3])) {
                            if (strlen($subject_family_array[3]) > 0) {
                                if (strpos($subject_family_array[3], 'undefined') == 'true') {
                                    $empty_subject_family[3] = '';
                                } else {
                                    $empty_subject_family[3] = $child_3_result[$subject_family_array[3]];
                                }
                            }
                        } else {
                            $empty_subject_family[2] = '';
                        }
                    } else {
                        $empty_subject_family = ['', '', '', ''];
                    }
                }

        /* ------- End OfFormatting Categories and Subcategories ------- */

       
        $rows[] = array(
            $c->date,
            isset($tqueues[$c->queue_id]) ? $tqueues[$c->queue_id] : "",
            isset($tagents[$c->agent_id]) ? $tagents[$c->agent_id] : "",
            $c->src,
            $c->dst,
            $c->event_type,
            sec_to_time($c->calltime),
            ($c->event_type == 'ABANDON' || $c->event_type == 'EXITWITHKEY' || $c->event_type == 'EXITWITHTIMEOUT' || $c->event_type == 'EXITEMPTY') ? sec_to_time($c->waittime) : sec_to_time($c->holdtime),
            $c->comment,
            $category_export_permission == 'yes' ? $empty_subject_family[0] : '',
            $category_export_permission == 'yes' ? $empty_subject_family[1] : '',
            $category_export_permission == 'yes' ? $empty_subject_family[2] : '',
            $category_export_permission == 'yes' ? $empty_subject_family[3] : '',
        );
        
        }
       /* echo "<pre>";
            print_r($rows);
        // echo "</pre>";*/
        $this->_prepare_headers('recordings-'.date('Ymd-His').'.xlsx');
        $writer     = new XLSXWriter();
        $writer->setAuthor('Quickqueues');
        $row_header = array(lang('stats'). ' '.$where['date >'].' > '.$where['date <']);
      
        // Set up formatting styles
        $style1     = array('font-style'=>'bold');
        $style2     = array('halign'=>'left', 'valign'=>'left');
        $style3     = array('border'=>'left,right,top,bottom', 'border-style'=>'medium');

        $writer->initializeSheet(lang('recordings'),[50, 40, 40, 40, 40, 40, 40, 40, 30, 30, 30, 30, 30, 30, 30]);
        $writer->writeSheetRow(lang('recordings'), $row_header, $style1, $style2, $style3);
        foreach($rows as $row) 
        {
            $writer->writeSheetRow(lang('recordings'), $row, $style2, $style3);
        }
        $writer->writeToStdOut();
        exit(0);
        
    }


    public function recordings_palitra()
    {
        $where = array();
        $like = array();


        $where['calltime >'] = $this->input->get('calltime_gt') ? $this->input->get('calltime_gt') : false;
        $where['calltime <'] = $this->input->get('calltime_lt') ? $this->input->get('calltime_lt') : false;

        if (strpos($where['calltime >'], ':')) {
            $t = explode(':', $where['calltime >']);
            $m = $t[0];
            $s = $t[1] + (60 * $m);
            $where['calltime >'] = $s;
            unset($t);
            unset($m);
            unset($s);
        }

        if (strpos($where['calltime <'], ':')) {
            $t = explode(':', $where['calltime <']);
            $m = $t[0];
            $s = $t[1] + (60 * $m);
            $where['calltime <'] = $s;
            unset($t);
            unset($m);
            unset($s);
        }


        $where['date >'] = $this->input->get('date_gt') ? $this->input->get('date_gt') : QQ_TODAY_START;
        $where['date <'] = $this->input->get('date_lt') ? $this->input->get('date_lt') : QQ_TODAY_END;

        if ($this->data->logged_in_user->role == 'admin') {
            $where['queue_id'] = $this->input->get('queue_id');
        } else {
            $where['queue_id'] = $this->input->get('queue_id') ? $this->input->get('queue_id') : $this->data->queue_ids;
        }

        $where['agent_id']    = $this->input->get('agent_id');

        $where['called_back'] = $this->input->get('called_back');
        $where['category_id'] = $this->input->get('search_category_id');

        $where['transferred'] = $this->input->get('transferred');
        $where['duplicate']   = $this->input->get('duplicate');
        $where['calltime']    = $this->input->get('calltime');
        $where['holdtime']    = $this->input->get('holdtime');

        $where['holdtime >']  = $this->input->get('holdtime_gt');
        $where['holdtime <']  = $this->input->get('holdtime_lt');

        $where['waittime >']  = $this->input->get('waittime_gt');
        $where['waittime <']  = $this->input->get('waittime_lt');

        $where['ticket_department_id'] = $this->input->get('search_ticket_department_id');
        $where['ticket_category_id'] = $this->input->get('search_ticket_category_id');
        $where['ticket_subcategory_id'] = $this->input->get('search_ticket_subcategory_id');

        if ($this->input->get('search_ticket_department_id')) {
            $this->data->json_vars['recordings_search.search_ticket_department_id'] = $this->input->get('search_ticket_department_id');
        }

        $this->data->js_vars['app_service_module'] = $this->data->config->app_service_module;

        if ($this->data->config->app_service_module == 'yes') {
            $where['service_id'] = $this->input->get('service_id');
            $where['service_product_id'] = $this->input->get('service_product_id');
            $where['service_product_type_id'] = $this->input->get('service_product_type_id');
            $where['service_product_subtype_id'] = $this->input->get('service_product_subtype_id');

            $this->data->js_vars['service_module_params'] = json_encode(
                array(
                    'service_id' => $this->input->get('service_id'),
                    'service_product_id' => $this->input->get('service_product_id'),
                    'service_product_type_id' => $this->input->get('service_product_type_id'),
                    'service_product_subtype_id' => $this->input->get('service_product_subtype_id'),
                )
            );

            $this->data->services = $this->Service_model->get_all();
        }

        if ($this->input->get('answered_elsewhere')) {
            $where['answered_elsewhere >'] = 1;
        }

        if ($this->input->get('calls_without_service')) {
            $where['called_back'] = 'no';
            $where['answered_elsewhere'] = 'isnull';
            $where['waittime >='] = $this->data->config->app_ignore_abandon;
            $this->data->calls_without_service = 'yes';
        } else {
            $this->data->calls_without_service = false;
        }


        if (strpos($this->input->get('uniqueid'), ',') !== false) {
            $where['uniqueid'] = explode(',', $this->input->get('uniqueid'));
        } else {
            $where['uniqueid'] = $this->input->get('uniqueid');
        }

        if ($this->input->get('event_type') == 'ANSWERED') {
            $where['event_type'] = array('COMPLETECALLER', 'COMPLETEAGENT');
        } elseif ($this->input->get('event_type') == 'UNANSWERED') {
            if ($this->data->config->app_track_ivrabandon == 'yes') {
                $where['event_type'] = array('ABANDON', 'EXITWITHKEY', 'EXITWITHTIMEOUT', 'EXITEMPTY', 'IVRABANDON');
            } else {
                $where['event_type'] = array('ABANDON', 'EXITWITHKEY', 'EXITWITHTIMEOUT', 'EXITEMPTY');
            }
        } elseif ($this->input->get('event_type') == 'OUTGOING_INTERNAL') {
            $where['event_type'] = array('OUT_FAILED', 'OUT_ANSWERED', 'OUT_NOANSWER', 'OUT_BUSY');
            $where['LENGTH(dst) <='] = 4;
        } elseif ($this->input->get('event_type') == 'OUTGOING_EXTERNAL') {
            $where['event_type'] = array('OUT_FAILED', 'OUT_ANSWERED', 'OUT_NOANSWER', 'OUT_BUSY');
            $where['LENGTH(dst) >'] = 4;
        } elseif ($this->input->get('event_type') == 'INCOMING') {
            $where['event_type'] = array('INC_FAILED', 'INC_ANSWERED', 'INC_NOANSWER', 'INC_BUSY');
        } else {
            $where['event_type'] = $this->input->get('event_type');
        }

        $like['src'] = $this->input->get('src');
        $like['dst'] = $this->input->get('dst');
        $like['transferdst'] = $this->input->get('transferred_to');
        $like['comment'] = $this->input->get('search_comment');

        $calls = $this->Call_model->search($where, $like);

        $rows[] = array(
            lang('date'),
            lang('department'),
            lang('call_category'),
            lang('call_subcategory'),
            lang('name'),
            lang('src'),
            lang('dst'),
            lang('comment'),
            lang('status'),
            lang('agent'),
        );

        foreach ($this->Ticket_department_model->get_all() as $d) {
            $departments[$d->id] = $d->name;
        }

        foreach ($this->Ticket_category_model->get_all() as $c) {
            $categories[$c->id] = $c->name;
        }

        foreach ($this->Ticket_subcategory_model->get_all() as $s) {
            $subcategories[$s->id] = $s->name;
        }

        foreach ($this->data->user_agents as $a) {
            $agents[$a->id] = $a->display_name;
        }

        foreach ($calls as $c) {
            $rows[] = array(
                $c->date,
                $c->ticket_department_id ? $departments[$c->ticket_department_id] : "",
                $c->ticket_category_id ? $categories[$c->ticket_category_id] : "",
                $c->ticket_subcategory_id ? $subcategories[$c->ticket_subcategory_id] : "",
                "",
                $c->src,
                $c->dst,
                $c->comment,
                lang($c->status),
                $c->agent_id ? $agents[$c->agent_id] : "",
            );
        }

        $this->_prepare_headers('calls-'.date('Ymd-His').'.xlsx');
        $this->_write_xlsx($rows);
    }


    public function service_stats()
    {
        $rows = array();

        $headers = array(
            lang('date'),
            lang('queue'),
            lang('agent'),
            lang('src'),
            lang('dst'),
            lang('cause'),
            lang('call_time'),
            lang('hold_time'),
            lang('comment'),
        );

        foreach ($this->Service_model->get_all() as $s) {
            $this->data->all_services[$s->id] = $s->name;
        }

        foreach ($this->Service_product_model->get_all() as $p) {
            $this->data->all_products[$p->id] = $p->name;
        }

        foreach ($this->Service_product_type_model->get_all() as $t) {
            $this->data->all_product_types[$t->id] = $t->name;
        }

        foreach ($this->Service_product_subtype_model->get_all() as $s) {
            $this->data->all_product_subtypes[$s->id] = $s->name;
        }

        if ($this->data->config->app_call_statuses == 'yes') {
            array_push($headers, lang('status'));
        }

        $rows[] = $headers;

        $tqueues = array();
        $tagents = array();

        foreach ($this->data->user_queues as $q) {
            $tqueues[$q->id] = $q->display_name;
        }

        foreach ($this->data->user_agents as $a) {
            $tagents[$a->id] = $a->display_name;
        }

        $where = array();
        $like = array();


        $where['calltime >'] = $this->input->get('calltime_gt') ? $this->input->get('calltime_gt') : false;
        $where['calltime <'] = $this->input->get('calltime_lt') ? $this->input->get('calltime_lt') : false;

        $where['date >'] = $this->input->get('date_gt') ? $this->input->get('date_gt') : QQ_TODAY_START;
        $where['date <'] = $this->input->get('date_lt') ? $this->input->get('date_lt') : QQ_TODAY_END;

        $where['agent_id'] = $this->input->get('agent_id');
        $where['queue_id'] = $this->input->get('queue_id');

        $where['called_back'] = $this->input->get('called_back');
        $where['category_id'] = $this->input->get('category_id');
        $where['transferred'] = $this->input->get('transferred');
        $where['duplicate']   = $this->input->get('duplicate');

        $where['service_id'] = $this->input->get('service_id');
        $where['service_product_id'] = $this->input->get('service_product_id');
        $where['service_product_type_id'] = $this->input->get('service_product_type_id');
        $where['service_product_subtype_id'] = $this->input->get('service_product_subtype_id');

        if ($this->input->get('event_type') == 'ANSWERED') {
            $where['event_type'] = array('COMPLETECALLER', 'COMPLETEAGENT');
        } elseif ($this->input->get('event_type') == 'UNANSWERED') {
            $where['event_type'] = array('ABANDON', 'EXITWITHKEY', 'EXITWITHTIMEOUT', 'EXITEMPTY');
        } elseif ($this->input->get('event_type') == 'OUTGOING') {
            $where['event_type'] = array('OUT_FAILED', 'OUT_ANSWERED', 'OUT_NOANSWER', 'OUT_BUSY');
        } else {
            $where['event_type'] = $this->input->get('event_type');
        }

        $like['src'] = $this->input->get('src');
        $like['dst'] = $this->input->get('dst');

        $calls = $this->Call_model->search($where, $like);

        foreach ($calls as $c) {
            $rows[] = array(
                $c->date,
                $c->queue_id ? $tqueues[$c->queue_id] : "",
                $c->agent_id ? $tagents[$c->agent_id] : "",
                $c->src,
                $c->dst,
                $c->event_type,
                sec_to_time($c->calltime),
                sec_to_time($c->holdtime),
                $c->comment,
                $c->status ? lang($c->status) : "",
                $c->service_id ? $this->data->all_services[$c->service_id] : "",
                $c->service_product_id ? $this->data->all_products[$c->service_product_id] : "",
                $c->service_product_type_id ? $this->data->all_product_types[$c->service_product_id] : "",
                $c->service_product_subtype_id ? $this->data->all_product_subtypes[$c->service_product_id] : ""
            );
        }

        $this->_prepare_headers('calls-'.date('Ymd-His').'.xlsx');
        $this->_write_xlsx($rows);

    }


    public function recordings_nova()
    {
        $rows = array();

        $headers = array(
            lang('date'),
            lang('agent'),
            lang('src'),
            lang('cause'),
            lang('call_time'),
            lang('comment'),
        );

        if ($this->data->config->app_call_tags == 'yes') {
            array_push($headers, lang('call_tag'));
            $ttags = array();
            foreach ($this->Call_tag_model->get_all() as $ct) {
                $ttags[$ct->id] = $ct->name;
            }
        }

        if ($this->data->config->app_call_statuses == 'yes') {
            array_push($headers, lang('status'));
        }

        $rows[] = $headers;

        $tqueues = array();
        $tagents = array();

        foreach ($this->data->user_queues as $q) {
            $tqueues[$q->id] = $q->display_name;
        }

        foreach ($this->data->user_agents as $a) {
            $tagents[$a->id] = $a->display_name;
        }


        $where = array();
        $like = array();


        $where['calltime >'] = $this->input->get('calltime_gt') ? $this->input->get('calltime_gt') : false;
        $where['calltime <'] = $this->input->get('calltime_lt') ? $this->input->get('calltime_lt') : false;

        $where['date >'] = $this->input->get('date_gt') ? $this->input->get('date_gt') : QQ_TODAY_START;
        $where['date <'] = $this->input->get('date_lt') ? $this->input->get('date_lt') : QQ_TODAY_END;

        $where['agent_id'] = $this->input->get('agent_id');
        $where['queue_id'] = $this->input->get('queue_id');

        $where['called_back'] = $this->input->get('called_back');
        $where['category_id'] = $this->input->get('category_id');
        $where['transferred'] = $this->input->get('transferred');
        $where['duplicate']   = $this->input->get('duplicate');

        if ($this->input->get('event_type') == 'ANSWERED') {
            $where['event_type'] = array('COMPLETECALLER', 'COMPLETEAGENT');
        } elseif ($this->input->get('event_type') == 'UNANSWERED') {
            $where['event_type'] = array('ABANDON', 'EXITWITHKEY', 'EXITWITHTIMEOUT', 'EXITEMPTY');
        } elseif ($this->input->get('event_type') == 'OUTGOING') {
            $where['event_type'] = array('OUT_FAILED', 'OUT_ANSWERED', 'OUT_NOANSWER', 'OUT_BUSY');
        } else {
            $where['event_type'] = $this->input->get('event_type');
        }

        $like['src'] = $this->input->get('src');
        $like['dst'] = $this->input->get('dst');

        $calls = $this->Call_model->search($where, $like);

        foreach ($calls as $c) {
            $rows[] = array(
                $c->date,
                $c->agent_id ? $tagents[$c->agent_id] : "",
                $c->src,
                $c->event_type,
                sec_to_time($c->calltime),
                $c->comment,
                $c->category_id ? $tcategories[$c->category_id] : "",
                $c->tag_id ? $ttags[$c->tag_id] : "",
                $c->status ? lang($c->status) : "",
            );
        }

        $this->_prepare_headers('calls-'.date('Ymd-His').'.xlsx');
        $this->_write_xlsx($rows);

    }


    public function overview_new()
    {
        $date_gt    = $this->input->get('date_gt') ? $this->input->get('date_gt') : QQ_TODAY_START;
        $date_lt    = $this->input->get('date_lt') ? $this->input->get('date_lt') : QQ_TODAY_END;
        $date_range = array('date_gt' => $date_gt, 'date_lt' => $date_lt);
        $precision  = $this->data->config->app_round_to_hundredth == 'yes' ? 2 : false;

        $row_header = array(lang('overall').' '.lang('stats'). ' '.$date_gt.' > '.$date_lt);
        $queue_ids  = array();

        foreach ($this->data->user_queues as $q) 
        {
            if (stripos($q->display_name, 'callback') === false) 
            {
                array_push($queue_ids, $q->id);
            }
        }

        $rows_overview   = array();
        $rows_agents     = array();
        $rows_queues     = array();
        $rows_days       = array();
        $rows_hours      = array();
        $rows_categories = array();

        ////////////////// -------- OVERVIEW SHEET ------/////////////////////////

        $total_stats = $this->Call_model->get_stats_for_start($queue_ids, $date_range);


        // Total Calls
        $rows_overview[] = array(lang('calls_total'), ($total_stats->calls_answered + $total_stats->calls_unanswered + $total_stats->calls_outgoing_answered + $total_stats->calls_outgoing_unanswered + $total_stats->callback_request));
        
        // Unique Incoming Calls
        $rows_overview[] = array(lang('calls_unique_in'), ($total_stats->unique_incoming_calls_answered + $total_stats->unique_incoming_calls_unanswered));
        
        // Unique Users
        $rows_overview[] = array(lang('calls_unique_users'), $total_stats->unique_incoming_calls_answered);
        
        // Incoming Answered
        $rows_overview[] = array(lang('start_menu_calls_answered'), $total_stats->calls_answered);

        // SLA Less Then Or Equal To 10 Sec
    
        if ($total_stats->sla_count_less_than_or_equal_to_10 > 0 && $total_stats->calls_answered > 0) 
        {
            $percentage           = ($total_stats->sla_count_less_than_or_equal_to_10 / $total_stats->calls_answered) * 100;
            $formatted_percentage = number_format($percentage, 2);
            $rows_overview[]      = array(
                lang('start_menu_sla_less_than_or_equal_to_10'),
                $total_stats->sla_count_less_than_or_equal_to_10,
                $formatted_percentage
            );
        }
        else
        {
            $rows_overview[] = array(lang('start_menu_sla_less_than_or_equal_to_10'),0,0);
        }
        
        // SLA Between 10 And 20 Sec
        if ($total_stats->sla_count_greater_than_10_and_less_than_or_equal_to_20 > 0 && $total_stats->calls_answered > 0) 
        {
            $percentage           = ($total_stats->sla_count_greater_than_10_and_less_than_or_equal_to_20 / $total_stats->calls_answered) * 100;
            $formatted_percentage = number_format($percentage, 2); // Format to 2 decimal places
        
            $rows_overview[] = array(
                lang('start_menu_sla_greater_than_10_less_then_or_equal_to_20'),
                $total_stats->sla_count_greater_than_10_and_less_than_or_equal_to_20,
                $formatted_percentage
            );
        }
        else
        {
            $rows_overview[] = array(lang('start_menu_sla_greater_than_10_less_then_or_equal_to_20'),0,0);
        }
        

        //  SLA Greater Then 20 Sec
        if ($total_stats->sla_count_greater_than_20 >= 0 && $total_stats->calls_answered > 0) 
        {
            $percentage           = ($total_stats->sla_count_greater_than_20 / $total_stats->calls_answered) * 100;
            $formatted_percentage = number_format($percentage, 2); // Format to 2 decimal places
            
            $rows_overview[] = array(
                lang('start_menu_sla_greater_than_20'),
                $total_stats->sla_count_greater_than_20,
                $formatted_percentage
            );
        }
        else
        {
            $rows_overview[] = array(lang('start_menu_sla_greater_than_20'),0,0);
        }
        

        // Hold Time Max
        $total_hold_wait_time      = $total_stats->total_holdtime + $total_stats->total_waittime;
        $total_answered_unanswered = $total_stats->calls_answered + $total_stats->calls_unanswered + $total_stats->callback_request;

        if($total_hold_wait_time > 0 && $total_answered_unanswered > 0)
        {
            $rows_overview[] = array(lang('hold_time').' ('.lang('max').')', sec_to_time(
                floor(
                    $total_stats->max_holdtime)
                )
            );
        }

        // Hold Time AVG
        
        if($total_hold_wait_time > 0 && $total_answered_unanswered > 0 && $total_stats->incoming_total_calltime_count > 0)
        {
            $rows_overview[] = array(lang('hold_time').' ('.lang('avg').')', sec_to_time(
                floor(
                    $total_stats->total_holdtime / $total_stats->incoming_total_calltime_count)
                ),
            );
        }
        else
        {
            $rows_overview[] = array(lang('hold_time').' ('.lang('avg').')', sec_to_time(0));
        }
        
       
       // Incoming Unanswered
        $calls_unanswered_percent = $total_stats->calls_unanswered > 0 && ($total_stats->calls_answered + $total_stats->calls_unanswered + $total_stats->callback_request) > 0 ?
        intval(($total_stats->calls_unanswered / ($total_stats->calls_answered + $total_stats->calls_unanswered + $total_stats->callback_request) * 100) * 100) / 100 : '0%';

        $rows_overview[] = array(lang('start_menu_calls_unanswered'), $total_stats->calls_unanswered, $calls_unanswered_percent);


        // Callback Request

        $callback_request_percent = $total_stats->callback_request > 0 && ($total_stats->calls_answered + $total_stats->calls_unanswered + $total_stats->callback_request) > 0 ?
        intval(($total_stats->callback_request / ($total_stats->calls_answered + $total_stats->calls_unanswered + $total_stats->callback_request) * 100) * 100) / 100 : '0%';

        $rows_overview[] = array(lang('callback_request'), $total_stats->calls_unanswered, $calls_unanswered_percent);

        // ATA AVG
        $rows_overview[] = array(lang('ata'), $total_stats->ata_count_total > 0 ? sec_to_time(
            floor($total_stats-> ata_total_waittime / $total_stats->ata_count_total)) : 0
        );
        
        // Without Service
        $rows_overview[] = array(lang('calls_without_service'), $total_stats->users_without_service . '(' . $total_stats->calls_without_service . ')');

        // Answered Elsewhere
        $rows_overview[] = array(lang('answered_elsewhere'), $total_stats->answered_elsewhere);

        // Answered Aoutcall - ნაპასუხები გადარეკილი
        $rows_overview[] = array(lang('answered_aoutcall'), $total_stats->called_back);

        // Outgoing Answered - ნაპასუხები გამავალი
        $rows_overview[] = array(lang('calls_outgoing_answered'), $total_stats->calls_outgoing_answered);
        
        // Outgoing Failed (Unanswered)
        $rows_overview[] = array(lang('calls_outgoing_failed'), $total_stats->calls_outgoing_unanswered);
        
        // Duplicated Calls
        $rows_overview[] = array(lang('duplicate_calls'), $total_stats->calls_duplicate);

        // Off Work
        $rows_overview[] = array(lang('start_menu_calls_offwork'), $total_stats->calls_offwork);
        
        // Incoming Talk Time SUM(Total)
        $rows_overview[] = array(lang('incoming_talk_time_sum_overview'), sec_to_time($total_stats->incoming_total_calltime));
        
         // Incoming Talk Time AVG
        $rows_overview[] = array(lang('incoming_talk_time_avg'), $total_stats->incoming_total_calltime_count > 0 ? sec_to_time(
            floor($total_stats-> incoming_total_calltime / $total_stats->incoming_total_calltime_count)) : 0
        );
         // Incoming Talk Time Max
        $rows_overview[] = array(lang('incoming_talk_time_max'), $total_stats->incoming_total_calltime_count > 0 ? sec_to_time(
            floor($total_stats->incoming_max_calltime)) : 0
        );

        // Outgoing Talk Time SUM
        $rows_overview[] = array(lang('outgoing_talk_time_sum_overview'), $total_stats->outgoing_max_calltime > 0 ? sec_to_time(
            $total_stats-> outgoing_total_calltime) : 0
        );
        

        // Outgoing Talk Time AVG
        $rows_overview[] = array(lang('outgoing_talk_time_avg'), $total_stats->outgoing_total_calltime_count > 0 ? sec_to_time(
            floor($total_stats-> outgoing_total_calltime / $total_stats->outgoing_total_calltime_count)) : 0
        );
        // Outgoing Talk Time Max
        $rows_overview[] = array(lang('outgoing_talk_time_max'), $total_stats->outgoing_total_calltime_count > 0 ? sec_to_time(
            floor($total_stats->outgoing_max_calltime)) : 0
        );
        
        // Position In Queue AVG
        $rows_overview[] = array(lang('start_menu_calls_waiting').' ('. lang('avg').') ', ceil($total_stats->origposition_avg));

        // Position In Queue MAX
        $rows_overview[] = array(lang('start_menu_calls_waiting').' ('. lang('max').') ', $total_stats->origposition_max);
        
        ////////////////// ----------- END OF OVERVIEW SHEET---------------/////////////////////////


        ////////////////// ------------ AGENTS OVERVIEW SHEET ----------------///////////////////
    
        $agent_call_stats  = $this->Call_model->get_agent_stats_for_start_page($queue_ids, $date_range);
        $agent_event_stats = $this->Event_model->get_agent_stats_for_start_page($queue_ids, $date_range);
        $agent_pause_stats = $this->Event_model->get_agent_pause_stats_for_start_page($date_range);
        
        foreach ($this->data->user_agents as $a) 
        {
            $agent_stats[$a->id] = array(
                'display_name'                                           => $a->display_name,
                'last_call'                                              => $a->last_call,
                'calls_total'                                            => 0,
                'calls_total_perc'                                       => 0,
                'calls_answered'                                         => 0,
                'calls_answered_perc'                                    => 0,
                'sla_count_less_than_or_equal_to_10'                     => 0,
                'sla_less_than_10_perc'                                  => 0,
                'sla_count_greater_than_10_and_less_than_or_equal_to_20' => 0,
                'sla_between_10_20_perc'                                 => 0,
                'sla_count_greater_than_20'                              => 0,
                'sla_more_than_20_perc'                                  => 0,
                'avg_ringtime'                                           => 0,
                'max_ringtime'                                           => 0,
                'calls_missed'                                           => 0,
                'incoming_talk_time_sum'                                 => 0,
                'incoming_talk_time_avg'                                 => 0,
                'incoming_talk_time_max'                                 => 0,
                'calls_outgoing_answered'                                => 0,
                'calls_outgoing_unanswered'                              => 0,
                'outgoing_talk_time_sum'                                 => 0,
                'outgoing_talk_time_avg'                                 => 0,  
                'outgoing_talk_time_max'                                 => 0,  
            );
        }
    
        
        // Calls Missed
        foreach ($agent_event_stats as $s) 
        {
            if ($s->agent_id) 
            {
                $agent_stats[$s->agent_id]['calls_missed'] = $s->calls_missed;
            }
            else
            {
                $agent_stats[$s->agent_id]['calls_missed'] = 0;
            }
        }
        foreach($agent_call_stats as $s) 
        {          
            
            // Total Calls
            $totalCalls = $total_stats->calls_answered + $total_stats->calls_unanswered + $total_stats->calls_outgoing_answered + $total_stats->calls_outgoing_unanswered;
    
            $agentCalls = $s->calls_answered + $s->calls_outgoing_answered + $s->calls_outgoing_unanswered;
         

            if($agentCalls > 0 && $totalCalls > 0)
            {
                $agent_stats[$s->agent_id]['calls_total_perc'] = intval((($agentCalls / $totalCalls) * 100) * 100) / 100;
            }
            else
            {
                $agent_stats[$s->agent_id]['calls_total_perc'] = '0';
            }

            $agent_stats[$s->agent_id]['calls_total']          = $agentCalls;

            //Calls Answered 

            $total_calls_answered = intval($total_stats->calls_answered);
            $agent_calls_answered = intval($s->calls_answered);
  
            if($agent_calls_answered > 0 && $total_calls_answered > 0) 
            {
                $agent_stats[$s->agent_id]['calls_answered']      = $agent_calls_answered;
                $percent                                          = ($agent_calls_answered / $total_calls_answered) * 100;
                $agent_stats[$s->agent_id]['calls_answered_perc'] = number_format($percent, 2);
            }
            else
            {
                $agent_stats[$s->agent_id]['calls_answered']      = 0;
                $agent_stats[$s->agent_id]['calls_answered_perc'] = 0;
            }

            // SLA Less Then Or Equal To 10 Sec

            if ($s->sla_count_less_than_or_equal_to_10 > 0 && $s->calls_answered > 0) 
            {
                $percentage                                                           = ($s->sla_count_less_than_or_equal_to_10 / $s->calls_answered) * 100;
                $formatted_percentage                                                 = number_format($percentage, 2); 
                $agent_stats[$s->agent_id]['sla_count_less_than_or_equal_to_10']      = $s->sla_count_less_than_or_equal_to_10;
                $agent_stats[$s->agent_id]['sla_less_than_10_perc']                   = $formatted_percentage;
            }
            else
            {
                $agent_stats[$s->agent_id]['sla_count_less_than_or_equal_to_10']      = 0;
                $agent_stats[$s->agent_id]['sla_less_than_10_perc']                   = 0;
            }

            // SLA Between 10 And 20 Sec
            if ($s->sla_count_greater_than_10_and_less_than_or_equal_to_20 > 0 && $s->calls_answered > 0) 
            {
                $percentage                                                                          = ($s->sla_count_greater_than_10_and_less_than_or_equal_to_20 / $s->calls_answered) * 100;
                $formatted_percentage                                                                = number_format($percentage, 2);
                $agent_stats[$s->agent_id]['sla_count_greater_than_10_and_less_than_or_equal_to_20'] = $s->sla_count_greater_than_10_and_less_than_or_equal_to_20;
                $agent_stats[$s->agent_id]['sla_between_10_20_perc']                                 =  $formatted_percentage;
            
            }
            else
            {
                $agent_stats[$s->agent_id]['sla_count_greater_than_10_and_less_than_or_equal_to_20'] = 0;
                $agent_stats[$s->agent_id]['sla_between_10_20_perc']                                 = 0;
            }
        

            //  SLA Greater Then 20 Sec
            if ($s->sla_count_greater_than_20 > 0 && $s->calls_answered > 0) 
            {
                $percentage                                                                          = ($s->sla_count_greater_than_20 / $s->calls_answered) * 100;
                $formatted_percentage                                                                = number_format($percentage, 2); 
                $agent_stats[$s->agent_id]['sla_count_greater_than_20']                              = $s->sla_count_greater_than_20;
                $agent_stats[$s->agent_id]['sla_more_than_20_perc']                                  = $formatted_percentage;
            }
            else
            {
                $agent_stats[$s->agent_id]['sla_count_greater_than_20']                              = 0;
                $agent_stats[$s->agent_id]['sla_more_than_20_perc']                                  = 0;
            }

            //Ring Time Avg

            if($s->total_ringtime > 0 && $s->incoming_total_calltime_count > 0)
            {
                $agent_stats[$s->agent_id]['avg_ringtime'] = sec_to_time(floor($s->total_ringtime/$s->incoming_total_calltime_count));   
            }

             // Ring Time Max

            if($s->total_ringtime > 0 && $s->incoming_total_calltime_count > 0)
            {
                $agent_stats[$s->agent_id]['max_ringtime'] = sec_to_time(floor($s->max_ringtime_answered));   
            }
                  
            // Incoming Talk Time SUM(Total)
           $agent_stats[$s->agent_id]['incoming_talk_time_sum'] = $s->incoming_total_calltime_count > 0 ? sec_to_time($s->incoming_total_calltime) : 0;

           // Incoming Talk Time AVG
           $agent_stats[$s->agent_id]['incoming_talk_time_avg'] = $s->incoming_total_calltime_count > 0 ? sec_to_time(
            floor($s-> incoming_total_calltime / $s->incoming_total_calltime_count)) : 0;

            
            // Incoming Talk Time Max
            $agent_stats[$s->agent_id]['incoming_talk_time_max'] = intval($s->incoming_total_calltime_count) > 0 ? sec_to_time(
            floor($s->incoming_max_calltime)) : 0;
  
       
            // Outgoing Answered 
      
            $agent_stats[$s->agent_id]['calls_outgoing_answered'] = (intval($s->calls_outgoing_answered));
            
            // Outgoing Unanswered 
            
            $agent_stats[$s->agent_id]['calls_outgoing_unanswered']   = intval($s->calls_outgoing_unanswered);
 
                
            // Outgoing Talk Time SUM
            $agent_stats[$s->agent_id]['outgoing_talk_time_sum']  = $s->outgoing_max_calltime > 0 ? sec_to_time(($s->outgoing_total_calltime)) : 0;
                

            // Outgoing Talk Time AVG
            $agent_stats[$s->agent_id]['outgoing_talk_time_avg']   = $s->outgoing_total_calltime_count > 0 ? sec_to_time(
                floor($s-> outgoing_total_calltime / $s->outgoing_total_calltime_count)) : 0;

            // Outgoing Talk Time Max
            $agent_stats[$s->agent_id]['outgoing_talk_time_max']   = $s->outgoing_total_calltime_count > 0 ? sec_to_time(
                floor($s->outgoing_max_calltime)) : 0;
        }
        
       
        $rows_agents[] = array(
            lang('agent'),
            lang('last_call'),
            lang('calls_total'),
            '%',
            lang('calls_answered'),
            '%',
            lang('start_menu_sla_less_than_or_equal_to_10'),
            '%',
            lang('start_menu_sla_greater_than_10_less_then_or_equal_to_20'),
            '%',
            lang('start_menu_sla_greater_than_20'),
            '%',
            lang('ring_time').' ('.lang('avg').')',
            lang('ring_time').' ('.lang('max').')',
            lang('ringnoanswer'),
            lang('incoming_talk_time_sum_overview'),
            lang('incoming_talk_time_avg'),
            lang('incoming_talk_time_max'),
            lang('calls_outgoing_answered'),
            lang('calls_outgoing_failed'),
            lang('outgoing_talk_time_sum_overview'),
            lang('outgoing_talk_time_avg'),
            lang('outgoing_talk_time_max'),
        );  

    
        foreach($agent_stats as $id => $i)
        {
           if($id == 0) {continue; }
           $rows_agents[] = array(
            array_key_exists('display_name', $i) ? $i['display_name'] : "დაარქივებული",
            $i['last_call'],
            $i['calls_total'],
            $i['calls_total_perc'],
            $i['calls_answered'],
            $i['calls_answered_perc'],
            $i['sla_count_less_than_or_equal_to_10'],
            $i['sla_less_than_10_perc'],
            $i['sla_count_greater_than_10_and_less_than_or_equal_to_20'],
            $i['sla_between_10_20_perc'],
            $i['sla_count_greater_than_20'],
            $i['sla_more_than_20_perc'],
            $i['avg_ringtime'],
            $i['max_ringtime'],
            $i['calls_missed'],
            $i['incoming_talk_time_sum'],
            $i['incoming_talk_time_avg'],
            $i['incoming_talk_time_max'],
            $i['calls_outgoing_answered'],
            $i['calls_outgoing_unanswered'],
            $i['outgoing_talk_time_sum'],
            $i['outgoing_talk_time_avg'],
            $i['outgoing_talk_time_max'],
           );
        }
        ////////////////// ------ END OF AGENTS SHEET ------- /////////////////////
        
      

        ////////////////// ------- QUEUE SHEET --------------/////////////////////
     
        $queue_call_stats = $this->Call_model->get_queue_stats_for_start_page($queue_ids, $date_range);
   
        foreach ($this->data->user_queues as $q) 
        {
            if (stripos($q->display_name, 'callback') === false) 
            {
               
                $queue_stats[$q->id] = array(
                    'display_name'                                                => $q->display_name,
                    'calls_total'                                                 => 0,
                    'unique_incoming_calls'                                       => 0,
                    'unique_users'                                                => 0,
                    'calls_answered'                                              => 0,
                    'calls_answered_perc'                                         => 0,
                    'sla_count_less_than_or_equal_to_10'                          => 0,
                    'sla_count_less_than_or_equal_to_10_perc'                     => 0,
                    'sla_count_greater_than_10_and_less_than_or_equal_to_20'      => 0,
                    'sla_count_greater_than_10_and_less_than_or_equal_to_20_perc' => 0,
                    'sla_count_greater_than_20'                                   => 0,
                    'sla_count_greater_than_20_perc'                              => 0,
                    'max_holdtime'                                                => 0,
                    'hold_time_avg'                                               => 0,
                    'calls_unanswered'                                            => 0,
                    'calls_unanswered_perc'                                       => 0,
                    'callback_request'                                            => 0,
                    'callback_request_perc'                                       => 0,
                    'ata_time_avg'                                                => 0,
                    'calls_without_service'                                       => 0,
                    'answered_elsewhere'                                          => 0,
                    'called_back'                                                 => 0,
                    'calls_outgoing_answered'                                     => 0, 
                    'calls_outgoing_unanswered'                                   => 0,
                    'calls_duplicate'                                             => 0,
                    'calls_offwork'                                               => 0,
                    'incoming_total_calltime_count'                               => 0,
                    'incoming_total_calltime_avg'                                 => 0,
                    'incoming_talk_time_max'                                      => 0,
                    'outgoing_total_calltime_count'                               => 0,
                    'outgoing_total_calltime_avg'                                 => 0,
                    'outgoing_total_calltime_max'                                 => 0,
                    'origposition_avg'                                            => 0,
                    'origposition_max'                                            => 0,
                );
            }
        }
        foreach($queue_call_stats as $s) 
        {
            //Calls Total
            $queue_stats[$s->queue_id]['calls_total']           = $s->calls_answered + $s->calls_unanswered + $s->calls_outgoing_answered + $s->calls_outgoing_unanswered + $s->callback_request;
           
           //Unique Incoming Calls
            $queue_stats[$s->queue_id]['unique_incoming_calls'] = $s->unique_incoming_calls_answered + $s->unique_incoming_calls_unanswered;
            
            //Unique Users
            $queue_stats[$s->queue_id]['unique_users']          = $s->unique_incoming_calls_answered;
            
            //Calls Answered
            $calls_answered_percent                             = $s->calls_answered > 0 && ($s->calls_answered + $s->calls_unanswered) > 0 ? (($s->calls_answered / ($s->calls_answered + $s->calls_unanswered)) * 100) : 0;
            $calls_answered_formatted_perc                      = intval($calls_answered_percent * 100) / 100;
            $queue_stats[$s->queue_id]['calls_answered']        = $s->calls_answered;
            $queue_stats[$s->queue_id]['calls_answered_perc']   = $calls_answered_formatted_perc;

             // SLA Less Then Or Equal To 10 Sec

             if ($s->sla_count_less_than_or_equal_to_10 > 0 && $s->calls_answered > 0) 
             {
                 $sla_less_10_percentage                                               = ($s->sla_count_less_than_or_equal_to_10 / $s->calls_answered) * 100;
                 $sla_less_10_percentage_formatted_percentage                          = number_format($sla_less_10_percentage, 2);
                 $queue_stats[$s->queue_id]['sla_count_less_than_or_equal_to_10']      = $s->sla_count_less_than_or_equal_to_10;
                 $queue_stats[$s->queue_id]['sla_count_less_than_or_equal_to_10_perc'] = $sla_less_10_percentage_formatted_percentage;    
             }
             else
             {
                $queue_stats[$s->queue_id]['sla_count_less_than_or_equal_to_10']      = 0;
                $queue_stats[$s->queue_id]['sla_count_less_than_or_equal_to_10_perc'] = 0;   
             }

            // SLA Between 10 And 20 Sec

            if ($s->sla_count_greater_than_10_and_less_than_or_equal_to_20 > 0 && $s->calls_answered > 0) 
            {
                $sla_between_10_20_percentage                                                             = ($s->sla_count_greater_than_10_and_less_than_or_equal_to_20 / $s->calls_answered) * 100;
                $sla_between_10_20_percentage_formatted_percentage                                        = number_format($sla_between_10_20_percentage, 2);
                $queue_stats[$s->queue_id]['sla_count_greater_than_10_and_less_than_or_equal_to_20']      = $s->sla_count_greater_than_10_and_less_than_or_equal_to_20;
                $queue_stats[$s->queue_id]['sla_count_greater_than_10_and_less_than_or_equal_to_20_perc'] =  $sla_between_10_20_percentage_formatted_percentage;  
            }
            else
            {
                $queue_stats[$s->queue_id]['sla_count_greater_than_10_and_less_than_or_equal_to_20']      = 0;
                $queue_stats[$s->queue_id]['sla_count_greater_than_10_and_less_than_or_equal_to_20_perc'] = 0;  
            }
        

            //  SLA Greater Then 20 Sec

            if ($s->sla_count_greater_than_20 > 0 && $s->calls_answered > 0) 
            {
                $sla_count_greater_than_20_percentage                        = ($s->sla_count_greater_than_20 / $s->calls_answered) * 100;
                $sla_count_greater_than_20_percentage_formatted_percentage   = number_format($sla_count_greater_than_20_percentage, 2);
                $queue_stats[$s->queue_id]['sla_count_greater_than_20']      = $s->sla_count_greater_than_20;
                $queue_stats[$s->queue_id]['sla_count_greater_than_20_perc'] = $sla_count_greater_than_20_percentage_formatted_percentage; 
            }
            else
            {
                $queue_stats[$s->queue_id]['sla_count_greater_than_20']      = 0;
                $queue_stats[$s->queue_id]['sla_count_greater_than_20_perc'] = 0; 
            }

           

            // Hold Time Max

            $total_hold_wait_time      = $s->total_holdtime + $s->total_waittime;
            $total_answered_unanswered = $s->calls_answered + $s->calls_unanswered + $s->callback_request;
    
            if($total_hold_wait_time > 0 && $total_answered_unanswered > 0)
            {
                $queue_stats[$s->queue_id]['max_holdtime'] = sec_to_time(floor($s->max_holdtime));
            }
  
         
           // Hold Time AVG

            if($total_hold_wait_time > 0 && $total_answered_unanswered > 0 && $s->incoming_total_calltime_count > 0 )
            {
                $queue_stats[$s->queue_id]['hold_time_avg'] = sec_to_time(floor(( $s->total_holdtime + $s->total_waittime) / $s->incoming_total_calltime_count));
            }
          
          // Incoming Unanswered
          $calls_unanswered_percent                           = $s->calls_unanswered > 0 && ($s->calls_answered + $s->calls_unanswered) > 0 ? intval(($s->calls_unanswered / ($s->calls_answered + $s->calls_unanswered + $s->callback_request) * 100) * 100) / 100: 0;
          $queue_stats[$s->queue_id]['calls_unanswered']      = $s->calls_unanswered;
          $queue_stats[$s->queue_id]['calls_unanswered_perc'] = $calls_unanswered_percent;

          // Callback Request
          $callback_request_percent                           = $s->callback_request > 0 ? intval(($s->callback_request / ($s->calls_answered + $s->calls_unanswered + $s->callback_request) * 100) * 100) / 100: 0;
          $queue_stats[$s->queue_id]['callback_request']      = $s->callback_request;
          $queue_stats[$s->queue_id]['callback_request_perc'] = $callback_request_percent;


  
          // ATA AVG
          $queue_stats[$s->queue_id]['ata_time_avg']          = $s->ata_count_total > 0 ? sec_to_time(floor($s->ata_total_waittime / $s->ata_count_total)) : 0;

          
          // Without Service

          $queue_stats[$s->queue_id]['calls_without_service'] = $s->users_without_service . '(' . $s->calls_without_service. ')';
  
          // Answered Elsewhere
          $queue_stats[$s->queue_id]['answered_elsewhere']    = $s->answered_elsewhere;
         
  
          // Answered Aoutcall - ნაპასუხები გადარეკილი

          $queue_stats[$s->queue_id]['called_back']           = $s->called_back;
  
          // Outgoing Answered - ნაპასუხები გამავალი

          $queue_stats[$s->queue_id]['calls_outgoing_answered']= $s->calls_outgoing_answered;

          
          // Outgoing Failed (Unanswered)

          $queue_stats[$s->queue_id]['calls_outgoing_unanswered']= $s->calls_outgoing_unanswered;
          
          // Duplicated Calls

          $queue_stats[$s->queue_id]['calls_duplicate']              = $s->calls_duplicate;
  
          // Off Work

          $queue_stats[$s->queue_id]['calls_offwork']                = $s->calls_offwork;
     
          
          // Incoming Talk Time SUM(Total)
          $queue_stats[$s->queue_id]['incoming_total_calltime_count']= $s->incoming_total_calltime_count > 0 ? sec_to_time($s->incoming_total_calltime) : 0;
          
           // Incoming Talk Time AVG

          $queue_stats[$s->queue_id]['incoming_total_calltime_avg']       = $s->incoming_total_calltime_count > 0 ? sec_to_time(
            floor($s->incoming_total_calltime / $s->incoming_total_calltime_count)) : 0;

          
          // Incoming Talk Time Max
          $queue_stats[$s->queue_id]['incoming_talk_time_max']        = $s->incoming_total_calltime_count > 0 ? sec_to_time(floor($s->incoming_max_calltime)) : 0;
  
          // Outgoing Talk Time SUM

          $queue_stats[$s->queue_id]['outgoing_total_calltime_count'] = $s->outgoing_max_calltime > 0 ? sec_to_time($s-> outgoing_total_calltime) : 0;
  
          // Outgoing Talk Time AVG

          $queue_stats[$s->queue_id]['outgoing_total_calltime_avg']         = $s->outgoing_total_calltime_count > 0 ? sec_to_time(floor($s->outgoing_total_calltime / $s->outgoing_total_calltime_count)) : 0;

          // Outgoing Talk Time Max

          $queue_stats[$s->queue_id]['outgoing_total_calltime_max']       = $s->outgoing_total_calltime_count > 0 ? sec_to_time(floor($s->outgoing_max_calltime)) : 0;
       
          // Position In Queue AVG

          $queue_stats[$s->queue_id]['origposition_avg']            = ceil($s->origposition_avg);
  
          // Position In Queue MAX

          $queue_stats[$s->queue_id]['origposition_max']            = ceil($s->origposition_max);
        }

        $rows_queues[] = array(
            lang('queue'),
            lang('calls_total'),
            lang('calls_unique_in'),
            lang('calls_unique_users'),
            lang('start_menu_calls_answered'),
            '%',
            lang('start_menu_sla_less_than_or_equal_to_10'),
            '%',
            lang('start_menu_sla_greater_than_10_less_then_or_equal_to_20'),
            '%',
            lang('start_menu_sla_greater_than_20'),
            '%',
            lang('hold_time').' ('.lang('max').')',
            lang('hold_time').' ('.lang('avg').')',
            lang('start_menu_calls_unanswered'),
            '%',
            lang('callback_request'),
            '%',
            lang('ata'),
            lang('calls_without_service'),
            lang('answered_elsewhere'),
            lang('answered_aoutcall'),
            lang('calls_outgoing_answered'),
            lang('calls_outgoing_failed'),
            lang('duplicate_calls'),
            lang('start_menu_calls_offwork'),
            lang('incoming_talk_time_sum_overview'),
            lang('incoming_talk_time_avg'),
            lang('incoming_talk_time_max'),
            lang('outgoing_talk_time_sum_overview'),
            lang('outgoing_talk_time_avg'),
            lang('outgoing_talk_time_max'),
            lang('start_menu_calls_waiting').' ('. lang('avg').') ',
            lang('start_menu_calls_waiting').' ('. lang('max').') ',   
        );
        foreach ($queue_stats as $i) {
            $rows_queues[] = array(
                $i['display_name'],
                $i['calls_total'],
                $i['unique_incoming_calls'],
                $i['unique_users'],
                $i['calls_answered'],
                $i['calls_answered_perc'],
                $i['sla_count_less_than_or_equal_to_10'],
                $i['sla_count_less_than_or_equal_to_10_perc'],
                $i['sla_count_greater_than_10_and_less_than_or_equal_to_20'],
                $i['sla_count_greater_than_10_and_less_than_or_equal_to_20_perc'],
                $i['sla_count_greater_than_20'],
                $i['sla_count_greater_than_20_perc'],
                $i['max_holdtime'],
                $i['hold_time_avg'],
                $i['calls_unanswered'],
                $i['calls_unanswered_perc'],
                $i['callback_request'],
                $i['callback_request_perc'],
                $i['ata_time_avg'],
                $i['calls_without_service'],
                $i['answered_elsewhere'],
                $i['called_back'],
                $i['calls_outgoing_answered'],
                $i['calls_outgoing_unanswered'],
                $i['calls_duplicate'],
                $i['calls_offwork'],
                $i['incoming_total_calltime_count'],
                $i['incoming_total_calltime_avg'],
                $i['incoming_talk_time_max'],
                $i['outgoing_total_calltime_count'],
                $i['outgoing_total_calltime_avg'],
                $i['outgoing_total_calltime_max'],
                $i['origposition_avg'],
                $i['origposition_max'],
            );
        }
      
        ////////////////// ---------- END OF QUEUE SHEET ----------////////////////////

        ////////////////// ----------  DAY SHEET --------------////////////////////
        $start_date      = new DateTime($date_range['date_gt']);
        $end_date        = new DateTime($date_range['date_lt']);
        $interval        = new DateInterval('P1D'); // 1 day interval
        $date_range_list = new DatePeriod($start_date, $interval, $end_date);
        $dates           = [];
        foreach ($date_range_list as $date) 
        {
            $dates[] = $date->format('Y-m-d');
        }

        $daily_call_stats = $this->Call_model->get_daily_stats_for_start_page($queue_ids, $date_range);
        $rows_days[] = array(
            lang('day'),
            lang('calls_answered'),
            lang('incoming_talk_time_sum_overview'),
            lang('start_menu_calls_unanswered'),
            lang('calls_outgoing_answered'),
            lang('outgoing_talk_time_sum_overview'),
            lang('calls_outgoing_failed'),
            lang('hold_time')
        );
        

        // Fill in missing dates with default values
        foreach ($dates as $date) {
            $found = false;
            foreach ($daily_call_stats as $i) 
            {
                if ($i->date == $date) 
                {
                    $found       = true;
                
                    if($i->calls_unanswered === 0)
                    {
    
                        $avg_holdtime = '00:00:00';
                    }
                    else
                    {
                        $avg_holdtime = sec_to_time(($i->total_holdtime + $i->total_waittime) / $i->calls_unanswered);
                    }

                    $rows_days[] = array(
                        'day'                       => $i->date,
                        'calls_answered'            => $i->calls_answered,
                        'incoming_total_calltime'   =>sec_to_time($i->incoming_total_calltime),
                        'calls_missed'              => $i->calls_unanswered,
                        'calls_outgoing_answered'   => $i->calls_outgoing_answered,
                        'outgoing_total_calltime'   => sec_to_time($i->outgoing_total_calltime),
                        'calls_outgoing_unanswered' => $i->calls_outgoing_unanswered,
                        'avg_holdtime'              => $avg_holdtime,
                    );

                    break;
                }
            }
            
            if (!$found) {
                // If the date is not found in $daily_call_stats, set all parameters to 0
                $rows_days[] = array(
                    'day'                       => $date,
                    'calls_answered'            => 0,
                    'incoming_total_calltime'   => sec_to_time(0),
                    'calls_missed'              => 0,
                    'calls_outgoing_answered'   => 0,
                    'outgoing_total_calltime'   => sec_to_time(0),
                    'calls_outgoing_unanswered' => 0,
                    'avg_holdtime'              => '00:00:00',
                    
                );
            }
        }
         ////////////////// ----------  END OF DAY SHEET --------------////////////////////

        /////////////////// ----------TIME SHEET ----------------//////////////////////////
        
        $hourly_call_stats = $this->Call_model->get_hourly_stats_for_start_page($queue_ids, $date_range);
      
        $hourly_stats = array();
        for ($i = 10; $i < 24; $i++) {
            $h = $i < 10 ? '0' . $i : $i;
            $hourly_stats[$h] = array(
                'calls_answered'            => 0,
                'incoming_total_calltime'   => 0,
                'calls_unanswered'          => 0,
                'calls_outgoing_answered'   => 0,
                'outgoing_total_calltime'   => 0,
                'calls_outgoing_unanswered' => 0,
                'hold_time_avg'             => 0,
            );
        }

        for ($i = 0; $i < 10; $i++) {
            $h = $i < 10 ? '0' . $i : $i;
            $hourly_stats[$h] = array(
                'calls_answered'            => 0,
                'incoming_total_calltime'   => 0,
                'calls_unanswered'          => 0,
                'calls_outgoing_answered'   => 0,
                'outgoing_total_calltime'   => 0,
                'calls_outgoing_unanswered' => 0,
                'hold_time_avg'             => 0,
            );
        }

        foreach ($hourly_call_stats as $s) {
            $hourly_stats[$s->hour]['calls_answered']            = $s->calls_answered;
            $hourly_stats[$s->hour]['incoming_total_calltime']   = $s->incoming_total_calltime;
            $hourly_stats[$s->hour]['calls_unanswered']          = $s->calls_unanswered;
            $hourly_stats[$s->hour]['calls_outgoing_answered']   = $s->calls_outgoing_answered;
            $hourly_stats[$s->hour]['outgoing_total_calltime']   = $s->outgoing_total_calltime;
            $hourly_stats[$s->hour]['calls_outgoing_unanswered'] = $s->calls_outgoing_unanswered;
            $hourly_stats[$s->hour]['hold_time_avg']             = ceil(($s->total_holdtime + $s->total_waittime) == 0 || $s->calls_unanswered == 0 ? 0 : ($s->total_holdtime + $s->total_waittime) / $s->calls_unanswered);
        }

        $rows_hours[] = array(
            lang('hour'),
            lang('calls_answered'),
            lang('incoming_talk_time_sum_overview'),
            lang('start_menu_calls_unanswered'),
            lang('calls_outgoing_answered'),
            lang('outgoing_talk_time_sum_overview'),
            lang('calls_outgoing_failed'),
            lang('hold_time')
        );

        for ($i = 10; $i < 24; $i++) {
            $h = $i < 10 ? '0' . $i : $i;
            $rows_hours[] = array(
                $h . ":00",
                $hourly_stats[$h]['calls_answered'],
                sec_to_time($hourly_stats[$h]['incoming_total_calltime']),
                $hourly_stats[$h]['calls_unanswered'],
                $hourly_stats[$h]['calls_outgoing_answered'],
                sec_to_time($hourly_stats[$h]['outgoing_total_calltime']),
                $hourly_stats[$h]['calls_outgoing_unanswered'],
                sec_to_time($hourly_stats[$h]['hold_time_avg']),
            );
        }

        for ($i = 0; $i < 10; $i++) {
            $h = $i < 10 ? '0' . $i : $i;
            $rows_hours[] = array(
                $h . ":00",
                $hourly_stats[$h]['calls_answered'],
                sec_to_time($hourly_stats[$h]['incoming_total_calltime']),
                $hourly_stats[$h]['calls_unanswered'],
                $hourly_stats[$h]['calls_outgoing_answered'],
                sec_to_time($hourly_stats[$h]['outgoing_total_calltime']),
                $hourly_stats[$h]['calls_outgoing_unanswered'],
                sec_to_time($hourly_stats[$h]['hold_time_avg']),
            );
        }

        /////////////////// ---------- END OFTIME SHEET ----------------//////////////////////////


        ////////////////// Category sheet //////////////////////////////////////////////////////////////
        // $category_call_stats = $this->Call_model->get_category_stats_for_start_page($queue_ids, $date_range);
        // foreach ($this->Call_category_model->get_all() as $c) {
        //     $cats[$c->id] = $c->name;
        // }

        // foreach ($category_call_stats as $c) {
        //     if ($c->category_id > 0) {
        //         if (array_key_exists($c->category_id, $cats)) {
        //             $category_stats[] = array('name' => $cats[$c->category_id], 'count' => $c->count);
        //         }
        //     }
        // }
        // $rows_categories[] = array(
        //     lang('call_category'),
        //     lang('amount'),
        // );
        // foreach ($category_stats as $c) {
        //     $rows_categories[] = array(
        //         $c['name'],
        //         $c['count'],
        //     );
        // }
        ////////////////// End category sheet /////////////////////////////////////////////////////

        $this->_prepare_headers('overview-'.date('Ymd-His').'.xlsx');
        

        $writer = new XLSXWriter();
        $writer->setAuthor('Quickqueues');
      
        // Set up formatting styles
        $style1     = array('font-style'=>'bold');
        $style2     = array('halign'=>'left', 'valign'=>'left');
        $style3     = array('border'=>'left,right,top,bottom', 'border-style'=>'medium');
        $writer->initializeSheet(lang('overview'),[70,10,10]);
        $writer->writeSheetRow(lang('overview'), $row_header, $style1, $style2, $style3);
        foreach($rows_overview as $row) 
        {
            $writer->writeSheetRow(lang('overview'), $row, $style2, $style3);
        }

        $writer->initializeSheet(lang('agents'),[70,15,15,5,30,5,15,5,30,5,20,5,30,30,30,40,40,40,40,30,20,40,40,40]);
        $writer->writeSheetRow(lang('agents'), $row_header,  $style1, $style2, $style3 );
        foreach($rows_agents as $row) 
        {
            $writer->writeSheetRow(lang('agents'), $row, $style2, $style3  );
        }

        $writer->initializeSheet(lang('queues'),[70,20,30,30,30,5,15,5,30,5,25,5,30,30,30,5,30,15,20,20,30,30,25,20,30,30,40,40,40,40,40,40,30,30]);
        $writer->writeSheetRow(lang('queues'), $row_header, $style1, $style2, $style3 );
        foreach($rows_queues as $row)
        {
            $writer->writeSheetRow(lang('queues'), $row, $style2,$style3 );
        }

        $writer->initializeSheet(lang('call_distrib_by_day'),[70,30,40,30,30,40,30,30]);
        $writer->writeSheetRow(lang('call_distrib_by_day'), $row_header, $style1, $style2, $style3 );
        foreach($rows_days as $row)
        {
            $writer->writeSheetRow(lang('call_distrib_by_day'), $row, $style2, $style3);
        }

        $writer->initializeSheet(lang('call_distrib_by_hour'),[70,30,40,30,30,40,30,30]);
        $writer->writeSheetRow(lang('call_distrib_by_hour'), $row_header, $style1, $style2, $style3);
        foreach($rows_hours as $row) 
        {
            $writer->writeSheetRow(lang('call_distrib_by_hour'), $row, $style2, $style3);
        }

        if ($this->data->config->app_call_categories == 'yes') 
        {
            $writer->initializeSheet(lang('call_distrib_by_category'),[70,30,30,30,30,30,30,30]);
            $writer->writeSheetRow(lang('call_distrib_by_category'), $row_header, $style1, $style2, $style3);
            foreach($rows_categories as $row) 
            {
                $writer->writeSheetRow(lang('call_distrib_by_category'), $row, $style2, $style3 );
            }
        }

        $writer->writeToStdOut();
        exit(0);
    }


    public function overview()
    {

        $date_gt = $this->input->get('date_gt') ? $this->input->get('date_gt') : QQ_TODAY_START;
        $date_lt = $this->input->get('date_lt') ? $this->input->get('date_lt') : QQ_TODAY_END;

        $row_header = array(lang('stats'). ' '.$date_gt.' > '.$date_lt);

        $queue_ids = array();

        foreach ($this->data->user_queues as $q) {
            array_push($queue_ids, $q->id);
        }

        $rows_overview = array();
        $rows_agents = array();
        $rows_queues = array();
        $rows_days = array();
        $rows_hours = array();
        $rows_categories = array();

        $calls_answered = $this->Call_model->count_by_complex(
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'event_type' => array('COMPLETECALLER', 'COMPLETEAGENT'),
            )
        );

        if ($this->data->config->app_mark_answered_elsewhere > 0) {
            $answered_elsewhere = $this->Call_model->count_by_complex(
                array(
                    'queue_id' => $queue_ids,
                    'date >' => $date_gt,
                    'date <' => $date_lt,
                    'event_type' => 'ABANDON',
                    'answered_elsewhere >' => 1
                )
            );

            $a_without_service = array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'called_back' => 'no',
                'event_type' => array('ABANDON', 'EXITEMPTY', 'EXITWITHTIMEOUT', 'EXITWITHKEY'),
                'answered_elsewhere' => 'isnull'
            );

            if ($this->data->config->app_ignore_abandon > 0) {
                $a_without_service['waittime >='] = $this->data->config->app_ignore_abandon;
            }

            $calls_without_service = $this->Call_model->count_by_complex($a_without_service);
            unset($a_without_service);

        }

        if ($this->data->config->app_ignore_abandon > 0) {
            $calls_unanswered_ignored = $this->Call_model->count_by_complex(
                array(
                    'queue_id' => $queue_ids,
                    'date >' => $date_gt,
                    'date <' => $date_lt,
                    'waittime <' => $this->data->config->app_ignore_abandon,
                    'event_type' => array('ABANDON', 'EXITEMPTY', 'EXITWITHTIMEOUT', 'EXITWITHKEY'),
                )
            );
        }

        $calls_unanswered = $this->Call_model->count_by_complex(
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'event_type' => array('ABANDON', 'EXITEMPTY', 'EXITWITHTIMEOUT', 'EXITWITHKEY'),
            )
        );

        $calls_abandon = $this->Call_model->count_by_complex(
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'event_type' => 'ABANDON',
            )
        );

        $calls_outgoing_internal = $this->Call_model->count_by_complex(
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'event_type' => array('OUT_FAILED', 'OUT_BUSY', 'OUT_NOANSWER', 'OUT_ANSWERED'),
                'LENGTH(dst) <=' => 4,
            )
        );

        $calls_outgoing_external = $this->Call_model->count_by_complex(
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'event_type' => array('OUT_FAILED', 'OUT_BUSY', 'OUT_NOANSWER', 'OUT_ANSWERED'),
                'LENGTH(dst) >' => 4,
            )
        );

        $calls_outgoing_external_answered = $this->Call_model->count_by_complex(
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'event_type' => 'OUT_ANSWERED',
                'LENGTH(dst) >' => 4,
            )
        );

        $calls_outgoing_external_failed = $this->Call_model->count_by_complex(
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'event_type' => array('OUT_FAILED', 'OUT_BUSY', 'OUT_NOANSWER'),
                'LENGTH(dst) >' => 4,
            )
        );

        $calls_duplicate = $this->Call_model->count_by_complex(
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'duplicate' => 'yes',
            )
        );

        $answered_within_10s = $this->Call_model->count_by_complex(
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'event_type' => array('COMPLETECALLER', 'COMPLETEAGENT'),
                'holdtime <' => 10
            )
        );

        $called_back = $this->Call_model->count_by_complex(
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'called_back' => 'yes',
            )
        );

        $called_back_nah = $this->Call_model->count_by_complex(
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'called_back' => 'nah',
                'event_type' => array('ABANDON', 'EXITEMPTY', 'WXITWITHKEY', 'EXITWITHTIMEOUT')
            )
        );
        $called_back_nop = $this->Call_model->count_by_complex(
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'called_back' => 'nop',
                'event_type' => array('ABANDON', 'EXITEMPTY', 'WXITWITHKEY', 'EXITWITHTIMEOUT')
            )
        );
        $called_back_no = $this->Call_model->count_by_complex(
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'called_back' => 'no',
                'event_type' => array('ABANDON', 'EXITEMPTY', 'WXITWITHKEY', 'EXITWITHTIMEOUT')
            )
        );


        $called_back_abandon = $this->Call_model->count_by_complex(
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'called_back' => 'yes',
                'event_type' => 'ABANDON',
            )
        );

        $calls_incoming = $this->Event_model->count_by_complex(
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'event_type' => array('INC_FAILED', 'INC_BUSY', 'INC_NOANSWER', 'INC_ANSWERED'),
            )
        );

        if ($this->data->config->app_track_outgoing != 'no') {
            $ct = array('COMPLETECALLER', 'COMPLETEAGENT', 'OUT_ANSWERED');
        } else {
            $ct = array('COMPLETECALLER', 'COMPLETEAGENT');
        }

        $total_calltime = $this->Event_model->sum_by_complex(
            'calltime',
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'event_type' => $ct
            )
        );

        $max_calltime = $this->Event_model->max_by_complex(
            'calltime',
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'event_type' => array('COMPLETECALLER', 'COMPLETEAGENT')
            )
        );

        $total_holdtime = $this->Event_model->sum_by_complex(
            'holdtime',
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt
            )
        );

        $max_holdtime = $this->Event_model->max_by_complex(
            'holdtime',
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'event_type' => array('COMPLETECALLER', 'COMPLETEAGENT')
            )
        );

        $total_ringtime = $this->Event_model->sum_by_complex(
            'ringtime',
            array(
                'queue_id' => $queue_ids,
                'event_type' => 'CONNECT',
                'date >' => $date_gt,
                'date <' => $date_lt
            )
        );

        $origposition_max = $this->Event_model->max_by_complex(
            'origposition',
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt
            )
        );

        $origposition = $this->Event_model->avg_by_complex(
            'origposition',
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt
            )
        );

        $calls_incomingoffwork = $this->Call_model->count_by_complex(
            array(
                'date >' => $date_gt,
                'date <' => $date_lt,
                'event_type' => array('INCOMINGOFFWORK'),
            )
        );

        $precision = $this->data->config->app_round_to_hundredth == 'yes' ? 2 : false;


        $rows_overview[] = array(lang('calls_total'), ($calls_answered + $calls_unanswered + $calls_outgoing_external + $calls_outgoing_internal));
        $rows_overview[] = array(lang('calls_answered'), $calls_answered, round($calls_answered / ($calls_answered + $calls_unanswered) * 100, $precision)."%");
        $rows_overview[] = array(lang('calls_answered_within_10s'), $answered_within_10s, round($answered_within_10s / $calls_answered * 100, $precision)."%");
        if ($this->data->config->app_mark_answered_elsewhere > 0) {
            $rows_overview[] = array(lang('answered_elsewhere'), $answered_elsewhere, round($answered_elsewhere / ($calls_answered + $calls_unanswered) *100, $precision)."%");
        }
        $rows_overview[] = array(lang('calls_unanswered'), $calls_unanswered, round($calls_unanswered / ($calls_answered + $calls_unanswered) *100, $precision)."%");
        if ($this->data->config->app_ignore_abandon > 0) {
            $rows_overview[] = array(lang('calls_unanswered').' (<'.$this->data->config->app_ignore_abandon.' '.lang('seconds').')', $calls_unanswered_ignored, round($calls_unanswered_ignored / ($calls_answered + $calls_unanswered), $precision)."%");
        }
        if ($this->data->config->app_mark_answered_elsewhere > 0) {
            $rows_overview[] = array(lang('calls_without_service'), $calls_without_service, round($calls_without_service / ($calls_answered + $calls_unanswered), $precision)."%");
        }
        $rows_overview[] = array(lang('called_back').' - '.lang('total'), $called_back);
        $rows_overview[] = array(lang('cb_nah'), $called_back_nah);
        $rows_overview[] = array(lang('cb_nop'), $called_back_nop);
        $rows_overview[] = array(lang('cb_no'), $called_back_no);
        $rows_overview[] = array(lang('calls_outgoing').' ('.lang('external').')', $calls_outgoing_external);
        $rows_overview[] = array(lang('calls_outgoing').' ('.lang('internal').')', $calls_outgoing_internal);
        $rows_overview[] = array(lang('calls_outgoing_answered'), $calls_outgoing_external_answered);
        $rows_overview[] = array(lang('calls_outgoing_failed'), $calls_outgoing_external_failed);

        $rows_overview[] = array(lang('calls_incoming'), $calls_incoming);
        $rows_overview[] = array(lang('duplicate_calls'), $calls_duplicate);
        $rows_overview[] = array(lang('calls_offwork'), $calls_incomingoffwork);
        $rows_overview[] = array(lang('call_time').' - '.lang('total'), sec_to_time($total_calltime));
        $rows_overview[] = array(lang('call_time').' - '.lang('max'), sec_to_time($max_calltime));
        $rows_overview[] = array(lang('call_time').' - '.lang('avg'), sec_to_time($total_calltime / $calls_answered));
        $rows_overview[] = array(lang('hold_time').' - '.lang('total'), sec_to_time($total_holdtime));
        $rows_overview[] = array(lang('hold_time').' - '.lang('max'), sec_to_time($max_holdtime));
        $rows_overview[] = array(lang('hold_time').' - '.lang('avg'), sec_to_time($total_holdtime / ($calls_answered + $calls_unanswered + $calls_outgoing_external)));
        $rows_overview[] = array(lang('calls_waiting').'('.lang('avg').')', $origposition);
        $rows_overview[] = array(lang('calls_waiting').'('.lang('max').')', $origposition_max);


        $calls = $this->Call_model->get_many_by_complex(
            array(
                'date > ' => $date_gt,
                'date < ' => $date_lt,
            )
        );

        $events = $this->Event_model->get_many_by_complex(
            array(
                'date > ' => $date_gt,
                'date < ' => $date_lt,
            )
        );

        foreach ($this->data->user_agents as $aid => $a) {
            $agent_stats[$a->id] = array(
                'agent_name' => $a->display_name." -".$a->extension,
                'calls_total' => 0,
                'calls_answered' => 0,
                'answered_within_10s' => 0,
                'calls_missed' => 0,
                'calls_outgoing' => 0,
                'calltime' => 0,
                'ringtime' => 0,
            );
        }

        foreach ($this->data->user_queues as $qid => $q) {
            $queue_stats[$q->id] = array(
                'queue_name' => $q->display_name,
                'calls_total' => 0,
                'calls_answered' => 0,
                'answered_within_10s' => 0,
                'calls_unanswered' => 0,
                'ABANDON' => 0,
                'EXITEMPTY' => 0,
                'EXITWITHTIMEOUT' => 0,
                'EXITWITHKEY' => 0,
                'calls_outgoing' => 0,
                'calltime' => 0,
                'holdtime' => 0,
            );
        }

        $a = array(
            'calls_answered' => 0,
            'calls_answered_within_10s' => 0,
            'calls_unanswered' => 0,
            'calls_unanswered_ignored' => 0,
            'calls_without_service' => 0,
            'calls_incomingoffwork' => 0,
            'answered_elsewhere' => 0,
            'calls_outgoing_internal' => 0,
            'calls_outgoing_external' => 0,
            'call_time' => 0,
            'hold_time' => 0,
        );

        $stats_by_day = array();
        $stats_by_hour = array();

        for ($i=0; $i < 24; $i++) {
            $h = $i < 10 ? '0'.$i : $i;
            $stats_by_hour[$h] = $a;
        }

        foreach ($calls as $c) {
            $d = date('Y-m-d', $c->timestamp);
            $h = date('H', $c->timestamp);

            if (!array_key_exists($d, $stats_by_day)) {
                $stats_by_day[$d] = $a;
            }
            if ($c->agent_id > 0 && in_array($c->queue_id, $queue_ids)) {
                $agent_stats[$c->agent_id]['calls_total']++;
            }
            if ($c->queue_id > 0 && in_array($c->queue_id, $queue_ids)) {
                $queue_stats[$c->queue_id]['calls_total']++;
            }
            if ($c->event_type == 'COMPLETECALLER' || $c->event_type == 'COMPLETEAGENT') {
                if (in_array($c->queue_id, $queue_ids)) {
                    $agent_stats[$c->agent_id]['calls_answered']++;
                    $queue_stats[$c->queue_id]['calls_answered']++;
                    $agent_stats[$c->agent_id]['calltime'] += $c->calltime;
                    $queue_stats[$c->queue_id]['calltime'] += $c->calltime;
                    $agent_stats[$c->agent_id]['ringtime'] += $c->ringtime;
                    $queue_stats[$c->queue_id]['holdtime'] += $c->holdtime;
                    $stats_by_day[$d]['calls_answered']++;
                    $stats_by_hour[$h]['calls_answered']++;
                    if ($c->holdtime < 10) {
                        $queue_stats[$c->queue_id]['answered_within_10s']++;
                        $stats_by_day[$d]['calls_answered_within_10s']++;
                        $stats_by_hour[$h]['calls_answered_within_10s']++;
                    }
                    if ($c->ringtime < 10) {
                        $agent_stats[$c->agent_id]['answered_within_10s']++;
                    }

                    $stats_by_day[$d]['call_time'] += $c->calltime;
                    $stats_by_day[$d]['hold_time'] += $c->holdtime;

                    $stats_by_hour[$h]['call_time'] += $c->calltime;
                    $stats_by_hour[$h]['hold_time'] += $c->holdtime;
                }
            }
            if ($c->event_type == 'ABANDON' && in_array($c->queue_id, $queue_ids)) {
                $queue_stats[$c->queue_id]['calls_unanswered']++;
                $stats_by_day[$d]['calls_unanswered']++;
                $stats_by_hour[$h]['calls_unanswered']++;
                $queue_stats[$c->queue_id]['ABANDON']++;
                if ($c->waittime >= $this->data->config->app_ignore_abandon && $c->answered_elsewhere < 1) {
                    $stats_by_day[$d]['calls_without_service']++;
                    $stats_by_hour[$h]['calls_without_service']++;
                }
                if ($this->data->config->app_ignore_abandon > $c->waittime) {
                    $stats_by_day[$d]['calls_unanswered_ignored']++;
                    $stats_by_hour[$h]['calls_unanswered_ignored']++;
                }
                if ($c->answered_elsewhere > 1) {
                    $stats_by_day[$d]['answered_elsewhere']++;
                    $stats_by_hour[$h]['answered_elsewhere']++;
                }
            }
            if ($c->event_type == 'EXITEMPTY' && in_array($c->queue_id, $queue_ids)) {
                $queue_stats[$c->queue_id]['calls_unanswered']++;
                $stats_by_day[$d]['calls_unanswered']++;
                $stats_by_hour[$h]['calls_unanswered']++;
                $queue_stats[$c->queue_id]['EXITEMPTY']++;
                if ($c->waittime > $this->data->config->app_ignore_abandon && $c->answered_elsewhere < 1) {
                    $stats_by_day[$d]['calls_without_service']++;
                    $stats_by_hour[$h]['calls_without_service']++;
                }
                if ($this->data->config->app_ignore_abandon > $c->waittime) {
                    $stats_by_day[$d]['calls_unanswered_ignored']++;
                    $stats_by_hour[$h]['calls_unanswered_ignored']++;
                }
                if ($c->answered_elsewhere > 1) {
                    $stats_by_day[$d]['answered_elsewhere']++;
                    $stats_by_hour[$h]['answered_elsewhere']++;
                }
            }
            if ($c->event_type == 'EXITWITHTIMEOUT' && in_array($c->queue_id, $queue_ids)) {
                $queue_stats[$c->queue_id]['calls_unanswered']++;
                $stats_by_day[$d]['calls_unanswered']++;
                $stats_by_hour[$h]['calls_unanswered']++;
                $queue_stats[$c->queue_id]['EXITWITHTIMEOUT']++;
                if ($c->waittime > $this->data->config->app_ignore_abandon && $c->answered_elsewhere < 1) {
                    $stats_by_day[$d]['calls_without_service']++;
                    $stats_by_hour[$h]['calls_without_service']++;
                }
                if ($this->data->config->app_ignore_abandon > $c->waittime) {
                    $stats_by_day[$d]['calls_unanswered_ignored']++;
                    $stats_by_hour[$h]['calls_unanswered_ignored']++;
                }
                if ($c->answered_elsewhere > 1) {
                    $stats_by_day[$d]['answered_elsewhere']++;
                    $stats_by_hour[$h]['answered_elsewhere']++;
                }
            }
            if ($c->event_type == 'EXITWITHKEY' && in_array($c->queue_id, $queue_ids)) {
                $queue_stats[$c->queue_id]['calls_unanswered']++;
                $stats_by_day[$d]['calls_unanswered']++;
                $stats_by_hour[$h]['calls_unanswered']++;
                $queue_stats[$c->queue_id]['EXITWITHKEY']++;
                if ($c->waittime > $this->data->config->app_ignore_abandon && $c->answered_elsewhere < 1) {
                    $stats_by_day[$d]['calls_without_service']++;
                    $stats_by_hour[$h]['calls_without_service']++;
                }
                if ($this->data->config->app_ignore_abandon > $c->waittime) {
                    $stats_by_day[$d]['calls_unanswered_ignored']++;
                    $stats_by_hour[$h]['calls_unanswered_ignored']++;
                }
                if ($c->answered_elsewhere > 1) {
                    $stats_by_day[$d]['answered_elsewhere']++;
                    $stats_by_hour[$h]['answered_elsewhere']++;
                }
            }
            if ($c->event_type == 'OUT_FAILED' || $c->event_type == 'OUT_BUSY' || $c->event_type == 'OUT_NOANSWER' || $c->event_type == 'OUT_ANSWERED') {
                if (in_array($c->queue_id, $queue_ids)) {
                    if (strlen($c->dst) > 4) {
                        $stats_by_day[$d]['calls_outgoing_external']++;
                        $stats_by_hour[$h]['calls_outgoing_external']++;

                    } else {
                        $stats_by_day[$d]['calls_outgoing_internal']++;
                        $stats_by_hour[$h]['calls_outgoing_internal']++;

                    }
                    $agent_stats[$c->agent_id]['calls_outgoing']++;
                    $queue_stats[$c->queue_id]['calls_outgoing']++;
                    if ($c->agent_id) {
                        $agent_stats[$c->agent_id]['calltime'] += $c->calltime;
                    }
                    if ($c->queue_id) {
                        $queue_stats[$c->queue_id]['calltime'] += $c->calltime;
                    }
                }
            }
            if ($c->event_type == 'INCOMINGOFFWORK') {
                $stats_by_day[$d]['calls_incomingoffwork']++;
                $stats_by_hour[$h]['calls_incomingoffwork']++;
            }
        }

        $track_ringnoanswer = $this->Config_model->get_item('app_track_ringnoanswer');

        foreach ($events as $e) {
            if ($e->event_type == 'RINGNOANSWER') {
                if ($track_ringnoanswer != 'no') {
                    if ($e->ringtime > 1) {
                        if (in_array($e->queue_id, $queue_ids)) {
                            $agent_stats[$e->agent_id]['calls_missed']++;
                        }
                    }
                }
            }
        }

        $rows_agents[] = array(
            lang('agent'),
            lang('calls_total'),
            lang('calls_answered'),
            lang('calls_answered_within_10s'),
            lang('calls_missed'),
            lang('calls_outgoing'),
            lang('call_time').' - '.lang('total'),
            lang('call_time').' - '.lang('avg'),
            lang('ring_time').' - '.lang('avg'),
        );
        foreach ($agent_stats as $i) {
            $rows_agents[] = array(
                $i['agent_name'],
                $i['calls_total'],
                $i['calls_answered'],
                $i['answered_within_10s'],
                $i['calls_missed'],
                $i['calls_outgoing'],
                sec_to_time($i['calltime']),
                sec_to_time(ceil($i['calltime'] / max($i['calls_answered'] + $i['calls_outgoing'], 1))),
                sec_to_time($i['calls_answered'] ==0 ? 0 : $i['ringtime'] / $i['calls_answered']),
            );
        }

        $rows_queues[] = array(
            lang('queue'),
            lang('calls_total'),
            lang('calls_answered'),
            lang('calls_answered_within_10s'),
            lang('calls_unanswered'),
            lang('ABANDON'),
            lang('EXITEMPTY'),
            lang('EXITWITHKEY'),
            lang('EXITWITHTIMEOUT'),
            lang('calls_outgoing'),
            lang('call_time').' - '.lang('total'),
            lang('call_time').' - '.lang('avg'),
            lang('hold_time').' - '.lang('total'),
        );

        foreach ($queue_stats as $i) {
            $rows_queues[] = array(
                $i['queue_name'],
                $i['calls_total'],
                $i['calls_answered'],
                $i['answered_within_10s'],
                $i['calls_unanswered'],
                $i['ABANDON'],
                $i['EXITEMPTY'],
                $i['EXITWITHKEY'],
                $i['EXITWITHTIMEOUT'],
                $i['calls_outgoing'],
                sec_to_time($i['calltime']),
                sec_to_time(ceil($i['calltime'] / max($i['calls_answered'] + $i['calls_outgoing'],1))),
                sec_to_time($i['holdtime']),
            );
        }

        $rows_days[] = array(
            lang('date'),
            lang('calls_total'),
            lang('calls_answered'),
            lang('calls_answered_within_10s'),
            lang('answered_elsewhere'),
            lang('calls_unanswered'),
            lang('calls_unanswered').' (<'.$this->data->config->app_ignore_abandon.' '.lang('seconds').')',
            lang('calls_without_service'),
            lang('calls_outgoing').'( '.lang('internal').')',
            lang('calls_outgoing').'( '.lang('external').')',
            lang('calls_offwork'),
            lang('call_time').' - '.lang('total'),
            lang('hold_time').' - '.lang('total'),

        );
        foreach ($stats_by_day as $e => $d) {
            $rows_days[] = array(
                $e,
                $d['calls_answered'] + $d['calls_unanswered'] + $d['calls_outgoing_external'] +$d['calls_outgoing_internal'] + $d['calls_incomingoffwork'],
                $d['calls_answered'],
                $d['calls_answered_within_10s'],
                $d['answered_elsewhere'],
                $d['calls_unanswered'],
                $d['calls_unanswered_ignored'],
                $d['calls_without_service'],
                $d['calls_outgoing_internal'],
                $d['calls_outgoing_external'],
                $d['calls_incomingoffwork'],
                sec_to_time($d['call_time']),
                sec_to_time($d['hold_time']),
            );
        }

        $rows_hours[] = array(
            lang('hour'),
            lang('calls_total'),
            lang('calls_answered'),
            lang('calls_answered_within_10s'),
            lang('answered_elsewhere'),
            lang('calls_unanswered'),
            lang('calls_unanswered').' (<'.$this->data->config->app_ignore_abandon.' '.lang('seconds').')',
            lang('calls_without_service'),
            lang('calls_outgoing').'( '.lang('internal').')',
            lang('calls_outgoing').'( '.lang('external').')',
            lang('calls_offwork'),
            lang('call_time').' - '.lang('total'),
            lang('hold_time').' - '.lang('total'),

        );
        foreach ($stats_by_hour as $e => $h) {
            $rows_hours[] = array(
                $e,
                $h['calls_answered'] + $h['calls_unanswered'] + $h['calls_outgoing_external'] + $h['calls_outgoing_internal'] + $h['calls_incomingoffwork'],
                $h['calls_answered'],
                $h['calls_answered_within_10s'],
                $h['answered_elsewhere'],
                $h['calls_unanswered'],
                $h['calls_unanswered_ignored'],
                $h['calls_without_service'],
                $h['calls_outgoing_internal'],
                $h['calls_outgoing_external'],
                $h['calls_incomingoffwork'],
                sec_to_time($h['call_time']),
                sec_to_time($h['hold_time']),
            );
        }

        $this->_prepare_headers('overview-'.date('Ymd-His').'.xlsx');

        $writer = new XLSXWriter();
        $writer->setAuthor('Quickqueues');

        $writer->writeSheetRow(lang('overview'), $row_header);
        foreach($rows_overview as $row) {
            $writer->writeSheetRow(lang('overview'), $row);
        }

        $writer->writeSheetRow(lang('agents'), $row_header);
        foreach($rows_agents as $row) {
            $writer->writeSheetRow(lang('agents'), $row);
        }

        $writer->writeSheetRow(lang('queues'), $row_header);
        foreach($rows_queues as $row) {
            $writer->writeSheetRow(lang('queues'), $row);
        }

        $writer->writeSheetRow(lang('call_distrib_by_day'), $row_header);
        foreach($rows_days as $row) {
            $writer->writeSheetRow(lang('call_distrib_by_day'), $row);
        }

        $writer->writeSheetRow(lang('call_distrib_by_hour'), $row_header);
        foreach($rows_hours as $row) {
            $writer->writeSheetRow(lang('call_distrib_by_hour'), $row);
        }

        if ($this->data->config->app_call_categories == 'yes') {
            $writer->writeSheetRow(lang('call_distrib_by_category'), $row_header);
            foreach($rows_categories as $row) {
                $writer->writeSheetRow(lang('call_distrib_by_category'), $row);
            }
        }

        $writer->writeToStdOut();
        exit(0);
    }


    public function overview_terabank()
    {

        $date_gt = $this->input->get('date_gt') ? $this->input->get('date_gt') : QQ_TODAY_START;
        $date_lt = $this->input->get('date_lt') ? $this->input->get('date_lt') : QQ_TODAY_END;

        $row_header = array(lang('stats'). ' '.$date_gt.' > '.$date_lt);

        $queue_ids = array();

        foreach ($this->data->user_queues as $q) {
            array_push($queue_ids, $q->id);
        }

        $rows_overview = array();
        $rows_agents = array();
        $rows_days = array();
        $rows_categories = array();


        $calls_answered = $this->Call_model->count_by_complex(
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'event_type' => array('COMPLETECALLER', 'COMPLETEAGENT'),
            )
        );

        if ($this->data->config->app_mark_answered_elsewhere > 0) {
            $answered_elsewhere = $this->Call_model->count_by_complex(
                array(
                    'queue_id' => $queue_ids,
                    'date >' => $date_gt,
                    'date <' => $date_lt,
                    'event_type' => 'ABANDON',
                    'answered_elsewhere >' => 1
                )
            );

            $a_without_service = array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'called_back' => 'no',
                'event_type' => array('ABANDON', 'EXITEMPTY', 'EXITWITHTIMEOUT', 'EXITWITHKEY'),
                'answered_elsewhere' => 'isnull'
            );

            if ($this->data->config->app_ignore_abandon > 0) {
                $a_without_service['waittime >='] = $this->data->config->app_ignore_abandon;
            }

            $calls_without_service = $this->Call_model->count_by_complex($a_without_service);
            unset($a_without_service);

        }

        if ($this->data->config->app_ignore_abandon > 0) {
            $calls_unanswered_ignored = $this->Call_model->count_by_complex(
                array(
                    'queue_id' => $queue_ids,
                    'date >' => $date_gt,
                    'date <' => $date_lt,
                    'waittime <' => $this->data->config->app_ignore_abandon,
                    'event_type' => array('ABANDON', 'EXITEMPTY', 'EXITWITHTIMEOUT', 'EXITWITHKEY'),
                )
            );
        }

        $calls_unanswered = $this->Call_model->count_by_complex(
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'event_type' => array('ABANDON', 'EXITEMPTY', 'EXITWITHTIMEOUT', 'EXITWITHKEY'),
            )
        );

        $calls_abandon = $this->Call_model->count_by_complex(
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'event_type' => 'ABANDON',
            )
        );

        $calls_outgoing_internal = $this->Call_model->count_by_complex(
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'event_type' => array('OUT_FAILED', 'OUT_BUSY', 'OUT_NOANSWER', 'OUT_ANSWERED'),
                'LENGTH(dst) <=' => 4,
            )
        );

        $calls_outgoing_external = $this->Call_model->count_by_complex(
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'event_type' => array('OUT_FAILED', 'OUT_BUSY', 'OUT_NOANSWER', 'OUT_ANSWERED'),
                'LENGTH(dst) >' => 4,
            )
        );

        $calls_duplicate = $this->Call_model->count_by_complex(
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'duplicate' => 'yes',
            )
        );

        $answered_within_10s = $this->Call_model->count_by_complex(
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'event_type' => array('COMPLETECALLER', 'COMPLETEAGENT'),
                'holdtime <' => 10
            )
        );

        $called_back = $this->Call_model->count_by_complex(
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'called_back' => 'yes',
            )
        );

        $called_back_abandon = $this->Call_model->count_by_complex(
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'called_back' => 'yes',
                'event_type' => 'ABANDON',
            )
        );

        $calls_incoming = $this->Event_model->count_by_complex(
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'event_type' => array('INC_FAILED', 'INC_BUSY', 'INC_NOANSWER', 'INC_ANSWERED'),
            )
        );

        $total_calltime = $this->Event_model->sum_by_complex(
            'calltime',
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'event_type' => array('COMPLETECALLER', 'COMPLETEAGENT')
            )
        );

        $max_calltime = $this->Event_model->max_by_complex(
            'calltime',
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'event_type' => array('COMPLETECALLER', 'COMPLETEAGENT')
            )
        );

        $total_holdtime = $this->Event_model->sum_by_complex(
            'holdtime',
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt
            )
        );

        $max_holdtime = $this->Event_model->max_by_complex(
            'holdtime',
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'event_type' => array('COMPLETECALLER', 'COMPLETEAGENT')
            )
        );

        $total_ringtime = $this->Event_model->sum_by_complex(
            'ringtime',
            array(
                'queue_id' => $queue_ids,
                'event_type' => 'CONNECT',
                'date >' => $date_gt,
                'date <' => $date_lt
            )
        );

        $origposition_max = $this->Event_model->max_by_complex(
            'origposition',
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt
            )
        );

        $origposition = $this->Event_model->avg_by_complex(
            'origposition',
            array(
                'queue_id' => $queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt
            )
        );

        $precision = $this->data->config->app_round_to_hundredth == 'yes' ? 2 : false;


        $rows_overview[] = array(lang('calls_total'), ($calls_answered + $calls_unanswered + $calls_outgoing_external + $calls_outgoing_internal));
        $rows_overview[] = array(lang('calls_answered'), $calls_answered, round($calls_answered / ($calls_answered + $calls_unanswered) * 100, $precision)."%");
        $rows_overview[] = array(lang('calls_answered_within_10s'), $answered_within_10s, round($answered_within_10s / $calls_answered * 100, $precision)."%");
        if ($this->data->config->app_mark_answered_elsewhere > 0) {
            $rows_overview[] = array(lang('answered_elsewhere'), $answered_elsewhere, round($answered_elsewhere / ($calls_answered + $calls_unanswered) *100, $precision)."%");
        }
        $rows_overview[] = array(lang('calls_unanswered'), $calls_unanswered, round($calls_unanswered / ($calls_answered + $calls_unanswered) *100, $precision)."%");
        if ($this->data->config->app_ignore_abandon > 0) {
            $rows_overview[] = array(lang('calls_unanswered').' (<'.$this->data->config->app_ignore_abandon.' '.lang('seconds').')', $calls_unanswered_ignored, round($calls_unanswered_ignored / ($calls_answered + $calls_unanswered), $precision)."%");
        }
        if ($this->data->config->app_mark_answered_elsewhere > 0) {
            $rows_overview[] = array(lang('calls_without_service'), $calls_without_service, round($calls_without_service / ($calls_answered + $calls_unanswered), $precision)."%");
        }
        $rows_overview[] = array(lang('called_back').' - '.lang('total'), $called_back);
        $rows_overview[] = array(lang('calls_outgoing').' ('.lang('external').')', $calls_outgoing_external);
        $rows_overview[] = array(lang('calls_outgoing').' ('.lang('internal').')', $calls_outgoing_internal);
        $rows_overview[] = array(lang('calls_incoming'), $calls_incoming);
        $rows_overview[] = array(lang('duplicate_calls'), $calls_duplicate);
        $rows_overview[] = array(lang('call_time').' - '.lang('total'), sec_to_time($total_calltime));
        $rows_overview[] = array(lang('call_time').' - '.lang('max'), sec_to_time($max_calltime));
        $rows_overview[] = array(lang('call_time').' - '.lang('avg'), sec_to_time($total_calltime / $calls_answered));
        $rows_overview[] = array(lang('hold_time').' - '.lang('total'), sec_to_time($total_holdtime));
        $rows_overview[] = array(lang('hold_time').' - '.lang('max'), sec_to_time($max_holdtime));
        $rows_overview[] = array(lang('hold_time').' - '.lang('avg'), sec_to_time($total_holdtime / ($calls_answered + $calls_unanswered + $calls_outgoing_external)));
        $rows_overview[] = array(lang('calls_waiting').'('.lang('avg').')', $origposition);
        $rows_overview[] = array(lang('calls_waiting').'('.lang('max').')', $origposition_max);


        $calls = $this->Call_model->get_many_by_complex(
            array(
                'date > ' => $date_gt,
                'date < ' => $date_lt
            )
        );

        $events = $this->Event_model->get_many_by_complex(
            array(
                'date > ' => $date_gt,
                'date < ' => $date_lt
            )
        );

        foreach ($this->data->user_agents as $aid => $a) {
            $agent_stats[$a->id] = array(
                'agent_name' => $a->display_name." -".$a->extension,
                'calls_total' => 0,
                'calls_answered' => 0,
                'answered_within_10s' => 0,
                'calls_missed' => 0,
                'calls_outgoing' => 0,
                'calltime' => 0,
                'ringtime' => 0,
            );
        }

        $a = array(
            'calls_answered' => 0,
            'calls_answered_within_10s' => 0,
            'calls_unanswered' => 0,
            'calls_unanswered_ignored' => 0,
            'calls_without_service' => 0,
            'answered_elsewhere' => 0,
            'calls_outgoing_internal' => 0,
            'calls_outgoing_external' => 0,
            'call_time' => 0,
            'hold_time' => 0,
        );

        $stats_by_day = array();

        foreach ($calls as $c) {
            $d = date('Y-m-d', $c->timestamp);
            if (!array_key_exists($d, $stats_by_day)) {
                $stats_by_day[$d] = $a;
            }
            if ($c->agent_id > 0) {
                $agent_stats[$c->agent_id]['calls_total']++;
            }
            if ($c->event_type == 'COMPLETECALLER' || $c->event_type == 'COMPLETEAGENT') {
                $agent_stats[$c->agent_id]['calls_answered']++;
                $agent_stats[$c->agent_id]['calltime'] += $c->calltime;
                $agent_stats[$c->agent_id]['ringtime'] += $c->ringtime;
                $stats_by_day[$d]['calls_answered']++;
                if ($c->holdtime < 10) {
                    $stats_by_day[$d]['calls_answered_within_10s']++;
                }
                if ($c->ringtime < 10) {
                    $agent_stats[$c->agent_id]['answered_within_10s']++;
                }

                $stats_by_day[$d]['call_time'] += $c->calltime;
                $stats_by_day[$d]['hold_time'] += $c->holdtime;


            }
            if ($c->event_type == 'ABANDON') {
                $stats_by_day[$d]['calls_unanswered']++;
                if ($c->waittime >= $this->data->config->app_ignore_abandon && $c->answered_elsewhere < 1) {
                    $stats_by_day[$d]['calls_without_service']++;
                }
                if ($this->data->config->app_ignore_abandon > $c->waittime) {
                    $stats_by_day[$d]['calls_unanswered_ignored']++;
                }
                if ($c->answered_elsewhere > 1) {
                    $stats_by_day[$d]['answered_elsewhere']++;
                }
            }
            if ($c->event_type == 'EXITEMPTY') {
                $stats_by_day[$d]['calls_unanswered']++;
                if ($c->waittime > $this->data->config->app_ignore_abandon && $c->answered_elsewhere < 1) {
                    $stats_by_day[$d]['calls_without_service']++;
                }
                if ($this->data->config->app_ignore_abandon > $c->waittime) {
                    $stats_by_day[$d]['calls_unanswered_ignored']++;
                }
                if ($c->answered_elsewhere > 1) {
                    $stats_by_day[$d]['answered_elsewhere']++;
                }
            }
            if ($c->event_type == 'EXITWITHTIMEOUT') {
                $stats_by_day[$d]['calls_unanswered']++;
                if ($c->waittime > $this->data->config->app_ignore_abandon && $c->answered_elsewhere < 1) {
                    $stats_by_day[$d]['calls_without_service']++;
                }
                if ($this->data->config->app_ignore_abandon > $c->waittime) {
                    $stats_by_day[$d]['calls_unanswered_ignored']++;
                }
                if ($c->answered_elsewhere > 1) {
                    $stats_by_day[$d]['answered_elsewhere']++;
                }
            }
            if ($c->event_type == 'EXITWITHKEY') {
                $stats_by_day[$d]['calls_unanswered']++;
                if ($c->waittime > $this->data->config->app_ignore_abandon && $c->answered_elsewhere < 1) {
                    $stats_by_day[$d]['calls_without_service']++;
                }
                if ($this->data->config->app_ignore_abandon > $c->waittime) {
                    $stats_by_day[$d]['calls_unanswered_ignored']++;
                }
                if ($c->answered_elsewhere > 1) {
                    $stats_by_day[$d]['answered_elsewhere']++;
                }
            }
            if ($c->event_type == 'OUT_FAILED' || $c->event_type == 'OUT_BUSY' || $c->event_type == 'OUT_NOANSWER' || $c->event_type == 'OUT_ANSWERED') {
                if (strlen($c->dst) > 4) {
                    $stats_by_day[$d]['calls_outgoing_external']++;
                } else {
                    $stats_by_day[$d]['calls_outgoing_internal']++;
                }
                $agent_stats[$c->agent_id]['calls_outgoing']++;
                if ($c->agent_id) {
                    $agent_stats[$c->agent_id]['calltime'] += $c->calltime;
                }
            }
        }

        $track_ringnoanswer = $this->Config_model->get_item('app_track_ringnoanswer');

        foreach ($events as $e) {
            if ($e->event_type == 'RINGNOANSWER') {
                if ($track_ringnoanswer != 'no') {
                    if ($e->ringtime > 1) {
                        $agent_stats[$e->agent_id]['calls_missed']++;
                    }
                }
            }
        }

        $rows_agents[] = array(
            lang('agent'),
            lang('calls_total'),
            lang('calls_answered'),
            lang('calls_answered_within_10s'),
            lang('calls_missed'),
            lang('calls_outgoing'),
            lang('call_time').' - '.lang('total'),
            lang('call_time').' - '.lang('avg'),
            lang('ring_time').' - '.lang('avg'),
        );
        foreach ($agent_stats as $i) {
            $rows_agents[] = array(
                $i['agent_name'],
                $i['calls_total'],
                $i['calls_answered'],
                $i['answered_within_10s'],
                $i['calls_missed'],
                $i['calls_outgoing'],
                sec_to_time($i['calltime']),
                sec_to_time(ceil($i['calltime'] / max($i['calls_answered'] + $i['calls_outgoing'], 1))),
                sec_to_time($i['calls_answered'] ==0 ? 0 : $i['ringtime'] / $i['calls_answered']),
            );
        }

        $rows_days[] = array(
            lang('date'),
            lang('calls_total'),
            lang('calls_answered'),
            lang('calls_answered_within_10s'),
            lang('answered_elsewhere'),
            lang('calls_unanswered'),
            lang('calls_unanswered').' (<'.$this->data->config->app_ignore_abandon.' '.lang('seconds').')',
            lang('calls_without_service'),
            lang('calls_outgoing').'( '.lang('internal').')',
            lang('calls_outgoing').'( '.lang('external').')',
            lang('call_time').' - '.lang('avg'),
            lang('hold_time').' - '.lang('avg'),

        );
        foreach ($stats_by_day as $e => $d) {
            $rows_days[] = array(
                $e,
                $d['calls_answered'] + $d['calls_unanswered'] + $d['calls_outgoing_external'],
                $d['calls_answered'],
                $d['calls_answered_within_10s'],
                $d['answered_elsewhere'],
                $d['calls_unanswered'],
                $d['calls_unanswered_ignored'],
                $d['calls_without_service'],
                $d['calls_outgoing_internal'],
                $d['calls_outgoing_external'],
                sec_to_time(ceil($d['call_time'] / $d['calls_answered'])),
                sec_to_time(ceil($d['hold_time'] / ($d['calls_answered'] + $d['calls_unanswered'] + $d['calls_outgoing_external']))),
            );
        }

        $this->_prepare_headers('overview-'.date('Ymd-His').'.xlsx');

        $writer = new XLSXWriter();
        $writer->setAuthor('Quickqueues');

        $writer->writeSheetRow(lang('overview'), $row_header);
        foreach($rows_overview as $row) {
            $writer->writeSheetRow(lang('overview'), $row);
        }

        $writer->writeSheetRow(lang('agents'), $row_header);
        foreach($rows_agents as $row) {
            $writer->writeSheetRow(lang('agents'), $row);
        }

        $writer->writeSheetRow(lang('call_distrib_by_day'), $row_header);
        foreach($rows_days as $row) {
            $writer->writeSheetRow(lang('call_distrib_by_day'), $row);
        }
        if ($this->data->config->app_call_categories == 'yes') {
            $writer->writeSheetRow(lang('call_distrib_by_category'), $row_header);
            foreach($rows_categories as $row) {
                $writer->writeSheetRow(lang('call_distrib_by_category'), $row);
            }
        }

        $writer->writeToStdOut();
        exit(0);
    }


    public function queue_stats($queue_id = false)
    {
        $date_gt    = $this->input->get('date_gt') ? $this->input->get('date_gt') : QQ_TODAY_START;
        $date_lt    = $this->input->get('date_lt') ? $this->input->get('date_lt') : QQ_TODAY_END;
        $date_range = array('date_gt' => $date_gt, 'date_lt' => $date_lt);
        $precision  = $this->data->config->app_round_to_hundredth == 'yes' ? 2 : false;
        $queue_id   = intval($this->input->get('queue_id'));
        $queue_array= $this->Queue_model->get_queue_entries();
        $queue = (object)$queue_array;

        foreach ($queue as $item) 
        {
            if ($item["id"] == ($this->input->get('queue_id'))) 
            {
                $queue_name = $item["display_name"];

                break;   
            }
        }
       
        
        $row_header = array(lang('stats').' ' .$date_gt.' > '.$date_lt.'('.lang('queue').': '.$queue_name.')');

        $rows_overview   = array();
        $rows_agents     = array();
        $rows_days       = array();
        $rows_hours      = array();
       

     
        ////////////////// -------- OVERVIEW SHEET ------/////////////////////////
        
        $total_stats = $this->Call_model->get_stats_for_start($queue_id, $date_range);
      
        // Total Calls
        $rows_overview[] = array(lang('calls_total'), ($total_stats->calls_answered + $total_stats->calls_unanswered + $total_stats->callback_request + $total_stats->calls_outgoing_answered + $total_stats->calls_outgoing_unanswered));
        
        // Unique Incoming Calls
        $rows_overview[] = array(lang('calls_unique_in'), ($total_stats->unique_incoming_calls_answered + $total_stats->unique_incoming_calls_unanswered));
        
        // Unique Users
        $rows_overview[] = array(lang('calls_unique_users'), $total_stats->unique_incoming_calls_answered);
        
        // Incoming Answered
        $calls_answered_percent        = $total_stats->calls_answered > 0 && ($total_stats->calls_answered + $total_stats->calls_unanswered) > 0 ? (($total_stats->calls_answered / ($total_stats->calls_answered + $total_stats->calls_unanswered)) * 100) : 0;
        $calls_answered_formatted_perc = intval($calls_answered_percent * 100) / 100;
        $rows_overview[] = array(lang('start_menu_calls_answered'), $total_stats->calls_answered, $calls_answered_formatted_perc);

        // SLA Less Then Or Equal To 10 Sec
        if ($total_stats->sla_count_less_than_or_equal_to_10 > 0 && $total_stats->calls_answered > 0) 
        {
            $percentage = ($total_stats->sla_count_less_than_or_equal_to_10 / $total_stats->calls_answered) * 100;
            $formatted_percentage = number_format($percentage, 2); // Format to 2 decimal places
        
            $rows_overview[] = array(
                lang('start_menu_sla_less_than_or_equal_to_10'),
                $total_stats->sla_count_less_than_or_equal_to_10,
                $formatted_percentage
            );
        }
        else
        {
            $rows_overview[] = array(lang('start_menu_sla_less_than_or_equal_to_10'),0,'0%');
        }
        
        // SLA Between 10 And 20 Sec
        if ($total_stats->sla_count_greater_than_10_and_less_than_or_equal_to_20 > 0 && $total_stats->calls_answered > 0) {
            $percentage = ($total_stats->sla_count_greater_than_10_and_less_than_or_equal_to_20 / $total_stats->calls_answered) * 100;
            $formatted_percentage = number_format($percentage, 2); // Format to 2 decimal places
        
            $rows_overview[] = array(
                lang('start_menu_sla_greater_than_10_less_then_or_equal_to_20'),
                $total_stats->sla_count_greater_than_10_and_less_than_or_equal_to_20,
                $formatted_percentage
            );
        }
        else
        {
            $rows_overview[] = array(lang('start_menu_sla_greater_than_10_less_then_or_equal_to_20'),0,'0%');
        }
        

        //  SLA Greater Then 20 Sec
        if ($total_stats->sla_count_greater_than_20 > 0 && $total_stats->calls_answered > 0) {
            $percentage = ($total_stats->sla_count_greater_than_20 / $total_stats->calls_answered) * 100;
            $formatted_percentage = number_format($percentage, 2); // Format to 2 decimal places
        
            $rows_overview[] = array(
                lang('start_menu_sla_greater_than_20'),
                $total_stats->sla_count_greater_than_20,
                $formatted_percentage
            );
        }
        else
        {
            $rows_overview[] = array(lang('start_menu_sla_greater_than_20'),0,'0%');
        }
        

        // Hold Time Max
        $total_hold_wait_time      = $total_stats->total_holdtime + $total_stats->total_waittime;
        $total_answered_unanswered = $total_stats->calls_answered + $total_stats->calls_unanswered + $total_stats->callback_request;

        if($total_hold_wait_time > 0 && $total_answered_unanswered > 0){
            $rows_overview[] = array(lang('hold_time').' ('.lang('max').')', sec_to_time(
                floor(
                    $total_stats->max_holdtime)
                )
            );
        }

        // Hold Time AVG

        if($total_hold_wait_time > 0 && $total_answered_unanswered > 0 && $total_stats->incoming_total_calltime_count > 0){
            $rows_overview[] = array(lang('hold_time').' ('.lang('avg').')', sec_to_time(
                floor(
                   ( $total_stats->total_holdtime + $total_stats->total_waittime) / ($total_stats->incoming_total_calltime_count))
                )
            );
        }
        
        // Incoming Unanswered
        $calls_unanswered_percent = $total_stats->calls_unanswered > 0 && ($total_stats->calls_answered + $total_stats->calls_unanswered) > 0 ?
        intval(($total_stats->calls_unanswered / ($total_stats->calls_answered + $total_stats->calls_unanswered) * 100) * 100) / 100 : '0%';

        $rows_overview[] = array(lang('start_menu_calls_unanswered'), $total_stats->calls_unanswered, $calls_unanswered_percent);
        
        // Callback Request
        $callback_request_percent = $total_stats->callback_request > 0 && ($total_stats->calls_answered + $total_stats->calls_unanswered + $total_stats->callback_request) > 0 ?
        intval(($total_stats->callback_request / ($total_stats->calls_answered + $total_stats->calls_unanswered + $total_stats->callback_request) * 100) * 100) / 100 : '0%';

        $rows_overview[] = array(lang('callback_request'), $total_stats->callback_request, $calls_unanswered_percent);


        // ATA AVG
        $rows_overview[] = array(lang('ata'), $total_stats->ata_count_total > 0 ? sec_to_time(
            floor($total_stats-> ata_total_waittime / $total_stats->ata_count_total)) : 0
        );
        
        // Without Service
        $rows_overview[] = array(lang('calls_without_service'), $total_stats->users_without_service . '(' . $total_stats->calls_without_service . ')');

        // Answered Elsewhere
        $rows_overview[] = array(lang('answered_elsewhere'), $total_stats->answered_elsewhere);

        // Answered Aoutcall - ნაპასუხები გადარეკილი
        $rows_overview[] = array(lang('answered_aoutcall'), $total_stats->called_back);

        // Outgoing Answered - ნაპასუხები გამავალი
        $rows_overview[] = array(lang('calls_outgoing_answered'), $total_stats->calls_outgoing_answered);
        
        // Outgoing Failed (Unanswered)
        $rows_overview[] = array(lang('calls_outgoing_failed'), $total_stats->calls_outgoing_unanswered);
        
        // Duplicated Calls
        $rows_overview[] = array(lang('duplicate_calls'), $total_stats->calls_duplicate);

        // Off Work
        $rows_overview[] = array(lang('start_menu_calls_offwork'), $total_stats->calls_offwork);
        
        // Incoming Talk Time SUM(Total)
        $rows_overview[] = array(lang('incoming_talk_time_sum_overview'), $total_stats->incoming_total_calltime_count > 0 ? sec_to_time($total_stats->incoming_total_calltime) : 0);
        
         // Incoming Talk Time AVG
        $rows_overview[] = array(lang('incoming_talk_time_avg'), $total_stats->incoming_total_calltime_count > 0 ? sec_to_time(
            floor($total_stats-> incoming_total_calltime / $total_stats->incoming_total_calltime_count)) : 0
        );
         // Incoming Talk Time Max
        $rows_overview[] = array(lang('incoming_talk_time_max'), $total_stats->incoming_total_calltime_count > 0 ? sec_to_time(
            floor($total_stats->incoming_max_calltime)) : 0
        );

        // Outgoing Talk Time SUM
        $rows_overview[] = array(lang('outgoing_talk_time_sum_overview'), $total_stats->outgoing_max_calltime > 0 ? sec_to_time(
            $total_stats-> outgoing_total_calltime) : 0
        );
        

        // Outgoing Talk Time AVG
        $rows_overview[] = array(lang('outgoing_talk_time_avg'), $total_stats->outgoing_total_calltime_count > 0 ? sec_to_time(
            floor($total_stats-> outgoing_total_calltime / $total_stats->outgoing_total_calltime_count)) : 0
        );
        // Outgoing Talk Time Max
        $rows_overview[] = array(lang('outgoing_talk_time_max'), $total_stats->outgoing_total_calltime_count > 0 ? sec_to_time(
            floor($total_stats->outgoing_max_calltime)) : 0
        );
        
        // Position In Queue AVG
        $rows_overview[] = array(lang('start_menu_calls_waiting').' ('. lang('avg').') ', ceil($total_stats->origposition_avg));

        // Position In Queue MAX
        $rows_overview[] = array(lang('start_menu_calls_waiting').' ('. lang('max').') ', $total_stats->origposition_max);;
        
        ////////////////// ----------- END OF OVERVIEW SHEET---------------/////////////////////////

        ////////////////// ------------ AGENTS SHEET ----------------///////////////////
        $agent_call_stats  = $this->Call_model->get_agent_stats_for_start_page($queue_id, $date_range);
        $agent_event_stats = $this->Event_model->get_agent_stats_for_start_page($queue_id, $date_range);
        $agent_pause_stats = $this->Event_model->get_agent_pause_stats_for_start_page($date_range);

        foreach ($this->Queue_model->get_agents($queue_id) as $a) {
            $agent_stats[$a->id] = array(
                'display_name'              => $a->display_name,
                'calls_answered'            => 0,
                'incoming_total_calltime'   => 0,
                'calls_missed'              => 0,
                'calls_outgoing_answered'   => 0,
                'outgoing_total_calltime'   => 0,
                'calls_outgoing_unanswered' => 0,
            );
        }

        foreach($agent_call_stats as $s) {
         

            
            $agent_stats[$s->agent_id]['calls_answered']            = $s->calls_answered;
            $agent_stats[$s->agent_id]['incoming_total_calltime']   = $s->incoming_total_calltime; 
            $agent_stats[$s->agent_id]['calls_outgoing_answered']   = $s->calls_outgoing_answered;
            $agent_stats[$s->agent_id]['outgoing_total_calltime']   = $s->outgoing_total_calltime;
            $agent_stats[$s->agent_id]['calls_outgoing_unanswered'] = $s->calls_outgoing_unanswered;
        
        }

        foreach ($agent_event_stats as $s) {
            $agent_stats[$s->agent_id]['calls_missed'] = $s->calls_missed;
        }
        foreach ($agent_pause_stats as $s) {
            $agent_stats[$s->agent_id]['total_pausetime'] = $s->total_pausetime;
        }
        $rows_agents[] = array(
            lang('agent'),
            lang('calls_answered'),
            lang('incoming_talk_time_sum_overview'),
            lang('ringnoanswer'),
            lang('calls_outgoing_answered'),
            lang('outgoing_talk_time_sum_overview'),
            lang('calls_outgoing_failed')
        );
       
        foreach ($agent_stats as $id => $i) {
            if ($id == 0) { continue; }
            $rows_agents[] = array(
                array_key_exists('display_name', $i) ? $i['display_name'] : "დაარქივებული",
                $i['calls_answered'],
                sec_to_time($i['incoming_total_calltime']),
                $i['calls_missed'],
                $i['calls_outgoing_answered'],
                sec_to_time($i['outgoing_total_calltime']),
                $i['calls_outgoing_unanswered'],

            );
        }

        ////////////////// ------ END OF AGENTS SHEET ------- /////////////////////

        ////////////////// ----------  DAY SHEET --------------////////////////////
        $start_date      = new DateTime($date_range['date_gt']);
        $end_date        = new DateTime($date_range['date_lt']);
        $interval        = new DateInterval('P1D'); // 1 day interval
        $date_range_list = new DatePeriod($start_date, $interval, $end_date);
        $dates           = [];
        foreach ($date_range_list as $date) 
        {
            $dates[] = $date->format('Y-m-d');
        }

        $daily_call_stats = $this->Call_model->get_daily_stats_for_start_page($queue_id, $date_range);
        $rows_days[] = array(
            lang('day'),
            lang('calls_answered'),
            lang('incoming_talk_time_sum_overview'),
            lang('calls_missed'),
            lang('calls_outgoing_answered'),
            lang('outgoing_talk_time_sum_overview'),
            lang('calls_outgoing_failed'),
            lang('hold_time')
        );
        

        // Fill in missing dates with default values
        foreach ($dates as $date) 
        {
            $found = false;
            foreach ($daily_call_stats as $i) 
            {
                if ($i->date == $date) 
                {
                    $found        = true;
                    $avg_holdtime = null;
                    // Calculate values as before
                    if (($i->total_holdtime + $i->total_waittime) > 0 && $i->calls_unanswered > 0)
                    {
                        $avg_holdtime = sec_to_time(($i->total_holdtime + $i->total_waittime) / $i->calls_unanswered);
                    }

                    $rows_days[] = array(
                        'day'                       => $i->date,
                        'calls_answered'            => $i->calls_answered,
                        'incoming_total_calltime'   =>sec_to_time($i->incoming_total_calltime),
                        'calls_missed'              => $i->calls_unanswered,
                        'calls_outgoing_answered'   => $i->calls_outgoing_answered,
                        'outgoing_total_calltime'   => sec_to_time($i->outgoing_total_calltime),
                        'calls_outgoing_unanswered' => $i->calls_outgoing_unanswered,
                        'avg_holdtime'              => $avg_holdtime,
                    );
                    break;
                }
            }
            

            if (!$found) {
                // If the date is not found in $daily_call_stats, set all parameters to 0
                $rows_days[] = array(
                    'day'                       => $date,
                    'calls_answered'            => 0,
                    'incoming_total_calltime'   => sec_to_time(0),
                    'calls_missed'              => 0,
                    'calls_outgoing_answered'   => 0,
                    'outgoing_total_calltime'   => sec_to_time(0),
                    'calls_outgoing_unanswered' => 0,
                    'avg_holdtime'              => '00:00:00',
                    
                );

            }
        }
         ////////////////// ----------  END OF DAY SHEET --------------////////////////////
         /////////////////// ----------TIME SHEET ----------------//////////////////////////
        
         $hourly_call_stats = $this->Call_model->get_hourly_stats_for_start_page($queue_id, $date_range);
      
        $hourly_stats = array();
        for ($i = 10; $i < 24; $i++) {
            $h = $i < 10 ? '0' . $i : $i;
            $hourly_stats[$h] = array(
                'calls_answered'            => 0,
                'incoming_total_calltime'   => 0,
                'calls_unanswered'          => 0,
                'calls_outgoing_answered'   => 0,
                'outgoing_total_calltime'   => 0,
                'calls_outgoing_unanswered' => 0,
                'hold_time_avg'             => 0,
            );
        }

        for ($i = 0; $i < 10; $i++) {
            $h = $i < 10 ? '0' . $i : $i;
            $hourly_stats[$h] = array(
                'calls_answered'            => 0,
                'incoming_total_calltime'   => 0,
                'calls_unanswered'          => 0,
                'calls_outgoing_answered'   => 0,
                'outgoing_total_calltime'   => 0,
                'calls_outgoing_unanswered' => 0,
                'hold_time_avg'             => 0,
            );
        }

        foreach ($hourly_call_stats as $s) {
            $hourly_stats[$s->hour]['calls_answered']            = $s->calls_answered;
            $hourly_stats[$s->hour]['incoming_total_calltime']   = $s->incoming_total_calltime;
            $hourly_stats[$s->hour]['calls_unanswered']          = $s->calls_unanswered;
            $hourly_stats[$s->hour]['calls_outgoing_answered']   = $s->calls_outgoing_answered;
            $hourly_stats[$s->hour]['outgoing_total_calltime']   = $s->outgoing_total_calltime;
            $hourly_stats[$s->hour]['calls_outgoing_unanswered'] = $s->calls_outgoing_unanswered;
            $hourly_stats[$s->hour]['hold_time_avg']             = ceil(($s->total_holdtime + $s->total_waittime) == 0 || $s->calls_unanswered == 0 ? 0 : ($s->total_holdtime + $s->total_waittime) / $s->calls_unanswered);
        }

        $rows_hours[] = array(
            lang('hour'),
            lang('calls_answered'),
            lang('incoming_talk_time_sum_overview'),
            lang('calls_missed'),
            lang('calls_outgoing_answered'),
            lang('outgoing_talk_time_sum_overview'),
            lang('calls_outgoing_failed'),
            lang('hold_time')
        );

        for ($i = 10; $i < 24; $i++) {
            $h = $i < 10 ? '0' . $i : $i;
            $rows_hours[] = array(
                $h . ":00",
                $hourly_stats[$h]['calls_answered'],
                sec_to_time($hourly_stats[$h]['incoming_total_calltime']),
                $hourly_stats[$h]['calls_unanswered'],
                $hourly_stats[$h]['calls_outgoing_answered'],
                sec_to_time($hourly_stats[$h]['outgoing_total_calltime']),
                $hourly_stats[$h]['calls_outgoing_unanswered'],
                sec_to_time($hourly_stats[$h]['hold_time_avg']),
            );
        }

        for ($i = 0; $i < 10; $i++) {
            $h = $i < 10 ? '0' . $i : $i;
            $rows_hours[] = array(
                $h . ":00",
                $hourly_stats[$h]['calls_answered'],
                sec_to_time($hourly_stats[$h]['incoming_total_calltime']),
                $hourly_stats[$h]['calls_unanswered'],
                $hourly_stats[$h]['calls_outgoing_answered'],
                sec_to_time($hourly_stats[$h]['outgoing_total_calltime']),
                $hourly_stats[$h]['calls_outgoing_unanswered'],
                sec_to_time($hourly_stats[$h]['hold_time_avg']),
            );
        }
         /////////////////// ---------- END OFTIME SHEET ----------------//////////////////////////
        $this->_prepare_headers('queue_' .$queue_name .'_stats-'.date('Ymd-His').'.xlsx');
        $writer = new XLSXWriter();
        $writer->setAuthor('Quickqueues');
      
        // Set up formatting styles
        $style1     = array('font-style'=>'bold');
        $style2     = array('halign'=>'left', 'valign'=>'left');
        $style3     = array(['border'=>'left','right','top','bottom'], ['border-style'=>'medium']);


       

        $writer->initializeSheet(lang('overview'),[90,10,10]);
        $writer->writeSheetRow(lang('overview'), $row_header, $style1, $style2, $style3);
        foreach($rows_overview as $row) 
        {
            $writer->writeSheetRow(lang('overview'), $row, $style2, $style3);
        }

        $writer->initializeSheet(lang('agents'),[90,30,40,30,30,40,30]);
        $writer->writeSheetRow(lang('agents'), $row_header,  $style1, $style2, $style3 );
        foreach($rows_agents as $row) 
        {
            $writer->writeSheetRow(lang('agents'), $row, $style2, $style3  );
        }

        $writer->initializeSheet(lang('call_distrib_by_day'),[90,30,40,30,30,40,30, 30]);
        $writer->writeSheetRow(lang('call_distrib_by_day'), $row_header, $style1, $style2, $style3 );
        foreach($rows_days as $row)
        {
            $writer->writeSheetRow(lang('call_distrib_by_day'), $row, $style2, $style3);
        }

        $writer->initializeSheet(lang('call_distrib_by_hour'),[90,30,40,30,30,40,30, 30]);
        $writer->writeSheetRow(lang('call_distrib_by_hour'), $row_header, $style1, $style2, $style3);
        foreach($rows_hours as $row) 
        {
            $writer->writeSheetRow(lang('call_distrib_by_hour'), $row, $style2, $style3);
        }

        $writer->writeToStdOut();
        exit(0);
    }


    public function agent_stats($agent_id = false)
    {

       
        $date_gt              = $this->input->get('date_gt') ? $this->input->get('date_gt') : QQ_TODAY_START;
        $date_lt              = $this->input->get('date_lt') ? $this->input->get('date_lt') : QQ_TODAY_END;
        $date_range           = array('date_gt' => $date_gt, 'date_lt' => $date_lt);
        $precision            = $this->data->config->app_round_to_hundredth == 'yes' ? 2 : false;
        $agent_id             = intval($this->input->get('agent_id'));
        $overall_stats_string = $this->input->get('overall_stats');
        $overall_stats        = json_decode($overall_stats_string);
        $rows_overview        = array();
        $rows_days            = array();
        $rows_hours           = array();
        
        
        
        
        ////////////////// -------- OVERVIEW SHEET ------/////////////////////////
        
        $total_stats         = $this->Call_model->get_agent_stats_for_agent_stats_page($agent_id, $date_range);
        $agent_event_stats   = $this->Event_model->get_agent_stats_for_agent_stats_page($agent_id, $date_range);
        $agent               = $this->Agent_model->get($agent_id);
        $row_header          = array(lang('stats').' '.$date_gt.' > '.$date_lt.'('.lang('agent').': '.$agent->display_name.')');
      
        // Last Call 
        $rows_overview[] = array(lang('last_call'), $agent->last_call);
       
        // Total Calls
        $overallCalls = (intval($overall_stats->calls_answered ) + intval($overall_stats->calls_unanswered)) + (intval($overall_stats->calls_outgoing_answered) + intval($overall_stats->calls_outgoing_unanswered));
        $agentCalls	  =  intval($total_stats->calls_answered) + intval($total_stats->calls_outgoing);
	   
        if ($agentCalls > 0 && $overallCalls > 0) 
        {
            $agent_calls_percent = intval((($agentCalls / $overallCalls) * 100) * 100) / 100;
        } 
        else 
        {
            $agent_calls_percent = '0';
        }
        
        $rows_overview[] = array(lang('calls_total'), $agentCalls, $agent_calls_percent);
        
        // Calls Answered

        $overall_calls_answered = intval($overall_stats->calls_answered);				   
		$agent_calls_answered	= intval($total_stats->calls_answered);
	    if($agent_calls_answered > 0 && $overall_calls_answered > 0) 
        {
            $agent_calls_answered_percent = intval((($agent_calls_answered / $overall_calls_answered) * 100) * 100) / 100;
        }
        else
        {
            $agent_calls_answered_percent = '0%';
        }
						  
        $rows_overview[] = array(lang('calls_answered'), $agent_calls_answered, $agent_calls_answered_percent);

        // SLA Less Then Or Equal To 10 Sec

        if ($total_stats->sla_count_less_than_or_equal_to_10 > 0 && $total_stats->calls_answered > 0) 
        {
            $percentage = ($total_stats->sla_count_less_than_or_equal_to_10 / $total_stats->calls_answered) * 100;
            $formatted_percentage = number_format($percentage, 2); // Format to 2 decimal places
        
            $rows_overview[] = array(
                lang('start_menu_sla_less_than_or_equal_to_10'),
                $total_stats->sla_count_less_than_or_equal_to_10,
                $formatted_percentage
            );
        }
        else
        {
            $rows_overview[] = array(lang('start_menu_sla_less_than_or_equal_to_10'),0,'0%');
        }
        
        // SLA Between 10 And 20 Sec
        if ($total_stats->sla_count_greater_than_10_and_less_than_or_equal_to_20 > 0 && $total_stats->calls_answered > 0) {
            $percentage = ($total_stats->sla_count_greater_than_10_and_less_than_or_equal_to_20 / $total_stats->calls_answered) * 100;
            $formatted_percentage = number_format($percentage, 2); // Format to 2 decimal places
        
            $rows_overview[] = array(
                lang('start_menu_sla_greater_than_10_less_then_or_equal_to_20'),
                $total_stats->sla_count_greater_than_10_and_less_than_or_equal_to_20,
                $formatted_percentage
            );
        }
        else
        {
            $rows_overview[] = array(lang('start_menu_sla_greater_than_10_less_then_or_equal_to_20'),0,'0%');
        }
        

        //  SLA Greater Then 20 Sec
        if ($total_stats->sla_count_greater_than_20 > 0 && $total_stats->calls_answered > 0) {
            $percentage = ($total_stats->sla_count_greater_than_20 / $total_stats->calls_answered) * 100;
            $formatted_percentage = number_format($percentage, 2); // Format to 2 decimal places
        
            $rows_overview[] = array(
                lang('start_menu_sla_greater_than_20'),
                $total_stats->sla_count_greater_than_20,
                $formatted_percentage
            );
        }
        else
        {
            $rows_overview[] = array(lang('start_menu_sla_greater_than_20'),0,'0%');
        }
        

        // Ring Time Avg
  

        if($total_stats->total_ringtime > 0 && $total_stats->incoming_total_calltime_count > 0)
        {
            $rows_overview[] = array(lang('ring_time').' ('.lang('avg').')', sec_to_time(
                floor(
                    $total_stats->total_ringtime/$total_stats->incoming_total_calltime_count)
                )
            );
        }

        // Ring Time Max

        if($total_stats->total_ringtime > 0 && $total_stats->incoming_total_calltime_count > 0)
        {
            $rows_overview[] = array(lang('ring_time').' ('.lang('max').')', sec_to_time(
                floor(
                    $total_stats->max_ringtime_answered)
                )
            );
        }
        
        // Calls Missed

        $rows_overview[] = array(lang('ringnoanswer'), $agent_event_stats->calls_missed);
          
        
        // Incoming Talk Time SUM(Total)
        $rows_overview[] = array(lang('incoming_talk_time_sum_overview'), $total_stats->incoming_total_calltime_count > 0 ? sec_to_time($total_stats->incoming_total_calltime) : 0);
        
        
         // Incoming Talk Time AVG
        $rows_overview[] = array(lang('incoming_talk_time_avg'), $total_stats->incoming_total_calltime_count > 0 ? sec_to_time(
            floor($total_stats-> incoming_total_calltime / $total_stats->incoming_total_calltime_count)) : 0
        );
        
         // Incoming Talk Time Max
        $rows_overview[] = array(lang('incoming_talk_time_max'), $total_stats->incoming_total_calltime_count > 0 ? sec_to_time(
            floor($total_stats->incoming_max_calltime)) : 0
        );

        // Outgoing Answered 
      
       $rows_overview[] = array(lang('calls_outgoing_answered'), (intval($total_stats->calls_outgoing_answered)));
       // Outgoing Unanswered 
      
       $rows_overview[] = array(lang('calls_outgoing_failed'), (intval($total_stats->calls_outgoing) - intval($total_stats->calls_outgoing_answered)));
        
       // Outgoing Talk Time SUM
        $rows_overview[] = array(lang('outgoing_talk_time_sum_overview'), $total_stats->outgoing_max_calltime > 0 ? sec_to_time(
            ($total_stats->outgoing_total_calltime)) : 0
        );
        

        // Outgoing Talk Time AVG
        $rows_overview[] = array(lang('outgoing_talk_time_avg'), $total_stats->outgoing_total_calltime_count > 0 ? sec_to_time(
            floor($total_stats-> outgoing_total_calltime / $total_stats->outgoing_total_calltime_count)) : 0
        );

        // Outgoing Talk Time Max
        $rows_overview[] = array(lang('outgoing_talk_time_max'), $total_stats->outgoing_total_calltime_count > 0 ? sec_to_time(
            floor($total_stats->outgoing_max_calltime)) : 0
        );
        
        ////////////////// ----------- END OF OVERVIEW SHEET---------------/////////////////////////

      
        ////////////////// ------ END OF AGENTS SHEET ------- /////////////////////

        ////////////////// ----------  DAY SHEET --------------////////////////////
        $start_date      = new DateTime($date_range['date_gt']);
        $end_date        = new DateTime($date_range['date_lt']);
        $interval        = new DateInterval('P1D'); // 1 day interval
        $date_range_list = new DatePeriod($start_date, $interval, $end_date);
        $dates           = [];
        foreach ($date_range_list as $date) 
        {
            $dates[] = $date->format('Y-m-d');
        }

        $daily_call_stats  = $this->Call_model->get_daily_stats_for_agent_page($agent_id, $date_range);
        $daily_event_stats = $this->Event_model->get_agent_daily_stats_for_agent_stats_page($agent_id, $date_range);
        $rows_days[] = array(
            lang('day'),
            lang('calls_answered'),
            lang('incoming_talk_time_sum_overview'),
            lang('ringnoanswer'),
            lang('calls_outgoing_answered'),
            lang('outgoing_talk_time_sum_overview'),
            lang('calls_outgoing_failed'),
            lang('hold_time')
        );
        

        // Fill in missing dates with default values
        foreach ($dates as $date) 
        {
             $rows_days[$date] = array(
                'day'                       => $date,
                'calls_answered'            => 0,
                'incoming_total_calltime'   => '00:00:00',
                'calls_missed'              => 0,
                'calls_outgoing_answered'   => 0,
                'outgoing_total_calltime'   => "00:00:00",
                'calls_outgoing_unanswered' => 0,
                'avg_holdtime'              => '00:00:00',
            );

            foreach($daily_event_stats as $e)
            {
                if($e->date)
                {
                    $rows_days[$e->date]['calls_missed']= $e->calls_missed;
                }
            }
            foreach ($daily_call_stats as $i) 
            {
                if ($i->date == $date) 
                { 
                    if ($rows_days[$i->date]['calls_missed'] == 0) 
                    {
                        $avg_holdtime = '00:00:00';
                    } 
                    else 
                    {
                        $avg_holdtime = sec_to_time(($i->total_holdtime + $i->total_waittime) / $rows_days[$i->date]['calls_missed']);
                    }

                    $rows_days[$i->date]['calls_answered']           = $i->calls_answered;
                    $rows_days[$i->date]['incoming_total_calltime']  = sec_to_time($i->incoming_total_calltime);
                    $rows_days[$i->date]['calls_outgoing_answered']  = $i->calls_outgoing_answered;
                    $rows_days[$i->date]['outgoing_total_calltime']  = sec_to_time($i->outgoing_total_calltime);
                    $rows_days[$i->date]['calls_outgoing_unanswered']= $i->calls_outgoing_unanswered;
                    $rows_days[$i->date]['avg_holdtime']             = $avg_holdtime;
  
                }
            }

        }
         ////////////////// ----------  END OF DAY SHEET --------------////////////////////
         /////////////////// ----------TIME SHEET ----------------//////////////////////////
        
        $hourly_call_stats = $this->Call_model->get_hourly_stats_for_agent_page($agent_id, $date_range);
        $hourly_event_stats= $this->Event_model->get_agent_hourly_stats_for_agent_stats_page($agent_id, $date_range);

        $hourly_stats = array();

        for ($i = 10; $i < 24; $i++) 
        {
            $h = $i < 10 ? '0' . $i : $i;
            $hourly_stats[$h] = array(
                'calls_answered'            => 0,
                'incoming_total_calltime'   => 0,
                'calls_missed'              => 0,
                'calls_outgoing_answered'   => 0,
                'outgoing_total_calltime'   => 0,
                'calls_outgoing_unanswered' => 0,
                'hold_time_avg'             => 0,
            );
        }

        for ($i = 0; $i < 10; $i++) 
        {
            $h = $i < 10 ? '0' . $i : $i;
            $hourly_stats[$h] = array(
                'calls_answered'            => 0,
                'incoming_total_calltime'   => 0,
                'calls_missed'              => 0,
                'calls_outgoing_answered'   => 0,
                'outgoing_total_calltime'   => 0,
                'calls_outgoing_unanswered' => 0,
                'hold_time_avg'             => 0,
            );
        }

        foreach($hourly_event_stats as $e)
        {
            if($e->hour)
            {
                $hourly_stats[$e->hour]['calls_missed'] = $e->calls_missed;
            }
        }

        foreach ($hourly_call_stats as $s) 
        {
            $hourly_stats[$s->hour]['calls_answered']            = $s->calls_answered;
            $hourly_stats[$s->hour]['incoming_total_calltime']   = $s->incoming_total_calltime;
            $hourly_stats[$s->hour]['calls_outgoing_answered']   = $s->calls_outgoing_answered;
            $hourly_stats[$s->hour]['outgoing_total_calltime']   = $s->outgoing_total_calltime;
            $hourly_stats[$s->hour]['calls_outgoing_unanswered'] = $s->calls_outgoing_unanswered;
            $hourly_stats[$s->hour]['hold_time_avg']             = ceil(($s->total_holdtime + $s->total_waittime) == 0 || $s->calls_unanswered == 0 ? 0 : ($s->total_holdtime + $s->total_waittime) / $s->calls_unanswered);
        }

        $rows_hours[] = array(
            lang('hour'),
            lang('calls_answered'),
            lang('incoming_talk_time_sum_overview'),
            lang('ringnoanswer'),
            lang('calls_outgoing_answered'),
            lang('outgoing_talk_time_sum_overview'),
            lang('calls_outgoing_failed'),
            lang('hold_time')
        );
        
        for ($i = 10; $i < 24; $i++) 
        {
            $h = $i < 10 ? '0' . $i : $i;
            $rows_hours[] = array(
                $h . ":00",
                $hourly_stats[$h]['calls_answered'],
                sec_to_time($hourly_stats[$h]['incoming_total_calltime']),
                $hourly_stats[$h]['calls_missed'],
                $hourly_stats[$h]['calls_outgoing_answered'],
                sec_to_time($hourly_stats[$h]['outgoing_total_calltime']),
                $hourly_stats[$h]['calls_outgoing_unanswered'],
                sec_to_time($hourly_stats[$h]['hold_time_avg']),
            );
        }

        for ($i = 0; $i < 10; $i++) {
            $h = $i < 10 ? '0' . $i : $i;
            $rows_hours[] = array(
                $h . ":00",
                $hourly_stats[$h]['calls_answered'],
                sec_to_time($hourly_stats[$h]['incoming_total_calltime']),
                $hourly_stats[$h]['calls_missed'],
                $hourly_stats[$h]['calls_outgoing_answered'],
                sec_to_time($hourly_stats[$h]['outgoing_total_calltime']),
                $hourly_stats[$h]['calls_outgoing_unanswered'],
                sec_to_time($hourly_stats[$h]['hold_time_avg']),
            );
        }
       

        

         /////////////////// ---------- END OFTIME SHEET ----------------//////////////////////////
        $this->_prepare_headers('agent_'.$agent->display_name.'_stats-'.date('Ymd-His').'.xlsx');
        

        $writer = new XLSXWriter();

        $writer->setAuthor('Quickqueues');
      
        // Set up formatting styles
        $style1     = array('font-style'=>'bold');
        $style2     = array('halign'=>'left', 'valign'=>'left');
        

       

        $writer->initializeSheet(lang('overview'),[90,30,15]);
        $writer->writeSheetRow(lang('overview'), $row_header, $style1, $style2);
        foreach($rows_overview as $row) 
        {
            $writer->writeSheetRow(lang('overview'), $row, $style2);
        }

        $writer->initializeSheet(lang('call_distrib_by_day'),[90,30,40,30,30,40,30,30]);
        $writer->writeSheetRow(lang('call_distrib_by_day'), $row_header, $style1, $style2);
        foreach($rows_days as $row)
        {
            $writer->writeSheetRow(lang('call_distrib_by_day'), $row, $style2);
        }

        $writer->initializeSheet(lang('call_distrib_by_hour'),[90,30,40,30,30,40,30,30]);
        $writer->writeSheetRow(lang('call_distrib_by_hour'), $row_header, $style1, $style2);
        foreach($rows_hours as $row) 
        {
            $writer->writeSheetRow(lang('call_distrib_by_hour'), $row, $style2);
        }

        $writer->writeToStdOut();
        exit(0);
    }


    public function agent_compare()
    {
        $date_gt = $this->input->get('date_gt') ? $this->input->get('date_gt') : QQ_TODAY_START;
        $date_lt = $this->input->get('date_lt') ? $this->input->get('date_lt') : QQ_TODAY_END;

        $stats = array();

        foreach ($this->data->user_agents as $a) {
            $stats[$a->id]['data'] = $a;

            $stats[$a->id]['calls_answered'] = $this->Call_model->count_by_complex(
                array(
                    'agent_id' => $a->id,
                    'date >' => $date_gt,
                    'date <' => $date_lt,
                    'queue_id' => $this->data->queue_ids,
                    'event_type' => array('COMPLETECALLER', 'COMPLETEAGENT'),
                )
            );

            if ($this->data->config->app_track_ringnoanswer != 'no') {
                $stats[$a->id]['calls_missed'] = $this->Event_model->count_by_complex(
                    array(
                        'agent_id'      => $a->id,
                        'date >'        => $date_gt,
                        'date <'        => $date_lt,
                        'event_type'    => 'RINGNOANSWER',
                        'queue_id'      => $this->data->queue_ids,
                        'ringtime >'    => 1,
                    )
                );
            }

            if ($this->data->config->app_track_outgoing != 'no') {
                $stats[$a->id]['calls_outgoing_external'] = $this->Call_model->count_by_complex(
                    array(
                        'agent_id'      => $a->id,
                        'date >'        => $date_gt,
                        'date <'        => $date_lt,
                        'event_type'    => array('OUT_BUSY', 'OUT_FAILED', 'OUT_ANSWERED', 'OUT_NOANSWER'),
                        'queue_id'      => $this->data->queue_ids,
                        'LENGTH(dst) >' => 4
                    )
                );
                $stats[$a->id]['calls_outgoing_internal'] = $this->Call_model->count_by_complex(
                    array(
                        'agent_id'      => $a->id,
                        'date >'        => $date_gt,
                        'date <'        => $date_lt,
                        'event_type'    => array('OUT_BUSY', 'OUT_FAILED', 'OUT_ANSWERED', 'OUT_NOANSWER'),
                        'queue_id'      => $this->data->queue_ids,
                        'LENGTH(dst) <='=> 4
                    )
                );
            }

            $stats[$a->id]['call_time'] = $this->Event_model->sum_by_complex(
                'calltime',
                array(
                    'agent_id'      => $a->id,
                    'date >'        => $date_gt,
                    'date <'        => $date_lt,
                    'queue_id'      => $this->data->queue_ids,
                )
            );

            $stats[$a->id]['ring_time'] = $this->Event_model->sum_by_complex(
                'ringtime',
                array(
                    'agent_id'      => $a->id,
                    'date >'        => $date_gt,
                    'date <'        => $date_lt,
                    'queue_id'      => $this->data->queue_ids,
                )
            );

            if ($this->data->config->app_track_agent_pause_time == 'yes') {
                $stats[$a->id]['pause_time'] = $this->Event_model->sum_by_complex(
                    'pausetime',
                    array(
                        'agent_id'      => $a->id,
                        'date >'        => $date_gt,
                        'date <'        => $date_lt,
                        'pausetime <'   => '28800', // Ignore large pauses, they are not pauses, rather end of work
                        'event_type'    => 'STOPPAUSE'
                    )
                );
            }

            $stats[$a->id]['days_with_calls'] = $this->Agent_model->count_days_with_calls($a->id, $date_gt, $date_lt);

        }

        $rows_total = array();
        $rows_total[] = array(
            lang('agent'),
            lang('calls_answered'),
            lang('calls_outgoing').' ('.lang('internal').')',
            lang('calls_outgoing').' ('.lang('external').')',
            lang('calls_missed'),
            lang('call_time'),
            lang('ring_time'),
            lang('pause_time'),
            lang('work_days')
        );

        $rows_avg = array();
        $rows_avg[] = array(
            lang('agent'),
            lang('calls_answered'),
            lang('calls_outgoing').' ('.lang('internal').')',
            lang('calls_outgoing').' ('.lang('external').')',
            lang('calls_missed'),
            lang('call_time'),
            lang('ring_time'),
            lang('pause_time')
        );

        foreach ($stats as $a) {
            $rows_total[] = array(
                $a['data']->display_name,
                $a['calls_answered'],
                $a['calls_outgoing_internal'],
                $a['calls_outgoing_external'],
                $a['calls_missed'],
                sec_to_time($a['call_time']),
                sec_to_time($a['ring_time']),
                sec_to_time($a['pause_time']),
                $a['days_with_calls']
            );

            $rows_avg[] = array(
                $a['data']->display_name,
                $a['days_with_calls'] == 0 ? 0 : $a['calls_answered'],
                $a['days_with_calls'] == 0 ? 0 : $a['calls_outgoing_internal'],
                $a['days_with_calls'] == 0 ? 0 : $a['calls_outgoing_external'],
                $a['days_with_calls'] == 0 ? 0 : $a['calls_missed'],
                $a['days_with_calls'] == 0 ? 0 : sec_to_time($a['call_time']),
                $a['days_with_calls'] == 0 ? 0 : sec_to_time($a['ring_time']),
                $a['days_with_calls'] == 0 ? 0 : sec_to_time($a['pause_time']),
                $a['days_with_calls'] == 0 ? 0 : $a['days_with_calls']
            );

        }

        $this->_prepare_headers('agent-compare_'.date('Ymd-His').'.xlsx');
        $writer = new XLSXWriter();
        $writer->setAuthor('Quickqueues');
        foreach($rows_total as $row) {
            $writer->writeSheetRow(lang('total'), $row);
        }
        foreach($rows_avg as $row) {
            $writer->writeSheetRow(lang('avg'), $row);
        }
        $writer->writeToStdOut();
        exit(0);

    }


    public function unique_callers()
    {
        $date_gt = $this->input->get('date_gt') ? $this->input->get('date_gt') : QQ_TODAY_START;
        $date_lt = $this->input->get('date_lt') ? $this->input->get('date_lt') : QQ_TODAY_END;

        $rows[] = array(
            lang('number'),
            lang('calls'),
        );

        $unique_callers = $this->Call_model->get_unique_fields_with_count_by_complex(
            'src',
            array(
                'queue_id' => $this->data->queue_ids,
                'date >' => $date_gt,
                'date <' => $date_lt,
                'event_type' => array('COMPLETECALLER', 'COMPLETEAGENT')
            )
        );

        foreach ($unique_callers as $u) {
            $rows[] = array($u->src, $u->count);
        }


        $this->_prepare_headers('unique-callers_'.date('Ymd-His').'.xlsx');
        $writer = new XLSXWriter();
        $writer->setAuthor('Quickqueues');
        foreach($rows as $row) {
            $writer->writeSheetRow(lang('total'), $row);
        }
        $writer->writeToStdOut();
        exit(0);
    }


    public function agent_historical_stats()
    {
        $call = $this->Call_model->get_first();

        $start_year = date('Y', $call->timestamp);
        $start_mnonth = date('m', $call->timestamp);

        $rows = array();

        $rows['header'] = array(lang('agent_historical_stats_monthly'));
        $rows['months'][] = "";
        for ($year = $start_year; $year <=date('Y'); $year++) {

            for ($month = 1; $month <= 12; $month++) {

                if ($month < 10 && strlen($month) != 2) { $month = '0'.$month; }
                if ($month == 12) { $month == '01'; }

                $rows['months'][] = "$year/$month";
            }
        }

        foreach ($this->Agent_model->get_all() as $a) {
            $rows[$a->display_name."-total"][] = $a->display_name;
            for ($year = $start_year; $year <=date('Y'); $year++) {
                for ($month = 1; $month <= 12; $month++) {

                    if ($month < 10 && strlen($month) != 2) { $month = '0'.$month; }
                    if ($month == 12) { $month == '01'; }
                    $total = $this->Call_model->count_by_complex(
                        array(
                            'agent_id'      => $a->id,
                            'date > '       => date('Y-m-01 00:00:00', strtotime("$year-$month-01")),
                            'date <'        => date('Y-m-t 00:00:00', strtotime("$year-$month-01")),
                            'event_type'    => array('COMPLETECALLER', 'COMPLETEAGENT'),
                        )
                    );

                    $rows[$a->display_name."-total"][] = $total;
                }
            }

        }

        $rows[] = array();
        $rows[] = array();
        $rows['subheader'] = array(lang('agent_historical_stats_daily'));
        foreach ($this->Agent_model->get_all() as $a) {
            $rows[$a->display_name."-daily"][] = $a->display_name;
            for ($year = $start_year; $year <=date('Y'); $year++) {
                for ($month = 1; $month <= 12; $month++) {

                    if ($month < 10 && strlen($month) != 2) { $month = '0'.$month; }
                    if ($month == 12) { $month == '01'; }
                    $total = $this->Call_model->count_by_complex(
                        array(
                            'agent_id'      => $a->id,
                            'date > '       => date('Y-m-01 00:00:00', strtotime("$year-$month-01")),
                            'date <'        => date('Y-m-t 00:00:00', strtotime("$year-$month-01")),
                            'event_type'    => array('COMPLETECALLER', 'COMPLETEAGENT'),
                        )
                    );

                    $rows[$a->display_name."-daily"][] = floor($total / date('t', strtotime("$year-$month-01")));
                }
            }

        }

        $this->_prepare_headers('agents-historical-data-'.date('Ymd-His').'.xlsx');
        $this->_write_xlsx($rows);

    }

    /* ---------- Category Export---------- */
    public function _write_xlsx_cell($rows = false)
    {
        $writer = new XLSXWriter();
        $writer->setAuthor('Quickqueues');

            $writer->writeSheetRow('Sheet1', $rows);

        $writer->writeToStdOut();
        exit(0);
    }

    // function category_export()
    // {
    //     $date_gt = $this->input->get('date_gt') ? $this->input->get('date_gt') : QQ_TODAY_START;
    //     $date_lt = $this->input->get('date_lt') ? $this->input->get('date_lt') : QQ_TODAY_END;

    //     header('Content-Encoding: UTF-16');
    //     header('Content-type: text/csv; charset=UTF-16');
    //     header('Content-Disposition: attachment; filename=Category.csv');
    //     header("Pragma: no-cache");
    //     header("Expires: 0");
    //     $handle = fopen('php://output', 'w');

    //     fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
    //     fputs($handle, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

    //     fputcsv($handle, array('პერიოდი: '.$date_gt.'დან'. $date_lt.'-მდე'));
    //     $main_subjects=$this->Call_subjects_model->get_main_subjects();

    //     foreach ($main_subjects as $main_subject) 
    //     {
    //         $subject_family = $main_subject['id'] . '|';

    //         //Calculate Child 1 amount
    //         $child_1_count = $this->Call_subjects_model->get_stat_parent_subjects($subject_family, $date_gt, $date_lt);
    //         if (count($child_1_count) > 0) 
    //         {
    //             //echo $main_subject['title'] . "-" . count($child_1_count);
    //             $main_subject_title=$main_subject['title'].'('.count($child_1_count).')';
    //             fputcsv($handle, array($main_subject_title));
    //         }

    //         //Child 1 subjects
    //         $child_1_subjects=$this->Call_subjects_model->get_child_1_subject_all(array('parent_id'=>$main_subject['id']));

    //         foreach ($child_1_subjects as $child_1_subject) 
    //         {
    //             $subject_family_1 = $main_subject['id'] . '|' . $child_1_subject['id'] . '|';

    //             //Calculate Child 2 amount
    //             $child_2_count = $this->Call_subjects_model->get_stat_parent_subjects($subject_family_1, $date_gt, $date_lt);
    //             if (count($child_2_count) > 0) 
    //             {
    //                 //echo $child_1_subject['title'] . "--" . count($child_2_count) . "=>";
    //                 $child_1_subject_title=$child_1_subject['title'] .'('.count($child_2_count).')';
    //                 fputcsv($handle, array('---', $child_1_subject_title));
    //             }

    //             //child 2 subjcets
    //             $child_2_subjects=$this->Call_subjects_model->get_child_2_subject(array('parent_id'=>$child_1_subject['id']));
    //             foreach ($child_2_subjects as $child_2_subject) 
    //             {
    //                 $subject_family_2 = $main_subject['id'] . '|' . $child_1_subject['id'] . '|' . $child_2_subject['id'] . '|';

    //                 //Calculate Child 3 amount
    //                 $child_3_count = $this->Call_subjects_model->get_stat_parent_subjects($subject_family_2, $date_gt, $date_lt);
    //                 if (count($child_3_count) > 0) 
    //                 {
    //                     //echo $child_2_subject['title'] . "---" . count($child_3_count) . "=>";
    //                     $child_2_subject_title=$child_2_subject['title'] .'('. count($child_3_count).')';
    //                     fputcsv($handle, array('---', '---', $child_2_subject_title));
    //                 }

    //                 //child 3 subjcets
    //                 $child_3_subjects=$this->Call_subjects_model->get_child_3_subject(array('parent_id'=>$child_2_subject['id']));
    //                 foreach ($child_3_subjects as $child_3_subject){
    //                     $subject_family_3=$main_subject['id'].'|'.$child_1_subject['id'].'|'.$child_2_subject['id'].'|'.$child_3_subject['id'].'|';

    //                     //Calculate Child 4 amount
    //                     $child_4_count=$this->Call_subjects_model->get_stat_parent_subjects($subject_family_3, $date_gt, $date_lt);

    //                     if(count($child_4_count)>0)
    //                     {
    //                         //echo $child_3_subject['title']."--".count($child_4_count)."<br>";
    //                         $child_3_subject_title=$child_3_subject['title'].'('.count($child_4_count).')';
    //                         fputcsv($handle, array('---', '---','---', $child_3_subject_title));
    //                     }
    //                 }
    //             }
    //         }
    //     }

    //     fclose($handle);
    // }

    function category_export()
    {

        $date_gt = $this->input->get('date_gt') ? $this->input->get('date_gt') : QQ_TODAY_START;
        $date_lt = $this->input->get('date_lt') ? $this->input->get('date_lt') : QQ_TODAY_END;

        $this->_prepare_headers('category_stats-'.date('Ymd-His').'.xlsx');
        $writer = new XLSXWriter();
        $writer->setAuthor('Quickqueues');


        $style1     = array('font-style'=>'bold');
        $style2     = array('halign'=>'left', 'valign'=>'left');
        $style3     = array(['border'=>'left','right','top','bottom'], ['border-style'=>'medium']);

        $row_header = array(lang('category_stats') . '-' . lang('period') . ':' . $date_gt . '-' . $date_lt);
        $writer->initializeSheet(lang('categories'),[120,30,30,30,30]);
        $writer->writeSheetRow(lang('categories'), $row_header, $style1, $style2, $style3);

        $main_subjects = $this->Call_subjects_model->get_main_subjects();

        // Loop through each main subject

        foreach ($main_subjects as $main_subject) 
        {
            // Build the subject_family for the main subject
            $subject_family = $main_subject['id'] . '|';
           
            // Calculate and display Child 1 amount
            $child_1_count = $this->Call_subjects_model->get_stat_parent_subjects($subject_family, $date_gt, $date_lt);

            $child_1_counter = 0;

            foreach($child_1_count as $child_count)
            {
                if($child_count['subject_family'] === $subject_family)
                {
                    $child_1_counter++;
                }
            }

            if (count($child_1_count) > 0 ) 
            {
                
                $main_subject_with_child = count($child_1_count) - $child_1_counter;

                if($main_subject_with_child > 0)
                {

                    $main_subject_title_2 = $main_subject['title'] . '(' . $main_subject_with_child  . ')';
                    $writer->writeSheetRow(lang('categories'), array($main_subject_title_2),$style2, $style3);

                    if($child_1_counter > 0)
                    {
                        $main_subject_title_1 = $main_subject['title'] . '(' . $child_1_counter  . ')';
                        $writer->writeSheetRow(lang('categories'), array($main_subject_title_1),$style2, $style3);
                    }
                }
                elseif($main_subject_with_child === 0)
                {

                    $main_subject_title_3 = $main_subject['title'] . '(' . count($child_1_count)  . ')';
                    $writer->writeSheetRow(lang('categories'), array($main_subject_title_3),$style2, $style3);
                }
                
            }

            // Get Child 1 subjects for the current main subject
            $child_1_subjects = $this->Call_subjects_model->get_child_1_subject_all(array('parent_id'=>$main_subject['id']));

            // Loop through each Child 1 subject
            foreach ($child_1_subjects as $child_1_subject) 
            {
                // Build the subject_family for Child 1 subject
                $subject_family_1 = $main_subject['id'] . '|' . $child_1_subject['id'] . '|';

                // Calculate and display Child 2 amount
                $child_2_count  = $this->Call_subjects_model->get_stat_parent_subjects($subject_family_1, $date_gt, $date_lt);
                $child_2_counter= 0;
                foreach($child_2_count as $child_count)
                {
             
                    if($child_count['subject_family'] === $subject_family_1)
                    {
                        $child_2_counter++;
                    }
                }
     
    
                if (count($child_2_count) > 0) 
                {
                    $child_1_subject_with_child = count($child_2_count) - $child_2_counter;

                    if($child_1_subject_with_child > 0)
                    {
                        $child_1_subject_title_2    = $child_1_subject['title'] . '(' . $child_1_subject_with_child . ')';
                        $writer->writeSheetRow(lang('categories'), array('---', $child_1_subject_title_2),$style2, $style3);
                        
                        if($child_2_counter > 0)
                        {
                            $child_1_subject_title_1    = $child_1_subject['title'] . '(' . $child_2_counter  . ')';
                            $writer->writeSheetRow(lang('categories'), array('---', $child_1_subject_title_1),$style2, $style3);
                        }

                    }
                    elseif($child_1_subject_with_child === 0)
                    {

                        $child_1_subject_title_3  = $child_1_subject['title'] . '(' . count($child_2_count) . ')';
                        $writer->writeSheetRow(lang('categories'), array('---', $child_1_subject_title_3),$style2, $style3);
                    }
                }

                // Get Child 2 subjects for the current Child 1 subject
                $child_2_subjects = $this->Call_subjects_model->get_child_2_subject(array('parent_id'=>$child_1_subject['id']));

                // Loop through each Child 2 subject
                foreach ($child_2_subjects as $child_2_subject) 
                {
                    // Build the subject_family for Child 2 subject
                    $subject_family_2 = $main_subject['id'] . '|' . $child_1_subject['id'] . '|' . $child_2_subject['id'] . '|';

                    // Calculate and display Child 3 amount
                    $child_3_count   = $this->Call_subjects_model->get_stat_parent_subjects($subject_family_2, $date_gt, $date_lt);
                    $child_3_counter = 0;

                    foreach($child_3_count as $child_count)
                    {
                        if($child_count['subject_family'] === $subject_family_2)
                        {
                            $child_3_counter++;
                        }
                    }
                    if (count($child_3_count) > 0) 
                    {
                        $child_2_subject_with_child = count($child_3_count) - $child_3_counter;
                        if($child_2_subject_with_child > 0)
                        {
                            $child_2_subject_title_2 = $child_2_subject['title'] . '(' . $child_2_subject_with_child . ')';
                            $writer->writeSheetRow(lang('categories'), array('---', '---', $child_2_subject_title_2),$style2, $style3);  
                            if($child_3_counter > 0)
                            {
                                $child_2_subject_title_1 = $child_2_subject['title'] . '(' . $child_3_counter . ')';
                                $writer->writeSheetRow(lang('categories'), array('---', '---', $child_2_subject_title_1),$style2, $style3);
                            }
                        }
                        elseif($child_2_subject_with_child)
                        {

                            $child_2_subject_title_3 = $child_2_subject['title'] . '(' . count($child_3_count) . ')';
                            $writer->writeSheetRow(lang('categories'), array('---', '---', $child_2_subject_title_3),$style2, $style3);   
                        }
                    }

                    // Get Child 3 subjects for the current Child 2 subject
                    $child_3_subjects = $this->Call_subjects_model->get_child_3_subject(array('parent_id'=>$child_2_subject['id']));

                    // Loop through each Child 3 subject
                    foreach ($child_3_subjects as $child_3_subject)
                    {
                        // Build the subject_family for Child 3 subject
                        $subject_family_3 = $main_subject['id'].'|'.$child_1_subject['id'].'|'.$child_2_subject['id'].'|'.$child_3_subject['id'].'|';

                        // Calculate and display Child 4 amount
                        $child_4_count = $this->Call_subjects_model->get_stat_parent_subjects($subject_family_3, $date_gt, $date_lt);

                        if (count($child_4_count) > 0)
                        {
                            $child_3_subject_title = $child_3_subject['title'] . '(' . count($child_4_count) . ')';
                            $writer->writeSheetRow(lang('categories'), array('---', '---','---', $child_3_subject_title),$style2, $style3);
                        }
                    }
                }
            }
        }
        $writer->writeToStdOut();
        exit(0);
    }

     //Visualisation for testing
    // function category_show(){
    //     $main_subjects=$this->Call_subjects_model->get_main_subjects();
    //     //Main subjects
    //     foreach ($main_subjects as $main_subject){
    //         $subject_family=$main_subject['id'].'|';

    //         //Calculate Child 1 amount
    //         $child_1_count=$this->Call_subjects_model->get_stat_parent_subjects($subject_family);
    //         if(count($child_1_count)>0){
    //             echo $main_subject['title']."-".count($child_1_count)."=>";
    //         }

    //         //Child 1 subjects
    //         $child_1_subjects=$this->Call_subjects_model->get_child_1_subject_all(array('parent_id'=>$main_subject['id']));

    //         foreach ($child_1_subjects as $child_1_subject){
    //             $subject_family_1=$main_subject['id'].'|'.$child_1_subject['id'].'|';

    //             //Calculate Child 2 amount
    //             $child_2_count=$this->Call_subjects_model->get_stat_parent_subjects($subject_family_1);
    //             if(count($child_2_count)>0){
    //                 echo $child_1_subject['title']."--".count($child_2_count)."=>";
    //             }

    //             //child 2 subjcets
    //             $child_2_subjects=$this->Call_subjects_model->get_child_2_subject(array('parent_id'=>$child_1_subject['id']));
    //             foreach ($child_2_subjects as $child_2_subject){
    //                 $subject_family_2=$main_subject['id'].'|'.$child_1_subject['id'].'|'.$child_2_subject['id'].'|';

    //                 //Calculate Child 3 amount
    //                 $child_3_count=$this->Call_subjects_model->get_stat_parent_subjects($subject_family_2);
    //                 if(count($child_3_count)>0){
    //                     echo $child_2_subject['title']."---".count($child_3_count)."=>";
    //                 }

    //                 //child 3 subjcets
    //                 $child_3_subjects=$this->Call_subjects_model->get_child_3_subject(array('parent_id'=>$child_2_subject['id']));
    //                 foreach ($child_3_subjects as $child_3_subject){
    //                     $subject_family_3=$main_subject['id'].'|'.$child_1_subject['id'].'|'.$child_2_subject['id'].'|'.$child_3_subject['id'].'|';

    //                     //Calculate Child 4 amount
    //                     $child_4_count=$this->Call_subjects_model->get_stat_parent_subjects($subject_family_3);

    //                     if(count($child_4_count)>0){
    //                         echo $child_3_subject['title']."--".count($child_4_count)."<br>";
    //                     }
    //                 }
    //             }
    //         }
    //     }
    // }

}
