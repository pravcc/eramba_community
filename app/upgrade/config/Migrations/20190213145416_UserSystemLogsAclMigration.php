<?php
use Phinx\Migration\AbstractMigration;

class UserSystemLogsAclMigration extends AbstractMigration
{
    public function up()
    {
        $this->_updateNotAdminGroupsForUserLogs();
    }

    public function down()
    {
    }

    protected function _updateNotAdminGroupsForUserLogs()
    {
        if (class_exists('App')) {
            App::uses('ClassRegistry', 'Utility');
     
            $Permission = ClassRegistry::init(['class' => 'Permission', 'alias' => 'Permission']);

            $groups = ClassRegistry::init('Group')->find('list', [
                'conditions' => [
                    'Group.id !=' => 10
                ],
                'fields' => [
                    'Group.id'
                ],
                'recursive' => -1
            ]);

            App::uses('CakeLog', 'Log');

            ClassRegistry::init('Setting')->syncAcl();

            $ret = true;

            foreach ($groups as $groupId) {
                $aclNode = [
                    'model' => 'Group',
                    'foreign_key' => $groupId
                ];

                $node = 'controllers/UserSystemLogs/index';

                $ret &= $r = $Permission->allow($aclNode, $node, '*', -1);
                if (!$r) {
                    CakeLog::write('debug', 'Node ACL cannot be configured:' . $node . ' - AND group - ' . print_r($aclNode, true));
                }
            }

            if (!$ret) {
                $log = 'Error occured when processing userSystemLogs acl sync.';
                CakeLog::write('debug', $log);
                throw new Exception($log, 1);
                return false;
            }
        }
    }
}
