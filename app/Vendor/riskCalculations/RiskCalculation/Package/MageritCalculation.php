<?php
namespace RiskCalculation\Package;
use RiskCalculation\BaseCalculation;

class MageritCalculation extends BaseCalculation {
	public $allowedModels = array('Risk');
	
	public function __construct() {
		parent::__construct();

		$this->name = 'Magerit';
		$this->description = __('Magerit is a Spanish risk calculation methodology that takes into account the value of the assets and then how impact might affect that value. The Spanish government keeps a very well documented website with the calculations used, we encourage users to review it.');
		$this->settings = array();
		$this->conditions = array();

		return $this;
	}

	public function calculate(\Model $Model, $options, $calculationValues = array()) {
		$this->otherData = array(
			'assetMaxVal' => array(),
			'classificationsPartMath' => array(),
			'classificationsSecondPartMath' => ''
		);

		$vals = array_values($options);
		$classificationIds = $vals[0];
		// debug($classificationIds);
		$assetIds = $vals[1];

		$assetValues = $this->getAssetTypeMaxValue($Model, $assetIds);
		// debug($assetValues);
		if (empty($assetValues)) {
			$this->setInvalid();
		}
		else {
			$this->otherData['assetMaxVal'] = $this->formatAssetVals($assetValues);
		}
		// $this->assetValues = $assetValues;

		$ret = (count($classificationIds)-1) == count($assetValues);
		if ($ret) {
			$specialClassificationId = array_pop($classificationIds);
			$ret &= !empty($specialClassificationId);
		}

		if ($ret) {
			$specialRiskClassificationRowData = $Model->RiskClassification->find('first', array(
				'conditions' => array(
					'RiskClassification.id' => $specialClassificationId
				),
				'recursive' => 0
			));
			$likelihoodValue = $specialRiskClassificationRowData['RiskClassification']['value'];
			$likelihoodType = $specialRiskClassificationRowData['RiskClassificationType']['name'];
			$likelihoodName = $specialRiskClassificationRowData['RiskClassification']['name'];
		}
		// special likelihood classification is not selected yet
		else {
			$this->setInvalid();

			$likelihoodValue = 0;
			$likelihoodType = '-';
			$likelihoodName = '-';
		}

		$sum = 0;
		$this->calculationMath = '';
		$i=0;
		// debug($assetValues);
		foreach ($assetValues as $key => $assetVal) {
			// debug($classificationIds[$key]);
			// if related classification is not yet selected in the form
			if (empty($classificationIds[$key])) {
				$this->otherData['classificationsPartMath'][] = '';
				$this->setInvalid();
				continue;
			}

			$riskClassificationRowData = $Model->RiskClassification->find('first', array(
				'conditions' => array(
					'RiskClassification.id' => $classificationIds[$key]
				),
				'recursive' => 0
			));

			if (empty($riskClassificationRowData)) {
				$this->otherData['classificationsPartMath'][] = '';
				$this->setInvalid();
				continue;
			}
		
			// group
			$cVal = $assetVal['Asset']['max_value'] * $riskClassificationRowData['RiskClassification']['value'];
			$this->otherData['classificationsPartMath'][] = $assetVal['Asset']['max_value'] . " x " . $riskClassificationRowData['RiskClassification']['value'] . ' = ' . $cVal;

			$ccVal = $cVal * $likelihoodValue;
			$classificationsSecondPartMath[] = $cVal . " x " . $likelihoodValue;


			$classificationsMath = array();
			$classificationsMath[] = $riskClassificationRowData['RiskClassification']['value'] . ' (' . $riskClassificationRowData['RiskClassificationType']['name'] . ' - ' . $riskClassificationRowData['RiskClassification']['name'] . ')';
			$classificationsMath[] = $likelihoodValue . ' (' . $likelihoodType . ' - ' . $likelihoodName . ')';

			$this->calculationMath .= $assetVal['Asset']['max_value'];

			$Model->Asset->AssetClassificationsAsset->bindModel(array(
				'belongsTo' => array('Asset', 'AssetClassification')
			));
			$Model->Asset->AssetClassificationsAsset->virtualFields = array(
				'max_value' => 'MAX(AssetClassification.value)'
			);

			$assetTmp = $Model->Asset->AssetClassificationsAsset->find('first', array(
				'conditions' => array(
					'Asset.id' => $assetIds,
					'AssetClassification.asset_classification_type_id' => $assetVal['AssetClassificationType']['id']
				),
				'fields' => array(
					'AssetClassificationsAsset.max_value',
					'AssetClassificationsAsset.asset_id',
					'Asset.name',
					'AssetClassification.name'
				)
			));

			$this->calculationMath .= sprintf(
				' (%s - %s - %s) x %s',
				$assetTmp['Asset']['name'],
				$assetVal['AssetClassificationType']['name'],
				$assetTmp['AssetClassification']['name'],
				implode(' x ', $classificationsMath)
			);

			$sum += $ccVal;

			if (count($assetValues)-1 == $key) {
				$this->calculationMath .= ' = ' . $sum;
				$this->otherData['classificationsSecondPartMath'] = implode(' + ', $classificationsSecondPartMath) . ' = ' . $sum;
			}
			else {
				$this->calculationMath .= ' +';
				$this->calculationMath .= '<br />';
			}

			
		}

		if ($this->isValid()) {
			return $sum;
		}

		return false;
	}

	private function getAssetTypeMaxValue(\Model $Model, $assetIds) {
		$Model->Asset->virtualFields = array(
			'max_value' => 'MAX(AssetClassification.value)'
		);

		$data = $Model->Asset->find('all', array(
			'conditions' => array('Asset.id' => $assetIds),
			'fields' => array(
				'Asset.id', 'Asset.name', 'Asset.max_value', 'AssetClassificationType.name', 'AssetClassificationType.id'
			),
			'contain' => array(
				'AssetClassification' => array(
					'fields' => array('name', 'value')
				)
			),
			'recursive' => -1,
			'group' => array('AssetClassificationType.id'),
			'order' => array('AssetClassificationType.id' => 'ASC'),
			'joins' => array(
				array(
					'table' => 'assets_related',
					'alias' => 'AssetsRelated',
					'type' => 'LEFT',
					'conditions' => array(
						'AssetsRelated.asset_id = Asset.id OR AssetsRelated.asset_related_id = Asset.id'
					)
				),
				array(
					'table' => 'asset_classifications_assets',
					'alias' => 'AssetClassificationsAsset',
					'type' => 'INNER',
					'conditions' => array(
						'AssetClassificationsAsset.asset_id IN (AssetsRelated.asset_related_id, AssetsRelated.asset_id, Asset.id)'
					)
				),
				array(
					'table' => 'asset_classifications',
					'alias' => 'AssetClassification',
					'type' => 'INNER',
					'conditions' => array(
						'AssetClassification.id = AssetClassificationsAsset.asset_classification_id',
					)
				),
				array(
					'table' => 'asset_classification_types',
					'alias' => 'AssetClassificationType',
					'type' => 'INNER',
					'conditions' => array(
						'AssetClassificationType.id = AssetClassification.asset_classification_type_id',
					)
				)
			),
			'recursive' => -1
		));

		// we remove the temporary virtual field for upcoming runtime
		unset($Model->Asset->virtualFields['max_value']);
		
		return $data;
	}

	private function formatAssetVals($vals) {
		$data = array();
		foreach ($vals as $asset) {
			$data[] = array(
				'assetType' => $asset['AssetClassificationType']['name'],
				'maxValue' => $asset['Asset']['max_value'],
				'name' => $asset['Asset']['name']
			);
		}

		return $data;
	}
}
