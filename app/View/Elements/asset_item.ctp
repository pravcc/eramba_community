<?php
/**
 * @deprecated
 */
?>
<div class="widget box widget-closed">
	<div class="widget-header">
		<h4><?php echo __( 'Asset' ); ?>: <?php echo $asset['Asset']['name']; ?></h4>
		<div class="toolbar no-padding">
			<div class="btn-group">
				<span class="btn btn-xs widget-collapse"><i class="icon-angle-up"></i></span>
				<span class="btn btn-xs dropdown-toggle" data-toggle="dropdown">
					<?php echo __( 'Manage' ); ?> <i class="icon-angle-down"></i>
				</span>
				<ul class="dropdown-menu pull-right">
					<li>
						<?php
						echo $this->Html->link('<i class="icon-pencil"></i> ' . __('Edit'), array(
							'controller' => 'assets',
							'action' => 'edit',
							$asset['Asset']['id']
						), array(
							'escape' => false
						));
						?>
					</li>
					<li>
						<?php
						echo $this->Html->link('<i class="icon-cog"></i> ' . __('Records'), array(
							'controller' => 'systemRecords',
							'action' => 'index',
							'Asset',
							$asset['Asset']['id']
						), array(
							'escape' => false
						));
						?>
					</li>
					<li>
						<?php
						echo $this->Html->link('<i class="icon-trash"></i> ' . __('Delete'), array(
							'controller' => 'assets',
							'action' => 'delete',
							$asset['Asset']['id']
						), array(
							'escape' => false
						)); ?>
					</li>
				</ul>
			</div>
		</div>
	</div>
	<div class="widget-subheader">
		<table class="table table-hover table-striped table-bordered table-highlight-head">
			<thead>
				<tr>
					<th><?php echo __( 'Description' ); ?></th>
					<th>
						<div class="bs-popover" data-trigger="hover" data-placement="top" data-original-title="<?php echo __( 'Help' ); ?>" data-content='<?php echo __( 'What labels apply to this asset. For example: Confidential, Restricited, Public, Etc' ); ?>'>
							<?php echo __( 'Type' ); ?>
							<i class="icon-info-sign"></i>
						</div>
					</th>
					<th>
						<div class="bs-popover" data-trigger="hover" data-placement="top" data-original-title="<?php echo __( 'Help' ); ?>" data-content='<?php echo __( 'What labels apply to this asset. For example: Confidential, Restricited, Public, Etc' ); ?>'>
							<?php echo __( 'Label' ); ?>
							<i class="icon-info-sign"></i>
						</div>
					</th>
					<th>
						<div class="bs-popover" data-trigger="hover" data-placement="top" data-original-title="<?php echo __( 'Help' ); ?>" data-content='<?php echo __( 'The liabilities that are asociated with this asset. This is a rather important field as those liabilites mapped to an asset will magnify all risks scores asociated with it.' ); ?>'>
							<?php echo __( 'Liabilities' ); ?>
							<i class="icon-info-sign"></i>
						</div>
					</th>
					<th>
						<?php echo __('Status'); ?>
					</th>
					<th class="align-center">
						<div class="bs-popover" data-trigger="hover" data-placement="top" data-original-title="<?php echo __( 'Help' ); ?>" data-content='<?php echo __( 'Workflows define the approvals required to create, modify or delete an item. Approved items are valid throughout the system, Draft items require approval and Pending Approvals or Validations means that the workflow is still in process and is pending user interaction.' ); ?>'>
							<?php echo __( 'Workflows' ); ?>
							<i class="icon-info-sign"></i>
						</div>
					</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php echo $asset['Asset']['description']; ?></td>
					<td><?php echo isset( $asset['AssetMediaType']['name'] ) ? $asset['AssetMediaType']['name'] : ''; ?></td>
					<td><?php echo isset( $asset['AssetLabel']['name'] ) ? $asset['AssetLabel']['name'] : ''; ?></td>
					<td>
						<?php
						$legals = array();
						foreach ($asset['Legal'] as $legal) {
							$legals[] = $legal['name'];
						}
						echo implode(', ', $legals);
						?>
					</td>
					<td>
						<?php
						echo $this->Assets->getStatuses($asset);
						?>
					</td>
					<td class="text-center">
						<?php
						echo $this->element('workflow/action_buttons_1', array(
							'id' => $asset['Asset']['id'],
							'item' => $this->Workflow->getActions($asset['Asset'], $asset['WorkflowAcknowledgement']),
							'pulledObjectFields' => array('BusinessUnit' => $asset['Asset']['asset_owner_id'])
						));
						?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="widget-content" style="display:none;">


		<table class="table table-hover table-striped table-bordered table-highlight-head">
			<thead>
				<tr>
					<th>
						<div class="bs-popover" data-trigger="hover" data-placement="top" data-original-title="<?php echo __( 'Help' ); ?>" data-content='<?php echo __( 'Select from the list of business unit, which one is the one owning the asset.' ); ?>'>
							<?php echo __( 'Owner' ); ?>
							<i class="icon-info-sign"></i>
						</div>
					</th>
					<th>
						<div class="bs-popover" data-trigger="hover" data-placement="top" data-original-title="<?php echo __( 'Help' ); ?>" data-content='<?php echo __( 'Select from the list of business unit, which one is in charge of maintening the asset.' ); ?>'>
							<?php echo __( 'Guardian' ); ?>
							<i class="icon-info-sign"></i>
						</div>
					</th>
					<th>
						<div class="bs-popover" data-trigger="hover" data-placement="top" data-original-title="<?php echo __( 'Help' ); ?>" data-content='<?php echo __( 'Select from the list of business unit, which one is using the asset. You can optionally choose "Everyone".' ); ?>'>
							<?php echo __( 'User' ); ?>
							<i class="icon-info-sign"></i>
						</div>
					</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php echo ( ! empty( $asset['AssetOwner'] ) ) ? $asset['AssetOwner']['name'] : ''; ?></td>
					<td><?php echo ( ! empty( $asset['AssetGuardian'] ) ) ? $asset['AssetGuardian']['name'] : ''; ?></td>
					<td><?php echo ( ! empty( $asset['Asset']['asset_user_id'] ) ) ? $asset['AssetUser']['name'] : __('Everyone'); ?></td>
				</tr>
			</tbody>
		</table>

		<?php if ( ! empty( $asset['AssetClassification'] ) ) : ?>
			<table class="table table-hover table-striped table-bordered table-highlight-head">
				<thead>
					<tr>
						<?php foreach ( $asset['AssetClassification'] as $classification ) : ?>
							<th><?php echo $classification['AssetClassificationType']['name']; ?></th>
						<?php endforeach; ?>
					</tr>
				</thead>
				<tbody>
					<tr>
						<?php foreach ( $asset['AssetClassification'] as $classification ) : ?>
							<td><?php echo $classification['name']; ?></td>
						<?php endforeach; ?>
					</tr>
				</tbody>
			</table>
		<?php endif; ?>
	</div>
</div>
