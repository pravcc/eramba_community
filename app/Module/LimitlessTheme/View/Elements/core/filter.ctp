<div class="btn-group pull-right">
	<div class="btn-group">
		<?php
			echo $this->Html->link( '<i class="icon-search"></i>' . __('Filter'),
				'#', array(
				'class' => 'btn btn-info',
				'id' => 'filter-btn',
				'escape' => false
			));
		?>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		$("#filter-btn").on('click', function(e){
			bootbox.dialog({
				title: "<?php echo __('Filter') ?>",
				message : '<?php echo(trim(preg_replace("/\s+/", " ", $filterElement))); ?>',
				buttons: {
					cancel: {
						label: "<?php echo  __('Cancel') ?>",
						className: "btn",
					},
					clear: {
						label: "<?php echo  __('Clear All') ?>",
						className: "btn-danger",
						callback: function (data) {
							$('input, select').each(function(){
							    $(this).val("");
							});
							$( ".filter-form" ).submit();
						}
					},
					success: {
						label: "<?php echo __('Filter') ?>",
						className: "btn-success",
						callback: function (data) {
							$( ".filter-form" ).submit();
						}
					},

				}
			});
			e.preventDefault();
		})
	});
</script>