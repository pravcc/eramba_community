<script>
	$(document).ready(function() {
		new PNotify({
			title: '<?= __('Error occured'); ?>',
			addclass: 'bg-danger',
			text: "<?= h($message); ?>",
			timeout: 6000
		});
	});
</script>