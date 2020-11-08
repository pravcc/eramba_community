<?php
use Phinx\Migration\AbstractMigration;

class ReviewsSystemFilterCompletedParam extends AbstractMigration
{
    public function up()
    {
        if (class_exists('App')) {
            $AdvancedFilter = ClassRegistry::init('AdvancedFilters.AdvancedFilter');
            $AdvancedFilterValue = $AdvancedFilter->AdvancedFilterValue;

            $filters = $AdvancedFilter->find('list', [
                'conditions' => [
                    'AdvancedFilter.slug' => 'due-in-14-days',
                    'AdvancedFilter.model' => [
                        'AssetReview',
                        'SecurityPolicyReview',
                        'RiskReview',
                        'ThirdPartyRiskReview',
                        'BusinessContinuityReview'
                    ]
                ],
                'fields' => ['id'],
                'recursive' => -1
            ]);

            $ret = true;
            foreach ($filters as $filterId) {
                $hasCompleted = $AdvancedFilterValue->find('count', [
                    'conditions' => [
                        'AdvancedFilterValue.field' => 'completed',
                        'AdvancedFilterValue.advanced_filter_id' => $filterId
                    ],
                    'recursive' => -1
                ]);

                if (!$hasCompleted) {
                    $AdvancedFilterValue->create();
                    $AdvancedFilterValue->set([
                        'advanced_filter_id' => $filterId,
                        'field' => 'completed',
                        'value' => '0',
                        'many' => '0'
                    ]);

                    $ret &= $AdvancedFilterValue->save();

                    $AdvancedFilterValue->create();
                    $AdvancedFilterValue->set([
                        'advanced_filter_id' => $filterId,
                        'field' => 'completed__comp_type',
                        'value' => '5',
                        'many' => '0'
                    ]);

                    $ret &= $AdvancedFilterValue->save();
                }
            }

            if (!$ret) {
                App::uses('CakeLog', 'Log');
                $log = "Error occured when processing database synchronization for Reviews System Filter Completion filter parameters.";
                CakeLog::write('error', "{$log}");

                throw new Exception($log, 1);
                return false;
            }
        }
    }

    public function down()
    {
    }
}

