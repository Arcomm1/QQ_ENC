<script>
/* Updated At 19.02.2023 */
$(document).ready(function() {
//  Display Child 3 Subjects Items
$('#child_2_items_list').on('click', '.child_2_subject', display_child_3_items);
    function display_child_3_items(){

        //Set Button Colors
        var child_2_btn_id=this.id;
        $('.child_2_subject').css("background-color","#ecf0f1");
        $('#'+child_2_btn_id+'.child_2_subject').css("background-color","#3498db");

        if(typeof(this.id) != "undefined" && this.id !== null) {
            parent_subject_id=this.id;
        }
        else{
            parent_subject_id=$('#subject_child_3_parent_id').val();
        }
        var parent_subject_title=$('#'+parent_subject_id+'.child_2_subject').text();
        $('#subject_child_3, #child_3_modal_title').text(parent_subject_title);
        $('#subject_child_3_parent_id').val(parent_subject_id);
        
        family_array.splice(2,2)
        family_array.push(parent_subject_id);
       
        $.post('<?php echo base_url() ?>index.php/Call_subjects/get_child_3_subject',
            {
                type: 'display_child_3_subject',
				parent_id: parent_subject_id,
            },
            function(dataResult,status){
                var dataResult = JSON.parse(dataResult);
                if(dataResult.statusCode==200){
                    var child_3_arr=dataResult.child_3_result;
                    var child_3_items_list_html='';
                    for(var i=0; i<child_3_arr.length; i++){
                        var child_3_id=child_3_arr[i]['id'];
                        var child_3_title=child_3_arr[i]['title'];
                        var child_3_visible=child_3_arr[i]['visible'];

                        //Child 3 Items List Dinamyc HTML
                        child_3_items_list_html+="<div class='w100' id='child_3_list_div'>";
                            //child_3_items_list_html+="<a href='#'>";
                                child_3_items_list_html+="<div class='div_inline btn btn-light mt-1 child_3_subject'";
                                child_3_items_list_html+="id="+child_3_id+">";
                                child_3_items_list_html+=child_3_title;
                                child_3_items_list_html+="</div>";
                            //child_3_items_list_html+="</a>";
                        child_3_items_list_html+="</div>";
                    }
                    $('#child_3_items_list').html(child_3_items_list_html);
                }
                else{
                    alert('Request Status Error!');
				}
        });
    }

    $(document).on('click', '.child_3_subject', function() {
        var this_id=this.id;

        //Set Button Colors
        var child_3_btn_id=this.id;
        $('.child_3_subject').css("background-color","#ecf0f1");
        $('#'+child_3_btn_id+'.child_3_subject').css("background-color","#3498db");

        if(!this_id){
            return false;
        }
        
        family_array.splice(3,1)
        family_array.push(this_id);
            
    });
}); 
  
</script>