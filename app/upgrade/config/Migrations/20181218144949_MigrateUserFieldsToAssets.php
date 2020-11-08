<?php
use Phinx\Migration\AbstractMigration;

class MigrateUserFieldsToAssets extends AbstractMigration
{
    public function up()
    {
        if (class_exists('App')) {
            App::uses('ClassRegistry', 'Utility');
            App::uses('BusinessUnit', 'Model');
            App::uses('Asset', 'Model');

            $BusinessUnit = ClassRegistry::init('BusinessUnit');
            $Asset = ClassRegistry::init('Asset');

            $assets = $Asset->find('all', [
                'fields' => [
                    'Asset.id', 'Asset.asset_owner_id', 'Asset.asset_guardian_id', 'Asset.asset_user_id'
                ],
                'recursive' => -1
            ]);
            $businessUnits = $BusinessUnit->find('all', [
                'softDelete' => false,
                'contain' => [
                    'BusinessUnitOwner',
                    'BusinessUnitOwnerGroup'
                ]
            ]);

            $allBuData = [];
            foreach ($businessUnits as $businessUnit) {
                $allBuData[$businessUnit['BusinessUnit']['id']] = [];
                if (!empty($businessUnit['BusinessUnitOwner'])) {
                    foreach ($businessUnit['BusinessUnitOwner'] as $buOwner) {
                        $allBuData[$businessUnit['BusinessUnit']['id']][] = $buOwner['id'];
                    }
                }
            }

            $ret = true;
            $adminUser = 'User-' . ADMIN_ID;
            foreach ($assets as $data) {
                $newData = [
                    'Asset' => [
                        'id' => $data['Asset']['id'],
                        'AssetOwner' => [],
                        'AssetGuardian' => [],
                        'AssetUser' => []
                    ]
                ];
                // Get Business Unit owner ID for AssetOwner
                if (!empty($data['Asset']['asset_owner_id'])) {
                    $assetOwnerBuId = $data['Asset']['asset_owner_id'];
                    if ($assetOwnerBuId == BU_EVERYONE) {
                        $newData['Asset']['AssetOwner'][] = $adminUser;
                    } else {
                        if (!empty($allBuData[$assetOwnerBuId])) {
                            $newData['Asset']['AssetOwner'] = $allBuData[$assetOwnerBuId];
                        }
                    }
                }

                // AssetOwner is mandatory so if because of any reason it is empty, set Admin user automatically
                if (empty($newData['Asset']['AssetOwner'])) {
                    $newData['Asset']['AssetOwner'][] = $adminUser;
                }

                // Get Business Unit owner ID for AssetGuardian
                if (!empty($data['Asset']['asset_guardian_id'])) {
                    $assetGuardianBuId = $data['Asset']['asset_guardian_id'];
                    if (!empty($allBuData[$assetGuardianBuId])) {
                        $newData['Asset']['AssetGuardian'] = $allBuData[$assetGuardianBuId];
                    }
                }
                // Get Business Unit owner ID for AssetUser
                if (!empty($data['Asset']['asset_user_id'])) {
                    $assetUserBuId = $data['Asset']['asset_user_id'];
                    if (!empty($allBuData[$assetUserBuId])) {
                        $newData['Asset']['AssetUser'] = $allBuData[$assetUserBuId];
                    }
                }

                $ret &= $Asset->saveAssociated($newData, [
                    'deep' => true,
                    'fieldList' => ['AssetOwner', 'AssetGuardian', 'AssetUser'],
                    'customCallbacks' => [
                        'Asset' => [
                            'after' => false
                        ]
                    ]
                ]);
            }

            if (!$ret) {
                App::uses('CakeLog', 'Log');
                $errorMsg = "Error occured when migrating UserFields to Assets";
                CakeLog::write('error', $errorMsg);

                throw new Exception($errorMsg, 1);
                return false;
            }
        }
    }

    public function down()
    {
        $this->query("DELETE FROM `user_fields_users` WHERE `model`='Asset' AND `field` IN ('AssetOwner', 'AssetGuardian', 'AssetUser')");
        $this->query("DELETE FROM `user_fields_groups` WHERE `model`='Asset' AND `field` IN ('AssetOwnerGroup', 'AssetGuardianGroup', 'AssetUserGroup')");
    }
}
