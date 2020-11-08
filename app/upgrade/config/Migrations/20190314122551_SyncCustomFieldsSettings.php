<?php
use Phinx\Migration\AbstractMigration;

class SyncCustomFieldsSettings extends AbstractMigration
{
    public function up()
    {
        if (class_exists('App')) {
            App::uses('AppModule', 'Lib');

            if (AppModule::loaded('CustomFields')) {
                $setting = ClassRegistry::init('CustomFields.CustomFieldSetting');

                $models = [
                    'Asset',
                    'BusinessContinuity',
                    'BusinessUnit',
                    'ComplianceAnalysisFinding',
                    'ComplianceException',
                    'ComplianceManagement',
                    'DataAsset',
                    'Goal',
                    'Legal',
                    'PolicyException',
                    'Process',
                    'ProgramIssue',
                    'Project',
                    'Risk',
                    'RiskException',
                    'SecurityIncident',
                    'SecurityPolicy',
                    'SecurityService',
                    'SecurityServiceAudit',
                    'SecurityServiceMaintenance',
                    'ServiceContract',
                    'TeamRole',
                    'ThirdParty',
                    'ThirdPartyRisk',
                    'VendorAssessments.VendorAssessmentFinding'
                ];

                foreach ($models as $model) {
                    $modelInst = ClassRegistry::init($model);

                    if ($modelInst instanceof AppModel && $modelInst->Behaviors->enabled('CustomFields.CustomFields')) {
                        $setting->syncSetting($modelInst);
                    }
                }
            }
        }
    }

    public function down()
    {
        
    }
}
