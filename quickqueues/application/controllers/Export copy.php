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

        $queue_ids = array();
        foreach ($tqueues as $tqid => $tqname) {
            $queue_ids[] = $tqid;
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

            foreach ($calls as $c) {
                //$category_export_permission= $this->data->config->app_call_categories;
                if($category_export_permission == 'yes') {
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
                $c->queue_id ? $tqueues[$c->queue_id] : "",
                $c->agent_id ? $tagents[$c->agent_id] : "",
                $c->src,
                $c->dst,
                $c->event_type,
                sec_to_time($c->calltime),
                sec_to_time($c->holdtime),
                $c->comment,
                $category_export_permission == 'yes' ? $empty_subject_family[0] : '',
                $category_export_permission == 'yes' ? $empty_subject_family[1] : '',
                $category_export_permission == 'yes' ? $empty_subject_family[2] : '',
                $category_export_permission == 'yes' ? $empty_subject_family[3] : '',
            );
        }
       /* echo "<pre>";
            print_r($rows);
        echo "</pre>";*/
        $this->_prepare_headers('recordings-'.date('Ymd-His').'.xlsx');
        $writer     = new XLSXWriter();
        $writer->setAuthor('Quickqueues');
        $row_header = array(lang('stats'). ' '.$where['date >'].' > '.$where['date <']);
      
        // Set up formatting styles
        $style1     = array('font-style'=>'bold');
        $style2     = array('halign'=>'left', 'valign'=>'left');
        $style3     = array('border'=>'left,right,top,bottom', 'border-style'=>'medium');

        $writer->initializeSheet(lang('recordings'),[50, 40, 40, 40, 40, 40, 40, 40, 50]);
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
        $date_gt = $this->input->get('date_gt') ? $this->input->get('date_gt') : QQ_TODAY_START;
        $date_lt = $this->input->get('date_lt') ? $this->input->get('date_lt') : QQ_TODAY_END;
        $date_range = array('date_gt' => $date_gt, 'date_lt' => $date_lt);
        $precision = $this->data->config->app_round_to_hundredth == 'yes' ? 2 : false;

        $row_header = array(lang('stats'). ' '.$date_gt.' > '.$date_lt);

        $queue_ids = array();

        foreach ($this->data->user_queues as $q) {
            array_push($queue_ids, $q->id);
        }

        $rows_overview   = array();
        $rows_agents     = array();
        $rows_queues     = array();
        $rows_days       = array();
        $rows_hours      = array();
        $rows_categories = array();

        ////////////////// -------- OVERVIEW SHEET ------/////////////////////////

        $total_stats = $this->Call_model->get_stats_for_start($queue_ids, $date_range);
      
        $sla_total_count_sum = $total_stats->sla_count_total;

        // Total Calls
        $rows_overview[] = array(lang('calls_total'), ($total_stats->calls_answered + $total_stats->calls_unanswered + $total_stats->calls_outgoing_answered + $total_stats->calls_outgoing_unanswered));
        
        // Unique Incoming Calls
        $rows_overview[] = array(lang('calls_unique_in'), ($total_stats->unique_incoming_calls_answered + $total_stats->unique_incoming_calls_unanswered));
        
        // Unique Users
        $rows_overview[] = array(lang('calls_unique_users'), $total_stats->unique_incoming_calls_answered);
        
        // Incoming Answered
        $rows_overview[] = array(lang('start_menu_calls_answered'), $total_stats->calls_answered);

        // SLA Less Then Or Equal To 10 Sec
        if ($total_stats->sla_count_less_than_or_equal_to_10 > 0 && $sla_total_count_sum > 0) 
        {
            $percentage = ($total_stats->sla_count_less_than_or_equal_to_10 / $total_stats->sla_count_total) * 100;
            $formatted_percentage = number_format($percentage, 2) . "%"; // Format to 2 decimal places
        
            $rows_overview[] = array(
                lang('start_menu_sla_less_than_or_equal_to_10'),
                $total_stats->sla_count_less_than_or_equal_to_10,
                $formatted_percentage
            );
        }
        
        // SLA Between 10 And 20 Sec
        if ($total_stats->sla_count_greater_than_10_and_less_than_or_equal_to_20 > 0 && $sla_total_count_sum > 0) {
            $percentage = ($total_stats->sla_count_greater_than_10_and_less_than_or_equal_to_20 / $total_stats->sla_count_total) * 100;
            $formatted_percentage = number_format($percentage, 2) . "%"; // Format to 2 decimal places
        
            $rows_overview[] = array(
                lang('start_menu_sla_greater_than_10_less_then_or_equal_to_20'),
                $total_stats->sla_count_greater_than_10_and_less_than_or_equal_to_20,
                $formatted_percentage
            );
        }
        

        //  SLA Greater Then 20 Sec
        if ($total_stats->sla_count_greater_than_20 > 0 && $sla_total_count_sum > 0) {
            $percentage = ($total_stats->sla_count_greater_than_20 / $total_stats->sla_count_total) * 100;
            $formatted_percentage = number_format($percentage, 2) . "%"; // Format to 2 decimal places
        
            $rows_overview[] = array(
                lang('start_menu_sla_greater_than_20'),
                $total_stats->sla_count_greater_than_20,
                $formatted_percentage
            );
        }
        

        // Hold Time Max
        $total_hold_wait_time      = $total_stats->total_holdtime + $total_stats->total_waittime;
        $total_answered_unanswered = $total_stats->calls_answered + $total_stats->calls_unanswered;

        if($total_hold_wait_time > 0 && $total_answered_unanswered > 0){
            $rows_overview[] = array(lang('hold_time').' ('.lang('max').')', sec_to_time(
                floor(
                    $total_stats->max_holdtime)
                )
            );
        }

        // Hold Time AVG

        if($total_hold_wait_time > 0 && $total_answered_unanswered > 0){
            $rows_overview[] = array(lang('hold_time').' ('.lang('avg').')', sec_to_time(
                floor(
                    $total_stats->total_holdtime / $total_stats->incoming_total_calltime_count)
                ),
            );
        }
        
       
       // Incoming Unanswered
        $calls_unanswered_percent = $total_stats->calls_unanswered > 0 && ($total_stats->calls_answered + $total_stats->calls_unanswered) > 0 ?
        (number_format(($total_stats->calls_unanswered / ($total_stats->calls_answered + $total_stats->calls_unanswered) * 100), 2) . '%') : '0%';

        $rows_overview[] = array(lang('start_menu_calls_unanswered'), $total_stats->calls_unanswered, $calls_unanswered_percent);


        // ATA AVG
        $rows_overview[] = array(lang('ata'), $total_stats->ata_count_total > 0 ? sec_to_time(
            floor($total_stats-> ata_total_waittime / $total_stats->ata_count_total)) : 0
        );
        
        // Without Service
        $rows_overview[] = array(lang('calls_without_service'), $total_stats->calls_without_service);

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
        $rows_overview[] = array(lang('incoming_talk_time_sum'), sec_to_time($total_stats->incoming_total_calltime));
        
         // Incoming Talk Time AVG
        $rows_overview[] = array(lang('incoming_talk_time_avg'), $total_stats->incoming_total_calltime_count > 0 ? sec_to_time(
            floor($total_stats-> incoming_total_calltime / $total_stats->incoming_total_calltime_count)) : 0
        );
         // Incoming Talk Time Max
        $rows_overview[] = array(lang('incoming_talk_time_max'), $total_stats->incoming_total_calltime_count > 0 ? sec_to_time(
            floor($total_stats->incoming_max_calltime)) : 0
        );

        // Outgoing Talk Time SUM
        $rows_overview[] = array(lang('outgoing_talk_time_sum'), $total_stats->outgoing_max_calltime > 0 ? sec_to_time(
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


        ////////////////// ------------ AGENTS SHEET ----------------///////////////////
        $agent_call_stats  = $this->Call_model->get_agent_stats_for_start_page($queue_ids, $date_range);
        $agent_event_stats = $this->Event_model->get_agent_stats_for_start_page($queue_ids, $date_range);
        $agent_pause_stats = $this->Event_model->get_agent_pause_stats_for_start_page($date_range);

        foreach ($this->data->user_agents as $a) {
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
            // $sla_total_count_sum = $s->sla_count_total; //For Counting

            $agent_stats[$s->agent_id]['calls_answered']            = $s->calls_answered;
            $agent_stats[$s->agent_id]['incoming_total_calltime']   = $s->incoming_total_calltime; 
            $agent_stats[$s->agent_id]['calls_outgoing_answered'] = $s->calls_outgoing_answered;
            $agent_stats[$s->agent_id]['outgoing_total_calltime']   = $s->outgoing_total_calltime;
            $agent_stats[$s->agent_id]['calls_outgoing_unanswered'] = $s->calls_outgoing_unanswered;
          
            
            //SLA Less Then 10 Sec
           /* if($s->sla_count_less_than_10 && $s->sla_count_less_than_10 > 0 && $sla_total_count_sum > 0){
                $agent_stats[$s->agent_id]['sla_count_less_than_10'] = ($s->sla_count_less_than_10 / $s->sla_count_total) * 100 ."%" ;
               
            } else{
                $agent_stats[$s->agent_id]['sla_count_less_than_10'] = 0;
            }

            //SLA Between 10 -> 20 Sec
            if($s->sla_count_between_10_20 && $s->sla_count_between_10_20 > 0 && $sla_total_count_sum > 0){
                $agent_stats[$s->agent_id]['sla_count_between_10_20'] = ($s->sla_count_between_10_20 / $s->sla_count_total) * 100 ."%" ;
               
            } else{
                $agent_stats[$s->agent_id]['sla_count_between_10_20'] = 0;
            }

            //SLA Grate Then 20 Sec
            if($s->sla_count_grate_than_20 && $s->sla_count_grate_than_20 > 0 && $sla_total_count_sum > 0){
                $agent_stats[$s->agent_id]['sla_count_grate_than_20'] = ($s->sla_count_grate_than_20 / $s->sla_count_total) * 100 ."%" ;
               
            } else{
                $agent_stats[$s->agent_id]['sla_count_grate_than_20'] = 0;
            }*/

            /*//Outgoing Talk Time AVG
            $agent_stats[$s->agent_id]['outgoing_talk_time_avg'] = $s->outgoing_total_calltime > 0 ?  $s->outgoing_total_calltime / $s->calls_outgoing_answered : 0;
            
            //Incoming Talk Time AVG
            $agent_stats[$s->agent_id]['incoming_talk_time_avg'] = $s->incoming_total_calltime > 0 ? $s->incoming_total_calltime / $s->incoming_total_calltime_count : 0;*/
              
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
            lang('incoming_talk_time_sum'),
            lang('calls_missed'),
            lang('calls_outgoing_answered'),
            lang('outgoing_talk_time_sum'),
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

        ////////////////// ------- QUEUE SHEET --------------/////////////////////
        $queue_call_stats = $this->Call_model->get_queue_stats_for_start_page($queue_ids, $date_range);
        foreach ($this->data->user_queues as $q) {
            $queue_stats[$q->id] = array(
                'display_name'              => $q->display_name,
                'calls_answered'            => 0,
                'incoming_total_calltime'   => 0,
                'calls_missed'              => 0,
                'calls_outgoing_answered'   => 0,
                'outgoing_total_calltime'   => 0,
                'calls_outgoing_unanswered' => 0,
                'avg_holdtime'              => 0,
            );
        }
        foreach($queue_call_stats as $s) {
            $queue_stats[$s->queue_id]['calls_answered']            = $s->calls_answered;
            $queue_stats[$s->queue_id]['incoming_total_calltime']   = $s->incoming_total_calltime;
            $queue_stats[$s->queue_id]['calls_missed']              = $s->calls_unanswered;
            $queue_stats[$s->queue_id]['calls_outgoing_answered']   = $s->calls_outgoing_answered;
            $queue_stats[$s->queue_id]['outgoing_total_calltime']   = $s->outgoing_total_calltime;
            $queue_stats[$s->queue_id]['calls_outgoing_unanswered'] = $s->calls_outgoing_unanswered;
            $queue_stats[$s->queue_id]['avg_holdtime']              = ceil($s->total_waittime + $s->total_holdtime > 0 ? ($s->total_waittime + $s->total_holdtime) / ($s->calls_answered + $s->calls_unanswered) : 0);
            
        }

        $rows_queues[] = array(
            lang('queue'),
            lang('calls_answered'),
            lang('incoming_talk_time_sum'),
            lang('calls_missed'),
            lang('calls_outgoing_answered'),
            lang('outgoing_talk_time_sum'),
            lang('calls_outgoing_failed'),
            lang('hold_time')
        );
        foreach ($queue_stats as $i) {
            $rows_queues[] = array(
                $i['display_name'],
                $i['calls_answered'],
                sec_to_time($i['incoming_total_calltime']),
                $i['calls_missed'],
                $i['calls_outgoing_answered'],
                sec_to_time($i['outgoing_total_calltime']),
                $i['calls_outgoing_unanswered'],
               sec_to_time($i['avg_holdtime']),
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
            lang('incoming_talk_time_sum'),
            lang('calls_missed'),
            lang('calls_outgoing_answered'),
            lang('outgoing_talk_time_sum'),
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
                    $found = true;
                    // Calculate values as before
                    if($i->calls_unanswered == 0)
                    {

                        $avg_holdtme = '00:00:00';
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
            lang('incoming_talk_time_sum'),
            lang('calls_missed'),
            lang('calls_outgoing_answered'),
            lang('outgoing_talk_time_sum'),
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


       

        $writer->initializeSheet(lang('overview'),[50,10,10]);
        $writer->writeSheetRow(lang('overview'), $row_header, $style1, $style2, $style3);
        foreach($rows_overview as $row) 
        {
            $writer->writeSheetRow(lang('overview'), $row, $style2, $style3);
        }

        $writer->initializeSheet(lang('agents'),[50,30,30,30,30,30,30]);
        $writer->writeSheetRow(lang('agents'), $row_header,  $style1, $style2, $style3 );
        foreach($rows_agents as $row) 
        {
            $writer->writeSheetRow(lang('agents'), $row, $style2, $style3  );
        }

        $writer->initializeSheet(lang('queues'),[50,30,30,30,30,30,30,30]);
        $writer->writeSheetRow(lang('queues'), $row_header, $style1, $style2, $style3 );
        foreach($rows_queues as $row)
        {
            $writer->writeSheetRow(lang('queues'), $row, $style2,$style3 );
        }

        $writer->initializeSheet(lang('call_distrib_by_day'),[50,30,30,30,30,30,30,30]);
        $writer->writeSheetRow(lang('call_distrib_by_day'), $row_header, $style1, $style2, $style3 );
        foreach($rows_days as $row)
        {
            $writer->writeSheetRow(lang('call_distrib_by_day'), $row, $style2, $style3);
        }

        $writer->initializeSheet(lang('call_distrib_by_hour'),[50,30,30,30,30,30,30,30]);
        $writer->writeSheetRow(lang('call_distrib_by_hour'), $row_header, $style1, $style2, $style3);
        foreach($rows_hours as $row) 
        {
            $writer->writeSheetRow(lang('call_distrib_by_hour'), $row, $style2, $style3);
        }

        if ($this->data->config->app_call_categories == 'yes') 
        {
            $writer->initializeSheet(lang('call_distrib_by_category'),[50,30,30,30,30,30,30,30]);
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

        $sla_total_count_sum = $total_stats->sla_count_total;

        // Total Calls
        $rows_overview[] = array(lang('calls_total'), ($total_stats->calls_answered + $total_stats->calls_unanswered + $total_stats->calls_outgoing_answered + $total_stats->calls_outgoing_unanswered));
        
        // Unique Incoming Calls
        $rows_overview[] = array(lang('calls_unique_in'), ($total_stats->unique_incoming_calls_answered + $total_stats->unique_incoming_calls_unanswered));
        
        // Unique Users
        $rows_overview[] = array(lang('calls_unique_users'), $total_stats->unique_incoming_calls_answered);
        
        // Incoming Answered
        $rows_overview[] = array(lang('start_menu_calls_answered'), $total_stats->calls_answered);

        // SLA Less Then Or Equal To 10 Sec
        if ($total_stats->sla_count_less_than_or_equal_to_10 > 0 && $sla_total_count_sum > 0) 
        {
            $percentage = ($total_stats->sla_count_less_than_or_equal_to_10 / $total_stats->sla_count_total) * 100;
            $formatted_percentage = number_format($percentage, 2) . "%"; // Format to 2 decimal places
        
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
        if ($total_stats->sla_count_greater_than_10_and_less_than_or_equal_to_20 > 0 && $sla_total_count_sum > 0) {
            $percentage = ($total_stats->sla_count_greater_than_10_and_less_than_or_equal_to_20 / $total_stats->sla_count_total) * 100;
            $formatted_percentage = number_format($percentage, 2) . "%"; // Format to 2 decimal places
        
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
        if ($total_stats->sla_count_greater_than_20 > 0 && $sla_total_count_sum > 0) {
            $percentage = ($total_stats->sla_count_greater_than_20 / $total_stats->sla_count_total) * 100;
            $formatted_percentage = number_format($percentage, 2) . "%"; // Format to 2 decimal places
        
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
        $total_answered_unanswered = $total_stats->calls_answered + $total_stats->calls_unanswered;

        if($total_hold_wait_time > 0 && $total_answered_unanswered > 0){
            $rows_overview[] = array(lang('hold_time').' ('.lang('max').')', sec_to_time(
                floor(
                    $total_stats->max_holdtime)
                )
            );
        }

        // Hold Time AVG

        if($total_hold_wait_time > 0 && $total_answered_unanswered > 0){
            $rows_overview[] = array(lang('hold_time').' ('.lang('avg').')', sec_to_time(
                floor(
                   ( $total_stats->total_holdtime + $total_stats->total_waittime) / ($total_stats->calls_answered + $total_stats->calls_unanswered))
                )
            );
        }
        
        // Incoming Unanswered
        $calls_unanswered_percent = $total_stats->calls_unanswered > 0 && ($total_stats->calls_answered + $total_stats->calls_unanswered) > 0 ?
        (number_format(($total_stats->calls_unanswered / ($total_stats->calls_answered + $total_stats->calls_unanswered) * 100), 2) . '%') : '0%';

        $rows_overview[] = array(lang('start_menu_calls_unanswered'), $total_stats->calls_unanswered, $calls_unanswered_percent);


        // ATA AVG
        $rows_overview[] = array(lang('ata'), $total_stats->ata_count_total > 0 ? sec_to_time(
            floor($total_stats-> ata_total_waittime / $total_stats->ata_count_total)) : 0
        );
        
        // Without Service
        $rows_overview[] = array(lang('calls_without_service'), $total_stats->calls_without_service);

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
        $rows_overview[] = array(lang('incoming_talk_time_sum'), $total_stats->incoming_total_calltime_count > 0 ? sec_to_time($total_stats->incoming_total_calltime) : 0);
        
         // Incoming Talk Time AVG
        $rows_overview[] = array(lang('incoming_talk_time_avg'), $total_stats->incoming_total_calltime_count > 0 ? sec_to_time(
            floor($total_stats-> incoming_total_calltime / $total_stats->incoming_total_calltime_count)) : 0
        );
         // Incoming Talk Time Max
        $rows_overview[] = array(lang('incoming_talk_time_max'), $total_stats->incoming_total_calltime_count > 0 ? sec_to_time(
            floor($total_stats->incoming_max_calltime)) : 0
        );

        // Outgoing Talk Time SUM
        $rows_overview[] = array(lang('outgoing_talk_time_sum'), $total_stats->outgoing_max_calltime > 0 ? sec_to_time(
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
            // $sla_total_count_sum = $s->sla_count_total; //For Counting

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
            lang('incoming_talk_time_sum'),
            lang('calls_missed'),
            lang('calls_outgoing_answered'),
            lang('outgoing_talk_time_sum'),
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
            lang('incoming_talk_time_sum'),
            lang('calls_missed'),
            lang('calls_outgoing_answered'),
            lang('outgoing_talk_time_sum'),
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
                    $found = true;
                    // Calculate values as before
                    if($i->calls_unanswered == 0)
                    {

                        $avg_holdtme = '00:00:00';
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
            lang('incoming_talk_time_sum'),
            lang('calls_missed'),
            lang('calls_outgoing_answered'),
            lang('outgoing_talk_time_sum'),
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


       

        $writer->initializeSheet(lang('overview'),[70,10,10]);
        $writer->writeSheetRow(lang('overview'), $row_header, $style1, $style2, $style3);
        foreach($rows_overview as $row) 
        {
            $writer->writeSheetRow(lang('overview'), $row, $style2, $style3);
        }

        $writer->initializeSheet(lang('agents'),[50,30,30,30,30,30,30]);
        $writer->writeSheetRow(lang('agents'), $row_header,  $style1, $style2, $style3 );
        foreach($rows_agents as $row) 
        {
            $writer->writeSheetRow(lang('agents'), $row, $style2, $style3  );
        }

        $writer->initializeSheet(lang('call_distrib_by_day'),[50,30,30,30,30,30,30, 30]);
        $writer->writeSheetRow(lang('call_distrib_by_day'), $row_header, $style1, $style2, $style3 );
        foreach($rows_days as $row)
        {
            $writer->writeSheetRow(lang('call_distrib_by_day'), $row, $style2, $style3);
        }

        $writer->initializeSheet(lang('call_distrib_by_hour'),[50,30,30,30,30,30,30, 30]);
        $writer->writeSheetRow(lang('call_distrib_by_hour'), $row_header, $style1, $style2, $style3);
        foreach($rows_hours as $row) 
        {
            $writer->writeSheetRow(lang('call_distrib_by_hour'), $row, $style2, $style3);
        }

        if ($this->data->config->app_call_categories == 'yes') 
        {
            $writer->initializeSheet(lang('call_distrib_by_category'),[50,30,30,30,30,30,30, 30]);
            $writer->writeSheetRow(lang('call_distrib_by_category'), $row_header, $style1, $style2, $style3);
            foreach($rows_categories as $row) 
            {
                $writer->writeSheetRow(lang('call_distrib_by_category'), $row, $style2, $style3 );
            }
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
        $row_header           = array(lang('stats').' '.$date_gt.' > '.$date_lt.'('.lang('agent').': '.$agent->display_name.')');
        $agent_daily_stats   = $this->Call_model->get_daily_stats_for_agent_page($agent_id, $date_range);
        $agent_hourly_stats  = $this->Call_model->get_hourly_stats_for_agent_page($agent_id, $date_range);
        $sla_total_count_sum = $total_stats->sla_count_total;

        // Last Call 
        $rows_overview[] = array(lang('last_call'), $agent->last_call);
       
        // Total Calls
        $overallCalls        = (intval($overall_stats->calls_answered ) + intval($overall_stats->calls_unanswered)) + (intval($overall_stats->calls_outgoing_answered) + intval($overall_stats->calls_outgoing_unanswered));				   
	    $agentCalls	         =  intval($total_stats->calls_answered) + intval($total_stats->calls_outgoing);
	    if ($agentCalls > 0 && $overallCalls > 0) 
        {
            $agent_calls_percent = number_format(($agentCalls / $overallCalls) * 100, 2) . '%';
        } 
        else 
        {
            $agent_calls_percent = '0%';
        }
        
        $rows_overview[] = array(lang('calls_total'), $agentCalls, $agent_calls_percent);
        
        // Calls Answered

        $overall_calls_answered = intval($overall_stats->calls_answered);				   
		$agent_calls_answered	= intval($total_stats->calls_answered);
	    if($agent_calls_answered > 0 && $overall_calls_answered > 0) 
        {
            $agent_calls_answered_percent = number_format(($agent_calls_answered / $overall_calls_answered) * 100, 2) .'%';
        }
        else
        {
            $agent_calls_answered_percent = '0%';
        }
						  
        $rows_overview[] = array(lang('calls_answered'), $agent_calls_answered, $agent_calls_answered_percent);

        // SLA Less Then Or Equal To 10 Sec

        if ($total_stats->sla_count_less_than_or_equal_to_10 > 0 && $sla_total_count_sum > 0) 
        {
            $percentage = ($total_stats->sla_count_less_than_or_equal_to_10 / $total_stats->sla_count_total) * 100;
            $formatted_percentage = number_format($percentage, 2) . "%"; // Format to 2 decimal places
        
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
        if ($total_stats->sla_count_greater_than_10_and_less_than_or_equal_to_20 > 0 && $sla_total_count_sum > 0) {
            $percentage = ($total_stats->sla_count_greater_than_10_and_less_than_or_equal_to_20 / $total_stats->sla_count_total) * 100;
            $formatted_percentage = number_format($percentage, 2) . "%"; // Format to 2 decimal places
        
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
        if ($total_stats->sla_count_greater_than_20 > 0 && $sla_total_count_sum > 0) {
            $percentage = ($total_stats->sla_count_greater_than_20 / $total_stats->sla_count_total) * 100;
            $formatted_percentage = number_format($percentage, 2) . "%"; // Format to 2 decimal places
        
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
        

        // Hold Time Avg
        $total_hold_wait_time      = $total_stats->total_holdtime + $total_stats->total_waittime;
        $total_answered_unanswered = $total_stats->calls_answered + $total_stats->calls_unanswered;

        if($total_hold_wait_time > 0 && $total_answered_unanswered > 0){
            $rows_overview[] = array(lang('hold_time').' ('.lang('avg').')', sec_to_time(
                floor(
                    $total_hold_wait_time/$total_answered_unanswered)
                )
            );
        }

        // Hold Time Max

        if($total_hold_wait_time > 0 && $total_answered_unanswered > 0)
        {
            $rows_overview[] = array(lang('hold_time').' ('.lang('max').')', sec_to_time(
                floor(
                    $total_stats->max_holdtime)
                )
            );
        }
        
        // Calls Missed
        $calls_unanswered_percent = $total_stats->calls_unanswered > 0 && ($total_stats->calls_answered + $total_stats->calls_unanswered) > 0 ?
        (number_format(($total_stats->calls_unanswered / ($total_stats->calls_answered + $total_stats->calls_unanswered) * 100), 2) . '%') : '0%';

        $rows_overview[] = array(lang('start_menu_calls_unanswered'), $total_stats->calls_unanswered, $calls_unanswered_percent);
        
        // Incoming Talk Time SUM(Total)
        $rows_overview[] = array(lang('incoming_talk_time_sum'), $total_stats->incoming_total_calltime_count > 0 ? sec_to_time($total_stats->incoming_total_calltime) : 0);
        
        
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
        $rows_overview[] = array(lang('outgoing_talk_time_sum'), $total_stats->outgoing_max_calltime > 0 ? sec_to_time(
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

        $daily_call_stats = $this->Call_model->get_daily_stats_for_agent_page($agent_id, $date_range);
        $rows_days[] = array(
            lang('day'),
            lang('calls_answered'),
            lang('incoming_talk_time_sum'),
            lang('calls_missed'),
            lang('calls_outgoing_answered'),
            lang('outgoing_talk_time_sum'),
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
                    $found = true;
                    // Calculate values as before
                    if($i->calls_unanswered == 0)
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
        
         $hourly_call_stats = $this->Call_model->get_hourly_stats_for_agent_page($agent_id, $date_range);

      
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
            $hourly_stats[$s->hour]['hold_time_avg']             = ceil(($s->total_holdtime + $s->total_waittime) == 0 ? 0 : ($s->total_holdtime + $s->total_waittime) / ($s->calls_answered + $s->calls_unanswered));
        }

        $rows_hours[] = array(
            lang('hour'),
            lang('calls_answered'),
            lang('incoming_talk_time_sum'),
            lang('calls_missed'),
            lang('calls_outgoing_answered'),
            lang('outgoing_talk_time_sum'),
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
        $this->_prepare_headers('agent_'.$agent->display_name.'_stats-'.date('Ymd-His').'.xlsx');
        

        $writer = new XLSXWriter();

        $writer->setAuthor('Quickqueues');
      
        // Set up formatting styles
        $style1     = array('font-style'=>'bold');
        $style2     = array('halign'=>'left', 'valign'=>'left');
        

       

        $writer->initializeSheet(lang('overview'),[70,30,15]);
        $writer->writeSheetRow(lang('overview'), $row_header, $style1, $style2);
        foreach($rows_overview as $row) 
        {
            $writer->writeSheetRow(lang('overview'), $row, $style2);
        }

        $writer->initializeSheet(lang('call_distrib_by_day'),[50,30,30,30,30,30,30, 30]);
        $writer->writeSheetRow(lang('call_distrib_by_day'), $row_header, $style1, $style2);
        foreach($rows_days as $row)
        {
            $writer->writeSheetRow(lang('call_distrib_by_day'), $row, $style2);
        }

        $writer->initializeSheet(lang('call_distrib_by_hour'),[50,30,30,30,30,30,30,30]);
        $writer->writeSheetRow(lang('call_distrib_by_hour'), $row_header, $style1, $style2);
        foreach($rows_hours as $row) 
        {
            $writer->writeSheetRow(lang('call_distrib_by_hour'), $row, $style2);
        }

        if ($this->data->config->app_call_categories == 'yes') 
        {
            $writer->initializeSheet(lang('call_distrib_by_category'),[50,30,30,30,30,30,30, 30]);
            $writer->writeSheetRow(lang('call_distrib_by_category'), $row_header, $style1, $style2);
            foreach($rows_categories as $row) 
            {
                $writer->writeSheetRow(lang('call_distrib_by_category'), $row, $style2);
            }
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

    function category_export(){
        $date_gt = $this->input->get('date_gt') ? $this->input->get('date_gt') : QQ_TODAY_START;
        $date_lt = $this->input->get('date_lt') ? $this->input->get('date_lt') : QQ_TODAY_END;

        header('Content-Encoding: UTF-16');
        header('Content-type: text/csv; charset=UTF-16');
        header('Content-Disposition: attachment; filename=Category.csv');
        header("Pragma: no-cache");
        header("Expires: 0");
        $handle = fopen('php://output', 'w');

        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
        fputs($handle, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

        fputcsv($handle, array('პერიოდი: '.$date_gt.'დან'. $date_lt.'-მდე'));
        $main_subjects=$this->Call_subjects_model->get_main_subjects();

        foreach ($main_subjects as $main_subject) {
            $subject_family = $main_subject['id'] . '|';

            //Calculate Child 1 amount
            $child_1_count = $this->Call_subjects_model->get_stat_parent_subjects($subject_family, $date_gt, $date_lt);
            if (count($child_1_count) > 0) {
                //echo $main_subject['title'] . "-" . count($child_1_count);
                $main_subject_title=$main_subject['title'].'('.count($child_1_count).')';
                fputcsv($handle, array($main_subject_title));
            }

            //Child 1 subjects
            $child_1_subjects=$this->Call_subjects_model->get_child_1_subject_all(array('parent_id'=>$main_subject['id']));

            foreach ($child_1_subjects as $child_1_subject) {
                $subject_family_1 = $main_subject['id'] . '|' . $child_1_subject['id'] . '|';

                //Calculate Child 2 amount
                $child_2_count = $this->Call_subjects_model->get_stat_parent_subjects($subject_family_1, $date_gt, $date_lt);
                if (count($child_2_count) > 0) {
                    //echo $child_1_subject['title'] . "--" . count($child_2_count) . "=>";
                    $child_1_subject_title=$child_1_subject['title'] .'('.count($child_2_count).')';
                    fputcsv($handle, array('---', $child_1_subject_title));
                }

                //child 2 subjcets
                $child_2_subjects=$this->Call_subjects_model->get_child_2_subject(array('parent_id'=>$child_1_subject['id']));
                foreach ($child_2_subjects as $child_2_subject) {
                    $subject_family_2 = $main_subject['id'] . '|' . $child_1_subject['id'] . '|' . $child_2_subject['id'] . '|';

                    //Calculate Child 3 amount
                    $child_3_count = $this->Call_subjects_model->get_stat_parent_subjects($subject_family_2, $date_gt, $date_lt);
                    if (count($child_3_count) > 0) {
                        //echo $child_2_subject['title'] . "---" . count($child_3_count) . "=>";
                        $child_2_subject_title=$child_2_subject['title'] .'('. count($child_3_count).')';
                        fputcsv($handle, array('---', '---', $child_2_subject_title));
                    }

                    //child 3 subjcets
                    $child_3_subjects=$this->Call_subjects_model->get_child_3_subject(array('parent_id'=>$child_2_subject['id']));
                    foreach ($child_3_subjects as $child_3_subject){
                        $subject_family_3=$main_subject['id'].'|'.$child_1_subject['id'].'|'.$child_2_subject['id'].'|'.$child_3_subject['id'].'|';

                        //Calculate Child 4 amount
                        $child_4_count=$this->Call_subjects_model->get_stat_parent_subjects($subject_family_3, $date_gt, $date_lt);

                        if(count($child_4_count)>0){
                            //echo $child_3_subject['title']."--".count($child_4_count)."<br>";
                            $child_3_subject_title=$child_3_subject['title'].'('.count($child_4_count).')';
                            fputcsv($handle, array('---', '---','---', $child_3_subject_title));
                        }
                    }
                }
            }
        }

        fclose($handle);
    }

    // Visualisation for testing
    /*function category_show(){
        $main_subjects=$this->Call_subjects_model->get_main_subjects();
        //Main subjects
        foreach ($main_subjects as $main_subject){
            $subject_family=$main_subject['id'].'|';

            //Calculate Child 1 amount
            $child_1_count=$this->Call_subjects_model->get_stat_parent_subjects($subject_family);
            if(count($child_1_count)>0){
                echo $main_subject['title']."-".count($child_1_count)."=>";
            }

            //Child 1 subjects
            $child_1_subjects=$this->Call_subjects_model->get_child_1_subject_all(array('parent_id'=>$main_subject['id']));

            foreach ($child_1_subjects as $child_1_subject){
                $subject_family_1=$main_subject['id'].'|'.$child_1_subject['id'].'|';

                //Calculate Child 2 amount
                $child_2_count=$this->Call_subjects_model->get_stat_parent_subjects($subject_family_1);
                if(count($child_2_count)>0){
                    echo $child_1_subject['title']."--".count($child_2_count)."=>";
                }

                //child 2 subjcets
                $child_2_subjects=$this->Call_subjects_model->get_child_2_subject(array('parent_id'=>$child_1_subject['id']));
                foreach ($child_2_subjects as $child_2_subject){
                    $subject_family_2=$main_subject['id'].'|'.$child_1_subject['id'].'|'.$child_2_subject['id'].'|';

                    //Calculate Child 3 amount
                    $child_3_count=$this->Call_subjects_model->get_stat_parent_subjects($subject_family_2);
                    if(count($child_3_count)>0){
                        echo $child_2_subject['title']."---".count($child_3_count)."=>";
                    }

                    //child 3 subjcets
                    $child_3_subjects=$this->Call_subjects_model->get_child_3_subject(array('parent_id'=>$child_2_subject['id']));
                    foreach ($child_3_subjects as $child_3_subject){
                        $subject_family_3=$main_subject['id'].'|'.$child_1_subject['id'].'|'.$child_2_subject['id'].'|'.$child_3_subject['id'].'|';

                        //Calculate Child 4 amount
                        $child_4_count=$this->Call_subjects_model->get_stat_parent_subjects($subject_family_3);

                        if(count($child_4_count)>0){
                            echo $child_3_subject['title']."--".count($child_4_count)."<br>";
                        }
                    }
                }
            }
        }
    }*/

}
