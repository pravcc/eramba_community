<?php
use Phinx\Migration\AbstractMigration;

class SecurityPolicyDocumentTypeMigration extends AbstractMigration
{

    protected function seedStaticData() {
        // insert custom field settings
        $rows = [
            [
                'id' => 1,
                'name'  => 'Procedure',
                'editable' => 0
            ],
            [
                'id' => 2,
                'name'  => 'Standard',
                'editable' => 0
            ],
            [
                'id' => 3,
                'name'  => 'Policy',
                'editable' => 0
            ],
        ];

        $table = $this->table('security_policy_document_types');
        $table->insert($rows);
        $table->saveData();
    }

    public function up()
    {

        $this->table('risks_security_policies')
            ->removeColumn('document_type')
            ->update();

        $this->table('security_policies_related')
            ->removeColumn('document_type')
            ->update();

        $this->table('security_policy_document_types')
            ->addColumn('name', 'string', [
                'default' => '',
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('editable', 'integer', [
                'default' => '1',
                'limit' => 11,
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
            ->create();

        $this->table('security_policies')
            ->addColumn('security_policy_document_type_id', 'integer', [
                'after' => 'document_type',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->addIndex(
                [
                    'security_policy_document_type_id',
                ],
                [
                    'name' => 'security_policy_document_type_id',
                ]
            )
            ->update();

        $this->table('security_policies')
            ->addForeignKey(
                'security_policy_document_type_id',
                'security_policy_document_types',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->seedStaticData();
    }

    public function down()
    {
        $this->table('security_policies')
            ->dropForeignKey(
                'security_policy_document_type_id'
            );

        $this->table('security_policies')
            ->removeIndexByName('security_policy_document_type_id')
            ->update();

        $this->table('security_policies')
            ->removeColumn('security_policy_document_type_id')
            ->update();

        $this->table('risks_security_policies')
            ->addColumn('document_type', 'string', [
                'after' => 'type',
                'default' => null,
                'length' => null,
                'null' => false,
            ])
            ->update();

        $this->table('security_policies_related')
            ->addColumn('document_type', 'string', [
                'after' => 'related_document_id',
                'default' => null,
                'length' => null,
                'null' => false,
            ])
            ->update();

        $this->dropTable('security_policy_document_types');
    }
}

