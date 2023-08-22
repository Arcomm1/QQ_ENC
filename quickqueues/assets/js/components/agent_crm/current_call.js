/* $(document).ready(function() {
    function get_current_call_uniqueid(){
        var baseurl = window.location.origin+window.location.pathname;
        var url_array=baseurl.split("/");
        url_array.splice(url_array.length -2, 2);

        var controller_url=url_array.toString();
        controller_url=controller_url.replace(/,/g,'/');
        
        $.get(controller_url+'/api/Agent/vendoo_get_last_call/2',
        
        function(dataResult){
            var dataResult = JSON.parse(dataResult);
            if(dataResult.statusCode==200){
              console.log(dataResult['current_call']['uniqueid']);
            }
            else{
                alert('Request Status Error!');
            }
        });

    }
    get_current_call_uniqueid();
}); */
