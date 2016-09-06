<?php defined('ROOT') OR exit('No direct script access allowed'); ?>
		</div>
	</div>
	<div id="footer">
		<p>
                  <?php echo $core->lang("<a target='_blank' href='http://99ko.org'>Just using 99ko</a>") ?> - <?php echo $core->lang("Theme") ?> <?php show::theme(); ?> - <a rel="nofollow" href="<?php echo ADMIN_PATH ?>"><?php echo $core->lang('Administration') ?></a>
                </p>
	</div>
</div>
<?php eval($core->callHook('endFrontBody')); ?>
</body>
</html>