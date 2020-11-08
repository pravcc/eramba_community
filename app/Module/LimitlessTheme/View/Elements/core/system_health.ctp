<?php
$label = __('This installation of eramba has a few issues in order to work properly. Check on the settings page under health monitor what could be the issues or <a href="javascript:void();" class="text-white text-bold" data-yjs-request="crud/load" data-yjs-event-on="click" data-yjs-server-url="get::%s" data-yjs-target="modal">click here.</a>', Router::url(['plugin' => false, 'controller' => 'settings', 'action' => 'systemHealth']));
?>
<script type="text/javascript">
	YoonityJS.ready(function() {
		new PNotify({
			title: '<?= __('System Health Warning'); ?>',
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
