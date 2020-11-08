<script>
	$(document).ready(function() {
		new PNotify({
			title: '<?= __('Notice'); ?>',
			addclass: 'bg-primary',
			text: "<?= h($message); ?>",
			timeout: 6000
		});
	});
</script>