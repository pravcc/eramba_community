<p>
	<?php echo __('Dear %s', $user['User']['full_name']); ?>. 
	<?php
	echo __(
		'The section by the name of "%s" has been shared with you by %s. You can access with the following link: %s',
		$sectionLabel,
		$whoShared,
		$this->Html->link(__('show'), Router::url($gatewayUrl, true))
	);
	?>
</p>