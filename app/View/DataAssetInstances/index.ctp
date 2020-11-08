<?php 
App::uses('DataAssetInstance', 'Model');
App::uses('DataAsset', 'Model');
App::uses('Country', 'Model');
App::uses('Hash', 'Utility');
// debug($data);
// exit;
?>
<div class="row">

	<div class="col-md-12">
		<div class="widget">
			<div class="btn-toolbar">
				<?php echo $this->AdvancedFilters->getViewList($savedFilters, 'DataAsset'); ?>

				<?php echo $this->Video->getVideoLink('DataAsset'); ?>

				<?php echo $this->CustomFields->getIndexLink(array(
					'DataAsset' => __('Data Flows'),
				)); ?>

				<?php echo $this->element(ADVANCED_FILTERS_ELEMENT_PATH . 'headerRight'); ?>
			</div>
		</div>
	</div>

</div>
<?php echo $this->element( CORE_ELEMENT_PATH . 'active_filter_box', array('filterName' => 'DataAsset')); ?>
<div class="row">
	<div class="col-md-12">
		<?php if ( ! empty( $data ) ) : ?>
			<?php foreach ( $data as $entry ) : ?>
				<div class="widget box <?= empty($activeDataAssetStatus) ? 'widget-closed' : '' ?>">
					<div class="widget-header">
						<h4><?php echo __( 'Asset' ); ?>: <?php echo $entry['Asset']['name']; ?></h4>
						<div class="toolbar no-padding">
							<div class="btn-group">
								<span class="btn btn-xs widget-collapse"><i class="icon-angle-<?= empty($activeDataAssetStatus) ? 'up' : 'down' ?>"></i></span>
								<span class="btn btn-xs dropdown-toggle" data-toggle="dropdown">
									<?php echo __( 'Manage' ); ?> <i class="icon-angle-down"></i>
								</span>
								<?php
								$this->Ajax->addToActionList(__('General Attributes'), [
									'controller' => 'dataAssetSettings',
									'action' => 'setup',
									$entry['DataAssetInstance']['id']
								], 'cog', 'edit');
								if ($entry['DataAssetInstance']['analysis_unlocked'] == DataAssetInstance::ANALYSIS_STATUS_UNLOCKED) {
									$this->Ajax->addToActionList(__('Add new analysis'), [
										'controller' => 'dataAssets',
										'action' => 'add',
										$entry['DataAssetInstance']['id']
									], 'plus-sign', 'add');
									$this->ObjectVersionHistory = $this->Helpers->load('ObjectVersion.ObjectVersionHistory');
									$url = $this->ObjectVersionHistory->getUrl('DataAssetSetting', $entry['DataAssetSetting']['id']);
									$this->Ajax->addToActionList(__('History'), $url, 'retweet', 'history');
									$this->Ajax->addToActionList(__('Export PDF'), [
										'controller' => 'dataAssetInstances',
										'action' => 'exportPdf',
										$entry['DataAssetInstance']['id']
									], 'file', false);
								}
								
								echo $this->Ajax->getUserDefinedActionList([
									'item' => $entry,
									'style' => 'normal'
								]);
								?>
							</div>
						</div>
					</div>
					<div class="widget-subheader">
						<table class="table table-hover table-striped table-bordered table-highlight-head">
							<thead>
								<tr>
									<th><?php echo __( 'Description' ); ?></th>
									<th>
										<div class="bs-popover" data-trigger="hover" data-placement="top" data-original-title="<?php echo __( 'Help' ); ?>" data-content='<?php echo __( 'What type of asset is this, remember you can define further types at Asset Management / Settings / Asset Types' ); ?>'>
											<?php echo __( 'Type' ); ?>
											<i class="icon-info-sign"></i>
										</div>
									</th>
									<th>
										<?php echo __('Data Owner'); ?>
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
								        <div class="bs-popover" data-trigger="hover" data-placement="top" data-original-title="<?php echo __( 'Help' ); ?>" data-content='<?php echo __( 'Assets must be reviewed at regular points in time to ensure they remain relevant and updated to the business. Notifications are triggered (optionaly) when this date arrives' ); ?>'>
											<?php echo __( 'Review Date' ); ?>
									        <i class="icon-info-sign"></i>
								        </div>
									</th>
									<th>
										<?php echo __('Status'); ?>
									</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>
										<?php
										echo $this->Eramba->getEmptyValue($entry['Asset']['description']);
										?>
									</td>
									<td><?php echo isset( $entry['Asset']['AssetMediaType']['name'] ) ? $entry['Asset']['AssetMediaType']['name'] : ''; ?></td>
									<td>
										<?= $this->UserField->convertAndShowUserFieldRecords('DataAssetSetting', 'DataOwner', isset($entry['DataAssetSetting']) ? $entry['DataAssetSetting'] : []); ?>
									</td>
									<td><?php echo isset( $entry['Asset']['AssetLabel']['name'] ) ? $entry['Asset']['AssetLabel']['name'] : ''; ?></td>
									<td>
										<?php
										$legals = array();
										foreach ($entry['Asset']['Legal'] as $legal) {
											$legals[] = $legal['name'];
										}
										echo implode(', ', $legals);
										?>
									</td>
									<td><?php echo $entry['Asset']['review']; ?></td>
									<td>
										<?php
										echo $this->ObjectStatus->get($entry, 'DataAssetInstance');
										?>
									</td>
								</tr>
							</tbody>
						</table>
					</div>

					<div class="widget-content">
						<table class="table table-hover table-striped table-bordered table-highlight-head">
							<thead>
								<tr>
									<th>
										<?php echo __('GDPR'); ?>
									</th>
									<?php if (!empty($entry['DataAssetSetting']['gdpr_enabled'])) : ?>
										<th>
											<?php echo __('DPO Role'); ?>
										</th>
										<th>
											<?php echo __('Processor Role'); ?>
										</th>
										<th>
											<?php echo __('Controller Role'); ?>
										</th>
										<th>
											<?php echo __('Controller Representative'); ?>
										</th>
										<th>
											<?php echo __('Supervisory Authority'); ?>
										</th>
									<?php endif; ?>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>
										<?php
										$gdprEnabled = (!empty($entry['DataAssetSetting']['gdpr_enabled'])) ? __('Yes') : $this->Eramba->getEmptyValue('');
										if ($entry['DataAssetSetting']['gdpr_enabled']) {
											$gdprLink = $this->AdvancedFilters->getItemFilteredLink(
												$gdprEnabled, 'DataAsset', 
												null, 
												[
													'query' => [
														'asset_id' => $entry['DataAssetInstance']['asset_id'],
														'asset_id__show' => true,
			                                            'gdpr_enabled__show' => true,
			                                            'driver_for_compliance__show' => true,
			                                            'dpo__show' => true,
			                                            'processor__show' => true,
			                                            'controller__show' => true,
			                                            'controller_representative__show' => true,
			                                            'supervisory_authority__show' => true,
			                                            'third_party_involved__show' => true,
			                                            'data_asset_status_id__show' => true,
			                                            'title__show' => true,
		                                        	]
	                                        	]
	                                        );
											$gdprEnabled = $this->Html->tag('span', $gdprLink . ' <i class="icon-info-sign"></i>', [
												'class' => 'bs-popover',
												'data-trigger' => 'hover',
												'data-placement' => 'right',
												'data-html' => 'true',
												'data-original-title' => __('Driver for Compliance'),
												'data-content' => nl2br(h($entry['DataAssetSetting']['driver_for_compliance']))
											]);
										}
										echo $gdprEnabled;
										?>
									</td>
									<?php if (!empty($entry['DataAssetSetting']['gdpr_enabled'])) : ?>
										<td>
											<?php echo $this->DataAssetSettings->getDpo($entry); ?>
										</td>
										<td>
											<?php echo implode(Hash::extract($entry, 'DataAssetSetting.Processor.{n}.name'), ', ') ?>
										</td>
										<td>
											<?php echo implode(Hash::extract($entry, 'DataAssetSetting.Controller.{n}.name'), ', ') ?>
										</td>
										<td>
											<?php echo $this->DataAssetSettings->getControllerRepresentative($entry); ?>
										</td>
										<td>
											<?php
											$countryIds = Hash::extract($entry, 'DataAssetSetting.SupervisoryAuthority.{n}.country_id');
											$countries = [];
											foreach ($countryIds as $countryId) {
												$countries[] = Country::countries()[$countryId];
											}
											echo implode($countries, ', ');
											?>
										</td>
									<?php endif; ?>
								</tr>
							</tbody>
						</table>

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
									<td><?php echo (!empty( $entry['Asset']['AssetOwner'])) ? $entry['Asset']['AssetOwner']['name'] : ''; ?></td>
									<td><?php echo (!empty( $entry['Asset']['AssetGuardian'])) ? $entry['Asset']['AssetGuardian']['name'] : ''; ?></td>
									<td><?php echo (!empty( $entry['Asset']['asset_user_id'])) ? $entry['Asset']['AssetUser']['name'] : __('Everyone'); ?></td>
								</tr>
							</tbody>
						</table>

						<?php foreach (DataAsset::statuses() as $statusId => $status) : ?>
							<div class="widget box widget-closed">
								<div class="widget-header">
									<h4><?php echo __('Stage') . ': ' . $status ?></h4>
									<div class="toolbar no-padding">
										<div class="btn-group">
											<?php 
											$url = Router::url(['controller' => 'dataAssetInstances', 'action' => 'listDataAssets', $entry['DataAssetInstance']['id'], $statusId]);
											?>
											<span data-url="<?= $url ?>" class="btn btn-xs widget-collapse btn-load-data-assets btn-load-data-assets-<?= $statusId ?>"><i class="icon-angle-up"></i></span>
										</div>
									</div>
								</div>
								<div class="widget-content" style="display:none;">
									<?php
									echo __('Loading data ...');
									?>
								</div>
							</div>
						<?php endforeach; ?>

					</div>
				</div>

			<?php endforeach; ?>

			<?php echo $this->element( CORE_ELEMENT_PATH . 'pagination' ); ?>
		<?php else : ?>
			<?php echo $this->element( 'not_found', array(
				'message' => __( 'There are no assets tagged as Data Asset. In order to use this module you need an asset (Asset Management / Asset Identification) tagged as Data Asset.' )
			) ); ?>
		<?php endif; ?>

	</div>
</div>

<script type="text/javascript">
$(function() {
	function loadDataAssets($link) {
		$contentWidget = $link.closest('.widget.box').find('.widget-content');
		Eramba.Ajax.blockEle($contentWidget);
		$.ajax({
			url: $link.data('url')
		}).done(function(response) {
			Eramba.Ajax.unblockEle($contentWidget);
			$contentWidget.html(response);
			Eramba.Ajax.UI.attachEvents();
		});
	}
	$('.btn-load-data-assets').one('click', function() {
		loadDataAssets($(this));
		return false;
	});
	<?php if (!empty($activeDataAssetStatus)) : ?>
		$('.btn-load-data-assets-<?= $activeDataAssetStatus ?>').trigger('click');
	<?php endif; ?>
});
</script>