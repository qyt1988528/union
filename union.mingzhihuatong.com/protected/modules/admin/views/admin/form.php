<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
            class="sr-only">Close</span></button>
    <h4 class="modal-title"><?php echo $title; ?></h4>
</div>
<div class="modal-body">
    <div class="col-lg">
        <form role="form" method="post" action="<?php echo $this->getFormActionUrl(); ?>"
             <?php if($this->allowUpload()) echo "enctype='multipart/form-data'"  ?>
            >
            <fieldset>
                <?php foreach ($fields as $field => $config) { ?>
                    <?php
                    if(is_integer($field)) {
                        $field = $config;
                        $config = array(
                            'type' => 'input'
                        );
                    }
                    $value = $model->getAttribute($field);
                    $name = "Attr[{$field}]";
                    if ($config['type'] === 'hidden') {
                        ?>
                        <input type='hidden' value='<?php echo $value; ?>' name='<?php echo $name; ?>'/>
                        <?php continue;
                    } ?>
                    <div class="form-group" for-field="<?php echo $field;?>">
                        <label><?php echo $model->getAttributeLabel($field); ?></label>
                        <?php
                        echo new BootstrapFormElement($name, $value, $config);
                        ?>
                        <p class="help-block">
                            <?php if (isset($config['hint'])) { ?>
                                <?php echo $config['hint']; ?>
                            <?php } ?>
                        </p>
                    </div>
                <?php } ?>
                <!--添加上传功能10.30-->
                <?php if($this->allowUpload()){ ?>
                <div class="form-group">
                    <button id="pickfiles" class="btn-primary">选择文件</button>
                    <a id="uploadfiles" href="javascript:;">上传</a>
                    <!--
                    <div class="progress progress-animated progress-striped active col-lg-5">
                        <div class="progress-bar progress-bar-info">
                            <span class="sr-only"></span>
                        </div>
                    </div>
                    -->
                </div>
                <?php } ?>
                <!--end-->
                <div class="form-group">
                    <button class="btn btn-primary btn-submitform" data-loading-text="正在保存...">保存</button>
                    <button class="btn btn-default" data-dismiss="modal">取消</button>
                </div>
            </fieldset>
        </form>
    <!-- /.col-lg-6 (nested) -->
    </div>
</div>
<?php if($this->allowUpload()){ ?>
<script type="text/javascript" src="/js/plupload.full.min.js"></script>
<script>
    var uploader = new plupload.Uploader({
        runtimes: 'html5,flash,silverlight,html4',
        browse_button : 'pickfiles', // 触发浏览文件按钮
        //container:$("#uploadtr").get(0),//默认是body 展现上传文件列表的容器
        url : '/admin/advertiseChannel/upload',
        chunk_size : '10mb',//当上传文件大于服务器接收端文件大小限制的时候，
        // 可以分多次请求发给服务器，如果不需要从设置中移出
        unique_names : true,//是否生成唯一的文件名，避免与服务器文件重名
        //resize : { width : 320, height : 240, quality : 90 },//修改图片属性
        //择文件扩展名的过滤器,每个过滤规则中只有title和ext两项[{title:'', extensions:''}]
        filters : {
            max_file_size : '10mb',
            mime_types: [
                {title : "App files", extensions : "apk,ipa"}
            ]
        },
        flash_swf_url : '/js/Moxie.swf',//flash文件地址
        silverlight_xap_url : '/js/Moxie.xap',//silverlight所在路径
        //插件初始化前回调函数

        // Post init events, bound after the internal events
        init : {
            PostInit: function() {
                // Called after initialization is finished and internal event handlers bound
                document.getElementById('uploadfiles').onclick = function() {
                    uploader.start();//点击上传后触发
                };

            },

            //文件上传完之前触发的事件
            BeforeUpload: function(up, file) {
                // Called right before the upload for a given file starts, can be used to cancel it if required
                /*下步进度条准备
                 $('.progress').show();
                 $('.progress-bar').css('width', '0%').text('0%');
                 */
            },
            //当文件正在被上传中触发
            UploadProgress: function(up, file) {
                // Called while file is being uploaded
                /*进度条
                 $('.progress-bar').css('width', file.percent+'%')
                 .text(file.percent==100?'已完成':file.percent+'%');
                 */
                var per =file.percent==100?'已完成':file.percent+'%' ;
                if(document.getElementById("upload-percent")){

                }else{
                    $("#uploadfiles").after("<span id='upload-percent'>"+per+"</span>");
                }
            },
            //用户选择文件时触发
            FilesAdded: function(up, files) {
                // Called when files are added to queue
                //uploader.start();
                $("#hint-text").remove();
                plupload.each(files, function(file) {
                    $("#filename").remove();
                    if(document.getElementById("filename")){

                    }else{
                        $("#pickfiles").after("<span id='filename'>"+file.name+"</span>");
                    }
                });
            },
            //文件上传成功的时候触发
            FileUploaded: function(up, file, info) {
                // Called when file has finished uploading
                var data=JSON.parse(info.response);
                $("#download_url").val(data.filepath);
                $("#upload-percent").remove();
                if(document.getElementById("hint-text")){

                }else{
                    $("#uploadfiles").after("<span id='hint-text'>上传成功</span>");
                }
            },
            //上传出错的时候触发
            Error: function(up, args) {
                // Called when error occurs
                if(document.getElementById("hint-text")){

                }else{
                    $("#uploadfiles").after("<span id='hint-text'>上传失败</span>");
                }
            }
        }
    });

    function log() {
        var str = "";

        plupload.each(arguments, function(arg) {
            var row = "";

            if (typeof(arg) != "string") {
                plupload.each(arg, function(value, key) {
                    // Convert items in File objects to human readable form
                    if (arg instanceof plupload.File) {
                        // Convert status to human readable
                        switch (value) {
                            case plupload.QUEUED:
                                value = 'QUEUED';
                                break;

                            case plupload.UPLOADING:
                                value = 'UPLOADING';
                                break;

                            case plupload.FAILED:
                                value = 'FAILED';
                                break;

                            case plupload.DONE:
                                value = 'DONE';
                                break;
                        }
                    }

                    if (typeof(value) != "function") {
                        row += (row ? ', ' : '') +'"'+key + '"' + ':' +'"'+value+'"';


                    }
                });
                row = '"{'+row+'}"';
                str += row + " ";
            } else {
                str += arg + " ";
            }
        });

    }
    uploader.init();
</script>
<?php } ?>
