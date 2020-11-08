<div class="training-list">
	<?php if (isset($noTrainings) && $noTrainings) : ?>
		<h2 class="awareness-title"><?php echo __('Your training is up to date!'); ?></h2>
		<h3 class="awareness-subtitle"><?php echo __('We will send you a reminder when the time comes and your training re-starts'); ?></h3>
	<?php else : ?>
		<?php if (!empty($neededTrainings)) : ?>
			<div class="row">
				<?php foreach ($neededTrainings as $program) : ?>
					<div class="col-md-6 training-item-wrapper">
						<div class="training-item">
							<h3 class="training-item-header"><?php echo $program['AwarenessProgram']['title']; ?></h3>
							<div class="training-item-content">
								<p><?php echo $program['AwarenessProgram']['description']; ?></p>

								<div class="text-center">
									<?php
									echo $this->Html->link(__('Start'), array(
										'controller' => 'awareness',
										'action' => 'training',
										$program['AwarenessProgram']['id']
									), array(
										'class' => 'btn btn-default',
										'escape' => false
									));
									?>
								</div>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>

			<br />
		<?php endif; ?>

		<?php if (!empty($demoTrainings)) : ?>
			<h3 class="awareness-subtitle text-center"><?php echo __('Demo Trainings'); ?></h3>
			<br />

			<div class="row">
				<?php foreach ($demoTrainings as $demo) : ?>
					<div class="col-md-6 training-item-wrapper">
						<div class="training-item training-demo">
							<h3 class="training-item-header"><?php echo $demo['AwarenessProgram']['title']; ?></h3>
							<div class="training-item-content">
								<p><?php echo $demo['AwarenessProgram']['description']; ?></p>

								<div class="text-center">
									<?php
									echo $this->Html->link(__('Start'), array(
										'controller' => 'awareness',
										'action' => 'demo',
										$demo['AwarenessProgramDemo']['id']
									), array(
										'class' => 'btn btn-default',
										'escape' => false
									));
									?>
								</div>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	<?php endif; ?>
</div>