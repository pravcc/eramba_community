<?php
use Phinx\Migration\AbstractMigration;

class Release67 extends AbstractMigration
{
    protected function bumpVersion($value) {
        $ret = true;

        $this->query("UPDATE `settings` SET `value`='" . $value . "' WHERE `settings`.`variable`='DB_SCHEMA_VERSION'");

        if (class_exists('App')) {
            $status = [];
            App::uses('Configure', 'Core');

            if (class_exists('Configure')) {
                Configure::write('Eramba.Settings.DB_SCHEMA_VERSION', $value);
            }

            // testing handler for exception
            if (Configure::read('Eramba.TRIGGER_UPDATE_FAIL') === true) {
                $status['ConfiguredFailTriggered'] = true;
                throw new Exception("This is a test exception for failed update.", 1);
                return false;
            }

            App::uses('ConnectionManager', 'Model');
            App::uses('ClassRegistry', 'Utility');

            $ds = ConnectionManager::getDataSource('default');
            $ds->cacheSources = false;

            ClassRegistry::init('Setting')->deleteCache(null);

            App::uses('AppModule', 'Lib');
            AppModule::loadAll();
            
            if (Configure::read('Eramba.version') === 'e1.0.6.066') {
                // $ret &= $this->updateAllButSettingsAcl();
            }
        
            ClassRegistry::init('Setting')->deleteCache(null);
        }

        if (!$ret) {
            App::uses('CakeLog', 'Log');
            $log = "Error occured when processing database synchronization for release 1.0.6.067.";
            CakeLog::write('error', "{$log} \n" . print_r($status, true));

            throw new Exception($log, 1);
            return false;
        }
    }

    protected function updateAllButSettingsAcl()
    {
        App::uses('ClassRegistry', 'Utility');
        
        $denyList = [
            'controllers/Settings/index',
            'controllers/Settings/edit',
            'controllers/Settings/edit',
            'controllers/Settings/logs',
            'controllers/Settings/deleteLogs',
            'controllers/Settings/downloadLogs',
            'controllers/Settings/getLogo',
            'controllers/Settings/testMailConnection',
            'controllers/Settings/resetDashboards',
            'controllers/Settings/customLogo',
            'controllers/Settings/deleteCache',
            'controllers/Settings/resetDatabase',
            'controllers/Settings/systemHealth',
            'controllers/Settings/getTimeByTimezone',
            'controllers/Settings/residualRisk',
            'controllers/Scopes/index',
            'controllers/Scopes/delete',
            'controllers/Scopes/edit',
            'controllers/Scopes/add',
            'controllers/Users/index',
            'controllers/Users/delete',
            'controllers/Users/add',
            'controllers/Users/edit',
            'controllers/Groups/index',
            'controllers/Groups/delete',
            'controllers/Groups/add',
            'controllers/Groups/edit',
            'controllers/Visualisation/VisualisationSettings/index',
            'controllers/Visualisation/VisualisationSettings/edit',
            'controllers/Visualisation/VisualisationSettings/sync',
            'controllers/OauthConnectors/index',
            'controllers/OauthConnectors/add',
            'controllers/OauthConnectors/edit',
            'controllers/OauthConnectors/delete',
            'controllers/Queue/index',
            'controllers/Cron/Cron/index',
            // 'controllers/NotificationSystem/listItems',
            'controllers/Acl/Aros/admin_ajax_role_permissions',
            'controllers/Acl/Aros/admin_grant_role_permission',
            'controllers/Acl/Aros/admin_deny_role_permission',
        ];

        $Permission = ClassRegistry::init(array('class' => 'Permission', 'alias' => 'Permission'));
        $allButSettings = [
            'model' => 'Group',
            'foreign_key' => 13
        ];

        $Group = ClassRegistry::init('Group');
        $hasGroup = $Group->find('count', [
            'conditions' => [
                'Group.id' => 13
            ],
            'recursive' => -1
        ]);

        App::uses('CakeLog', 'Log');

        $ret = true;
        if ($hasGroup) {
            ClassRegistry::init('Setting')->syncAcl();

            foreach ($denyList as $node) {
                $ret &= $r = $Permission->allow($allButSettings, $node, '*', -1);
                if (!$r) {
                    CakeLog::write('debug', 'Node ACL cannot be configured:' . $node);
                }
            }

            if (!$ret) {
                App::uses('CakeLog', 'Log');
                $log = "Error occured when processing ACL Sync for All but settings group.";
                CakeLog::write('debug', "{$log}");
            }
        }

        return $ret;
    }

    public function up()
    {
        $this->bumpVersion('e1.0.1.047');
    }

    public function down()
    {
        $this->bumpVersion('e1.0.1.046');
    }
}
