<?php
$this->set('errorView', true);
$this->set('hidePageHeader', true);
$this->set('title_for_layout', __('Error Occured'));
?>
<div class="text-center error-msg">
	<h2><?php echo __('Congratulations! You have just found a new bug :-!'); ?></h2>
	<p class="error">
		<?php
		$debugUrl = Router::url(array('plugin' => null, 'controller' => 'settings', 'action' => 'edit', 'DEBUGCFG'));
		$mailTo = sprintf('mailto:info@eramba.org?subject=Report an issue&body=App: %s%%0ADB: %s%%0A%%0A%%0A', Configure::read('Eramba.version'), DB_SCHEMA_VERSION);
		?>
		<?php echo  __('Something went wrong, most likely some system bug. Can we ask you to <strong><a href="%s">enable debug on the system</a></strong> (System Management / Settings / Debug Config) and re-create what you just did? Then just send us by email (<a href="%s">info@eramba.org</a>) a copy or screenshot of the full debug message. We’ll take care of this issue as quick as possible. Thanks a lot!', $debugUrl, $mailTo); ?>
	</p>
</div>