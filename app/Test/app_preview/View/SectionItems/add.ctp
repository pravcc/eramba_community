<div class="row">
	<div class="col-lg-7">

		<div class="widget box widget-form">
			<div class="widget-header">
				<h4>&nbsp;</h4>
			</div>
			<div class="widget-content">

				<?php
					if (isset($edit)) {
						echo $this->Form->create('SectionItem', array(
							'url' => array('action' => 'edit'),
							'class' => 'form-horizontal row-border',
							'novalidate' => true
						));

						echo $this->Form->input( 'id', array( 'type' => 'hidden' ) );
						$submit_label = __( 'Edit' );
					}
					else {
						echo $this->Form->create('SectionItem', array(
							'url' => array('action' => 'add'),
							'class' => 'form-horizontal row-border',
							'novalidate' => true
						));

						$submit_label = __( 'Add' );
					}
				?>

				<div class="tabbable box-tabs box-tabs-styled">
					<ul class="nav nav-tabs">
						<li class="active"><a href="#tab_general" data-toggle="tab"><?php echo __('General Fields'); ?></a></li>
						<li><a href="#tab_user_fields" data-toggle="tab"><?php echo __('User Fields'); ?></a></li>
						<li><a href="#tab_extensions" data-toggle="tab"><?php echo __('Extensions'); ?></a></li>
						<?php
						echo $this->element('CustomFields.tabs');
						?>
					</ul>
					<div class="tab-content">
						<div class="tab-pane fade in active" id="tab_general">
							<?php
							echo $this->Ux->getAlert(__('Commonly used input fields with example properties.'), ['type' => 'info']);

							/**
							 * This is an example on how to easily build a field input using FieldData layer.
							 * One way of doing it is this:
							 */
							// $SectionItem = ClassRegistry::init('SectionItem');
							// $FieldDataEntity = $SectionItem->getFieldDataEntity('varchar');
							// echo $this->FieldData->input($FieldDataEntity);

							/**
							 * Other way of showing an input:
							 */
							// $FieldDataCollection provides access to FieldDataEntities and is set in a controller
							// echo $this->FieldData->input($FieldDataCollection->varchar);

							/**
							 * Display a collection of editable fields all at once
							 */
							echo $this->FieldData->inputs([
								$FieldDataCollection->varchar,
								$FieldDataCollection->text,
								$FieldDataCollection->date,
								$FieldDataCollection->user_id,
								$FieldDataCollection->Tag,
								$FieldDataCollection->HasAndBelongsToMany,
								$FieldDataCollection->tinyint_status,
								$FieldDataCollection->toggle_status,
							]);
							?>
						</div>

						<div class="tab-pane fade in" id="tab_user_fields">
							<?php
								echo $this->Ux->getAlert(__('UserField functionality.'), ['type' => 'info']);

								echo $this->FieldData->inputs([
									$FieldDataCollection->UserField
								]);
							?>
						</div>

						<div class="tab-pane fade in" id="tab_extensions">
							<?php
							echo $this->Ux->getAlert(__('Test for getting an extension instance for a FieldDataEntity instance.'), ['type' => 'info']);

							var_dump($FieldDataCollection->user_id->Preview->testOptions());
							?>
						</div>
						<?php
						echo $this->element('CustomFields.tabs_content');
						?>
					</div>
					
				</div>

				<div class="form-actions">
					<?php echo $this->Form->submit( $submit_label, array(
						'class' => 'btn btn-primary',
						'div' => false
					) ); ?>
					&nbsp;
					<?php
					echo $this->Ajax->cancelBtn('SectionItem');
					?>
				</div>

				<?php echo $this->Form->end(); ?>

			</div>
		</div>
	</div>
	<div class="col-lg-5">
		<?php
		echo $this->element('ajax-ui/sidebarWidget', array(
			'model' => 'SectionItem',
			'id' => isset($edit) ? $this->data['SectionItem']['id'] : null
		));
		?>
	</div>
</div>
