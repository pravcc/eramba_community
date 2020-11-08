<?php
App::uses('DataAsset', 'Model');
App::uses('DataAssetGdpr', 'Model');
App::uses('DataAssetGdprDataType', 'Model');
App::uses('DataAssetGdprLawfulBase', 'Model');
// debug($dataAssetInstance);
?>
<div class="row">
	<div class="col-lg-7">
		<div class="widget box widget-form">
			<div class="widget-header">
				<h4>&nbsp;</h4>
			</div>
			<div class="widget-content">

				<?php
					if (isset($edit)) {
						echo $this->Form->create( 'DataAsset', array(
							'url' => array( 'controller' => 'dataAssets', 'action' => 'edit' ),
							'class' => 'form-horizontal row-border',
							'novalidate' => true
						) );
						echo $this->Form->input( 'id', array( 'type' => 'hidden' ) );
						$submit_label = __( 'Edit' );
					}
					else {
						echo $this->Form->create( 'DataAsset', array(
							'url' => array( 'controller' => 'dataAssets', 'action' => 'add', $dataAssetInstance['DataAssetInstance']['id'] ),
							'class' => 'form-horizontal row-border',
							'novalidate' => true
						) );
						$submit_label = __( 'Add' );
					}
				?>

				<?php echo $this->Form->input( 'data_asset_instance_id', array(
					'type' => 'hidden',
				) ); ?>

				<div class="tabbable box-tabs box-tabs-styled">
					<ul class="nav nav-tabs">
						<li class="active"><a href="#tab_general" data-toggle="tab"><?php echo __('General'); ?></a></li>
						<li><a href="#risk-management" data-toggle="tab"><?php echo __('Risk Management'); ?></a></li>
						<li><a href="#mitigating-controls" data-toggle="tab"><?php echo __('Mitigating Controls'); ?></a></li>
						<li><a href="#gdpr" data-toggle="tab"><?php echo __('GDPR'); ?></a></li>
						<?php echo $this->element('CustomFields.tabs'); ?>
					</ul>
					<div class="tab-content">
						<div class="tab-pane fade in active" id="tab_general">
							<?php
                            echo $this->FieldData->input($FieldDataCollection->data_asset_status_id, [
                            	'div' => 'form-group data-asset-status-id-wrapper',
                            	'id' => 'data-asset-status-id'
                        	]);
                        	echo $this->FieldData->inputs([
                        		$FieldDataCollection->title,
                        		$FieldDataCollection->description,
                    		]);
                    		echo $this->QuickActionFields->input($FieldDataCollection->BusinessUnit, [
                    			'data-quick-add' => [
									'url' => ['controller' => 'businessUnits', 'action' => 'add'],
									'text' => __('Add Business Unit')
								],
                			]);
							echo $this->QuickActionFields->input($FieldDataCollection->ThirdParty, [
                    			'data-quick-add' => [
									'url' => ['controller' => 'thirdParties', 'action' => 'add'],
									'text' => __('Add Third Party')
								],
                			]);
                    		if (!empty($order)) {
                    			echo $this->FieldData->input($FieldDataCollection->order, [
	                            	'type' => 'select',
	                            	'options' => $order
	                        	]);
                    		}
                            ?>
						</div>
						<div class="tab-pane fade in" id="risk-management">
							<?php
                    		echo $this->QuickActionFields->input($FieldDataCollection->Risk, [
                    			'data-quick-add' => [
									'url' => ['controller' => 'risks', 'action' => 'add'],
									'text' => __('Add Asset Risk')
								],
								'id' => 'risk-id',
                			]);
                			echo $this->QuickActionFields->input($FieldDataCollection->ThirdPartyRisk, [
                    			'data-quick-add' => [
									'url' => ['controller' => 'thirdPartyRisks', 'action' => 'add'],
									'text' => __('Add Third Party Risk')
								],
								'id' => 'third-party-risk-id',
                			]);
                			echo $this->QuickActionFields->input($FieldDataCollection->BusinessContinuity, [
                    			'data-quick-add' => [
									'url' => ['controller' => 'businessContinuities', 'action' => 'add'],
									'text' => __('Add Business Continuity')
								],
								'id' => 'business-continuity-id',
                			]);
                            ?>
						</div>
						<div class="tab-pane fade in" id="mitigating-controls">
							<?php
                			echo $this->QuickActionFields->input($FieldDataCollection->SecurityService, [
                    			'data-quick-add' => [
									'url' => ['controller' => 'securityServices', 'action' => 'add'],
									'text' => __('Add Compensating control')
								],
								'id' => 'security-service-id'
                			]);
							echo $this->QuickActionFields->input($FieldDataCollection->Project, [
                    			'data-quick-add' => [
									'url' => ['controller' => 'projects', 'action' => 'add'],
									'text' => __('Add Project')
								],
								'id' => 'project-id'
                			]);
							echo $this->QuickActionFields->input($FieldDataCollection->SecurityPolicy, [
                    			'data-quick-add' => [
									'url' => ['controller' => 'securityPolicies', 'action' => 'add'],
									'text' => __('Add Security Policy')
								],
								'id' => 'security-policy-id'
                			]);
                            ?>
						</div>
						<div class="tab-pane fade in" id="gdpr">
							<?php
							$activeStatus = (isset($this->request->data['DataAsset']['data_asset_status_id'])) ? $this->request->data['DataAsset']['data_asset_status_id'] : DataAsset::STATUS_COLLECTED;
							$infoMessage = ($dataAssetInstance['DataAssetSetting']['gdpr_enabled']) ? __('Since GDPR is enabled all fields in this tab must be completed.') : __('Since GDPR is disabled at the asset general attributes this fields are disabled.');

							if (isset($this->request->data['DataAssetGdpr']['id'])) {
								echo $this->Form->input('DataAssetGdpr.id', array(
									'type' => 'hidden',
								));
							}
							$disabled = ($dataAssetInstance['DataAssetSetting']['gdpr_enabled']) ? false : true;
							$allCountriesCheckbox = $this->Form->input('DataAssetGdpr.third_party_involved_all', [
                                'type' => 'checkbox',
                                'label' => __('Anywhere in the world'),
                                'id' => 'third-party-involved-all',
                            ]);
                            $thirdPartyInvolvedDesc = $this->Html->tag('span', $FieldDataCollectionGdpr->ThirdPartyInvolved->getDescription(), [
                                'class' => 'help-block'
                            ]);
							foreach (DataAssetGdpr::$fieldGroups as $key => $fieldGroup) {
								$inputs = '';
								$hidden = ($activeStatus != $key) ? 'hidden' : '';
								if (!empty($fieldGroup)) {
									foreach ($fieldGroup as $field) {
										$options = [
											'disabled' => $disabled,
										];
										if ($field == 'ThirdPartyInvolved') {
											$options['id'] = 'third-party-involved';
											$options['after'] = $allCountriesCheckbox . $thirdPartyInvolvedDesc . '</div>';
										}

										if ($field == 'DataAssetGdprArchivingDriver') {
											$archivingDriverEmpty = $this->Form->input('DataAssetGdpr.archiving_driver_empty', [
				                                'type' => 'checkbox',
				                                'label' => __('Not applicable'),
				                            ]);
				                            $archivingDriverEmptyScript = $this->Html->scriptBlock("$('#DataAssetGdprArchivingDriverEmpty').on('change', function() {
				                                if ($(this).is(':checked')) {
				                                    $('#DataAssetGdprDataAssetGdprArchivingDriver').select2('val', '');
				                                }
				                            });
				                            $('#DataAssetGdprDataAssetGdprArchivingDriver').on('select2-selecting', function(e) { 
				                                $('#DataAssetGdprArchivingDriverEmpty').prop('checked', false);
				                            });");
											$options['beforeAfter'] = $archivingDriverEmpty . $archivingDriverEmptyScript;
										}

										$inputs .= $this->FieldData->input($FieldDataCollectionGdpr->$field, $options);
										if ($field == 'DataAssetGdprDataType') {
											$inputs .= $this->Eramba->getNotificationBox(__('Sensitive Data: Processing sensitive data may be prohibited unless certain conditions apply. You may want to review Rec.51-56; Art.9, Art.9(2)(a) to Art.9(2)(j) and Art.9(4).'), [
												'class' => 'data-asset-gdpr-data-type-info data-asset-gdpr-data-type-info-' . DataAssetGdprDataType::SENSITIVE
											]);
											$inputs .= $this->Eramba->getNotificationBox(__('Criminal Offences: You may want to look at certain constraints stated on Art.10, 23(1)(j).'), [
												'class' => 'data-asset-gdpr-data-type-info data-asset-gdpr-data-type-info-' . DataAssetGdprDataType::CRIMINAL_OFFENCES
											]);
										}
										if ($field == 'DataAssetGdprLawfulBase') {
											$inputs .= $this->Eramba->getNotificationBox(__('Consent: You may want to look at certain constraints stated on Rec.32, 43; Art.7(4), Rec.32; Art.6(1)(a), Rec.32, 42; Art.4(11), 7(1), Rec 32, Art.7(2), Rec.42, 65; Art.7(3), Rec.111; Art.49(1)(a), (3), Rec.171 and Rec.42; Art.7(1).'), [
												'class' => 'data-asset-gdpr-lawful-base-info data-asset-gdpr-lawful-base-info-' . DataAssetGdprLawfulBase::CONSENT
											]);
										}
										// if ($field == 'transfer_outside_eea') {
										// 	$inputs .= $this->Eramba->getNotificationBox(__('TBD'), [
										// 		'class' => 'data-asset-gdpr-transfer-outside-eea-info'
										// 	]);
										// }
									}

									$inputs = $this->Eramba->getNotificationBox($infoMessage) . $inputs;
								}
								else {
									$inputs = $this->Ux->getAlert(__('There are not GDPR related fields for this stage.'));
								}
								
								echo $this->Html->div('gdpr-field-group ' . $hidden, $inputs, [
									'id' => 'gdpr-field-group-' . $key
								]);
							}
							// echo $this->Form->input('DataAssetGdprDataType', array(
							// 	'options' => $dataAssetGdprDataTypes,
							// 	'label' => false,
							// 	'div' => false,
							// 	'class' => 'form-control',
							// ));
                            ?>
						</div>

						<?php echo $this->element('CustomFields.tabs_content'); ?>
					</div>
				</div>

				<div class="form-actions">
					<?php echo $this->Form->submit( $submit_label, array(
						'class' => 'btn btn-primary',
						'div' => false
					) ); ?>
					&nbsp;
					<?php
					echo $this->Ajax->cancelBtn('DataAsset');
					?>
				</div>

				<?php echo $this->Form->end(); ?>

			</div>
		</div>
	</div>
	<div class="col-lg-5">
		<?php
		echo $this->element('ajax-ui/sidebarWidget', array(
			'model' => 'DataAsset',
			'id' => isset($edit) ? $this->data['DataAsset']['id'] : null
		));
		?>
	</div>
