							<?php defined('ROOT') OR exit('No direct script access allowed'); ?>
							<?php if($runPlugin->useAdminTabs()){ ?>
							</div>
							<?php } ?>
                        </div>
					</div>
			  </div>
	   </div>


	   <?php eval($core->callHook('endAdminBody')); ?>
	   </body>
</html>
