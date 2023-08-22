<script>
$(document).ready(function() {
// Add Parent Subject
    $('#add_subject').on('click', function() {
        var title = $('#title').val();
        var comment = $('textarea#comment').val();
        if(name.length<1){
            $('#edit_subject_title').css('borderColor','red');
            return false;
        }

        $('#add_subject').attr('disabled', 'disabled');

        $.post('<?php echo base_url() ?>index.php/Call_subjects/add_parent_subject',
            {
                type: 'add_subject',
				title: title,
				comment: comment,
            },
            function(dataResult,status){
                var dataResult = JSON.parse(dataResult);
                if(dataResult.statusCode==200){
                    $('#add_subject').removeAttr('disabled');
                    window.location.reload();
                }
                else{
                    alert('Request Status Error!');
				}
        });
    });


/* --- Edit Parent Subject --- */
    //Collect Parent Subject Data
    $('.edit_parent_subject').on('click', function() {
        var id=this.id;
        $("#parent_edit_id").val(id);

        $.ajax({
            url: "<?php echo base_url() ?>index.php/Call_subjects/get_main_subject",
            type: "POST",
            data: {
                type: "get_main_subject",
                id: id,
            },
            cache: false,
            success: function(dataResult){
                var dataResult = JSON.parse(dataResult);
                console.log(dataResult[0].title);
                var modal_title=dataResult[0].title;
                $('#edit_main_subjects_modal_title').text(modal_title);
                $("#edit_subject_title").val(dataResult[0].title);
                $("#edit_subject_comment").val(dataResult[0].comment);
                //$("#id").val(dataResult[0].id);
            }
        });
    });

    //Update And Save Parent Subjec Data
    $('#edit_main_subject_button').on('click', function() {
        var name = $('#edit_subject_title').val();
        var comment = $('textarea#edit_subject_comment').val();
        var id=$("#parent_edit_id").val();

        if(name.length<1){
            $('#edit_subject_title').css('borderColor','red');
            return false;
        }

        $.ajax({
            url: "<?php echo base_url() ?>index.php/Call_subjects/save_main_subject",
            type: "POST",
            data: {
                type: "update_main_subject",
                id: id,
                name: name,
                comment: comment,
            },
            cache: false,
            success: function(dataResult){
                window.location.reload();
            }
        });
    });
 /* --- End Of Edit Parent Subject --- */

 /* Hide & Show Main Subject */
    $('.trash_main_subject').on('click', function() {
            var id=this.id;
            if($(this).hasClass( "visible0")){
                var visible='1';
            }
            if($(this).hasClass( "visible1")){
                var visible='0';
            }

            $.ajax({
                url: '<?php echo base_url() ?>index.php/Call_subjects/hide_show_main_subject',
                type: "POST",
                data: {
                    type: "hide_show_main_subject",
                    id: id,
                    visible:visible,
                },
                cache: false,
                success: function(dataResult){
                    window.location.reload();
                }
            });
        });
/* End Of Hide Main Subject */
});

</script>
