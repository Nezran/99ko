<?php defined('ROOT') OR exit('No direct script access allowed'); ?>
<p>
      <label><?php echo $core->lang("Admin mail"); ?></label><br>
      <input type="email" name="adminEmail" value="<?php echo $core->getConfigVal('adminEmail'); ?>" />
</p>
  <p>
      <label><?php echo $core->lang("New admin password"); ?></label><br>
      <input type="password" name="adminPwd" value="" autocomplete="off" style="display: none;" />
      <input type="password" name="_adminPwd" value="" autocomplete="off" />
</p>
  <p>
      <label><?php echo $core->lang("Confirmation"); ?></label><br>
      <input type="password" name="_adminPwd2" value="" autocomplete="off" />
</p>
  
  <p><button type="submit" class="button success radius"><?php echo $core->lang("Save"); ?></button></p>