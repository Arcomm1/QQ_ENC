<script>
/* Updated At 19.02.2023 */
$(document).ready(function() {
//  Display Child 1 Subjects Items
    $('.parent_subject').click(display_child_1_items);
    
    function display_child_1_items(){
        $('#child_3_items_list').html('');
        $('#subject_child_3').html('');

        $('#child_2_items_list').html('');
        $('#subject_child_2').html('');

        if(typeof(this.id) != "undefined" && this.id !== null) {
            parent_subject_id=this.id;
        }
        else{
            parent_subject_id=$('#subject_child_1_parent_id').val();
        }
        
        var parent_subject_title=$('#'+parent_subject_id).text();
        $('#subject_child_1').text(parent_subject_title);
        $('#subject_child_1_parent_id').val(parent_subject_id);
      
        family_array.splice(0,4)
        family_array.push(parent_subject_id);
       
        $.post('<?php echo base_url() ?>index.php/Call_subjects/get_child_1_subject_all',
            {
                type: 'display_child_1_subject',
				parent_id: parent_subject_id,
            },
            function(dataResult,status){
                var dataResult = JSON.parse(dataResult);
                if(dataResult.statusCode==200){
                    var child_1_arr=dataResult.child_1_result;
                    var child_1_items_list_html='';
                    for(var i=0; i<child_1_arr.length; i++){
                        var child_1_id=child_1_arr[i]['id'];
                        var child_1_title=child_1_arr[i]['title'];
                        var child_1_visible=child_1_arr[i]['visible'];
                        var child_1_comment=child_1_arr[i]['comment'];

                        //Child 1 Items List Dinamyc HTML
                        if(child_1_visible == 1) {
                            child_1_items_list_html += "<div class='w100' id='child_1_list_div'>";
                            //child_1_items_list_html+="<a href='#'>";
                            child_1_items_list_html += "<div class='div_inline btn btn-light mt-1 child_1_subject'";
                            child_1_items_list_html += " id=" + child_1_id + ">";
                            child_1_items_list_html += child_1_title;
                            child_1_items_list_html += "</div>";
                            //child_1_items_list_html+="</a>";
                            child_1_items_list_html += "</div>";
                        }
                    }
                    $('#child_1_items_list').html(child_1_items_list_html);
                }
                else{
                    alert('Request Status Error!');
				}
        });
    }
}); 
  
</script>
