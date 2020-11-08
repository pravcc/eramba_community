<script>
	$(document).ready(function() {
		new PNotify({
			title: '<?= __('Success'); ?>',
			addclass: 'bg-success',
			text: "<?= h($message); ?>",
			timeout: 6000
		});
	});
</script>