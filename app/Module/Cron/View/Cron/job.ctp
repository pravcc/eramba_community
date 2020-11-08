<?php
echo $this->Html->css("Cron.cron.css");
?>

<div class="widget widget-cron">
	<?php if ($success === true) : ?>
		<div class="widget-header">
			<h4>
				<?php echo $this->Ux->getIcon('ok-sign'); ?> 
				<?php echo __('Success'); ?>
			</h4>
		</div>
		<div class="widget-content">
			<?php
			$cronIndexUrl = $this->Html->link(__('CRON index'), [
				'plugin' => 'cron',
				'controller' => 'cron',
				'action' => 'index'
			]);

			echo $this->Ux->getAlert(__('Your CRON Job is running in the background. Status can be viewed on the %s.', $cronIndexUrl), [
				'type' => 'success'
			]);
			?>
		</div>
	<?php else : ?>
		<div class="widget-header">
			<h4>
				<?php echo $this->Ux->getIcon('warning'); ?> 
				<?php echo __('Error'); ?>
			</h4>
		</div>
		<div class="widget-content">
			<?php
			echo $this->Ux->getAlert(__('There has been one or more issues while running the CRON'), [
				'type' => 'danger'
			]);

			echo $this->Html->nestedList($errors, [
				'class' => 'list-cron-issues'
			]);
			?>
		</div>
	<?php endif; ?>
</div>