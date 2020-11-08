<?php
use Phinx\Migration\AbstractMigration;

class NotificationsReportCustomRoleMigration extends AbstractMigration
{
    public function up()
    {
        if (class_exists('App')) {
            App::uses('AppModule', 'Lib');
            $ret = true;
            if (AppModule::loaded('NotificationSystem')) {
                $ret &= $this->sync();
            }

            if (!$ret) {
                App::uses('CakeLog', 'Log');
                $log = "Error occured when processing database synchronization for notification custom role sync.";
                CakeLog::write('error', "{$log}");

                throw new Exception($log, 1);
                return false;
            }
        }
    }

    public function down()
    {
    }

    public function sync() {
        //
        App::uses('Hash', 'Utility');
        $NotificationSystem = ClassRegistry::init('NotificationSystem.NotificationSystem');

        $reportNotif = $NotificationSystem->find('all', [
            'conditions' => [
                'NotificationSystem.type' => NOTIFICATION_TYPE_REPORT
            ],
            'contain' => [
                'NotificationUser',
                'NotificationUserCompletedFeedback'
            ],
            'recursive' => -1
        ]);

        $ret = true;
        $adminUser = 'User-' . ADMIN_ID;
        foreach ($reportNotif as $data) {
            $id = $data['NotificationSystem']['id'];
            $newData = [
                'NotificationSystem' => [
                    'id' => $id,
                    'type' => $data['NotificationSystem']['type'],
                    'filename' => $data['NotificationSystem']['filename'],
                    // 'NotificationUser' => [],
                    // 'NotificationUserCompletedFeedback' => [],
                ]
            ];

            $count = $NotificationSystem->NotificationCustomRole->find('count', [
                'conditions' => [
                    'NotificationCustomRole.notification_system_item_id' => $id
                ],
                'recursive' => -1
            ]);

            if ($count) {
                $ret &= $NotificationSystem->NotificationCustomRole->deleteAll([
                    'NotificationCustomRole.notification_system_item_id' => $id
                ]);

                $userData = Hash::extract($data, 'NotificationUser.{n}.id');

                if (!in_array($adminUser, $userData)) {
                    $userData[] = $adminUser;
                }

                $newData['NotificationSystem']['NotificationUser'] = $userData;
            }

            $count = $NotificationSystem->NotificationCustomRoleCompletedFeedback->find('count', [
                'conditions' => [
                    'NotificationCustomRoleCompletedFeedback.notification_system_item_id' => $id
                ],
                'recursive' => -1
            ]);

            if ($count) {
                $ret &= $NotificationSystem->NotificationCustomRoleCompletedFeedback->deleteAll([
                    'NotificationCustomRoleCompletedFeedback.notification_system_item_id' => $id
                ]);

                $userData = Hash::extract($data, 'NotificationUserCompletedFeedback.{n}.id');

                if (!in_array($adminUser, $userData)) {
                    $userData[] = $adminUser;
                }

                $newData['NotificationSystem']['NotificationUserCompletedFeedback'] = $userData;
            }

            $fieldList = [];
            if (isset($newData['NotificationSystem']['NotificationUser'])) {
                $fieldList[] = 'NotificationUser';
            }

            if (isset($newData['NotificationSystem']['NotificationUserCompletedFeedback'])) {
                $fieldList[] = 'NotificationUserCompletedFeedback';
            }
            
            if (!empty($fieldList)) {
                $ret &= $NotificationSystem->saveAssociated($newData, [
                    'deep' => true,
                    'fieldList' => $fieldList
                ]);
            }
        }

        return $ret;
    }
}
