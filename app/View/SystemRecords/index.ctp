<div class="row">

	<div class="col-md-12">
		<div class="widget">
			<div class="btn-toolbar">
				<div class="btn-group">
					<button class="btn dropdown-toggle" data-toggle="dropdown"><?php echo __( 'Actions' ); ?> <span class="caret"></span></button>
					<ul class="dropdown-menu pull-left" style="text-align: left;">
						<li><a href="<?php echo Router::url( array( 'action' => 'export' ) ); ?>"><i class="icon-file"></i> <?php echo __( 'Export CSV' ); ?></a></li>
					</ul>
				</div>

				<?php echo $this->Video->getVideoLink('SystemRecord'); ?>

				<?php echo $this->element( CORE_ELEMENT_PATH . 'filter' , array('filterElement' => $this->element(FILTERS_ELEMENT_PATH . 'filter_system_record'))); ?>
			</div>
		</div>
	</div>
</div>
<?php echo $this->element( CORE_ELEMENT_PATH . 'active_filter_box', array('filterName' => 'SystemRecord')); ?>
<div class="row">
	<div class="col-md-12">
		<div class="widget">
			<?php if ( ! empty( $data ) ) : ?>
				<table class="table table-hover table-striped table-bordered table-highlight-head">
					<thead>
						<tr>
						<th>
						        <div class="bs-popover" data-trigger="hover" data-placement="top" data-original-title="<?php echo __( 'Help' ); ?>" data-content='<?php echo __( 'The date the record was created.' ); ?>'>
						<?php echo $this->Paginator->sort( 'SystemRecord.created', __( 'Date' ) ); ?>
						        <i class="icon-info-sign"></i>
						        </div>
						</th>
						<th>
						        <div class="bs-popover" data-trigger="hover" data-placement="top" data-original-title="<?php echo __( 'Help' ); ?>" data-content='<?php echo __( 'The type of record describes if the object was inserted, updated, deleted, etc.' ); ?>'>
						<?php echo $this->Paginator->sort( 'SystemRecord.type', __( 'Type' ) ); ?>
						        <i class="icon-info-sign"></i>
						        </div>
						</th>
						<th>
						        <div class="bs-popover" data-trigger="hover" data-placement="top" data-original-title="<?php echo __( 'Help' ); ?>" data-content='<?php echo __( 'Where the object resides (in which part of the system)' ); ?>'>
						<?php echo $this->Paginator->sort( 'SystemRecord.model_nice', __( 'Section' ) ); ?>
						        <i class="icon-info-sign"></i>
						        </div>
						</th>
						<th>
						        <div class="bs-popover" data-trigger="hover" data-placement="top" data-original-title="<?php echo __( 'Help' ); ?>" data-content='<?php echo __( 'The name of the object' ); ?>'>
						<?php echo $this->Paginator->sort( 'SystemRecord.item', __( 'Item' ) ); ?>
						        <i class="icon-info-sign"></i>
						        </div>
						</th>
						<th>
						        <div class="bs-popover" data-trigger="hover" data-placement="top" data-original-title="<?php echo __( 'Help' ); ?>" data-content='<?php echo __( 'This field usually points to the notes included by the user on the record.' ); ?>'>
						<?php echo $this->Paginator->sort( 'SystemRecord.notes', __( 'Notes' ) ); ?>
						        <i class="icon-info-sign"></i>
						        </div>
						</th>
						<?php /*
						<th>
						        <div class="bs-popover" data-trigger="hover" data-placement="top" data-original-title="<?php echo __( 'Help' ); ?>" data-content='<?php echo __( 'The workflow status indicates in which phase of the workflow process the object is. Draft, Pre-Validated, Pre-approved or Approved' ); ?>'>
						<?php echo $this->Paginator->sort( 'SystemRecord.workflow_status', __( 'Workflow Status' ) ); ?>
						        <i class="icon-info-sign"></i>
						        </div>
						</th>
						<th>
						        <div class="bs-popover" data-trigger="hover" data-placement="top" data-original-title="<?php echo __( 'Help' ); ?>" data-content='<?php echo __( 'User input comments when the workflows execute (the feedback the user provides when the workflow is activated)' ); ?>'>
						<?php echo $this->Paginator->sort( 'SystemRecord.workflow_comment', __( 'Workflow Comment' ) ); ?>
						        <i class="icon-info-sign"></i>
						        </div>
						</th>
						*/ ?>
						<th>
						        <div class="bs-popover" data-trigger="hover" data-placement="top" data-original-title="<?php echo __( 'Help' ); ?>" data-content='<?php echo __( 'The id of the object (this is an internal ID)' ); ?>'>
						<?php echo $this->Paginator->sort( 'SystemRecord.item', __( 'ID' ) ); ?>
						        <i class="icon-info-sign"></i>
						        </div>
						</th>
						<th>
						        <div class="bs-popover" data-trigger="hover" data-placement="top" data-original-title="<?php echo __( 'Help' ); ?>" data-content='<?php echo __( 'The IP from where the request was made' ); ?>'>
							<?php echo __( 'IP' ); ?>
						        <i class="icon-info-sign"></i>
						        </div>
						</th>
						<th>
						        <div class="bs-popover" data-trigger="hover" data-placement="top" data-original-title="<?php echo __( 'Help' ); ?>" data-content='<?php echo __( 'The user that executed this record.' ); ?>'>
							<?php echo __( 'User' ); ?>
						        <i class="icon-info-sign"></i>
						        </div>
						</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $data as $entry ) : ?>
							<tr>
								<td><?php echo $entry['SystemRecord']['created']; ?></td>
								<td><?php echo $types[ $entry['SystemRecord']['type'] ]; ?></td>
								<td><?php echo $entry['SystemRecord']['model_nice']; ?></td>
								<td><?php echo $entry['SystemRecord']['item']; ?></td>
								<td><?php echo $entry['SystemRecord']['notes']; ?></td>
								<?php /*
								<td>
									<?php
									if ($entry['SystemRecord']['workflow_status'] !== null) {
										echo $workflowStatuses[$entry['SystemRecord']['workflow_status']];
									}
									?>
								</td>
								<td><?php echo $entry['SystemRecord']['workflow_comment']; ?></td>
								*/ ?>
								<td>#<?php echo $entry['SystemRecord']['foreign_key']; ?></td>
								<td><?php echo $entry['SystemRecord']['ip']; ?></td>
								<td><?php echo $entry['User']['name'] . ' ' . $entry['User']['surname']; ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>

				<?php
				echo $this->element(CORE_ELEMENT_PATH . 'pagination', array(
					'url' => $backUrl
				));
				?>
			<?php else : ?>
				<?php
				echo $this->element('not_found', array(
					'message' => __('No System Records found.')
				));
				?>
			<?php endif; ?>

		</div>
	</div>
</div>
