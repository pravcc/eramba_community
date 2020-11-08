<?php
App::uses('Inflector', 'Utility');

if (!isset($message)) {
	$camelCase = ucfirst($requestType);
	$humanizedRequestType = Inflector::humanize(Inflector::underscore($camelCase));

	$message = __('Are you sure you want to %s?', mb_strtolower($humanizedRequestType));
}
?>

<div class="row">
	<div class="col-md-12">
		<div class="widget box">
			<div class="widget-content">
				<?php
				echo $this->Form->create('WorkflowInstanceAction', array(
					'url' => $this->Workflows->getRequestUrl(
						$Instance,
						$requestType,
						$param
					),
					'class' => 'form-horizontal row-border',
					'novalidate' => true
				));

				echo $this->Form->input('requestType', array(
					'type' => 'hidden',
					'value' => $requestType
				));

				echo $this->Form->input('param', array(
					'type' => 'hidden',
					'value' => $param
				));
				?>

				<div class="alert alert-info">
					<?php
					echo $message;
					?>
				</div>

				<?php
				// if ($this->Form->isFieldError('WorkflowInstanceRequest.user_id')) {
				// 	echo $this->Form->error('WorkflowInstanceRequest.user_id');
				// }
				?>

				<div class="form-actions">
					<?php echo $this->Form->submit(__('Continue'), array(
						'class' => 'btn btn-primary',
						'div' => false
					)); ?>
					&nbsp;
					<?php
					echo $this->Ajax->cancelBtn('WorkflowInstanceSubmit');
					?>
				</div>

				<?php echo $this->Form->end(); ?>
			</div>
		</div>
	</div>
</div>