<?php defined('ROOT') OR exit('No direct script access allowed'); ?>
		</div>
	</div>
	<div id="sidebar">
		<div id="sidebar_content">
			<?php eval($core->callHook('sidebar')); ?>
			<?php eval($core->callHook('endSidebar')); ?>
		</div>
	</div>
	<div id="footer">
		<div id="footer_content">
			<p>
				<?php echo $core->lang("<a target='_blank' href='http://99ko.org'>Just using 99ko</a>") ?> - <?php echo $core->lang("Theme") ?> <?php show::theme(); ?> - <a rel="nofollow" href="<?php echo ADMIN_PATH ?>"><?php echo $core->lang('Administration') ?></a>
			</p>
		</div>
	</div>
</div>
<?php eval($core->callHook('endFrontBody')); ?>
</body>
</html>