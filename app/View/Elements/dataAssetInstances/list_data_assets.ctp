<?php
App::uses('Hash', 'Utility');
App::uses('Inflector', 'Utility');
App::uses('DataAssetGdpr', 'Model');
?>
<?php if (!empty($data)) : ?>
	<table class="table table-hover table-striped">
		<thead>
			<tr>
				<th>
					<?php echo __('Title'); ?>
				</th>
				<th>
					<?php echo __('Business Unit'); ?>
				</th>
				<th>
					<?php echo __('Third Parties'); ?>
				</th>
				<th>
					<?php echo __('Risks'); ?>
				</th>
				<th>
					<?php echo __('Third Party Risks'); ?>
				</th>
				<th>
					<?php echo __('Business Continuities'); ?>
				</th>
				<th>
					<?php echo __('Controls'); ?>
				</th>
				<th>
					<?php echo __('Policies'); ?>
				</th>
				<th>
					<?php echo __('Projects'); ?>
				</th>
				<th>
					<?php echo __('GDPR'); ?>
				</th>
				<?php if (!empty($customFields_enabled) && !empty($customFields_data)) : ?>
					<th><?php echo __('Custom Fields') ?></th>
				<?php endif; ?>
				<th class="align-center"><?php echo __('Action'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($data as $dataAsset) : ?>
				<tr>
					<td>
						<span class="bs-popover" data-trigger="hover" data-placement="right" data-html="true" data-original-title="<?php echo __( 'Description' ); ?>" data-content="<?php echo nl2br(h($dataAsset['DataAsset']['description'])) ?>">
							<?php echo $dataAsset['DataAsset']['title'] ?>
							<i class="icon-info-sign"></i>
						</span>
					</td>
					<td><?php echo $this->Eramba->getEmptyValue(implode(Hash::extract($dataAsset, 'BusinessUnit.{n}.name'), ', ')) ?></td>
					<td><?php echo $this->Eramba->getEmptyValue(implode(Hash::extract($dataAsset, 'ThirdParty.{n}.name'), ', ')) ?></td>
					<td>
						<?php 
						if (!empty($dataAsset['DataAsset']['stats']['risksCount'])) {
							echo $this->AdvancedFilters->getItemFilteredLink(__('List (%d)', $dataAsset['DataAsset']['stats']['risksCount']), 'Risk', null, ['query' => [
								'data_asset_id' => $dataAsset['DataAsset']['id'],
							]]);
						}
						else {
							echo __('None');
						}
						?>
					</td>
					<td>
						<?php 
						if (!empty($dataAsset['DataAsset']['stats']['thirdPartyRisksCount'])) {
							echo $this->AdvancedFilters->getItemFilteredLink(__('List (%d)', $dataAsset['DataAsset']['stats']['thirdPartyRisksCount']), 'ThirdPartyRisk', null, ['query' => [
								'data_asset_id' => $dataAsset['DataAsset']['id'],
							]]);
						}
						else {
							echo __('None');
						}
						?>
					</td>
					<td>
						<?php 
						if (!empty($dataAsset['DataAsset']['stats']['businessContinuitiesCount'])) {
							echo $this->AdvancedFilters->getItemFilteredLink(__('List (%d)', $dataAsset['DataAsset']['stats']['businessContinuitiesCount']), 'BusinessContinuity', null, ['query' => [
								'data_asset_id' => $dataAsset['DataAsset']['id'],
							]]);
						}
						else {
							echo __('None');
						}
						?>
					</td>
					<td>
						<?php 
						if (!empty($dataAsset['DataAsset']['stats']['securityServicesCount'])) {
							echo $this->AdvancedFilters->getItemFilteredLink(__('List (%d)', $dataAsset['DataAsset']['stats']['securityServicesCount']), 'SecurityService', null, ['query' => [
								'data_asset_id' => $dataAsset['DataAsset']['id'],
							]]);
						}
						else {
							echo __('None');
						}
						?>
					</td>
					<td>
						<?php 
						if (!empty($dataAsset['DataAsset']['stats']['securityPoliciesCount'])) {
							echo $this->AdvancedFilters->getItemFilteredLink(__('List (%d)', $dataAsset['DataAsset']['stats']['securityPoliciesCount']), 'SecurityPolicy', null, ['query' => [
								'data_asset_id' => $dataAsset['DataAsset']['id'],
							]]);
						}
						else {
							echo __('None');
						}
						?>
					</td>
					<td>
						<?php 
						if (!empty($dataAsset['DataAsset']['stats']['projectsCount'])) {
							echo $this->AdvancedFilters->getItemFilteredLink(__('List (%d)', $dataAsset['DataAsset']['stats']['projectsCount']), 'Project', null, ['query' => [
								'data_asset_id' => $dataAsset['DataAsset']['id'],
							]]);
						}
						else {
							echo __('None');
						}
						?>
					</td>
					<td>
						<?php 
						if (!empty($dataAsset['DataAsset']['stats']['dataAssetGdprCount'])) {
							$gdprFields = ['id', 'data_asset_status_id', 'title'];
							foreach (DataAssetGdpr::$fieldGroups[$dataAsset['DataAsset']['data_asset_status_id']] as $item) {
								$gdprFields[] = Inflector::underscore($item);
							}
							echo $this->AdvancedFilters->getItemFilteredLink(__('List (%d)', $dataAsset['DataAsset']['stats']['dataAssetGdprCount']), 'DataAsset', null, [
								'query' => [
									'id' => $dataAsset['DataAsset']['id'],
								],
								'controller' => 'dataAssets',
								'show' => $gdprFields
							]);
						}
						else {
							echo __('None');
						}
						?>
					</td>
					<?php if (!empty($customFields_enabled) && !empty($customFields_data)) : ?>
						<td><?php echo $this->CustomFields->advancedFilterLink($customFields_data, array('id', 'title'), array('id' => $dataAsset['DataAsset']['id'])); ?></td>
					<?php endif; ?>
					<td class="align-center">
						<?php
						echo $this->Ajax->getActionList($dataAsset['DataAsset']['id'], array(
							'controller' => 'dataAssets',
							'model' => 'DataAsset',
							'style' => 'icons',
							'item' => $dataAsset['DataAsset'],
							'history' => true
						));
						?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php else : ?>
	<?php echo $this->Eramba->getNotificationBox(__('No flow analysis has been created for this stage yet. Click on Manage / Add New Analysis if you want to create one.')); ?>
<?php endif; ?>