/**
 * Created with JetBrains PhpStorm.
 * User: karisan
 * Date: 2013/8/26
 * Time: 下午 12:06
 * To change this template use File | Settings | File Templates.
 */
$(document).ready(function(){
    // 按下 新一筆、舊一筆時
    $("div[id^=record]").click(function(){
        var path = argv['v1'];
        $('#myform').attr('action',path);

        // 設定新一筆、舊一筆的id
        $('#q_id').attr('value',$(this).attr("value"));
        //alert($(this).attr("value"));

        // 條件
        //$('#q_str').val();
        //alert($('#q_str').val());

        // submit()
        if(navigator.userAgent.toLowerCase().indexOf('firefox') > -1) {
            $('#myform').appendTo("body").submit();
        } else {
            $('#myform').submit();
        }
    });

    // 按下 回列表頁 時
    $('#back').click(function(){
        if ($('#q_str').length != 0) {
            var path = argv['v2'];
        } else {
            var path = argv['v3'];
        }
        $('#myform').attr('action',path);

        // submit()
        if(navigator.userAgent.toLowerCase().indexOf('firefox') > -1) {
            $('#myform').appendTo("body").submit();
        } else {
            $('#myform').submit();
        }
    });

    // 按下 清除條件 時
    $('#clean').click(function(){
        var path = argv['v1'];
        $('#myform').attr('action',path);

        // 清除條件
        $('#q_str').val('');

        // submit()
        if(navigator.userAgent.toLowerCase().indexOf('firefox') > -1) {
            $('#myform').appendTo("body").submit();
        } else {
            $('#myform').submit();
        }
    });

});
