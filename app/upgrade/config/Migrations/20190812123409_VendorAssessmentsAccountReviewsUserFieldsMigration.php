<?php
use Phinx\Migration\AbstractMigration;

class VendorAssessmentsAccountReviewsUserFieldsMigration extends AbstractMigration
{

    public function up()
    {
        if (class_exists('App')) {
            App::uses('ClassRegistry', 'Utility');

            $this->_migrateUserFields();
        }
        
        $this->dropTable('account_review_findings_users');

        $this->dropTable('account_reviews_users');

        $this->dropTable('users_vendor_assessment_findings');

        $this->dropTable('users_vendor_assessments');
    }

    public function down()
    {

        $this->table('account_review_findings_users')
            ->addColumn('user_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('account_review_finding_id', 'integer', [
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
                    'account_review_finding_id',
                ]
            )
            ->addIndex(
                [
                    'user_id',
                ]
            )
            ->create();

        $this->table('account_reviews_users')
            ->addColumn('account_review_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('user_id', 'integer', [
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
                    'account_review_id',
                ]
            )
            ->addIndex(
                [
                    'user_id',
                ]
            )
            ->create();

        $this->table('users_vendor_assessment_findings')
            ->addColumn('user_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('vendor_assessment_finding_id', 'integer', [
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
                    'vendor_assessment_finding_id',
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

        $this->table('account_review_findings_users')
            ->addForeignKey(
                'account_review_finding_id',
                'account_review_findings',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'user_id',
                'users',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('account_reviews_users')
            ->addForeignKey(
                'account_review_id',
                'account_reviews',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'user_id',
                'users',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('users_vendor_assessment_findings')
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
                'vendor_assessment_finding_id',
                'vendor_assessment_findings',
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
    }

    protected function _migrateUserFields()
    {
        $UserFieldsUser = ClassRegistry::init('UserFields.UserFieldsUser');

        $users = ClassRegistry::init('UsersVendorAssessment')->find('all', ['recursive' => -1]);

        foreach ($users as $user) {
            $UserFieldsUser->create();

            $UserFieldsUser->save([
                'model' => 'VendorAssessment',
                'foreign_key' => $user['UsersVendorAssessment']['vendor_assessment_id'],
                'user_id' => $user['UsersVendorAssessment']['user_id'],
                'field' => ($user['UsersVendorAssessment']['type'] == 1) ? 'Auditor' : 'Auditee'
            ]);
        }

        $users = ClassRegistry::init('UsersVendorAssessmentFinding')->find('all', ['recursive' => -1]);

        foreach ($users as $user) {
            $UserFieldsUser->create();

            $UserFieldsUser->save([
                'model' => 'VendorAssessmentFinding',
                'foreign_key' => $user['UsersVendorAssessmentFinding']['vendor_assessment_finding_id'],
                'user_id' => $user['UsersVendorAssessmentFinding']['user_id'],
                'field' => ($user['UsersVendorAssessmentFinding']['type'] == 1) ? 'Auditor' : 'Auditee'
            ]);
        }

        $users = ClassRegistry::init('AccountReviewsUser')->find('all', ['recursive' => -1]);

        foreach ($users as $user) {
            $UserFieldsUser->create();

            $UserFieldsUser->save([
                'model' => 'AccountReview',
                'foreign_key' => $user['AccountReviewsUser']['account_review_id'],
                'user_id' => $user['AccountReviewsUser']['user_id'],
                'field' => ($user['AccountReviewsUser']['type'] == 1) ? 'Owner' : 'Reviewer'
            ]);
        }

        $users = ClassRegistry::init('AccountReviewFindingsUser')->find('all', ['recursive' => -1]);

        foreach ($users as $user) {
            $UserFieldsUser->create();

            $UserFieldsUser->save([
                'model' => 'AccountReviewFinding',
                'foreign_key' => $user['AccountReviewFindingsUser']['account_review_finding_id'],
                'user_id' => $user['AccountReviewFindingsUser']['user_id'],
                'field' => ($user['AccountReviewFindingsUser']['type'] == 1) ? 'Owner' : 'Reviewer'
            ]);
        }
    }
}

