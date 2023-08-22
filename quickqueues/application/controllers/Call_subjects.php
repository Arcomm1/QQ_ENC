<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Call_subjects extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
    }


    public function index()
    {
        $data['cat_title']='category title';
        $this->load->view('common/header', $this->data);
        $this->load->view("call_subjects/index", $data);
        $this->load->view('common/footer');
    }

/* --- Add New Subject --- */
    public function add_parent_subject(){
        if($this->input->post('type')=="add_subject"){
            $this->form_validation->set_rules('title', 'თემის დასახელება', 'required');
            
            $data['title']=$this->input->post('title');
			$data['comment']=$this->input->post('comment');
            
            if ($this->form_validation->run() == TRUE){
                $this->Call_subjects_model->save_parent_call_subject($data);	
			    echo json_encode(array(
				"statusCode"=>200
			));
            }
            else{
                echo 'Form Not Valid !';
            }
		}
    }

/* --- Edit Subject --- */
    public function get_main_subject(){
        if($this->input->post('type')=="get_main_subject"){
            $id=$this->input->post('id');
            $data['main_subject_details'] = $this->Call_subjects_model->get_by_id_main_subject($id);
            echo json_encode(array(
                $data['main_subject_details']));
        }
    }


    public function save_main_subject() {
        if($this->input->post('type')=='update_main_subject'){
            $id = $this->input->post('id');
            $this->Call_subjects_model->update_main_subject(
                $id,
                array(
                    'title'=> $this->input->post('name'),
                    'comment'=>$this->input->post('comment'),
                )
            );
        }
    }

/* --- Hide & Show Main Subject --- */
    public function hide_show_main_subject() {
        $id = $this->input->post('id');
        $hided_at='';
        
        if($this->input->post('visible')==0){
            $hided_at=date("Y-m-d H:i");
        }
        $this->Call_subjects_model->hide_show_main_subject(
            $id,
            array(
                'visible'=> $this->input->post('visible'),
                'hided_at'=>$hided_at,
            )
        );
    }    



/* --- Display Child 1 Subject Items --- */
    public function get_child_1_subject_all(){
        if($this->input->post('type')=="display_child_1_subject"){
            $data['parent_id']=$this->input->post('parent_id');
            $result=$this->Call_subjects_model->get_child_1_subject_all($data);	
           
            echo json_encode(array(
            "statusCode"=>200,
            "child_1_result"=>$result,
            ));
        }
        else{
            echo 'Form Not Valid !';
        }
    }

/* --- Add Child 1 Subject */
    public function add_child_1_subject(){
        if($this->input->post('type')=="add_child_1_subject"){
            $this->form_validation->set_rules('title', 'თემის დასახელება', 'required');
            
            $data['parent_id']=$this->input->post('parent_id');
            $data['title']=$this->input->post('title');
            $data['comment']=$this->input->post('comment');
            
            if ($this->form_validation->run() == TRUE){
                $this->Call_subjects_model->save_child_1_subject($data);	
                echo json_encode(array(
                "statusCode"=>200
            ));
            }
            else{
                echo 'Form Not Valid !';
            }
        }
    } 

/* --- Edit Child 1 --- */
public function get_child_1_details(){
    if($this->input->post('type')=="get_child_1_details"){
        $id=$this->input->post('id');
        $data['subject_1_details'] = $this->Call_subjects_model->get_by_id_child_1($id);
        echo json_encode(array(
            $data['subject_1_details']));
    }
}


public function save_child_1_subject() {
    if($this->input->post('type')=='save_child_1_subject'){
        $id = $this->input->post('id');
        $this->Call_subjects_model->update_child_1(
            $id,
            array(
                'title'=> $this->input->post('name'),
                'comment'=>$this->input->post('comment'),
            )
        );
    }
} 

/* --- Hide & Show Child 1 --- */
    public function hide_show_child_1_subject() {
        $id = $this->input->post('id');
        $hided_at='';
        
        if($this->input->post('visible')==0){
            $hided_at=date("Y-m-d H:i");
        }
        $this->Call_subjects_model->hide_show_child_1(
            $id,
            array(
                'visible'=> $this->input->post('visible'),
                'hided_at'=>$hided_at,
            )
        );
    }   

 /* --- Display Child 2 Subject Items --- */
    public function get_child_2_subject(){
        if($this->input->post('type')=="display_child_2_subject"){
            $data['parent_id']=$this->input->post('parent_id');
            $result=$this->Call_subjects_model->get_child_2_subject($data);	
        
            echo json_encode(array(
            "statusCode"=>200,
            "child_2_result"=>$result,
            ));
        }
        else{
            echo 'Form Not Valid !';
        }
    }
    
/* --- Add Child 2 Subject */
    public function add_child_2_subject(){
        if($this->input->post('type')=="add_child_2_subject"){
            $this->form_validation->set_rules('title', 'თემის დასახელება', 'required');
            
            $data['parent_id']=$this->input->post('parent_id');
            $data['title']=$this->input->post('title');
            $data['comment']=$this->input->post('comment');
            
            if ($this->form_validation->run() == TRUE){
                $this->Call_subjects_model->save_child_2_subject($data);	
                echo json_encode(array(
                "statusCode"=>200
            ));
            }
            else{
                echo 'Form Not Valid !';
            }
        }
    }

