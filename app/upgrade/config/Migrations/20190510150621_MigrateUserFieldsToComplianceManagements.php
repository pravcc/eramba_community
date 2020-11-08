<?php
use Phinx\Migration\AbstractMigration;

class MigrateUserFieldsToComplianceManagements extends AbstractMigration
{
    public function fixComplianceOwners()
    {
        $User = ClassRegistry::init('User');
        $userList = $User->find('list', [
            'fields' => [
                'id', 'id'
            ],
            'recursive' => -1
        ]);

        // fix wrong user ids stored in the compliance table
        $ComplianceManagement = ClassRegistry::init('ComplianceManagement');
        $ret = (bool) $ComplianceManagement->updateAll([
            'ComplianceManagement.owner_id' => null
        ], [
            'ComplianceManagement.owner_id !=' => $userList
        ]);

        $this->table('compliance_managements')
            ->addIndex(
                [
                    'owner_id',
                ],
                [
                    'name' => 'idx_owner_id',
                ]
            )
            ->update();

        $this->table('compliance_managements')
            ->addForeignKey(
                'owner_id',
                'users',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'SET NULL'
                ]
            )
            ->update();

        return $ret;
    }

    public function up()
    {
        if (class_exists('App')) {
            App::uses('VisualisationShell', 'Visualisation.Console/Command');
            $VisualisationShell = new VisualisationShell();
            $VisualisationShell->startup();

            $ret = $VisualisationShell->acl_sync();

            App::uses('Model', 'Model');
            App::uses('ComplianceManagement', 'Model');
            App::uses('UserFieldsBehavior', 'UserFields.Model/Behavior');

            $ret &= $this->fixComplianceOwners();

            // we have to initialize a blank compliance package model as the table structure was modified
            // and cake has a problem loading new table schema
            $modelConfig = [
                'table' => 'compliance_packages',
                'name' => 'BootstrapNewCompliancePackage',
                'ds' => 'default'
            ];
            $BootstrapNewCompliancePackage = (new Model($modelConfig));

            App::uses('ComplianceManagement', 'Model');
            App::uses('UserFieldsBehavior', 'UserFields.Model/Behavior');

            ClassRegistry::init('CompliancePackageItem')->bindModel([
                'belongsTo' => [
                    'BootstrapNewCompliancePackage' => [
                        'foreignKey' => 'compliance_package_id'
                    ]
                ]
            ]);
            ClassRegistry::init('BootstrapNewCompliancePackage')->bindModel([
                'belongsTo' => [
                    'CompliancePackageRegulator' => [
                        'foreignKey' => 'compliance_package_regulator_id'
                    ]
                ]
            ]);
            $ComplianceManagement = ClassRegistry::init('ComplianceManagement');

            $complianceManagements = $ComplianceManagement->find('all', [
                'contain' => [
                    'CompliancePackageItem' => [
                        'BootstrapNewCompliancePackage' => [
                            'CompliancePackageRegulator' => [
                                'Owner' => [
                                    'fields' => [
                                        'id'
                                    ]
                                ],
                                'OwnerGroup' => [
                                    'fields' => [
                                        'id'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]);

            $adminUser = UserFieldsBehavior::getUserIdPrefix() . ADMIN_ID;
            foreach ($complianceManagements as $data) {
                $newData = [
                    'ComplianceManagement' => [
                        'id' => $data['ComplianceManagement']['id'],
                        'Owner' => []
                    ]
                ];

                // Get owner ID for new Owner user field
                $ownerId = $data['ComplianceManagement']['owner_id'];
                $newData['ComplianceManagement']['Owner'][] = !empty($ownerId) ? UserFieldsBehavior::getUserIdPrefix() . $ownerId : $adminUser;

                //
                // Merge user fields from compliance package regulators
                $cprOwner = $data['CompliancePackageItem']['BootstrapNewCompliancePackage']['CompliancePackageRegulator']['Owner'];
                $cprOwnerGroup = $data['CompliancePackageItem']['BootstrapNewCompliancePackage']['CompliancePackageRegulator']['OwnerGroup'];
                foreach ($cprOwner as $co) {
                    $coUser = UserFieldsBehavior::getUserIdPrefix() . $co['id'];
                    if (!in_array($coUser, $newData['ComplianceManagement']['Owner'], true)) {
                        $newData['ComplianceManagement']['Owner'][] = $coUser;
                    }
                }
                foreach ($cprOwnerGroup as $cog) {
                    $coGroup = UserFieldsBehavior::getGroupIdPrefix() . $cog['id'];
                    $newData['ComplianceManagement']['Owner'][] = $coGroup;
                }
                //
                
                $ret &= $ComplianceManagement->saveAssociated($newData, [
                    'deep' => true,
                    'fieldList' => ['Owner']
                ]);
            }

            if (!$ret) {
                App::uses('CakeLog', 'Log');
                $errorMsg = "Error occured when migrating UserFields to Compliance Managements";
                CakeLog::write('error', $errorMsg);

                throw new Exception($errorMsg, 1);
                return false;
            }
        }
    }

    public function down()
    {
        $this->query("DELETE FROM `user_fields_users` WHERE `model`='ComplianceManagement' AND `field` IN ('Owner')");
        $this->query("DELETE FROM `user_fields_groups` WHERE `model`='ComplianceManagement' AND `field` IN ('OwnerGroup')");
    }
}
