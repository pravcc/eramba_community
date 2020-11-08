<?php
App::uses('Cron', 'Cron.Model');
App::uses('CronTask', 'Cron.Model');

echo $this->Html->css("Cron.cron.css");
?>

<div class="widget widget-cron">
	<?php if ($cron['Cron']['status'] === Cron::STATUS_SUCCESS) : ?>
		<div class="widget-header">
			<h4>
				<?php echo $this->Ux->getIcon('ok-sign'); ?> 
				<?php echo __('Success'); ?>
			</h4>
		</div>
		<div class="widget-content">
			<strong>
				<?php
				echo $this->Ux->getAlert(__('Your CRON Job "%s" has been completed successfully', Cron::types($cron['Cron']['type'])), [
					'type' => 'success'
				]);
				?>
			</strong>
		</div>
	<?php else : ?>
		<div class="widget-header">
			<h4>
				<?php echo $this->Ux->getIcon('warning'); ?> 
				<?php echo __('Error'); ?>
			</h4>
		</div>
		<div class="widget-content">
			<strong>
				<?php
				echo $this->Ux->getAlert(__('There has been one or more issues while running the CRON Job "%s".', Cron::types($cron['Cron']['type'])), [
					'type' => 'danger'
				]);
				?>
			</strong>
		</div>
	<?php endif; ?>

	<div class="widget-content">
		<?php foreach ($cronTasks as $task) : ?>
			<?php
			if ($task['CronTask']['status'] == CronTask::STATUS_SUCCESS) {
				$class = 'alert alert-success';
			}
			else {
				$class = 'alert alert-danger';
			}
			?>
			<div class="<?= $class; ?>">
				<strong>Task: </strong><?= $task['CronTask']['task']; ?><br />
				<strong>Status: </strong><?= CronTask::statuses($task['CronTask']['status']); ?><br />
				<strong>Message: </strong><?= $task['CronTask']['message']; ?><br />
				<strong>Execution Time: </strong><?= $task['CronTask']['execution_time']; ?>s<br />
			</div>
		<?php endforeach; ?>
	</div>
</div>