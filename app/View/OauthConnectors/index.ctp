<div class="row">

	<div class="col-md-12">
		<div class="widget">
			<div class="btn-toolbar pull-right">
				<div class="btn-group">
					<?php
					echo $this->Html->link(__('Back'), array(
						'plugin' => false,
						'controller' => 'settings',
						'action' => 'index'
					), array(
						'class' => 'btn btn-info',
						'escape' => false
					));
					?>
				</div>
			</div>
			<div class="btn-toolbar">
				<div class="btn-group">
					<?php echo $this->Ajax->addAction(); ?>
				</div>
			</div>
		</div>
	</div>

</div>

<div class="row">
	<div class="col-md-12">
		<div class="widget">
			<?php if ( ! empty( $data ) ) : ?>
				<table class="table table-hover table-striped table-bordered table-highlight-head">
					<thead>
						<tr>
							<th>
								<?php echo $this->Paginator->sort('OauthConnector.name', __('Connector Name')); ?>
							</th>
							<th>
								<?php echo $this->Paginator->sort('OauthConnector.provider', __('Provider')); ?>
							</th>
							<th>
								<?php echo __('Status'); ?>
							</th>
							<th class="align-center">
								<div class="bs-popover" data-trigger="hover" data-placement="top" data-original-title="<?php echo __( 'Help' ); ?>" data-content='<?php echo __( 'Use these icons in order to view the details of this object, system records such as when the item was created or modified, add or review comments or simply delete the item.' ); ?>'>
							<?php echo __( 'Actions' ); ?>
									<i class="icon-info-sign"></i>
								</div>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($data as $item) : ?>
							<tr>
								<td><?php echo $item['OauthConnector']['name']; ?></td>
								<td>
									<?php
									$provider = OauthConnector::providers($item['OauthConnector']['provider']);
									echo $this->Eramba->getEmptyValue($provider);
									?>
								</td>
								<td>
									<?php
									echo $this->OauthConnectors->getStatuses($item);
									?>
								</td>
								<td class="align-center">
									<?php
									echo $this->Ajax->getActionList($item['OauthConnector']['id'], array(
										'style' => 'icons',
										'notifications' => false,
										'item' => $item,
										'history' => true
									));
									?>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>

				<?php echo $this->element(CORE_ELEMENT_PATH . 'pagination'); ?>
			<?php else : ?>
				<?php
				echo $this->element('not_found', array(
					'message' => __('No OAuth Connectors found.')
				));
				?>
			<?php endif; ?>

		</div>
	</div>

</div>
