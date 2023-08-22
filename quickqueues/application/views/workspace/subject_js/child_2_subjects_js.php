<script>
/* Updated At 19.02.2023 */
$(document).ready(function() {
//  Display Child 2 Subjects Items
    $('#child_1_items_list').on('click', '.child_1_subject', display_child_2_items);
    
    function display_child_2_items(){
        $('#child_3_items_list').html('');
        $('#subject_child_3').html('');

        //Set Button Colors
        var child_1_btn_id=this.id;
        $('.child_1_subject').css("background-color","#ecf0f1");
        $('#'+child_1_btn_id+'.child_1_subject').css("background-color","#3498db");

        if(typeof(this.id) != "undefined" && this.id !== null) {
            parent_subject_id=this.id;
        }
        else{
            parent_subject_id=$('#subject_child_2_parent_id').val();
        }
        var parent_subject_title=$('#'+parent_subject_id+'.child_1_subject').text();
        $('#subject_child_2').text(parent_subject_title);
        $('#subject_child_2_parent_id').val(parent_subject_id);
        
        family_array.splice(1,3)
        family_array.push(parent_subject_id);
        
        $.post('<?php echo base_url() ?>index.php/Call_subjects/get_child_2_subject',
            {
                type: 'display_child_2_subject',
				parent_id: parent_subject_id,
            },
            function(dataResult,status){
                var dataResult = JSON.parse(dataResult);
                if(dataResult.statusCode==200){
                    var child_2_arr=dataResult.child_2_result;
                    var child_2_items_list_html='';
                    for(var i=0; i<child_2_arr.length; i++){
                        var child_2_id=child_2_arr[i]['id'];
                        var child_2_title=child_2_arr[i]['title'];
                        var child_2_visible=child_2_arr[i]['visible'];

                        //Child 2 Items List Dinamyc HTML
                         child_2_items_list_html+="<div class='w100' id='child_2_list_div'>";
                            //child_2_items_list_html+="<a href='#'>";
                                child_2_items_list_html+="<div class='div_inline btn btn-light mt-1 child_2_subject'";
                                child_2_items_list_html+="id="+child_2_id+">";
                                child_2_items_list_html+=child_2_title;
                                child_2_items_list_html+="</div>";
                            //child_2_items_list_html+="</a>";
                        child_2_items_list_html+="</div>";
                    }
                    $('#child_2_items_list').html(child_2_items_list_html);
                }
                else{
                    alert('Request Status Error!');
				}
        });
    }
}); 
  
</script>