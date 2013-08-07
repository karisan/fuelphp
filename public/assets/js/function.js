/**
 * Created with JetBrains PhpStorm.
 * User: karisan
 * Date: 2013/8/5
 * Time: 下午 6:09
 * To change this template use File | Settings | File Templates.
 */
function clean_form(isclean) {
    if (isclean==true) {
        /*
        $("form").each(function(){
            this.reset();
        });
        */
        $("form").find(':input').each(function() {
            switch(this.type) {
                case 'password':
                case 'select-multiple':
                case 'select-one':
                case 'text':
                case 'textarea':
                    $(this).val('');
                    break;
                case 'checkbox':
                case 'radio':
                    this.checked = false;
            }
        });
    }
}
function del_msg(m_id){
    if (confirm('確定刪除此留言？')) {
        $('input[name=del_id]').val(m_id);
        //alert($("#del_id").value);
        //$('input[name=hiddeninputname]').val(theValue);
        //alert($('input[name=del_id]').val());

        //$('input[type="submit"]').submit();
        document.myform.submit();
        //$('form[name=myform]').submit();
        //document.forms["myform"].submit();




    }
}
