<?php
use Phinx\Migration\AbstractMigration;

class CreateModuleFiltersMigration extends AbstractMigration
{

    public function up()
    {
        if (class_exists('App')) {
            App::uses('ClassRegistry', 'Utility');

            $users = ClassRegistry::init('User')->find('all', [
                'fields' => ['User.id'],
                'recursive' => -1
            ]);

            $AdvancedFilters = ClassRegistry::init('AdvancedFilters.AdvancedFilter');

            // not one of the below list of models is used in community
            if (AppModule::loaded('AccountReviews')) {
                $sections = [
                    'AccountReview',
                    'AccountReviewFeedback',
                    'AccountReviewFinding',
                    'AccountReviewPull',
                    'AccountReviewPullSystemLog',
                    'VendorAssessment',
                    'VendorAssessmentFeedback',
                    'VendorAssessmentFinding',
                    'VendorAssessmentSystemLog'
                ];

                foreach ($users as $user) {
                    foreach ($sections as $section) {
                        $filterExists = (boolean) $AdvancedFilters->find('count', [
                            'conditions' => [
                                'AdvancedFilter.model' => $section,
                                'AdvancedFilter.slug' => 'all-items',
                                'AdvancedFilter.user_id' => $user['User']['id']
                            ]
                        ]);

                        if ($filterExists) {
                            continue;
                        }

                        $AdvancedFilters->syncDefaultIndex($user['User']['id'], $section);
                    }
                }
            }
        }
    }

    public function down()
    {

    }
}
