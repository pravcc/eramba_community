<?php
use Phinx\Migration\AbstractMigration;

class UserManagementGroupMigration extends AbstractMigration
{
    public function up()
    {
        // add users to visualisation settings
        $data = [
            [
                'model' => 'User',
                'status' => '1'
            ]
        ];

        $table = $this->table('visualisation_settings');
        $table->insert($data)->saveData();

        if (class_exists('App')) {
            App::uses('ClassRegistry', 'Utility');
            App::uses('CakeLog', 'Log');

            App::uses('VisualisationShell', 'Visualisation.Console/Command');
            $VisualisationShell = new VisualisationShell();
            $VisualisationShell->startup();
            $VisualisationShell->acl_sync();

            $this->_visualisationShareUsers();

            $Group = ClassRegistry::init('Group');

            $Group->create();
            $ret = (bool) $Group->save([
                'Group' => [
                    'name' => 'User Management',
                    'description' => 'This group allows members to add, edit, import and delete user accounts. Add this group to System / Settings / User Management if you want them to be able to edit and delete accounts other than theirs.',
                    'status' => 1,
                    'slug' => 'USER_MANAGEMENT'
                ]
            ]);

            ClassRegistry::init('Setting')->deleteCache('');
            ClassRegistry::init('Setting')->syncAcl();

            if ($ret) {
                $groupId = $Group->field('id', ['slug' => 'USER_MANAGEMENT']);

                $Permission = ClassRegistry::init(['class' => 'Permission', 'alias' => 'Permission']);

                $aro = [
                    'model' => 'Group',
                    'foreign_key' => $groupId
                ];

                $permissions = [
                    'controllers/Settings/index',
                    'controllers/Users/add',
                    'controllers/Users/checkConflicts',
                    'controllers/Users/delete',
                    'controllers/Users/downloadAttachment',
                    'controllers/Users/edit',
                    'controllers/Users/index',
                    'controllers/Users/searchLdapUsers',
                    'controllers/Users/unblock',
                    'controllers/ImportTool/ImportTool/downloadTemplate',
                    'controllers/ImportTool/ImportTool/preview',
                    'controllers/ImportTool/ImportTool/upload',
                    'controllers/AdvancedFilters/AdvancedFilters/exportCsvAll',
                    'controllers/AdvancedFilters/AdvancedFilters/exportCsvAllQuery',
                    'controllers/AdvancedFilters/AdvancedFilters/exportDailyCountResults',
                    'controllers/AdvancedFilters/AdvancedFilters/exportDailyDataResults',
                    'controllers/AdvancedFilters/AdvancedFilters/redirectAdvancedFilter',
                    'controllers/AdvancedFilters/AdvancedFilters/edit',
                    'controllers/AdvancedFilters/AdvancedFilters/delete',
                    'controllers/AdvancedFilters/AdvancedFilters/add',
                    'controllers/AdvancedFilters/AdvancedFilterUserParams/save',
                    'controllers/Widget/Widget/index',
                    'controllers/Comments/Comments/add',
                    'controllers/Comments/Comments/delete',
                    'controllers/Comments/Comments/index',
                    'controllers/Attachments/Attachments/add',
                    'controllers/Attachments/Attachments/addTmp',
                    'controllers/Attachments/Attachments/delete',
                    'controllers/Attachments/Attachments/index',
                    'controllers/Attachments/Attachments/indexTmp',
                ];

                foreach ($permissions as $aco) {
                    $ret &= $r = $Permission->allow($aro, $aco, '*', 1);
                    if (!$r) {
                        CakeLog::write('debug', "Node ACL cannot be configured: {$aco}");
                    }
                }

                if (!$ret) {
                    CakeLog::write('debug', "Error occured when processing ACL Sync for User Management group.");
                }
            }

            // remove unwanted settings nodes from All But Settings Group
            $allButSettingsGroupId = $Group->field('id', ['slug' => 'ALL_BUT_SETTINGS']);

            if (!empty($allButSettingsGroupId)) {
                $Permission = ClassRegistry::init(['class' => 'Permission', 'alias' => 'Permission']);

                $aro = [
                    'model' => 'Group',
                    'foreign_key' => $allButSettingsGroupId
                ];

                $permissions = [
                    'controllers/Settings/index',
                    'controllers/Acl/Aros/admin_ajax_role_permissions',
                    'controllers/Visualisation/VisualisationSettings/index',
                    'controllers/OauthConnectors/add',
                    'controllers/OauthConnectors/edit',
                    'controllers/OauthConnectors/delete',
                    'controllers/OauthConnectors/index',
                    'controllers/BackupRestore/BackupRestore/downloadFile',
                    'controllers/BackupRestore/BackupRestore/getBackup',
                    'controllers/BackupRestore/BackupRestore/index',
                    'controllers/BackupRestore/BackupRestore/prepareFiles',
                    'controllers/Settings/backup',
                    'controllers/Settings/debug',
                    'controllers/Settings/currency',
                    'controllers/Settings/timezone',
                    'controllers/Settings/csv',
                    'controllers/Settings/email',
                    'controllers/Translations/Translations/add',
                    'controllers/Translations/Translations/delete',
                    'controllers/Translations/Translations/download',
                    'controllers/Translations/Translations/downloadAttachment',
                    'controllers/Translations/Translations/downloadTemplate',
                    'controllers/Translations/Translations/edit',
                    'controllers/Translations/Translations/index',
                    'controllers/Settings/bruteForceProtection',
                    'controllers/Settings/sslOffload',
                    'controllers/Settings/enterpriseUser',
                    'controllers/Settings/crontab',
                    'controllers/Settings/pdf',
                    'controllers/SamlConnectors/add',
                    'controllers/SamlConnectors/delete',
                    'controllers/SamlConnectors/downloadAttachment',
                    'controllers/SamlConnectors/edit',
                    'controllers/SamlConnectors/getMetadata',
                    'controllers/SamlConnectors/index',
                    'controllers/SamlConnectors/singleLogout',
                    'controllers/SamlConnectors/singleSingOn',
                    'controllers/Cron/Cron/index',
                    'controllers/Updates/index',
                    'controllers/LdapSync/LdapSynchronizationSystemLogs/downloadAttachment',
                    'controllers/LdapSync/LdapSynchronizationSystemLogs/index',
                    'controllers/LdapSync/LdapSynchronizations/add',
                    'controllers/LdapSync/LdapSynchronizations/delete',
                    'controllers/LdapSync/LdapSynchronizations/downloadAttachment',
                    'controllers/LdapSync/LdapSynchronizations/edit',
                    'controllers/LdapSync/LdapSynchronizations/forceSync',
                    'controllers/LdapSync/LdapSynchronizations/simulateSync',
                ];

                foreach ($permissions as $aco) {
                    $ret &= $r = $Permission->allow($aro, $aco, '*', -1);
                    if (!$r) {
                        CakeLog::write('debug', "Node ACL cannot be configured: {$aco}");
                    }
                }

                if (!$ret) {
                    CakeLog::write('debug', "Error occured when processing ACL Sync for User Management group.");
                }
            }
        }
    }

    public function down()
    {
    }

    protected function _visualisationShareUsers()
    {
        $ret = true;

        $UsersTable = ClassRegistry::init('User');

        $users = $UsersTable->find('all', [
            'fields' => ['User.id'],
            'contain' => []
        ]);

        $VisualisationShareUser = ClassRegistry::init('Visualisation.VisualisationShareUser');

        foreach ($users as $user) {
            $ret &= $VisualisationShareUser->share($user['User']['id'], [$UsersTable->alias, $user['User']['id']], false);
        }

        return $ret;
    }
}