</div>
<script type="text/javascript">
$(function() {
	$('#risk-id').erambaAutoComplete({
		url: '/dataAssets/getAssociatedRiskData',
		requestKey: ['riskIds'],
		requestType: 'GET',
		responseKey: ['securityServices', 'projects', 'securityPolicies'],
		assocInput: '#security-service-id, #project-id, #security-policy-id'
	});
	$('#third-party-risk-id').erambaAutoComplete({
		url: '/dataAssets/getAssociatedThirdPartyRiskData',
		requestKey: ['riskIds'],
		requestType: 'GET',
		responseKey: ['securityServices', 'projects', 'securityPolicies'],
		assocInput: '#security-service-id, #project-id, #security-policy-id'
	});
	$('#business-continuity-id').erambaAutoComplete({
		url: '/dataAssets/getAssociatedBusinessContinuityData',
		requestKey: ['riskIds'],
		requestType: 'GET',
		responseKey: ['securityServices', 'projects', 'securityPolicies'],
		assocInput: '#security-service-id, #project-id, #security-policy-id'
	});
	$('#security-service-id').erambaAutoComplete({
		url: '/dataAssets/getAssociatedSecurityServices',
		requestKey: ['serviceIds'],
		requestType: 'GET',
		assocInput: '#security-policy-id'
	});
	$('.data-asset-status-id-wrapper .col-md-10').append('<?php echo $this->Eramba->getNotificationBox('<span id="data-asset-status-info"></span>'); ?>');
	function updateStatusInfo() {
		var requestUrl = "<?= Router::url(['controller' => 'dataAssets', 'action' => 'getStatusInfo']); ?>" + '/' + $('#data-asset-status-id').val();
		$.ajax({
			url: requestUrl,
		}).done(function(data) {
			$('#data-asset-status-info').html(data);
		});
	}
	function unsetInputValues() {
		var status = $('#data-asset-status-id').val();
		$('.gdpr-field-group:not(#gdpr-field-group-' + status + ') textarea').val('');
		$('.gdpr-field-group:not(#gdpr-field-group-' + status + ') select').select2('val', '');
		$('.gdpr-field-group:not(#gdpr-field-group-' + status + ') input').each(function() {
			if ($(this).attr('type') == 'checkbox') {
				$(this).prop('checked', false);
			}
			else {
				$(this).val('');
			}
		});

		$.uniform.update();
	}
	function toggleGdprFieldGroup() {
		var status = $('#data-asset-status-id').val();
		$('.gdpr-field-group').addClass('hidden');
		$('#gdpr-field-group-' + status).removeClass('hidden');
		unsetInputValues();
	}
	toggleGdprFieldGroup();
	updateStatusInfo();
	$('#data-asset-status-id').on('change', function() {
		toggleGdprFieldGroup();
		updateStatusInfo();
	});
	//data type warning
    function toggleDataTypeWarning() {
    	$('.data-asset-gdpr-data-type-info').addClass('hidden');
    	if ($('#DataAssetGdprDataAssetGdprDataType').val() !== null) {
    		$('#DataAssetGdprDataAssetGdprDataType').val().forEach(function(item) {
	    		$('.data-asset-gdpr-data-type-info-' + item).removeClass('hidden');
	    	});
    	}
    }
	toggleDataTypeWarning();
    $('#DataAssetGdprDataAssetGdprDataType').on('change', function() {
        toggleDataTypeWarning();
    });
    //lawful base warning
    function toggleLawfulBaseWarning() {
    	$('.data-asset-gdpr-lawful-base-info').addClass('hidden');
    	if ($('#DataAssetGdprDataAssetGdprLawfulBase').val() !== null) {
    		$('#DataAssetGdprDataAssetGdprLawfulBase').val().forEach(function(item) {
	    		$('.data-asset-gdpr-lawful-base-info-' + item).removeClass('hidden');
	    	});
    	}
    }
	toggleLawfulBaseWarning();
    $('#DataAssetGdprDataAssetGdprLawfulBase').on('change', function() {
        toggleLawfulBaseWarning();
    });
 //    //lawful base warning
 //    function toggleTransferOutsideEeaWarning() {
 //    	$('.data-asset-gdpr-transfer-outside-eea-info').addClass('hidden');
 //    	if ($('#DataAssetGdprTransferOutsideEea').is(':checked')) {
 //    		$('.data-asset-gdpr-transfer-outside-eea-info').removeClass('hidden');
 //    	}
 //    }
	// toggleTransferOutsideEeaWarning();
 //    $('#DataAssetGdprTransferOutsideEea').on('change', function() {
 //        toggleTransferOutsideEeaWarning();
 //    });
    //all countries checkbox handle
    $('#third-party-involved-all').on('change', function() {
        if ($(this).is(':checked')) {
            $('#third-party-involved').select2('val', '');
        }
    });
    $('#third-party-involved').on('select2-selecting', function(e) { 
        $('#third-party-involved-all').prop('checked', false);
    });
    //disable inputs dependent on transfer outside EEA
    function toggleEeaFields() {
    	// var disabled = ($('#DataAssetGdprTransferOutsideEea').is(':checked')) ? true : false;
    	if (!$('#DataAssetGdprTransferOutsideEea').is(':checked')) {
    		$('#third-party-involved').prop('disabled', true).select2('val', '');
			$('#third-party-involved-all').prop('disabled', true).prop('checked', false);;
			$('#DataAssetGdprDataAssetGdprThirdPartyCountry').prop('disabled', true).select2('val', '');
    	}
    	else {
    		$('#third-party-involved').prop('disabled', false);
			$('#third-party-involved-all').prop('disabled', false);
			$('#DataAssetGdprDataAssetGdprThirdPartyCountry').prop('disabled', false);
    	}
    	
    }
    toggleEeaFields();
    $('#DataAssetGdprTransferOutsideEea').on('change', function() {
        toggleEeaFields();
    });
});
</script>