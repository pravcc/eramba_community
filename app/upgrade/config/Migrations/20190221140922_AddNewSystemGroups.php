<?php
use Phinx\Migration\AbstractMigration;

class AddNewSystemGroups extends AbstractMigration
{
    public function up()
    {
        //
        // Add slug column to groups table
        $this->table('groups')
            ->addColumn('slug', 'string', [
                'after' => 'status',
                'default' => null,
                'length' => 100,
                'null' => true,
            ])
            ->addIndex(['slug'], ['unique' => true])
            ->update();
        //
        
        //
        // Set system groups flag to existing system groups
        $sg_flags = [
            10 => 'ADMIN',
            11 => 'THIRD_PARTY_FEEDBACK',
            12 => 'NOTIFICATION_FEEDBACK',
            13 => 'ALL_BUT_SETTINGS'
        ];
        foreach ($sg_flags as $id => $flag) {
            $this->query("UPDATE `groups` SET `slug`='{$flag}' WHERE `id`={$id}");
        }
        //
        
        if (class_exists('App')) {
            $groups = [
                [
                    'data' => [
                        'name' => 'System Group - View Policies and Reviews',
                        'description' => "This group only allows users to see policies and their reviews under the policy management module. 
                            Disclaimer: always review the group permissions before assigning them to users, they might grant access you do not want or be outdated as releases move forward.",
                        'status' => 1,
                        'slug' => 'VIEW_POLICIES_AND_REVIEWS'
                    ],
                    'permissions' => [
                        'controllers/SecurityPolicies/index',
                        'controllers/SecurityPolicyReviews/index'
                    ],
                ],
                [
                    'data' => [
                        'name' => 'System Group - View Item Reports',
                        'description' => "This group allows users to visualise item reports from any section that they have access (granted by another group). Disclaimer: always review the group permissions before assigning them to users, they might grant access you do not want or be outdated as releases move forward.",
                        'status' => 1,
                        'slug' => 'VIEW_ITEM_REPORTS'
                    ],
                    'permissions' => [
                        'Reports/Reports/view'
                    ]
                ],
                [
                    'data' => [
                        'name' => 'System Group - View Internal Controls and Audits, Maintenances and Issues',
                        'description' => "This group grants permissions to only view internal controls and their related items. Disclaimer: always review the group permissions before assigning them to users, they might grant access you do not want or be outdated as releases move forward.",
                        'status' => 1,
                        'slug' => 'VIEW_INT_CTRL_AND_AMI'
                    ],
                    'permissions' => [
                        'controllers/SecurityServiceAudits/index',
                        'controllers/SecurityServiceMaintenances/index',
                        'controllers/SecurityServices/index',
                        'controllers/SecurityServiceIssues/index'
                    ]
                ],
                [
                    'data' => [
                        'name' => 'System Group - View All Types of Risks and their Reviews',
                        'description' => "This group grants access to view all three types of risks and their respective reviews. Disclaimer: always review the group permissions before assigning them to users, they might grant access you do not want or be outdated as releases move forward.",
                        'status' => 1,
                        'slug' => 'VIEW_RISKS_AND_REVIEWS'
                    ],
                    'permissions' => [
                        'controllers/BusinessContinuities/index',
                        'controllers/Risks/index',
                        'controllers/ThirdPartyRisks/index',
                        'controllers/BusinessContinuityReviews/index',
                        'controllers/RiskReviews/index',
                        'controllers/ThirdPartyRiskReviews/index'
                    ],
                ],
                [
                    'data' => [
                        'name' => 'System Group - Projects and Tasks',
                        'description' => "This group grants access to view projects and tasks. Disclaimer: always review the group permissions before assigning them to users, they might grant access you do not want or be outdated as releases move forward.",
                        'status' => 1,
                        'slug' => 'PROJECTS_AND_TASKS'
                    ],
                    'permissions' => [
                        'controllers/ProjectAchievements/index',
                        'controllers/Projects/index'
                    ]
                ]
            ];
            
            ClassRegistry::init('Setting')->deleteCache('');
            ClassRegistry::init('Setting')->syncAcl();

            App::uses('Group', 'Model');
            $Group = ClassRegistry::init('Group');
            foreach ($groups as $group) {
                $stmt = $this->query("SELECT `id` FROM `groups` WHERE `slug`='{$group['data']['slug']}'");
                if (!empty($stmt->fetchAll())) {
                    continue;
                }

                // Prepare model for next group
                $Group->clear();

                // Save group to DB
                $Group->save($group['data'], false);

                // Set group permissions
                $this->setPermissionsToGroup($Group->getInsertID(), $group['permissions']);
            }
        }
    }

    /**
     * Set permision for group
     * @param int   $groupId  ID of group for which you want to set permissions
     * @param array $permissions    Array of permissions you want to allow for the group
     */
    protected function setPermissionsToGroup($groupId, $permissions)
    {
        App::uses('ClassRegistry', 'Utility');
        App::uses('CakeLog', 'Log');

        $Permission = ClassRegistry::init(array('class' => 'Permission', 'alias' => 'Permission'));
        $aro = [
            'model' => 'Group',
            'foreign_key' => $groupId
        ];

        $ret = true;

        foreach ($permissions as $aco) {
            $ret &= $r = $Permission->allow($aro, $aco, '*', 1);
            if (!$r) {
                CakeLog::write('debug', 'Node ACL cannot be configured:' . $aco);
            }
        }

        if (!$ret) {
            App::uses('CakeLog', 'Log');
            $log = "Error occured when processing ACL Sync for system groups.";
            CakeLog::write('debug', "{$log}");
        }

        return $ret;
    }

    public function down()
    {
        // Delete system groups
        $this->query("DELETE FROM `groups` WHERE `slug` IN ('VIEW_POLICIES_AND_REVIEWS', 'VIEW_ITEM_REPORTS', 'VIEW_INT_CTRL_AND_AMI', 'VIEW_RISKS_AND_REVIEWS', 'PROJECTS_AND_TASKS')");

        $this->table('groups')
            ->removeIndex(['slug'])
            ->removeColumn('slug')
            ->update();
    }
}
