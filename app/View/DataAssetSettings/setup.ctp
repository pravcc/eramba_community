<div class="row">
    <div class="col-lg-7">
        <div class="widget box widget-form">
            <div class="widget-header">
                <h4>&nbsp;</h4>
            </div>
            <div class="widget-content">

                <?php
                echo $this->Form->create('DataAssetSetting', [
                    'url' => ['controller' => 'dataAssetSettings', 'action' => 'setup', $dataAssetInstance['DataAssetInstance']['id']],
                    'class' => 'form-horizontal row-border',
                    'novalidate' => true
                ]);
                if (isset($edit)) {
                    echo $this->Form->input('id', ['type' => 'hidden']);
                    $submit_label = __( 'Edit' );
                }
                else {
                    $submit_label = __( 'Add' );
                }

                echo $this->Form->input('data_asset_instance_id', ['type' => 'hidden']);
                ?>

                <div class="tabbable box-tabs box-tabs-styled">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tab_general" data-toggle="tab"><?php echo __('General'); ?></a></li>
                        <li><a href="#gdpr" data-toggle="tab"><?php echo __('GDPR'); ?></a></li>
                        <?php echo $this->element('CustomFields.tabs'); ?>
                    </ul>
                    <div class="tab-content">

                        <div class="tab-pane fade in active" id="tab_general">
                            <?php
                            echo $this->FieldData->input($FieldDataCollection->name, [
                                'default' => (isset($dataAssetInstance['Asset']['name'])) ? $dataAssetInstance['Asset']['name'] : '',
                                'disabled' => true
                            ]);

                            /*
                            echo $this->QuickActionFields->input($FieldDataCollection->Owner, [
                                'default' => array_keys($buOwners),
                                'data-quick-add' => [
                                    'url' => ['controller' => 'users', 'action' => 'add'],
                                    'text' => __('Add Owner')
                                ],
                            ]);
                            */
                            ?>

                            <?= $this->FieldData->input($FieldDataCollection->DataOwner, [
                                'default' => !isset($edit) ? array_keys($buOwners) : []
                            ]); ?>
                        </div>
                        <div class="tab-pane fade in" id="gdpr">
                            <?php
                            echo $this->FieldData->input($FieldDataCollection->gdpr_enabled, [
                                'id' => 'gdpr-enabled'
                            ]);

                            echo $this->FieldData->input($FieldDataCollection->driver_for_compliance, [
                                'class' => ['gdpr-dependent']
                            ]);

                            $dpoEmpty = $this->Form->input('DataAssetSetting.dpo_empty', [
                                'type' => 'checkbox',
                                'label' => __('Not applicable'),
                            ]);
                            $dpoEmptyScript = $this->Html->scriptBlock("$('#DataAssetSettingDpoEmpty').on('change', function() {
                                if ($(this).is(':checked')) {
                                    $('#DataAssetSettingDpo').select2('val', '');
                                }
                            });
                            $('#DataAssetSettingDpo').on('select2-selecting', function(e) { 
                                $('#DataAssetSettingDpoEmpty').prop('checked', false);
                            });");
                            echo $this->QuickActionFields->input($FieldDataCollection->Dpo, [
                                'class' => ['gdpr-dependent'],
                                'data-quick-add' => [
                                    'url' => ['controller' => 'users', 'action' => 'add'],
                                    'text' => __('Add DPO Role')
                                ],
                                'beforeAfter' => $dpoEmpty . $dpoEmptyScript
                            ]);

                            $processorEmpty = $this->Form->input('DataAssetSetting.processor_empty', [
                                'type' => 'checkbox',
                                'label' => __('Not applicable'),
                            ]);
                            $processorEmptyScript = $this->Html->scriptBlock("$('#DataAssetSettingProcessorEmpty').on('change', function() {
                                if ($(this).is(':checked')) {
                                    $('#DataAssetSettingProcessor').select2('val', '');
                                }
                            });
                            $('#DataAssetSettingProcessor').on('select2-selecting', function(e) { 
                                $('#DataAssetSettingProcessorEmpty').prop('checked', false);
                            });");
                            echo $this->QuickActionFields->input($FieldDataCollection->Processor, [
                                'class' => ['gdpr-dependent'],
                                'data-quick-add' => [
                                    'url' => ['controller' => 'thirdParties', 'action' => 'add'],
                                    'text' => __('Add Processor Role')
                                ],
                                'beforeAfter' => $processorEmpty . $processorEmptyScript
                            ]);

                            $controllerEmpty = $this->Form->input('DataAssetSetting.controller_empty', [
                                'type' => 'checkbox',
                                'label' => __('Not applicable'),
                            ]);
                            $controllerEmptyScript = $this->Html->scriptBlock("$('#DataAssetSettingControllerEmpty').on('change', function() {
                                if ($(this).is(':checked')) {
                                    $('#controller-select').select2('val', '');
                                }
                            });
                            $('#controller-select').on('select2-selecting', function(e) { 
                                $('#DataAssetSettingControllerEmpty').prop('checked', false);
                            });");
                            echo $this->QuickActionFields->input($FieldDataCollection->Controller, [
                                'class' => ['gdpr-dependent'],
                                'data-quick-add' => [
                                    'url' => ['controller' => 'thirdParties', 'action' => 'add'],
                                    'text' => __('Add Controller Role')
                                ],
                                'id' => 'controller-select',
                                'beforeAfter' => $controllerEmpty . $controllerEmptyScript
                            ]);

                            echo $this->Eramba->getNotificationBox(__('If more than one controller has control over the data there might be additional legal constraints, please review items Rec.79; Art.4(7), 26 and Rec.79, 146; Art.26(3), 82(3)-(5).'), ['id' => 'controller-select-info']);

                            $controllerRepresentativeEmpty = $this->Form->input('DataAssetSetting.controller_representative_empty', [
                                'type' => 'checkbox',
                                'label' => __('Not applicable'),
                            ]);
                            $controllerRepresentativeEmptyScript = $this->Html->scriptBlock("$('#DataAssetSettingControllerRepresentativeEmpty').on('change', function() {
                                if ($(this).is(':checked')) {
                                    $('#DataAssetSettingControllerRepresentative').select2('val', '');
                                }
                            });
                            $('#DataAssetSettingControllerRepresentative').on('select2-selecting', function(e) { 
                                $('#DataAssetSettingControllerRepresentativeEmpty').prop('checked', false);
                            });");
                            echo $this->QuickActionFields->input($FieldDataCollection->ControllerRepresentative, [
                                'class' => ['gdpr-dependent'],
                                'data-quick-add' => [
                                    'url' => ['controller' => 'users', 'action' => 'add'],
                                    'text' => __('Add Controller Representative')
                                ],
                                'beforeAfter' => $controllerRepresentativeEmpty . $controllerRepresentativeEmptyScript
                            ]);

                            echo $this->FieldData->input($FieldDataCollection->SupervisoryAuthority, [
                                'class' => ['gdpr-dependent'],
                            ]);

                            ?>
                            <script type="text/javascript">
                            $(function() {
                                //gdpr toggle
                                function toggleGdprFields() {
                                    $('.gdpr-dependent').prop('disabled', !$('#gdpr-enabled').is(':checked'));
                                }

                                toggleGdprFields();
                                $('#gdpr-enabled').on('change', function() {
                                    toggleGdprFields();
                                });

                                //controller role warning
                                function toggleControllerWarning() {
                                    if ($('#controller-select').select2('data').length > 1) {
                                        $('#controller-select-info').removeClass('hidden');
                                    }
                                    else {
                                        $('#controller-select-info').addClass('hidden');
                                    }
                                }

                                toggleControllerWarning();
                                $('#controller-select').on('change', function() {
                                    toggleControllerWarning();
                                });
                            });
                            </script>
                        </div>

                        <?php echo $this->element('CustomFields.tabs_content'); ?>
                    </div>
                </div>

                <div class="form-actions">
                    <?php echo $this->Form->submit($submit_label, [
                        'class' => 'btn btn-primary',
                        'div' => false
                    ]); ?>
                    &nbsp;
                    <?php
                    echo $this->Ajax->cancelBtn('Asset');
                    ?>
                </div>

                <?php echo $this->Form->end(); ?>

            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <?php
        echo $this->element('ajax-ui/sidebarWidget', array(
            'model' => 'DataAssetSetting',
            'id' => isset($edit) ? $this->data['DataAssetSetting']['id'] : null
        ));
        ?>
    </div>
</div>