/* --- Edit Child 2 --- */
    public function get_child_2_details(){
        if($this->input->post('type')=="get_child_2_details"){
            $id=$this->input->post('id');
            $data['subject_1_details'] = $this->Call_subjects_model->get_by_id_child_2($id);
            echo json_encode(array(
                $data['subject_1_details']));
        }
    }


    public function save_child_2_subject() {
        if($this->input->post('type')=='save_child_2_subject'){
            $id = $this->input->post('id');
            $this->Call_subjects_model->update_child_2(
                $id,
                array(
                    'title'=> $this->input->post('name'),
                    'comment'=>$this->input->post('comment'),
                )
            );
        }
    }
    
/* --- Hide & Show Child 2 --- */
    public function hide_show_child_2_subject() {
        $id = $this->input->post('id');
        $hided_at='';
        
        if($this->input->post('visible')==0){
            $hided_at=date("Y-m-d H:i");
        }
        $this->Call_subjects_model->hide_show_child_2(
            $id,
            array(
                'visible'=> $this->input->post('visible'),
                'hided_at'=>$hided_at,
            )
        );
    }     

/* --- Display Child 3 Subject Items --- */
    public function get_child_3_subject(){
        if($this->input->post('type')=="display_child_3_subject"){
            $data['parent_id']=$this->input->post('parent_id');
            $result=$this->Call_subjects_model->get_child_3_subject($data);	
        
            echo json_encode(array(
            "statusCode"=>200,
            "child_3_result"=>$result,
            ));
        }
        else{
            echo 'Form Not Valid !';
        }
    }

/* --- Add Child 3 Subject */
    public function add_child_3_subject(){
        if($this->input->post('type')=="add_child_3_subject"){
            $this->form_validation->set_rules('title', 'თემის დასახელება', 'required');
            
            $data['parent_id']=$this->input->post('parent_id');
            $data['title']=$this->input->post('title');
            $data['comment']=$this->input->post('comment');
            
            if ($this->form_validation->run() == TRUE){
                $this->Call_subjects_model->save_child_3_subject($data);	
                echo json_encode(array(
                "statusCode"=>200
            ));
            }
            else{
                echo 'Form Not Valid !';
            }
        }
    }

/* --- Edit Child 3 --- */
    public function get_child_3_details(){
        if($this->input->post('type')=="get_child_3_details"){
            $id=$this->input->post('id');
            $data['subject_1_details'] = $this->Call_subjects_model->get_by_id_child_3($id);
            echo json_encode(array(
                $data['subject_1_details']));
        }
    }


    public function save_child_3_subject() {
        if($this->input->post('type')=='save_child_3_subject'){
            $id = $this->input->post('id');
            $this->Call_subjects_model->update_child_3(
                $id,
                array(
                    'title'=> $this->input->post('name'),
                    'comment'=>$this->input->post('comment'),
                )
            );
        }
    }

/* --- Hide & Show Child 3 --- */
    public function hide_show_child_3_subject() {
        $id = $this->input->post('id');
        $hided_at='';
        
        if($this->input->post('visible')==0){
            $hided_at=date("Y-m-d H:i");
        }
        $this->Call_subjects_model->hide_show_child_3(
            $id,
            array(
                'visible'=> $this->input->post('visible'),
                'hided_at'=>$hided_at,
            )
        );
    }
/*  Update Call Subject Comments */    
    public function add_subject_comment(){

        if($this->input->post('type')=='add_subject_comment'){
            $id = $this->input->post('id');

            $this->Call_subjects_model->add_subject_comments(
                $id,
                array(
                    'subject_family'=> $this->input->post('subject_family'),
                    'comment'=>$this->input->post('subject_comment'),
                )
            );
            echo json_encode(array(
                "statusCode"=>'200'));
        }
    }
/* Get All Required Comments Params */    

    public function get_call_comment_params(){

        if($this->input->post('type')=='get_call_comment_params'){
            $id = $this->input->post('id');

            $comment_params=$this->Call_subjects_model->get_call_params($id);
            echo json_encode(array(
                "statusCode"=>'200',
                $comment_params,
            ));
        }
    }

/* Get Child And SubChild Subjects */
 public function get_child_sub_childs(){
        if($this->input->post('type')=="get_child_sub_childs"){
            $id=$this->input->post('child_parent_id');
            $data['child_sub_childs'] = $this->Call_subjects_model->get_child_sub_childs($id);
            echo json_encode(array(
                $data['child_sub_childs']));
        }
    }

    public function  get_parents_childs(){
        if($this->input->post('type')=="get_parents_childs"){

            $table_name_array=array('0'=>'qq_call_subjects_parent',
                              '1'=>'qq_call_subjects_child_1',
                              '2'=>'qq_call_subjects_child_2',
                              '3'=>'qq_call_subjects_child_3');
                             

            $id=$this->input->post('parent_child_id');
            $table_id=$this->input->post('table_id');
            $table_name=$table_name_array[$table_id];

            $data['parents_childs'] = $this->Call_subjects_model->get_parents_childs($id, $table_name);
            echo json_encode(array(
                $data['parents_childs']));
        }
    }    

}
