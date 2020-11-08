<?php
if (Configure::read('Eramba.DISABLE_DEBUG_NOTIFICATION')) {
	return true;
}

$label = __('You are in Debug mode - <a href="javascript:void();" class="text-white text-bold" data-yjs-request="crud/load" data-yjs-event-on="click" data-yjs-server-url="get::%s" data-yjs-target="modal">click here to disable it</a>', Router::url(array('controller' => 'settings', 'action' => 'edit', 'DEBUGCFG', 'admin' => false, 'plugin' => null)));
?>
<script type="text/javascript">
	YoonityJS.ready(function() {
		new PNotify({
			title: '<?= __('Debug mode is enabled'); ?>',
			addclass: 'bg-warning',
			text: '<?= $label; ?>',
			timeout: 6000,
			after_init: function(notice) {
				new YoonityJS.InitTemplate({
					template: notice.elem
				});
			}
		});
	});
</script>