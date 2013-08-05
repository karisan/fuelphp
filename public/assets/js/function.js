/**
 * Created with JetBrains PhpStorm.
 * User: karisan
 * Date: 2013/8/5
 * Time: 下午 6:09
 * To change this template use File | Settings | File Templates.
 */
function clean_form($isclean) {
    if ($isclean==true) {
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
