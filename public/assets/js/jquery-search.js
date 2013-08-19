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
        $(":input").each(function(index) {
            urlstr += $(this).attr("id") + '=' + $(this).val() + '&';
        });

        //alert(urlstr);

        $.get('show_log2_ajax?' + urlstr , function(returnData) {
                $('#results').html(returnData);
            }
        );
    });

    $('#id').keyup();   //呼叫函數，以載入預設資料
});
