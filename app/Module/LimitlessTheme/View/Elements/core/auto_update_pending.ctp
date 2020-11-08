<?php
if (empty($autoUpdatePending) || !isAdmin($logged)) {
	return true;
}

$label = __('New updates are available for your application. <a href="%s" class="text-white text-bold">Click here for more details.</a>', Router::url(array('plugin' => null, 'controller' => 'updates', 'action' => 'index')));
?>
<script type="text/javascript">
	if (typeof pendingUpdatesNotyShown == "undefined") {
		YoonityJS.ready(function() {
			new PNotify({
				title: '<?= __('New updates available'); ?>',
				addclass: 'bg-info',
				text: '<?= $label; ?>',
				timeout: 6000
			});
		});
		var pendingUpdatesNotyShown = true;
	}
</script>
