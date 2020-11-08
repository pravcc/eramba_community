<div class="hidden" style="display:none">
	<?php echo $this->Ajax->flash(); ?>
	<script type="text/javascript">
		jQuery(function($) {
			Eramba.Ajax.UI.triggerIndexReload = true;
			Eramba.Ajax.UI.reloadIndex();
		});
	</script>
</div>