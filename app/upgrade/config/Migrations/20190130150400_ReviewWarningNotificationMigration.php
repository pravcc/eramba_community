<?php
use Phinx\Migration\AbstractMigration;

class ReviewWarningNotificationMigration extends AbstractMigration
{
    public function up()
    {
        if (class_exists('App')) {
            App::uses('AppModule', 'Lib');
            $ret = true;
            if (AppModule::loaded('NotificationSystem')) {
                $ret &= $this->migrateReviewNotifications();
            }

            if (!$ret) {
                App::uses('CakeLog', 'Log');
                $log = "Error occured when processing database synchronization for reviews notifications.";
                CakeLog::write('error', "{$log}");

                throw new Exception($log, 1);
                return false;
            }
        }
    }

    public function down()
    {
    }

   public function migrateReviewNotifications()
    {
        $update = [
            'Asset' => [
                'asset_expiration_-1day' => 'review_expiration_-1day',
                'asset_expiration_-5day' => 'review_expiration_-5day',
                'asset_expiration_-10day' => 'review_expiration_-10day',
                'asset_expiration_-20day' => 'review_expiration_-20day',
                'asset_expiration_-30day' => 'review_expiration_-30day',
                'asset_expiration_+1day' => 'review_expiration_+1day',
                'asset_expiration_+5day' => 'review_expiration_+5day',
                'asset_expiration_+10day' => 'review_expiration_+10day',
                'asset_expiration_+20day' => 'review_expiration_+20day',
                'asset_expiration_+30day' => 'review_expiration_+30day',
            ],
            'SecurityPolicy' => [
                'security_policy_review_-1day' => 'review_expiration_-1day',
                'security_policy_review_-5day' => 'review_expiration_-5day',
                'security_policy_review_-10day' => 'review_expiration_-10day',
                'security_policy_review_-20day' => 'review_expiration_-20day',
                'security_policy_review_-30day' => 'review_expiration_-30day',
                'security_policy_review_+1day' => 'review_expiration_+1day',
                'security_policy_review_+5day' => 'review_expiration_+5day',
                'security_policy_review_+10day' => 'review_expiration_+10day',
                'security_policy_review_+20day' => 'review_expiration_+20day',
                'security_policy_review_+30day' => 'review_expiration_+30day',
            ],
            'Risk' => [
                'risk_expiration_-1day' => 'review_expiration_-1day',
                'risk_expiration_-5day' => 'review_expiration_-5day',
                'risk_expiration_-10day' => 'review_expiration_-10day',
                'risk_expiration_-20day' => 'review_expiration_-20day',
                'risk_expiration_-30day' => 'review_expiration_-30day',
                'risk_expiration_+1day' => 'review_expiration_+1day',
                'risk_expiration_+5day' => 'review_expiration_+5day',
                'risk_expiration_+10day' => 'review_expiration_+10day',
                'risk_expiration_+20day' => 'review_expiration_+20day',
                'risk_expiration_+30day' => 'review_expiration_+30day',
            ],
            'ThirdPartyRisk' => [
                'third_party_risk_expiration_-1day' => 'review_expiration_-1day',
                'third_party_risk_expiration_-5day' => 'review_expiration_-5day',
                'third_party_risk_expiration_-10day' => 'review_expiration_-10day',
                'third_party_risk_expiration_-20day' => 'review_expiration_-20day',
                'third_party_risk_expiration_-30day' => 'review_expiration_-30day',
                'third_party_risk_expiration_+1day' => 'review_expiration_+1day',
                'third_party_risk_expiration_+5day' => 'review_expiration_+5day',
                'third_party_risk_expiration_+10day' => 'review_expiration_+10day',
                'third_party_risk_expiration_+20day' => 'review_expiration_+20day',
                'third_party_risk_expiration_+30day' => 'review_expiration_+30day',
            ],
            'BusinessContinuity' => [
                'business_continuity_expiration_-1day' => 'review_expiration_-1day',
                'business_continuity_expiration_-5day' => 'review_expiration_-5day',
                'business_continuity_expiration_-10day' => 'review_expiration_-10day',
                'business_continuity_expiration_-20day' => 'review_expiration_-20day',
                'business_continuity_expiration_-30day' => 'review_expiration_-30day',
                'business_continuity_expiration_+1day' => 'review_expiration_+1day',
                'business_continuity_expiration_+5day' => 'review_expiration_+5day',
                'business_continuity_expiration_+10day' => 'review_expiration_+10day',
                'business_continuity_expiration_+20day' => 'review_expiration_+20day',
                'business_continuity_expiration_+30day' => 'review_expiration_+30day',
            ],
        ];

        $NotificationSystem = ClassRegistry::init('NotificationSystem.NotificationSystem');

        $ret = true;
        foreach ($update as $model => $items) {
            foreach ($items as $from => $to) {
                $field = [
                    'model' =>  "'" . $model . 'Review' . "'",
                    'filename' => "'" . $to . "'"
                ];

                $conditions = [
                    'NotificationSystem.model' => $model,
                    'NotificationSystem.filename' => $from
                ];

                $entries = $NotificationSystem->find('list', [
                    'conditions' => $conditions,
                    'fields' => ['id', 'id'],
                    'recursive' => -1
                ]);

                if (!empty($entries)) {
                    $ret &= (bool) $NotificationSystem->updateAll($field, $conditions);

                    foreach ($entries as $entryId) {
                        $ret &= (bool) $NotificationSystem->NotificationObject->deleteAll([
                            'NotificationObject.notification_system_item_id' => $entryId
                        ]);

                        $ret &= $NotificationSystem->sync($entryId);
                    }
                }
            }
        }

        return $ret;
    }
}
