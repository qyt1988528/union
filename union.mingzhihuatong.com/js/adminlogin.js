$(document).ready(function(){
    $("#email").blur(function(){
        var email = $("#email").val();
        var verifyFlag = $("#verifyFlag label").size();
        
        if(!verifyFlag){
            if(email){
                $.ajax({ 
                    type:"post",                                                        
                    url:"/admin/Login/showVerify",                                                   
                    data:"email="+email,                                          
                    success:function(msg){                                              
                        if(msg){
                            var str = "<label>验证码</label><div style='height:35px;'><input class='form-control' name='verify' type='text' style='width:200px;float:left;'/><img src='/admin/Login/imgcode' alt='看不清，换一张' title='看不清，换一张' style='float:left;cursor:pointer;margin-top:5px;' align='absmiddle' id='img'/></div>";
                            $("#showVerify").html(str);
                            $("#img").click(function(){
                                document.getElementById("img").src="/admin/Login/imgcode?d="+new Date();
                            });
                       
                        }
                    },
                    dataType:'json'
                });
            }
        }
    });

    $("#img").click(function(){
        document.getElementById("img").src="/admin/Login/imgcode?d="+new Date();
    });
});


