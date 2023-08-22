<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Call_subjects_statistics extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
    }

/* --- Display Subjects Items --- */
    public function get_parent_subjects(){
        $parent_subject_id=array();
        $parent_subject_title=array();
        $parent_subject_count=array();


        $date_gt = $this->input->post('date_gt') ? $this->input->post('date_gt') : QQ_TODAY_START;
        $date_lt = $this->input->post('date_lt') ? $this->input->post('date_lt') : QQ_TODAY_END;

        $all_parent_subjects=$this->Call_subjects_model->get_main_subjects();
        //print_r($all_parent_subjects);
        foreach($all_parent_subjects as $parent_subject){
            //echo $parent_subject['title']."<br>";
            $call_counter=0;
            $subject_family=$parent_subject['id'].'|';
            
            $filtered_subjects=$this->Call_subjects_model->get_stat_childs($subject_family, $date_gt, $date_lt);
            
            //Child 1 amount
            foreach($filtered_subjects as $filtered_subject){
                $call_counter++;
            }

            if($call_counter>0){
                array_push($parent_subject_id, $parent_subject['id']);
                array_push($parent_subject_title, $parent_subject['title']);
                array_push($parent_subject_count, $call_counter);
                
            }
        }
       //print_r($parent_subject_title);
        echo json_encode(array(
            "statusCode"=>200,
            "parent_subjects_id"=>$parent_subject_id,
            "parent_subjects_title"=>$parent_subject_title,
            "parent_subjects_count"=>$parent_subject_count,
            ));
    }
/* --- Display Child 1 Items --- */
    public function get_child_1_subjects(){
        $child_2_count=array();
        $child_1_count=array();
        $child_2_subject_array=array();
        $child_1_item_array=[];
        $child_1_subject_array=array();

        $parent_id=$this->input->post('parent_id');

        $date_gt=$this->input->post('date_gt');
        $date_lt=$this->input->post('date_lt');

        $child_1_subjects=$this->Call_subjects_model->get_child_1_subject_all(array('parent_id'=>$parent_id));

        foreach($child_1_subjects as $child_1_subject){
            $subject_family_1=$parent_id.'|'.$child_1_subject['id'].'|'; //first 2 digits

            $child_1_id_result=$this->Call_subjects_model->get_stat_childs($subject_family_1, $date_gt, $date_lt);
            array_push($child_1_count, count($child_1_id_result));
            foreach ($child_1_id_result as $child_1_id){
                $child_1_id=explode('|', $child_1_id['subject_family']);
                $child_1_id=$child_1_id['1'];
                $child_1_item_array[]=$this->Call_subjects_model->get_by_id_child_1($child_1_id);

            }
          //Child 2 amount;
            $child_2_subjects_result=$this->Call_subjects_model->get_child_2_subject(array('parent_id'=>$child_1_subject['id']));

            foreach($child_2_subjects_result as $child_2_subjects){
                $subject_family_2=$parent_id.'|'.$child_1_subject['id'].'|'.$child_2_subjects['id'].'|';
                $stat_child_2_result=$this->Call_subjects_model->get_stat_childs($subject_family_2, $date_gt, $date_lt);
                array_push($child_2_count, count($stat_child_2_result));

            }
        }
          foreach($child_1_item_array as $child_1_item){
              if(!in_array($child_1_item, $child_1_subject_array)) {
                  array_push($child_1_subject_array, $child_1_item);
              }
          }

        echo json_encode(array(
            "statusCode"=>200,
            "child_1_subject" => $child_1_subject_array,
            "child_1_count"=>$child_1_count,
            "child_2_count"=>$child_2_count,
        ));
    }

/* --- Display Child 2 Items --- */
    public function get_child_2_subjects(){
        $child_2_subject_array=array();
        $child_3_count=array();
        $child_2_count=array();
        $child_2_item_array=[];

        $main_subject_id=$this->input->post('main_subject_id');
        $parent_id=$this->input->post('parent_id');

        $date_gt=$this->input->post('date_gt');
        $date_lt=$this->input->post('date_lt');

        $child_2_subjects=$this->Call_subjects_model->get_child_2_subject(array('parent_id'=>$parent_id));

        foreach($child_2_subjects as $child_2_subject){
            $subject_family_2=$main_subject_id.'|'.$parent_id.'|'.$child_2_subject['id'].'|';

            $child_2_id_result=$this->Call_subjects_model->get_stat_childs($subject_family_2, $date_gt, $date_lt);
            array_push($child_2_count, count($child_2_id_result));

            foreach ($child_2_id_result as $child_2_id){
                $child_2_id=explode('|', $child_2_id['subject_family']);
                $child_2_id=$child_2_id['2'];
                $child_2_item_array[]=$this->Call_subjects_model->get_by_id_child_2($child_2_id);

            }
             //Child 3 amount;
             $child_3_subjects_result=$this->Call_subjects_model->get_child_3_subject(array('parent_id'=>$child_2_subject['id']));

             foreach($child_3_subjects_result as $child_3_subjects){
                 $subject_family_3=$main_subject_id.'|'.$parent_id.'|'.$child_2_subject['id'].'|';
                 $stat_child_3_result=$this->Call_subjects_model->get_stat_childs($subject_family_3, $date_gt, $date_lt);
                 array_push($child_3_count, count($stat_child_3_result));

             }
            array_push($child_2_subject_array, $child_2_subject);
        }
        //print_r($stat_child_3_result);
        foreach($child_2_item_array as $child_2_item){
            if(!in_array($child_2_item, $child_2_subject_array)) {
                array_push($child_2_subject_array, $child_2_item);
            }
        }
        echo json_encode(array(
            "statusCode"=>200,
            "child_2_subject" => $child_2_subject_array,
            "child_2_count" =>$child_2_count,
            "child_3_count"=>$child_3_count,
        ));
    }

    /* --- Display Child 3 Items --- */
    public function get_child_3_subjects(){
        $child_3_subject_array=array();
        $child_4_count=array();
        $main_subject_id=$this->input->post('main_subject_id');
        $main_sub_subject_id=$this->input->post('main_sub_subject_id');
        $parent_id=$this->input->post('parent_id');

        $date_gt=$this->input->post('date_gt');
        $date_lt=$this->input->post('date_lt');

        $child_3_subjects=$this->Call_subjects_model->get_child_3_subject(array('parent_id'=>$parent_id));
        
        foreach($child_3_subjects as $child_3_subject){
            array_push($child_3_subject_array, $child_3_subject);
            //print_r($child_3_subject);
            $subject_family_4=$main_subject_id.'|'.$main_sub_subject_id.'|'.$parent_id.'|'.$child_3_subject['id'].'|';
            $stat_child_4_result=$this->Call_subjects_model->get_stat_childs($subject_family_4, $date_gt, $date_lt);
            array_push($child_4_count, count($stat_child_4_result));
            //echo $subject_family_4."<br>";
        }
        //echo $main_subject_id.'-'.$parent_id.'-'.$child_3_subject['parent_id']."-".$child_3_subject['id']."<br>";
        echo json_encode(array(
            "statusCode"=>200,
            "child_3_subject" => $child_3_subject_array,
            "child_4_count"=>$child_4_count,
        ));
    }
}
