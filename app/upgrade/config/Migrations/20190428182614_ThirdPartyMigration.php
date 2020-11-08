<?php
use Phinx\Migration\AbstractMigration;

class ThirdPartyMigration extends AbstractMigration
{

    public function up()
    {
        // clean the non-regulator-related compliance packages that have been left over
        // in the database
        $this->_deleteLeftOverCompliances();

        // first create new compliance packages tables
        $this->_createComplianceTables();

        $data = [
            [
                'model' => 'CompliancePackageRegulator',
                'status' => '1'
            ],
        ];

        $table = $this->table('visualisation_settings');
        $table->insert($data)->saveData();

        if (class_exists('App')) {
            ClassRegistry::init('Setting')->deleteCache('');
        }

        // then copy third parties to the new compliance tables keeping their primary IDs
        if (!$this->_copyThirdParties()) {
            throw new Exception('Error occured while trying to copy Third Parties during the update process.', 1);
            return false;
        }

        // then switch third_party_id for compliance_package_regulator_id relation
        // in compliance_packages table
        $this->_switchForeignKey();

        // manage compliance_analysis_findings_third_parties table
        $this->_renameFindingTable();

        // migrate all new filter argument names
        $this->_migrateFilterFields();

        
    }

    protected function _renameFindingTable()
    {
        $this->table('compliance_analysis_findings_third_parties')
            ->dropForeignKey('third_party_id')
            ->dropForeignKey('compliance_analysis_finding_id')
            ->removeIndexByName('third_party_id')
            ->removeIndexByName('compliance_analysis_finding_id')
            ->update();

        $this->table('compliance_analysis_findings_third_parties')
            ->rename('compliance_analysis_findings_compliance_package_regulators')
            ->update();

        $this->table('compliance_analysis_findings_compliance_package_regulators')
            ->renameColumn('third_party_id', 'compliance_package_regulator_id')
            ->update();

        $this->table('compliance_analysis_findings_compliance_package_regulators')    
            ->addIndex(
                [
                    'compliance_analysis_finding_id',
                ],
                [
                    'name' => 'compliance_analysis_finding_id',
                ]
            )
            ->addIndex(
                [
                    'compliance_package_regulator_id',
                ],
                [
                    'name' => 'compliance_package_regulator_id',
                ]
            )
            ->addForeignKey(
                'compliance_analysis_finding_id',
                'compliance_analysis_findings',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE',
                    'constraint' => 'compliance_analysis_findings_join_ibfk_1'
                ]
            )
            ->addForeignKey(
                'compliance_package_regulator_id',
                'compliance_package_regulators',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE',
                    'constraint' => 'compliance_analysis_findings_join_ibfk_2'
                ]
            )
            ->update();
    }

    public function down()
    {
        // switch the foreign key column
        $this->table('compliance_packages')
            ->dropForeignKey(
                'compliance_package_regulator_id'
            );

        $this->table('compliance_packages')
            ->removeIndexByName('idx_compliance_package_regulator_id')
            ->update();

        $this->table('compliance_packages')
            ->renameColumn('compliance_package_regulator_id', 'third_party_id')
            ->addIndex(
                [
                    'third_party_id',
                ],
                [
                    'name' => 'third_party_id',
                ]
            )
            ->update();

        $this->table('compliance_packages')
            ->addForeignKey(
                'third_party_id',
                'third_parties',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        // then remove the new table
        $this->table('compliance_package_regulators_legals')
            ->dropForeignKey(
                'compliance_package_regulator_id'
            )
            ->dropForeignKey(
                'legal_id'
            );

        $this->dropTable('compliance_package_regulators');

        $this->dropTable('compliance_package_regulators_legals');
    }

    protected function _switchForeignKey()
    {
        $this->table('compliance_packages')
            ->dropForeignKey([], 'compliance_packages_ibfk_1')
            ->removeIndex(['third_party_id'])
            ->update();

        $this->table('compliance_packages')
            ->renameColumn('third_party_id', 'compliance_package_regulator_id')
            ->addIndex(
                [
                    'compliance_package_regulator_id',
                ],
                [
                    'name' => 'idx_compliance_package_regulator_id',
                ]
            )
            ->update();

        $this->table('compliance_packages')
            ->addForeignKey(
                'compliance_package_regulator_id',
                'compliance_package_regulators',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();
    }

    protected function _deleteLeftOverCompliances()
    {
        $ret = true;

        if (class_exists('App')) {
            App::uses('ThirdParty', 'Model');
            App::uses('Model', 'Model');

            $modelConfig = [
                'table' => 'third_parties',
                'name' => 'BootstrapThirdParty',
                'ds' => 'default'
            ];
            $regulatorIds = (new Model($modelConfig))->find('list', [
                'conditions' => [
                    'OR' => [
                        [
                            'BootstrapThirdParty.third_party_type_id !=' => ThirdParty::TYPE_REGULATORS
                        ],
                        [
                            'BootstrapThirdParty.third_party_type_id' => ThirdParty::TYPE_REGULATORS,
                            'BootstrapThirdParty.deleted' => '1'
                        ]
                    ]
                    
                ],
                'fields' => ['BootstrapThirdParty.id', 'BootstrapThirdParty.id'],
                'recursive' => -1
            ]);

            $modelConfig = [
                'table' => 'compliance_packages',
                'name' => 'BootstrapCompliancePackage',
                'ds' => 'default'
            ];
            $ret &= (new Model($modelConfig))->deleteAll([
                'BootstrapCompliancePackage.third_party_id' => $regulatorIds
            ]);

            $modelConfig = [
                'table' => 'compliance_analysis_findings_third_parties',
                'name' => 'BootstrapComplianceAnalysisFindingsThirdParty',
                'ds' => 'default'
            ];
            $ret &= (new Model($modelConfig))->deleteAll([
                'BootstrapComplianceAnalysisFindingsThirdParty.third_party_id' => $regulatorIds
            ]);
        }

        return $ret;
    }

    protected function _copyThirdParties()
    {
        $ret = true;

        if (class_exists('App')) {
            // remove cache to load up the new tables
            ClassRegistry::init('Setting')->deleteCache('');

            App::uses('Hash', 'Utility');
            App::uses('ThirdParty', 'Model');

            $TP = ClassRegistry::init('ThirdParty');
            $regulators = $TP->find('all', [
                'conditions' => [
                    'ThirdParty.third_party_type_id' => ThirdParty::TYPE_REGULATORS,
                ],
                'contain' => [
                    'Legal',
                    'Sponsor',
                    'SponsorGroup'
                ]
            ]);

            foreach ($regulators as $regulator) {
                $owners = Hash::extract($regulator, 'Sponsor.{n}.id');
                if (empty($owners)) {
                    $owners = ['User-' . ADMIN_ID];
                }

                $saveData = [
                    'CompliancePackageRegulator' => [
                        'id' => $regulator['ThirdParty']['id'],
                        'name' => $regulator['ThirdParty']['name'],
                        'description' => $regulator['ThirdParty']['description'],
                        'Legal' => Hash::extract($regulator, 'Legal.{n}.id'),
                        'Owner' => $owners
                    ]
                ];

                $ret &= ClassRegistry::init('CompliancePackageRegulator')->saveAssociated($saveData, [
                    'validate' => 'first',
                    'atomic' => true,
                    'deep' => true
                ]);
            }
        }

        return $ret;
    }

    protected function _createComplianceTables()
    {
        $this->table('compliance_package_regulators')
            ->addColumn('name', 'string', [
                'default' => null,
                'limit' => 128,
                'null' => false,
            ])
            ->addColumn('description', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('publisher_name', 'string', [
                'default' => null,
                'limit' => 128,
                'null' => false,
            ])
            ->addColumn('version', 'string', [
                'default' => null,
                'limit' => 128,
                'null' => false,
            ])
            ->addColumn('language', 'string', [
                'default' => null,
                'limit' => 128,
                'null' => false,
            ])
            ->addColumn('url', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('restriction', 'integer', [
                'default' => null,
                'limit' => 3,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('edited', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->create();

        $this->table('compliance_package_regulators_legals')
            ->addColumn('compliance_package_regulator_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('legal_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addIndex(
                [
                    'compliance_package_regulator_id',
                ]
            )
            ->addIndex(
                [
                    'legal_id',
                ]
            )
            ->create();

        $this->table('compliance_package_regulators_legals')
            ->addForeignKey(
                'compliance_package_regulator_id',
                'compliance_package_regulators',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'legal_id',
                'legals',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();
    }

    protected function _migrateFilterFields()
    {
        if (class_exists('App')) {
            App::uses('AdvancedFiltersNamesMigration', 'AdvancedFilters.Lib');

            $AdvancedFiltersNamesMigration = new AdvancedFiltersNamesMigration();

            $AdvancedFiltersNamesMigration->migrate([
                'ComplianceManagement' => [
                    'CompliancePackage-third_party_id' => 'CompliancePackage-compliance_package_regulator_id'
                ],
                'CompliancePackageItem' => [
                    'CompliancePackage-third_party_id' => 'CompliancePackage-compliance_package_regulator_id'
                ],
                'Asset' => [
                    'CompliancePackage-third_party_id' => 'CompliancePackage-compliance_package_regulator_id'
                ],
                'ComplianceAnalysisFinding' => [
                    'CompliancePackage-third_party_id' => 'CompliancePackage-compliance_package_regulator_id'
                ],
                'ComplianceException' => [
                    'CompliancePackage-third_party_id' => 'CompliancePackage-compliance_package_regulator_id'
                ],
                'Project' => [
                    'CompliancePackage-third_party_id' => 'CompliancePackage-compliance_package_regulator_id'
                ],
                'SecurityPolicy' => [
                    'CompliancePackage-third_party_id' => 'CompliancePackage-compliance_package_regulator_id'
                ],
                'SecurityService' => [
                    'CompliancePackage-third_party_id' => 'CompliancePackage-compliance_package_regulator_id'
                ],
                'SecurityServiceAudit' => [
                    'CompliancePackage-third_party_id' => 'CompliancePackage-compliance_package_regulator_id'
                ],
                'SecurityServiceMaintenance' => [
                    'CompliancePackage-third_party_id' => 'CompliancePackage-compliance_package_regulator_id'
                ],
                'Risk' => [
                    'CompliancePackage-third_party_id' => 'CompliancePackage-compliance_package_regulator_id'
                ],
                'ThirdPartyRisk' => [
                    'CompliancePackage-third_party_id' => 'CompliancePackage-compliance_package_regulator_id'
                ],
                'BusinessContinuity' => [
                    'CompliancePackage-third_party_id' => 'CompliancePackage-compliance_package_regulator_id'
                ],
            ]);
        }
    }
}

