<?php if (!BANNERS_OFF) : ?>
	<!-- Button trigger modal -->
	<button class="btn btn-primary btn-lg" data-toggle="modal" data-target="#dashboard-modal" id="dashboard-modal-btn" style="display:none;">
	</button>

	<!-- Modal -->
	<div class="modal fade" id="dashboard-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?php echo __('Close'); ?></span></button>
					<h4 class="modal-title" id="myModalLabel"><?php echo __('Warning!'); ?></h4>
				</div>
				<div class="modal-body">
					<?php
						echo __('While every organization is a different thing, dashboards at eramba are general. Therefore you might not find this dashboards useful or accurate. If you want custom dashboards contact eramba\'s core team at %s or %s',
							'<a href="mailto:info@eramba.org">info@eramba.org</a>',
							'<a href="http://www.eramba.org/about/" target="_blank">www.eramba.org/about/</a>'
						);
					?>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Close'); ?></button>
				</div>
			</div>
		</div>
	</div>

	<script type="text/javascript">
	jQuery(function($) {
		if (typeof localStorage.dashboardModal == "undefined") {
			$('#dashboard-modal').modal();
			$("#dashboard-modal-btn").trigger("click");
			localStorage.dashboardModal = true;
		}
	});
	</script>
<?php endif; ?>
