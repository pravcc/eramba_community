<div class="tabbable box-tabs box-tabs-styled">
	<ul class="nav nav-tabs">
		<?php
		echo $this->element('ajax-ui/customTabsNav');
		?>
	</ul>
	<div class="tab-content">
		<?php
		echo $this->element('ajax-ui/customTabsContent', array(
			'model' => $model,
			'foreign_key' => $id
		));
		?>
	</div>
</div>