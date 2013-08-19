/**
 * Created with JetBrains PhpStorm.
 * User: karisan
 * Date: 2013/8/5
 * Time: 下午 6:09
 * To change this template use File | Settings | File Templates.
 */
$(document).ready(function(){
    /*
     Set the inner html of the table, tell the user to enter a search term to get started.
     We could place this anywhere in the document. I chose to place it
     in the table.
     */
    //$('#results').html('<p style="padding:5px;">Enter a search term to start filtering.</p>');

    /* When the user enters a value such as "j" in the search box
     * we want to run the .get() function. */

    $(":input").keyup(function() {

        /* Get the value of the search input each time the keyup() method fires so we
         * can pass the value to our .get() method to retrieve the data from the database */
        var id = $('#id').val();
        var username = $('#username').val();
        var time = $('#time').val();
        var ip = $('#ip').val();
        var action = $('#action').val();
        var status = $('#status').val();
        var url = $('#url').val();
        var info = $('#info').val();

        /* If the searchVal var is NOT empty then check the database for possible results
         * else display message to user */

        /*
        if( id !== '' ||
            username !== '' ||
            time !== '' ||
            ip !== '' ||
            action !== '' ||
            status !== '' ||
            url !== '' ||
            info !== ''
        ) {
            $.get(
                'show_log2_ajax'+
                '?id='+id+
                '&username='+username+
                '&time='+time+
                '&ip='+ip+
                '&action='+action+
                '&status='+status+
                '&url='+url+
                '&info='+info
                , function(returnData) {
                    $('#results').html(returnData);
                }
            );
        } else {
            //$('#results').html('<p style="padding:5px;">Enter a search term to start filtering.</p>');
        }
        */

        $.get(
            'show_log2_ajax'+
                '?id='+id+
                '&username='+username+
                '&time='+time+
                '&ip='+ip+
                '&action='+action+
                '&status='+status+
                '&url='+url+
                '&info='+info
            , function(returnData) {
                $('#results').html(returnData);
            }
        );
    });
});
