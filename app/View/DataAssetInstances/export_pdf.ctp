<?php 
App::uses('DataAsset', 'Model');
App::uses('Country', 'Model');
App::uses('Hash', 'Utility');
?>
<div class="row">
    <div class="col-xs-12">

        <div class="header">
            <div class="title">
                <h1>
                    <?php echo __('Data Asset Analysis'); ?>
                </h1>
            </div>
            <div class="subtitle">
                <h2>
                    <?php echo __('General Asset information'); ?>
                </h2>
            </div>
        </div>

    </div>
</div>
<div class="row">
    <div class="col-xs-12">

        <div class="body">
            <div class="item">
                <table class="triple-column" style="table-layout:fixed;">
                    <tr>
                        <th>
                            <?php echo __('Name'); ?>
                        </th>
                        <th>
                            <?php echo __('Status'); ?>
                        </th>
                        <th>
                            <?php echo __('GDPR'); ?>
                        </th>
                    </tr>
                    <tr>
                        <td>
                            <?php echo $item['Asset']['name']; ?>
                        </td>
                        <td>
                            <?php echo $this->ObjectStatus->get($item, 'DataAssetInstance'); ?>
                        </td>
                        <td>
                            <?php echo (!empty($item['DataAssetSetting']['gdpr_enabled'])) ? __('Yes') : $this->Eramba->getEmptyValue('') ?>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="item">
                <table>
                    <tr>
                        <th>
                            <?php echo __('Description'); ?>
                        </th>
                    </tr>
                    <tr>
                        <td>
                            <?php
                            if (!empty($item['Asset']['description'])) {
                                echo nl2br($item['Asset']['description']);
                            }
                            else {
                                echo '-';
                            }
                            ?>
                        </td>
                    </tr>
                </table>
            </div>

            <?php if (!empty($item['DataAssetSetting']['gdpr_enabled'])) : ?>
                <div class="item">
                    <table>
                        <tr>
                            <th>
                                <?php echo __('Driver for Compliance'); ?>
                            </th>
                        </tr>
                        <tr>
                            <td>
                                <?php echo nl2br(h($item['DataAssetSetting']['driver_for_compliance'])); ?>
                            </td>
                        </tr>
                    </table>
                </div>
            <?php endif; ?>

            <?php if (!empty($item['DataAssetSetting']['gdpr_enabled'])) : ?>
                <div class="separator"></div>
                <div class="item">
                    <table style="table-layout:fixed !important;">
                        <tr>
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
                        </tr>
                        <tr>
                            <td>
                                <?php echo $this->DataAssetSettings->getDpo($item); ?>
                            </td>
                            <td>
                                <?php echo implode(Hash::extract($item, 'DataAssetSetting.Processor.{n}.name'), ', ') ?>
                            </td>
                            <td>
                                <?php echo implode(Hash::extract($item, 'DataAssetSetting.Controller.{n}.name'), ', ') ?>
                            </td>
                            <td>
                                <?php echo $this->DataAssetSettings->getControllerRepresentative($item); ?>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="item">
                    <table style="table-layout:fixed !important;">
                        <tr>
                            <th>
                                <?php echo __('Supervisory Authority'); ?>
                            </th>
                        </tr>
                        <tr>
                            <td>
                                <?php
                                $countryIds = Hash::extract($item, 'DataAssetSetting.SupervisoryAuthority.{n}.country_id');
                                $countries = [];
                                foreach ($countryIds as $countryId) {
                                    $countries[] = Country::countries()[$countryId];
                                }
                                echo implode($countries, ', ');
                                ?>
                            </td>
                        </tr>
                    </table>
                </div>
            <?php endif; ?>

            <div class="separator"></div>

            <div class="item">
                <table style="table-layout:fixed !important;">
                    <tr>
                        <th>
                            <?php echo __('Type'); ?>
                        </th>
                        <th>
                            <?php echo __('Data Owner'); ?>
                        </th>
                        <th>
                            <?php echo __('Label'); ?>
                        </th>
                        <th>
                            <?php echo __('Liabilities'); ?>
                        </th>
                        <th>
                            <?php echo __('Review Date'); ?>
                        </th>
                    </tr>
                    <tr>
                        <td>
                            <?php echo $item['Asset']['AssetMediaType']['name']; ?>
                        </td>
                        <td>
                            <?= $this->UserField->convertAndShowUserFieldRecords('DataAssetSetting', 'DataOwner', isset($item['DataAssetSetting']) ? $item['DataAssetSetting'] : []); ?>
                        </td>
                        <td>
                            <?php echo isset( $item['Asset']['AssetLabel']['name'] ) ? $item['Asset']['AssetLabel']['name'] : '-'; ?>
                        </td>
                        <td>
                            <?php
                            $legals = array();
                            foreach ($item['Asset']['Legal'] as $legal) {
                                $legals[] = $legal['name'];
                            }
                            echo implode(', ', $legals);
                            ?>
                        </td>
                        <td>
                            <?php echo $item['Asset']['review']; ?>
                        </td>
                    </tr>

                </table>
            </div>

            <div class="item">
                <table class="triple-column" style="table-layout:fixed;">
                    <tr>
                        <th>
                            <?php echo __('Owner'); ?>
                        </th>
                        <th>
                            <?php echo __('Guardian'); ?>
                        </th>
                        <th>
                            <?php echo __('User'); ?>
                        </th>
                    </tr>
                    <tr>
                        <td>
                            <?php echo ( ! empty( $item['Asset']['AssetOwner'] ) ) ? $item['Asset']['AssetOwner']['name'] : ''; ?>
                        </td>
                        <td>
                            <?php echo ( ! empty( $item['Asset']['AssetGuardian'] ) ) ? $item['Asset']['AssetGuardian']['name'] : ''; ?>
                        </td>
                        <td>
                            <?php echo ( ! empty( $item['Asset']['asset_user_id'] ) ) ? $item['Asset']['AssetUser']['name'] : __('Everyone'); ?>
                        </td>
                    </tr>

                </table>
            </div>  
        </div>
    </div>
