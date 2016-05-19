<?php defined('ROOT') OR exit('No direct script access allowed'); ?>

<?php include_once(THEMES.$core->getConfigVal('theme').'/header.php') ?>
<?php
if($pageItem->getFile()) include_once(THEMES.$core->getConfigVal('theme').'/'.$pageItem->getFile());
else echo $pageItem->getContent();
?>
<?php include_once(THEMES.$core->getConfigVal('theme').'/footer.php') ?>