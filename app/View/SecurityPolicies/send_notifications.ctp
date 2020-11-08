<?php
echo $this->Form->create('SecurityPolicyNotification', array(
	'url' => array('controller' => 'securityPolicies', 'action' => 'sendNotifications', $securityPolicyId)
));
?>

<div class="row">

	<div class="col-md-12">
		<div class="widget">
			<div class="btn-toolbar">
				<?php
				echo $this->Form->submit(__('Send'), array(
					'class' => 'btn btn-primary',
					'div' => false
				));

				echo $this->Html->link(__('Cancel'), array(
					'controller' => 'securityPolicies',
					'action' => 'index'
				), array(
					'class' => 'btn btn-inverse'
				));
				?>
			</div>
		</div>
	</div>

</div>

<div class="row">

	<div class="col-md-12">

		<div class="widget box">
			<div class="widget-header">
				<h4><?php echo $data['SecurityPolicy']['index']; ?></h4>
				<div class="toolbar no-padding">
					<div class="btn-group">
						
					</div>
				</div>
			</div>
			<div class="widget-content">
				<?php if (!empty($allowedUsers)) : ?>
					<table class="table table-hover table-striped">
						<thead>
							<tr>
								<th>
									<?php
									echo $this->Form->input('SecurityPolicyNotification.checkAll', array(
										'type' => 'checkbox',
										'label' => false,
										'div' => false,
										'class' => 'uniform',
										'checked' => true,
										'id' => 'check-all-checkbox'
									));
									?>
									<?php echo __('Mail All'); ?>
								</th>
								<th><?php echo __('User'); ?></th>
								<th><?php echo __('Groups'); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php $key = 0; ?>
							<?php foreach ($allowedUsers as $user => $groups) : ?>
							<tr>
								<td>
									<?php
									echo $this->Form->input('SecurityPolicyNotification.send.' . $key, array(
										'type' => 'checkbox',
										'label' => false,
										'div' => false,
										'class' => 'uniform notification-checkbox',
										'value' => $user,
										'hiddenField' => false
									));
									$key++;
									?>
								</td>
								<td><?php echo $user; ?></td>
								<td><?php echo implode(', ', $groups); ?></td>
							</tr>
							<?php endforeach ; ?>
						</tbody>
					</table>
				<?php endif; ?>

			</div>
		</div>

	</div>

</div>

<?php echo $this->Form->end(); ?>

<script type="text/javascript">
jQuery(function($) {
	$("#check-all-checkbox").on("change", function(e) {
		if ($(this).is(":checked")) {
			$(".notification-checkbox").prop("checked", true);
		}
		else {
			$(".notification-checkbox").prop("checked", false);
		}

		$.uniform.update();
	}).trigger("change");

	$(".notification-checkbox").on("change", function(e) {
		var checked = $(".notification-checkbox:checked").length;
		if (checked < $(".notification-checkbox").length) {
			$("#check-all-checkbox").prop("checked", false);

			$.uniform.update('#check-all-checkbox');
		}
		else if(checked == $(".notification-checkbox").length) {
			$("#check-all-checkbox").prop("checked", true);

			$.uniform.update('#check-all-checkbox');
		}
	});
});
</script>