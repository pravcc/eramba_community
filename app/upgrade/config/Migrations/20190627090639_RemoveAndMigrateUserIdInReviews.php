<?php
use Phinx\Migration\AbstractMigration;

class RemoveAndMigrateUserIdInReviews extends AbstractMigration
{
    public function up()
    {
        if (class_exists('App')) {
            App::uses('ClassRegistry', 'Utility');
            App::uses('ThirdPartyRiskReview', 'Model');
            App::uses('SecurityPolicyReview', 'Model');
            App::uses('RiskReview', 'Model');
            App::uses('BusinessContinuityReview', 'Model');
            App::uses('AssetReview', 'Model');

            //
            // remove foreign key from old user_id field and allow null
            $users = $this->table('reviews');
            $users->dropForeignKey('user_id')->changeColumn('user_id', 'integer', [
                'limit' => 11,
                'null' => true
            ])->save();
            //
            
            //
            // Models: ThirdPartyRisk, SecurityPolicy, Risk, BusinessContinuity, Asset
            $migrateModels = [
                'ThirdPartyRiskReview',
                'SecurityPolicyReview',
                'RiskReview',
                'BusinessContinuityReview',
                'AssetReview'
            ];

            $ret = true;
            foreach ($migrateModels as $model) {
                $ModelObj = ClassRegistry::init($model);
                $results = $ModelObj->find('all', [
                    'fields' => [
                        'id', 'user_id', 'model', 'foreign_key'
                    ],
                    'conditions' => [
                        'model' => $ModelObj->getRelatedModel()
                    ]
                ]);

                if (!empty($results)) {
                    foreach ($results as $data) {
                        $TempModel = ClassRegistry::init($data[$model]['model']);
                        if (!$TempModel->exists($data[$model]['foreign_key'])) {
                            continue;
                        }

                        $userId = $data[$model]['user_id'];
                        if (empty($userId)) {
                            continue;
                        }

                        $reviewer = 'User-' . $userId;

                        $ret &= $ModelObj->saveAssociated([
                            $model => [
                                'id' => $data[$model]['id'],
                                'Reviewer' => [$reviewer]
                            ]
                        ], [
                            'deep' => true,
                            'fieldList' => ['Reviewer']
                        ]);
                    }
                }
            }
            //
            
            if (!$ret) {
                App::uses('CakeLog', 'Log');
                $errorMsg = "Error occured when migrating user_id field to Reviewer user field";
                CakeLog::write('error', $errorMsg);

                throw new Exception($errorMsg, 1);
                return false;
            }
        }
    }

    public function down()
    {

    }
}
