var editor;
KindEditor.ready(function(K){
    editor=K.create("#kindeditor",{
        height:"450px",
        width:"100px",
        pasteType:1,
        themeType:'simple',
        uploadJson : '/js/kindeditor/php/upload_json.php',
        htmlTags:{
            a:["href","target","name"],
        img : ['src', 'width', 'height', 'border', 'alt', 'title', 'align', '.width', '.height', '.border'],
        'p,ol,ul,li,blockquote,h1,h2,h3,h4,h5,h6' : [
        'align', '.text-align', '.color', '.background-color', '.font-size', '.font-family', '.background',
        '.font-weight', '.font-style', '.text-decoration', '.vertical-align', '.text-indent', '.margin-left','.magrin-bottom'
        ],
        hr : ['class', '.page-break-after'],
        strong:[]
        }
    });
});


$(function(){                                                                                    

    var obj = document.getElementsByName("id[]");                       
    var length = obj.length;
    $("#selectAll").click(function(){                                                            
        for(var i = 0; i < length; i++){                                                         
            obj[i].checked = true;                                                               
        }                                                                                        
    });                                                                                          

    $("#cancelAll").click(function(){                                                            
        for(var i = 0; i < length; i++){                                                         
            obj[i].checked = false;                                                              
        }                                                                                        
    });



    /*
       $("#mes").click(function(){
       document.frm.action = "/admin/notice/index?f[methods]=mes";
       document.frm.submit();
       });
       */

    $('#email').click(function () {
        $('#dialog-email').dialog('open');
        return false;
    });


    // Dialog email
    $("#dialog-email").dialog({
        autoOpen: false,
        modal: true,
        width:700,
        buttons: {
            "确认发送": function () {
                var content = editor.html();
                var subject = $("#subject").val();
                document.frm.content.value = content;
                document.frm.subject.value = subject;
                document.frm.action = "/admin/notice/index?f[methods]=email";
                document.frm.submit();
            },
        "取消":function(){
            $(this).dialog('close');
        }
        }
    });

    $('#mes').click(function () {
        $('#dialog-mes').dialog('open');
        return false;
    });


    // Dialog mes
    $("#dialog-mes").dialog({
        autoOpen: false,
        modal: true,
        width:600,
        buttons: {
            "确认发送": function () {
                document.frm.action = "/admin/notice/index?f[methods]=mes";
                document.frm.submit();
            },
        "取消":function(){
            $(this).dialog('close');
        }
        }
    });
    /* 
       var lists;
       $.ajax({
       type:"post",
       url:"/admin/notice/alldata",
       data:"",
       success:function(data){
       lists = data;
       },
       dataType:'json'

       });
       */

    $("#typeahead").typeahead({
        source:function(query,process){
            $.ajax({
                type:"post",
                url:"/admin/notice/search",
                data:"",
                success:function(data){
                    process(data);
                },
                dataType:'json'

            });
        },


        /*
           updater:function(name){
           var list = _.find(lists, function (p) {
           return p.name == name;
           });

           alert(list.id);//使用id来查询的方法
           }

*/
        updater:function(name){
            var phone;
            var email;
            $.ajax({
                type:"post",
                url:"/admin/notice/index",
                data:"name="+name,
                success:function(data){
                    if(!data.contactor_phone){
                        phone =" ";
                    }else{
                        phone = data.contactor_phone;
                    }


                    if(!data.contactor_email){
                        email = " ";
                    }else{
                        email = data.contactor_email;
                    }
                    var str = "<tr><td><input type='checkbox' name='id[]' value='"+data.id+"'/></td><td>"+data.name+"</td><td>"+phone+"</td><td>"+email+"</td></tr>";
                    var p = $("#show_data");
                    $("#all_data").html(" ");
                    $("#show_data").css("display",'');
                    $(str).appendTo(p); 
                },
                dataType:'json'
            });

        }
    });



    $('#return_top').click(function(){$('html,body').animate({scrollTop: '0px'}, 800);return false;});
});
