/**
 * Created with JetBrains PhpStorm.
 * User: karisan
 * Date: 2013/8/5
 * Time: 下午 6:09
 * To change this template use File | Settings | File Templates.
 */
$(document).ready(function(){

    $(":input").bind("keyup paste", function() {

        // 方法1，使用陣列抓取列表中的物件，並串成Query args
        /*
        var list = new Array('id', 'username', 'time', 'ip', 'action', 'status', 'url', 'info');
        var urlstr = '';
        for (var i=0;i<list.length;i++) {
            urlstr += list[i] + '=' + $('#'+list[i]).val() + '&';
        }
        */

        // 方法2，自動抓取input，串成Query args
        var urlstr = '';
        var json_data = '';
        $(":input").each(function(index) {

            urlstr += $(this).attr("id") + '=' + $(this).val() + '&';
            //console.log($(this).serializeArray());

            //json format
            json_data += '"' + $(this).attr("id") + '":"' + $(this).val() + '",';
        });
        json_data = '{' + json_data.substring(0,json_data.length-1) + '}';

        //alert(urlstr);

        $.get('show_log2_ajax?' + urlstr , function(returnData) {

            $('#results').html(returnData);

            //alert($('#results').children().length);

            //$('#results').children().each(function(){
                //tmp_id = $(this).attr("id").substr(4);
                //$(this).attr("onclick", "alert('"+ $(this).attr("id") +"')");

                //$(this).attr("onclick", "alert('"+ $(this).attr("id").substr(4) +"')");

                // 直接連至網頁
                //$(this).attr("onclick", "window.location='show_log_detail?id=" + tmp_id + "'");

            //});

            // 點選row之後，以POST方式進入log detail頁面
            $('#results').children().click(function(){

                // 設定id值
                tmp_id = $(this).attr("id").substr(4);
                //alert($(this).attr("id").substr(4));
                $('#q_id').val(tmp_id);
                $('#q_str').val(json_data);

                //var str = $("#myform").serialize();
                //alert(str);

                if(navigator.userAgent.toLowerCase().indexOf('firefox') > -1) {
                    $('#myform').appendTo("body").submit();
                } else {
                    $('#myform').submit();
                }
            });

            // 有anchor tag時，跳至該地方
            var str = window.location.toString();
            var n = str.indexOf("#");
            var jump = str.substr(n+1);
            var new_position = $('#'+jump).offset();
            window.scrollTo(new_position.left,new_position.top);

        });
    });


    $('#id').keyup();   //呼叫函數，以載入預設資料

});


