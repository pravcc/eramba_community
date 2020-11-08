<?php
use Phinx\Seed\AbstractSeed;

/**
 * Suggestion seed.
 */
class SuggestionSeed extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeds is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'id' => '1',
                'suggestion' => 'Suggestion\\Package\\AssetClassificationType\\Availability',
                'model' => 'AssetClassificationType',
                'foreign_key' => '1',
                'created' => '2017-04-10 15:37:31',
            ],
            [
                'id' => '2',
                'suggestion' => 'Suggestion\\Package\\AssetClassification\\AvailabilityHigh',
                'model' => 'AssetClassification',
                'foreign_key' => '1',
                'created' => '2017-04-10 15:37:31',
            ],
            [
                'id' => '3',
                'suggestion' => 'Suggestion\\Package\\AssetClassification\\AvailabilityLow',
                'model' => 'AssetClassification',
                'foreign_key' => '2',
                'created' => '2017-04-10 15:37:39',
            ],
            [
                'id' => '4',
                'suggestion' => 'Suggestion\\Package\\AssetClassification\\AvailabilityMedium',
                'model' => 'AssetClassification',
                'foreign_key' => '3',
                'created' => '2017-04-10 15:37:47',
            ],
            [
                'id' => '5',
                'suggestion' => 'Suggestion\\Package\\AssetClassificationType\\Confidentiality',
                'model' => 'AssetClassificationType',
                'foreign_key' => '2',
                'created' => '2017-04-10 15:37:55',
            ],
            [
                'id' => '6',
                'suggestion' => 'Suggestion\\Package\\AssetClassification\\ConfidentialityHigh',
                'model' => 'AssetClassification',
                'foreign_key' => '4',
                'created' => '2017-04-10 15:37:55',
            ],
            [
                'id' => '7',
                'suggestion' => 'Suggestion\\Package\\AssetClassification\\ConfidentialityMedium',
                'model' => 'AssetClassification',
                'foreign_key' => '5',
                'created' => '2017-04-10 15:38:03',
            ],
            [
                'id' => '8',
                'suggestion' => 'Suggestion\\Package\\AssetClassification\\ConfidentialityLow',
                'model' => 'AssetClassification',
                'foreign_key' => '6',
                'created' => '2017-04-10 15:38:08',
            ],
            [
                'id' => '9',
                'suggestion' => 'Suggestion\\Package\\AssetClassificationType\\Integrity',
                'model' => 'AssetClassificationType',
                'foreign_key' => '3',
                'created' => '2017-04-10 15:38:49',
            ],
            [
                'id' => '10',
                'suggestion' => 'Suggestion\\Package\\AssetClassification\\IntegrityLow',
                'model' => 'AssetClassification',
                'foreign_key' => '7',
                'created' => '2017-04-10 15:38:49',
            ],
            [
                'id' => '11',
                'suggestion' => 'Suggestion\\Package\\AssetClassification\\IntegrityHigh',
                'model' => 'AssetClassification',
                'foreign_key' => '8',
                'created' => '2017-04-10 15:38:55',
            ],
            [
                'id' => '12',
                'suggestion' => 'Suggestion\\Package\\AssetClassification\\IntegrityMedium',
                'model' => 'AssetClassification',
                'foreign_key' => '9',
                'created' => '2017-04-10 15:39:11',
            ],
            [
                'id' => '13',
                'suggestion' => 'Suggestion\\Package\\RiskClassificationType\\Likelihood',
                'model' => 'RiskClassificationType',
                'foreign_key' => '1',
                'created' => '2017-04-11 12:55:49',
            ],
            [
                'id' => '14',
                'suggestion' => 'Suggestion\\Package\\RiskClassification\\LikelihoodLow',
                'model' => 'RiskClassification',
                'foreign_key' => '1',
                'created' => '2017-04-11 12:55:49',
            ],
            [
                'id' => '15',
                'suggestion' => 'Suggestion\\Package\\RiskClassificationType\\Impact',
                'model' => 'RiskClassificationType',
                'foreign_key' => '2',
                'created' => '2017-04-11 12:55:54',
            ],
            [
                'id' => '16',
                'suggestion' => 'Suggestion\\Package\\RiskClassification\\ImpactMedium',
                'model' => 'RiskClassification',
                'foreign_key' => '2',
                'created' => '2017-04-11 12:55:54',
            ],
            [
                'id' => '17',
                'suggestion' => 'Suggestion\\Package\\RiskClassification\\LikelihoodHigh',
                'model' => 'RiskClassification',
                'foreign_key' => '3',
                'created' => '2017-04-11 12:56:01',
            ],
            [
                'id' => '18',
                'suggestion' => 'Suggestion\\Package\\RiskClassification\\LikelihoodMedium',
                'model' => 'RiskClassification',
                'foreign_key' => '4',
                'created' => '2017-04-11 12:56:07',
            ],
            [
                'id' => '19',
                'suggestion' => 'Suggestion\\Package\\RiskClassification\\ImpactHigh',
                'model' => 'RiskClassification',
                'foreign_key' => '5',
                'created' => '2017-04-11 12:56:16',
            ],
            [
                'id' => '20',
                'suggestion' => 'Suggestion\\Package\\RiskClassification\\ImpactLow',
                'model' => 'RiskClassification',
                'foreign_key' => '6',
                'created' => '2017-04-11 12:56:27',
            ],
        ];

        $table = $this->table('suggestions');
        $table->insert($data)->save();
    }
}
