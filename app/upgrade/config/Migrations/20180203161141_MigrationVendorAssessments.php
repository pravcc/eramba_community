<?php
use Phinx\Migration\AbstractMigration;

class MigrationVendorAssessments extends AbstractMigration
{

    public function up()
    {

        $this->table('third_parties_vendor_assessments')
            ->addColumn('third_party_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('vendor_assessment_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addIndex(
                [
                    'third_party_id',
                ]
            )
            ->addIndex(
                [
                    'vendor_assessment_id',
                ]
            )
            ->create();

        $this->table('users_vendor_assessments')
            ->addColumn('user_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('vendor_assessment_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('type', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addIndex(
                [
                    'user_id',
                ]
            )
            ->addIndex(
                [
                    'vendor_assessment_id',
                ]
            )
            ->create();

        $this->table('vendor_assessment_feedbacks')
            ->addColumn('vendor_assessment_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('vendor_assessment_question_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('vendor_assessment_option_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('answer', 'text', [
                'default' => null,
                'limit' => null,
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
            ->addIndex(
                [
                    'vendor_assessment_id',
                ]
            )
            ->addIndex(
                [
                    'vendor_assessment_option_id',
                ]
            )
            ->addIndex(
                [
                    'vendor_assessment_question_id',
                ]
            )
            ->create();

        $this->table('vendor_assessment_files')
            ->addColumn('filename', 'string', [
                'default' => '',
                'limit' => 255,
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

        $this->table('vendor_assessment_options')
            ->addColumn('vendor_assessment_question_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('title', 'string', [
                'default' => '',
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('warning', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('weight', 'decimal', [
                'default' => '1.0000',
                'null' => false,
                'precision' => 11,
                'scale' => 4,
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
            ->addIndex(
                [
                    'vendor_assessment_question_id',
                ]
            )
            ->create();

        $this->table('vendor_assessment_questionnaires')
            ->addColumn('name', 'string', [
                'default' => '',
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('description', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('vendor_assessment_file_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
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
            ->addIndex(
                [
                    'vendor_assessment_file_id',
                ]
            )
            ->create();

        $this->table('vendor_assessment_questions')
            ->addColumn('vendor_assessment_questionnaire_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('chapter_number', 'string', [
                'default' => '',
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('chapter_title', 'string', [
                'default' => '',
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('chapter_description', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('number', 'string', [
                'default' => '',
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('title', 'string', [
                'default' => '',
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('description', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('answer_type', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('score', 'decimal', [
                'default' => null,
                'null' => false,
                'precision' => 11,
                'scale' => 4,
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
            ->addIndex(
                [
                    'vendor_assessment_questionnaire_id',
                ]
            )
            ->create();

        $this->table('vendor_assessments')
            ->addColumn('hash', 'string', [
                'default' => '',
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('title', 'string', [
                'default' => '',
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('description', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('vendor_assessment_questionnaire_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('portal_title', 'string', [
                'default' => '',
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('portal_description', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('finding_download', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('questions_download', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('incomplete_submit', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('scheduling', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('start_date', 'date', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('end_date', 'date', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('recurrence', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('recurrence_period', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('recurrence_auto_load', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('status', 'integer', [
                'default' => '0',
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('submited', 'integer', [
                'default' => '0',
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('submit_date', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
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
            ->addColumn('deleted', 'integer', [
                'default' => '0',
                'limit' => 2,
                'null' => false,
            ])
            ->addColumn('deleted_date', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addIndex(
                [
                    'vendor_assessment_questionnaire_id',
                ]
            )
            ->create();

        $this->table('third_parties_vendor_assessments')
            ->addForeignKey(
                'third_party_id',
                'third_parties',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'vendor_assessment_id',
                'vendor_assessments',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('users_vendor_assessments')
            ->addForeignKey(
                'user_id',
                'users',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'vendor_assessment_id',
                'vendor_assessments',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('vendor_assessment_feedbacks')
            ->addForeignKey(
                'vendor_assessment_id',
                'vendor_assessments',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'vendor_assessment_option_id',
                'vendor_assessment_options',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'vendor_assessment_question_id',
                'vendor_assessment_questions',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('vendor_assessment_options')
            ->addForeignKey(
                'vendor_assessment_question_id',
                'vendor_assessment_questions',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('vendor_assessment_questionnaires')
            ->addForeignKey(
                'vendor_assessment_file_id',
                'vendor_assessment_files',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'RESTRICT'
                ]
            )
            ->update();

        $this->table('vendor_assessment_questions')
            ->addForeignKey(
                'vendor_assessment_questionnaire_id',
                'vendor_assessment_questionnaires',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('vendor_assessments')
            ->addForeignKey(
                'vendor_assessment_questionnaire_id',
                'vendor_assessment_questionnaires',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'RESTRICT'
                ]
            )
            ->update();

        $this->table('ldap_connector_authentication')
            ->addColumn('auth_vendor_assessment', 'integer', [
                'after' => 'auth_compliance_audit',
                'default' => '0',
                'length' => 1,
                'null' => false,
            ])
            ->update();
    }

    public function down()
    {
        $this->table('third_parties_vendor_assessments')
            ->dropForeignKey(
                'third_party_id'
            )
            ->dropForeignKey(
                'vendor_assessment_id'
            );

        $this->table('users_vendor_assessments')
            ->dropForeignKey(
                'user_id'
            )
            ->dropForeignKey(
                'vendor_assessment_id'
            );

        $this->table('vendor_assessment_feedbacks')
            ->dropForeignKey(
                'vendor_assessment_id'
            )
            ->dropForeignKey(
                'vendor_assessment_option_id'
            )
            ->dropForeignKey(
                'vendor_assessment_question_id'
            );

        $this->table('vendor_assessment_options')
            ->dropForeignKey(
                'vendor_assessment_question_id'
            );

        $this->table('vendor_assessment_questionnaires')
            ->dropForeignKey(
                'vendor_assessment_file_id'
            );

        $this->table('vendor_assessment_questions')
            ->dropForeignKey(
                'vendor_assessment_questionnaire_id'
            );

        $this->table('vendor_assessments')
            ->dropForeignKey(
                'vendor_assessment_questionnaire_id'
            );

        $this->table('ldap_connector_authentication')
            ->removeColumn('auth_vendor_assessment')
            ->update();

        $this->dropTable('third_parties_vendor_assessments');

        $this->dropTable('users_vendor_assessments');

        $this->dropTable('vendor_assessment_feedbacks');

        $this->dropTable('vendor_assessment_files');

        $this->dropTable('vendor_assessment_options');

        $this->dropTable('vendor_assessment_questionnaires');

        $this->dropTable('vendor_assessment_questions');

        $this->dropTable('vendor_assessments');
    }
}

