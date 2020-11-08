<div class="row">

	<div class="col-md-12">
		<div class="widget">
			<div class="btn-toolbar">
			<?php echo $this->element( CORE_ELEMENT_PATH . 'filter' , array('filterElement' => $this->element(FILTERS_ELEMENT_PATH . 'filter_workflow'))); ?>
			</div>
		</div>
	</div>
</div>
<?php echo $this->element( CORE_ELEMENT_PATH . 'active_filter_box', array('filterName' => 'Workflow')); ?>
<div class="row">
	<div class="col-md-12">
		<div class="widget">
			<?php if ( ! empty( $data ) ) : ?>
				<table class="table table-hover table-striped table-bordered table-highlight-head">
					<thead>
						<tr>
							<th><?php echo $this->Paginator->sort( 'Workflow.name', __( 'Name' ) ); ?></th>
							<th class="align-center"><?php echo __( 'Action' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $data as $entry ) : ?>
							<tr>
								<td><?php echo $entry['Workflow']['name']; ?></td>
								<td class="align-center">
									<ul class="table-controls">
										<li>
											<?php echo $this->Html->link('<i class="icon-pencil"></i>', array(
												'controller' => 'workflows',
												'action' => 'edit',
												$entry['Workflow']['id']
											), array(
												'class' => 'bs-tooltip',
												'escape' => false,
												'title' => __('Edit')
											)); ?>
										</li>
									</ul>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>

				<?php echo $this->element( CORE_ELEMENT_PATH . 'pagination' ); ?>
			<?php else : ?>
				<?php echo $this->element( 'not_found', array(
					'message' => __( 'No Workflows found.' )
				) ); ?>
			<?php endif; ?>

		</div>
	</div>

</div>