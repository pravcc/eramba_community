<?php
use Phinx\Migration\AbstractMigration;

class AllButSettingsAclConfig extends AbstractMigration
{
    public function up()
    {
        if (class_exists('App')) {
            App::uses('ClassRegistry', 'Utility');
            
            $denyList = [
                'controllers/Settings/index',
                'controllers/Settings/edit',
                'controllers/Settings/edit',
                'controllers/Settings/logs',
                'controllers/Settings/deleteLogs',
                'controllers/Settings/downloadLogs',
                'controllers/Settings/zipErrorLogFiles',
                'controllers/Settings/getLogo',
                'controllers/Settings/testMailConnection',
                'controllers/Settings/resetDashboards',
                'controllers/Settings/customLogo',
                'controllers/Settings/deleteCache',
                'controllers/Settings/resetDatabase',
                'controllers/Settings/systemHealth',
                'controllers/Settings/getTimeByTimezone',
                'controllers/Settings/residualRisk',
                'controllers/LdapConnectors/index',
                'controllers/LdapConnectors/delete',
                'controllers/LdapConnectors/add',
                'controllers/LdapConnectors/edit',
                'controllers/LdapConnectors/authentication',
                'controllers/LdapConnectors/testLdapForm',
                'controllers/LdapConnectors/testLdap',
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

            if ($hasGroup) {
                $ret = true;
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

                    // throw new \Exception($log, 1);
                    // return false;
                }
            }
        }
    }

    public function down()
    {
    }
}
