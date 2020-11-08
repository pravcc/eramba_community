<?php
use Phinx\Migration\AbstractMigration;

class RestoreMissingUserFieldsFromHistory extends AbstractMigration
{
    public function up()
    {
        if (class_exists('App')) {
            App::uses('ClassRegistry', 'Utility');
            App::uses('Hash', 'Utility');

            $AuditModel = ClassRegistry::init('ObjectVersion.Audit');
            $UserModel = ClassRegistry::init('User');
            $GroupModel = ClassRegistry::init('Group');
            $UserFieldsUserModel = ClassRegistry::init('UserFields.UserFieldsUser');
            $UserFieldsGroupModel = ClassRegistry::init('UserFields.UserFieldsGroup');

            $modelList = [
                'PolicyException',
                'DataAssetInstance',
                'DataAsset',
                'AssetRisk',
                'RiskIncident',
                'RiskTreatment',
                'ThirdPartyRiskIncident',
                'ThirdPartyRiskTreatment',
                'BusinessContinuityPlan',
                'BusinessContinuityIncident',
                'BusinessContinuityTreatment',
                'SecurityPolicyReview',
                'RelatedDocuments',
                'SecurityPolicyTreatment',
                'SecurityPolicyIncident',
                'SecurityIncident',
                'Project',
                'SecurityPolicy',
                'CompliancePackageRegulator',
                'ComplianceAnalysisFinding',
                'ComplianceException',
                'ComplianceManagement',
                'Risk',
                'RiskReview',
                'RiskException',
                'ThirdPartyRisk',
                'ThirdPartyRiskReview',
                'ThirdParty',
                'ServiceContract',
                'SecurityService',
                'BusinessContinuity',
                'BusinessContinuityReview',
                'BusinessUnit',
                'AssetReview',
                'RelatedAssets',
                'Asset',
                'Legal',
                'BusinessContinuityTask',
                'DataAssetSetting',
                'ProjectAchievement',
                'Review',
                'SecurityServiceAudit',
                'SecurityServiceMaintenance'
            ];

            foreach ($modelList as $model) {
                $ModelObj = ClassRegistry::init($model);
                if (!$ModelObj->Behaviors->enabled('UserFields.UserFields')) {
                    continue;
                }

                if ($ModelObj->Behaviors->enabled('SoftDelete')) {
                    $ModelObj->softDelete(false);
                }
                
                $items = $ModelObj->find('all', [
                    'fields' => [
                        'id'
                    ]
                ]);
                foreach ($items as $item) {
                    // Get history of item
                    $itemHistory = $AuditModel->getHistory($model, $item[$model]['id']);
                    $itemHistory = Hash::extract($itemHistory, '{n}.AuditDelta.{n}');
                    //
                    // Get all UserFields fields from model
                    $fields = $ModelObj->Behaviors->UserFields->settings[$model]['fields'];

                    // Go through all fields
                    $stop = false;
                    foreach ($fields as $field) {
                        //
                        // Get newest property_name=UserFieldUser and property_name=UserFieldGroup
                        foreach ($itemHistory as $ih) {
                            if ($ih['property_name'] === $field) {

                                if ($ih['new_value'] !== "") {
                                    $userFields = explode(',', $ih['new_value']);
                                    foreach ($userFields as $userField) {
                                        // 
                                        // Check if UserFieldUser exists in users table and add it to user_fields_users table if do
                                        if (strpos($userField, 'User-') === 0) {
                                            $temp = explode('-', $userField);
                                            $userId = $temp[1];
                                            $exists = $UserFieldsUserModel->find('first', [
                                                'conditions' => [
                                                    'model' => $model,
                                                    'foreign_key' => $item[$model]['id'],
                                                    'field' => $field,
                                                    'user_id' => $userId
                                                ]
                                            ]);

                                            if (empty($exists) && $UserModel->exists($userId)) {
                                                $UserFieldsUserModel->clear();
                                                $UserFieldsUserModel->save([
                                                    'model' => $model,
                                                    'foreign_key' => $item[$model]['id'],
                                                    'field' => $field,
                                                    'user_id' => $userId
                                                ], false);
                                            }

                                        }
                                        //
                                        
                                        // 
                                        // Check if UserFieldGroup exists in groups table and add it to user_fields_groups table if do
                                        if (strpos($userField, 'Group-') === 0) {
                                            $field .= 'Group';
                                            $temp = explode('-', $userField);
                                            $groupId = $temp[1];
                                            $exists = $UserFieldsGroupModel->find('first', [
                                                'conditions' => [
                                                    'model' => $model,
                                                    'foreign_key' => $item[$model]['id'],
                                                    'field' => $field,
                                                    'group_id' => $groupId
                                                ]
                                            ]);

                                            if (empty($exists) && $GroupModel->exists($groupId)) {
                                                $UserFieldsGroupModel->clear();
                                                $UserFieldsGroupModel->save([
                                                    'model' => $model,
                                                    'foreign_key' => $item[$model]['id'],
                                                    'field' => $field,
                                                    'group_id' => $groupId
                                                ], false);
                                            }

                                        }
                                        //
                                    }
                                }

                                $stop = true;
                                continue;
                            }
                        }

                        if ($stop) {
                            continue;
                        }
                    }
                }

                if ($ModelObj->Behaviors->enabled('SoftDelete')) {
                    $ModelObj->softDelete(true);
                }
            }
        }
    }

    public function down()
    {
    }
}
