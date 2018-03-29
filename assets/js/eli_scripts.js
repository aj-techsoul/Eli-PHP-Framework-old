function formsubmit(formid){
        
       //alert(formid);
       $('.result').html('<p style="display:block; text-align:center;"><i class="fa fa-refresh fa-spin fa-fw" aria-hidden="true"></i><span class="sr-only">Process...</span> Processing</p>');
        var action = $(formid).attr('action');
        var formdata = $(formid).serialize();
	
        
        $.post(action,formdata, function(data, status){
            //alert("Action: " + action + "\nStatus: " + status);
            if(status==='success')
            {
                $('.result').html(data);
            }
            else
            {
                $('.result').html("Kindly check your internet connection.");
            }
        });
    };

function eliSubmit(formid){
        
       //alert(formid);
       $('.result').html('<p style="display:block; text-align:center;"><i class="fa fa-refresh fa-spin fa-fw" aria-hidden="true"></i><span class="sr-only">Process...</span> Processing</p>');
        var action = $(formid).attr('action');
        var formdata = $(formid).serialize();
	
        
        $.post(action,formdata, function(data, status){
             $('.result').html(data);
            // alert("Data: " + action + "\nStatus: " + status);
            if(status==='success')
            {
                $('.result').html(data);
              var tag = 'success'; 
              
              // $('.result').html("");
               
              
                if(data.indexOf(tag) != -1){
                    Materialize.toast(data, 5000,'green');
                    $('.submitcol').hide();
//                    $('.goodmsg').show();
                }
                else
                {
                    Materialize.toast(data, 5000,'red');
                    $('.submitcol').show();
//                    $('.goodmsg').hide();
                }
                
            }
            else
            {
                $('.result').html("Kindly check your internet connection.");
            }
        });
    };

function getData(action,senddata='',embedtoid,processto){
        
       //alert(formid);
       $(processto).html('<p style="display:block; text-align:center;"><i class="fa fa-refresh fa-spin fa-fw" aria-hidden="true"></i><span class="sr-only">Process...</span> Processing</p>');
	
        
        $.post(action,senddata, function(data, status){
            //$(processto).html(data);
            //alert("Action: " + data + "\nStatus: " + status);
            if(status==='success')
            {   
                data = data;
            
                $(processto).html(data);
                $(embedtoid).select2({ data: data });
                //$(processto).html('');
                Materialize.toast('Loaded', 2000,'teal');
            }
            else
            {
                $(processto).html("Kindly check your internet connection.");
            }
        });
    };