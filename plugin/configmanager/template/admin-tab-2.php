   <?php defined('ROOT') OR exit('No direct script access allowed'); ?>

   <?php show::msg($core->lang("Do not change advanced settings if you're not on what you're doing."), "info"); ?>
   
<p>
      <input <?php if($core->getConfigVal('debug')){ ?>checked<?php } ?> type="checkbox" name="debug" /> <label for="debug"><?php echo $core->lang("Debug Mod"); ?></label> 
</p>
  <p>
      <input id="urlRewriting" type="checkbox" onclick="updateHtaccess('<?php echo $rewriteBase; ?>');" <?php if($core->getConfigVal('urlRewriting')){ ?>checked<?php } ?> name="urlRewriting" /> <label for="urlRewriting"><?php echo $core->lang("URL rewriting"); ?></label>
</p>
  <p>
      <label><?php echo $core->lang("URL of the site (no trailing slash)"); ?></label><br>
      <input type="text" name="siteUrl" value="<?php echo $core->getConfigVal('siteUrl'); ?>" />
</p>
  <p>
      <label><?php echo $core->lang("URL separator"); ?></label><br>
      <select name="urlSeparator">
		<option <?php if($core->getConfigVal('urlSeparator') == ','){ ?>selected<?php } ?> value=",">, (<?php echo $core->lang("Comma"); ?>)</option>
        <option <?php if($core->getConfigVal('urlSeparator') == '.'){ ?>selected<?php } ?> value=".">. (<?php echo $core->lang("Dot"); ?>)</option>
        <option <?php if($core->getConfigVal('urlSeparator') == '/'){ ?>selected<?php } ?> value="/">/ (<?php echo $core->lang("Slash"); ?>)</option>
      </select>
</p>
  <p>
      <label><?php echo $core->lang(".htaccess"); ?></label><br>
      <textarea id="htaccess" name="htaccess"><?php echo $htaccess; ?></textarea>
</p>
  <p>
            
  <button type="submit" class="button success radius"><?php echo $core->lang("Save"); ?></button></p>
</form>