</div>
<?php foreach (DataAsset::statuses() as $statusId => $status) : ?>
    <?php 
    $dataAssets = Hash::extract($item['DataAsset'], '{n}[data_asset_status_id=' . $statusId . ']');
    ?>
    <div class="separator"></div>
    <div class="row">
        <div class="col-xs-12">
            <div class="header">
                <div class="subtitle">
                    <h2>
                        <?php echo __('Stage') . ': ' . $status ?>
                    </h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">

            <div class="body">
                <div class="item">
                    <?php if (!empty($dataAssets)) : ?>
                        <table class="special-borders">
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
                            </tr>
                            <?php foreach ($dataAssets as $dataAsset) : ?>
                                <tr>
                                    <td><?php echo $dataAsset['title'] ?></td>
                                    <td><?php echo $this->Eramba->getEmptyValue(implode(Hash::extract($dataAsset, 'BusinessUnit.{n}.name'), ', ')) ?></td>
                                    <td><?php echo $this->Eramba->getEmptyValue(implode(Hash::extract($dataAsset, 'ThirdParty.{n}.name'), ', ')) ?></td>
                                    <td>
                                        <?php 
                                        if (!empty($dataAsset['stats']['risksCount'])) {
                                            echo $this->AdvancedFilters->getItemFilteredLink(__('List (%d)', $dataAsset['stats']['risksCount']), 'Risk', null, ['query' => [
                                                'data_asset_id' => $dataAsset['id'],
                                            ]]);
                                        }
                                        else {
                                            echo __('None');
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                        if (!empty($dataAsset['stats']['thirdPartyRisksCount'])) {
                                            echo $this->AdvancedFilters->getItemFilteredLink(__('List (%d)', $dataAsset['stats']['thirdPartyRisksCount']), 'ThirdPartyRisk', null, ['query' => [
                                                'data_asset_id' => $dataAsset['id'],
                                            ]]);
                                        }
                                        else {
                                            echo __('None');
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                        if (!empty($dataAsset['stats']['businessContinuitiesCount'])) {
                                            echo $this->AdvancedFilters->getItemFilteredLink(__('List (%d)', $dataAsset['stats']['businessContinuitiesCount']), 'BusinessContinuity', null, ['query' => [
                                                'data_asset_id' => $dataAsset['id'],
                                            ]]);
                                        }
                                        else {
                                            echo __('None');
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                        if (!empty($dataAsset['stats']['securityServicesCount'])) {
                                            echo $this->AdvancedFilters->getItemFilteredLink(__('List (%d)', $dataAsset['stats']['securityServicesCount']), 'SecurityService', null, ['query' => [
                                                'data_asset_id' => $dataAsset['id'],
                                            ]]);
                                        }
                                        else {
                                            echo __('None');
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                        if (!empty($dataAsset['stats']['securityPoliciesCount'])) {
                                            echo $this->AdvancedFilters->getItemFilteredLink(__('List (%d)', $dataAsset['stats']['securityPoliciesCount']), 'SecurityPolicy', null, ['query' => [
                                                'data_asset_id' => $dataAsset['id'],
                                            ]]);
                                        }
                                        else {
                                            echo __('None');
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                        if (!empty($dataAsset['stats']['projectsCount'])) {
                                            echo $this->AdvancedFilters->getItemFilteredLink(__('List (%d)', $dataAsset['stats']['projectsCount']), 'Project', null, ['query' => [
                                                'data_asset_id' => $dataAsset['id'],
                                            ]]);
                                        }
                                        else {
                                            echo __('None');
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                        if (!empty($dataAsset['stats']['dataAssetGdprCount'])) {
                                            echo $this->AdvancedFilters->getItemFilteredLink(__('List (%d)', $dataAsset['stats']['dataAssetGdprCount']), 'DataAsset', null, ['query' => [
                                                'id' => $dataAsset['id'],
                                            ], 'controller' => 'dataAssets']);
                                        }
                                        else {
                                            echo __('None');
                                        }
                                        ?>
                                    </td>
                                    <?php if (!empty($customFields_enabled) && !empty($customFields_data)) : ?>
                                        <td><?php echo $this->CustomFields->advancedFilterLink($customFields_data, array('id', 'title'), array('id' => $dataAsset['DataAsset']['id'])); ?></td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    <?php else : ?>
                        <?php echo $this->Eramba->getNotificationBox(__('TBD Empty Message')); ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>