<script type='text/javascript'>
//<![CDATA[
    $(window).load(function() {
            $.getJSON("<?php echo $isValidURL;?>", function(valid) { if (!valid) location.reload() })
});
$(window).unload(function() { });
//]]>
</script>