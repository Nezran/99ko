<?php defined('ROOT') OR exit('No direct script access allowed'); ?>

<?php show::msg($core->lang("Clear Cache plugin can solve a bug in your site."), "info"); ?>

    <p><a class="button" href="index.php?p=pluginsmanager&action=cache&token=<?php echo $administrator->getToken(); ?>"><?php echo $core->lang("Clear plugins cache"); ?></a>
</p>


</form>

<script type="text/javascript">
$(document).ready(function(){
    var temp = $('.tabs li:first-child a').html();
    temp+= ' (<?php echo $nbPlugins; ?>)';
    $('.tabs li:first-child a').html(temp);
});
</script>