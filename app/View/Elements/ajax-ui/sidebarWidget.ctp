<div class="widget box widget-form widget-popup-sidebar">
	<div class="widget-header">
		<h4>&nbsp;</h4>
	</div>
	<div class="widget-content">
		<?php if (isset($edit)) : ?>
			<?php
			echo $this->element('ajax-ui/sidebarTabs', array(
				'model' => $model,
				'id' => $id
			));
			?>
		<?php else : ?>
			<div class="tabbable box-tabs box-tabs-styled">
				<ul class="nav nav-tabs">
					<?php
					echo $this->element('ajax-ui/customTabsNav');
					?>
				</ul>
				<div class="tab-content">
					<div class="tab-pane fade in active" id="comments">
						<?php
						echo $this->element('not_found', array(
							'message' => __('Comments will be available when item is created.')
						));
						?>
					</div>

					<div class="tab-pane fade" id="records">
						<?php
						echo $this->element('not_found', array(
							'message' => __('Records will be available when item is created.')
						));
						?>
					</div>

					<div class="tab-pane fade" id="attachments">
						<?php
						echo $this->element('not_found', array(
							'message' => __('Attachments will be available when item is created.')
						));
						?>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>

<?php
if (isset($modalSidebarWidget) && $modalSidebarWidget) {
	echo $this->Ajax->cancelBtn('Ajax');
}
?>