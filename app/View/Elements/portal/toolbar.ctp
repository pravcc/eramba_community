<!-- Breadcrumbs line -->
<div class="crumbs">
	<?php
	echo $this->Html->getCrumbList([
		'separator' => '',
		'lastClass' => 'current',
		'id' => 'breadcrumbs',
		'class' => 'breadcrumb'
	], [
	    'text' => $this->Html->tag('i', '', ['class' => 'icon-home']) . __('Portal'),
	    'url' => false,
	    'escape' => false
	]);
	?>
</div>
<!-- /Breadcrumbs line -->