<script type="text/javascript">
	jQuery(function($) {
		$(window).on("load", function(e) {
			noty({
				text: '<strong><?php echo h($message); ?></strong>',
				type: 'success',
				timeout: 6000
			});
		});
	});
</script>