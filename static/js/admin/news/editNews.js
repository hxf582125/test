var picData = [];
for(var i = 0; i < 6; i++){
    picData.push('');
}
var skuData = {};
var editNews = function(){

    return {
        init: function(){

            var form = $('#submitForm');
            var error = $('.alert-error', form);
            var success = $('.alert-success', form);

            var pageError = $('.alert_page_error', form);
            var pageSuccess = $('.alert_page_success', form);

            var validate = form.validate({
                debug: false,
                doNotHideMessage: true, //this option enables to show the error/success messages on tab switch.
                errorElement: 'span', //default input error message container
                errorClass: 'validate-inline', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {
                    //account
                    title: {
                        required: true
                    },
                    content: {
                        required: true
                    },
                    type: {
                        required: true
                    },
                    hours: {
                        required: true
                    }
                },

                messages: { // custom messages for radio buttons and checkboxes
                    'payment[]': {
                        required: "Please select at least one option",
                        minlength: jQuery.format("Please select at least one option")
                    }
                },

                errorPlacement: function (error, element) { // render error placement for each input type
                    if (element.attr("name") == "gender") { // for uniform radio buttons, insert the after the given container
                        error.addClass("no-left-padding").insertAfter("#form_gender_error");
                    } else if (element.attr("name") == "payment[]") { // for uniform radio buttons, insert the after the given container
                        error.addClass("no-left-padding").insertAfter("#form_payment_error");
                    } else {
                        error.insertAfter(element); // for other inputs, just perform default behavoir
                    }
                },

                invalidHandler: function (event, validator) { //display error alert on form submit
                    success.hide();
                    error.show();
                    App.scrollTo(error, -200);
                },

                highlight: function (element) { // hightlight error inputs
                    $(element)
                        .closest('.help-inline').removeClass('ok'); // display OK icon
                    $(element)
                        .closest('.control-group').removeClass('success').addClass('error'); // set error class to the control group
                },

                unhighlight: function (element) { // revert the change dony by hightlight
                    $(element)
                        .closest('.control-group').removeClass('error'); // set error class to the control group
                },

                success: function (label) {
                    if (label.attr("for") == "gender" || label.attr("for") == "payment[]") { // for checkboxes and radip buttons, no need to show OK icon
                        label
                            .closest('.control-group').removeClass('error').addClass('success');
                        label.remove(); // remove error label here
                    } else { // display success icon for other inputs
                        label
                            .addClass('valid ok') // mark the current input as valid and display OK icon
                            .closest('.control-group').removeClass('error').addClass('success'); // set success class to the control group
                    }
                },

                submitHandler: function (form) {
                    success.show();
                    error.hide();
                    //add here some ajax code to submit your form or just call form.submit() if you want to submit the form without ajax
                }

            });

            $('#submit').click(function () {

                pageSuccess.hide();
                pageError.hide();

                if (form.valid() == false) {
                    return false;
                }
                //图片
                $('.pic-list img').each(function(){
                    if($(this).attr('imgid')){
                        picData.push($(this).attr('imgid'));
                    }
                });
                var str = JSON.stringify(picData);//上传图片

                var imgs = {};
                $('.img-list').each(function (index) {
                    imgs[$(this).attr('id')] = [];
                    for(var i = 0;i < 6;i++){
                        if ($(this).children('li').eq(i).find('img').length >= 1) {
                            if(typeof $(this).children('li').eq(i).find('img').eq(0).attr('img_id')=="undefined"){
                                var img_id =$(this).children('li').eq(i).find('img').eq(0).attr('imgid');
                            }else{
                                var img_id =$(this).children('li').eq(i).find('img').eq(0).attr('img_id');
                            }
                            imgs[$(this).attr('id')].push(img_id);
                        }

                    }
                    //console.log('@@@',img_id);
                    //imgs=JSON.stringify(imgs)
                })
                //整理需要提交的数据
                var title = $("#title").val();
                var content = ue.getContent().replace(/&nbsp;*/g,' ').replace(/&gt;*/g,'>').replace(/&lt;*/g,'<');
                var type = $("#type").val();
                var pubTime = $("#pubTime").val();
                var hours = $("#hours").val();
                var isTop = $("input:radio[name='isTop']:checked").val();
                var isShow = $("input:radio[name='isShow']:checked").val();
                var img= $("#img-list").find("li").find('img');
                var id = $("#id").val();
                if(!content)
                {
                    alert('请输入新闻内容');
                    return;
                }
                
                var requestData = {
                    "title":title,
                    "content":content,
                    "type":type,
                    "pubTime":pubTime,
                    "hours":hours,
                    "isTop":isTop,
                    "isShow":isShow,
                    "img":img.attr('imgurl'),
                    "id":id
                };
                //console.log(requestData);
                success.hide();
                error.hide();
                //采用异步提交
                $.ajax({
                    url : '/anask/admin/editNewsAjax',
                    type : "post",
                    data : requestData,
                    dataType :"json",
                    success: function(result){
                        //console.log(result)
                        if(result.status == 0 ){
                            pageSuccess.hide();
                            //设置msg
                            pageError.text(result.msg);
                            pageError.show();
                            return;
                        }
                        pageError.hide();
                        pageSuccess.text(result.msg);
                        pageSuccess.show();

                        //重置表单
                        validate.resetForm();

                        $("input").val('');
                        $("textarea").val('');
                        setTimeout(function(){
                            pageError.hide();
                            pageSuccess.hide();
                            window.history.go(-1);
                        },3000);
                    }
                });
            });
        }
    }
}();
$(function()
{
    //缩略图
    imgList($('#img-list'),$('#img_upload'),'image');
});
function imgList($list,$file,url)
{
    $file.uploadify({
        'preventCaching': false,
        'fileSizeLimit': '1024KB',  //上传大小限制
        'fileTypeExts': '*.jpg; *.jpeg; *.png', //格式限制
        'queueSizeLimit': 6, //数量限制
        'swf': '/static/flash/uploadify.swf',
        'uploader': '/anask/uploadimg?u='+url,
        'onUploadSuccess': function (file, data, response) { //成功回调
            //console.log('SSSS',data);
            if (response) {
                data = JSON.parse(data);
                for (var i = 0; i < $list.find('li').length; i++) {
                    var $item = $list.find('li').eq(i);
                    if (!$item.find('.p-img').html()) {
                        $item.find('.p-img').html('<img width="70" height="70" imgurl='+data.data.imgurl+' src="' + data.data.imgurl + '">');

                        break;
                    }
                }
            }
        },
        'onUploadError': function (file, errorCode, errorMsg, errorString) {  //失败回调
        }
    });

    $list.find('li').hover(function () {
        if ($(this).find('.p-img').html()) {
            $(this).find('.ctrl-bar').show();
        }
    }, function () {
        $(this).find('.ctrl-bar').hide();
    })

    $list.find('.ctrl-bar .to-l').bind('click', function (e) {
        e.preventDefault();
        var index = $(this).parents('li.p-item').index();
        console.log($list)
        if (index > 0) {
            changePisiton($list, index, (index - 1));

        }
    })
    $list.find('.ctrl-bar .to-r').bind('click', function (e) {
        e.preventDefault();
        var index = $(this).parents('li.p-item').index();
        if (index < 6) {
            changePisiton($list, index, (index + 1));

        }
    })
    $list.find('.ctrl-bar .del').bind('click', function (e) {
        // var sku_img_id = $(this).parents('li').attr();

        var imgurl = $(this).parents('.ctrl-bar').siblings('.p-img').find('img').attr('src');
        var img_name = imgurl.substring(imgurl.lastIndexOf("/")+1,imgurl.length);

        e.preventDefault();
        $(this).parents('.ctrl-bar').siblings('.p-img').html('');
        picData[$(this).parents('.p-item').index()] = [];

    })
}
function changePisiton($list, index, nIndex) {
    var $img = $list.find('li').eq(index).find('.p-img');
    var $newImg = $list.find('li').eq(nIndex).find('.p-img');
    var img = $img.html();
    var newImg = $newImg.html();
    var imgData = picData[index];
    var newImgData = picData[nIndex];
    $img.html(newImg);
    picData[index] = newImgData;
    $newImg.html(img);
    picData[nIndex] = imgData;
    $('.ctrl-bar').hide();
}