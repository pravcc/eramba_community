<?php
App::uses('AppMigration', 'Lib');
class E101002 extends AppMigration {

/**
 * Should this migration update Database version in `settings` table on current DataSource connection.
 *
 * @var bool
 */
	public $updateVersion = true;

/**
 * Migration description. Used as a database version after successful migration if `$this->updateVersion` is true.
 *
 * @var string
 */
	public $description = 'e1.0.1.002';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_table' => array(
				'advanced_filter_values' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
					'advanced_filter_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
					'field' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'value' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'many' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 4, 'unsigned' => true),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						'advanced_filter_values_ibfk_1' => array('column' => 'advanced_filter_id', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'advanced_filters' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
					'user_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
					'name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'model' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
					'modified' => array('type' => 'datetime', 'null' => false, 'default' => null),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						'advanced_filters_ibfk_1' => array('column' => 'user_id', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'assets_related' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
					'asset_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
					'asset_related_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
					'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						'asset_id' => array('column' => 'asset_id', 'unique' => 0),
						'asset_related_id' => array('column' => 'asset_related_id', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'risk_calculation_values' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
					'risk_calculation_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
					'field' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'value' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
					'modified' => array('type' => 'datetime', 'null' => false, 'default' => null),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						'risk_calculation_id' => array('column' => 'risk_calculation_id', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'risk_calculations' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
					'model' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'method' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'modified' => array('type' => 'datetime', 'null' => false, 'default' => null),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
			),
			'create_field' => array(
				'asset_classifications' => array(
					'value' => array('type' => 'float', 'null' => true, 'default' => null, 'unsigned' => false, 'after' => 'criteria'),
				),
				'risks' => array(
					'risk_score_formula' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'risk_score'),
				),
			),
			'alter_field' => array(
				'business_continuities' => array(
					'risk_score' => array('type' => 'float', 'null' => true, 'default' => null, 'unsigned' => false),
					'residual_risk' => array('type' => 'float', 'null' => false, 'default' => null, 'unsigned' => false),
				),
				'risk_classifications' => array(
					'value' => array('type' => 'float', 'null' => true, 'default' => null, 'unsigned' => false),
				),
				'risks' => array(
					'risk_score' => array('type' => 'float', 'null' => true, 'default' => null, 'unsigned' => false),
					'residual_risk' => array('type' => 'float', 'null' => false, 'default' => null, 'unsigned' => false),
				),
				'third_party_risks' => array(
					'risk_score' => array('type' => 'float', 'null' => true, 'default' => null, 'unsigned' => false),
					'residual_risk' => array('type' => 'float', 'null' => false, 'default' => null, 'unsigned' => false),
				),
			),
		),
		'down' => array(
			'drop_table' => array(
				'advanced_filter_values', 'advanced_filters', 'assets_related', 'risk_calculation_values', 'risk_calculations'
			),
			'drop_field' => array(
				'asset_classifications' => array('value'),
				'risks' => array('risk_score_formula'),
			),
			'alter_field' => array(
				'business_continuities' => array(
					'risk_score' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
					'residual_risk' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
				),
				'risk_classifications' => array(
					'value' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
				),
				'risks' => array(
					'risk_score' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
					'residual_risk' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
				),
				'third_party_risks' => array(
					'risk_score' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
					'residual_risk' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
				),
			),
		),
	);

/**
 * Before migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function before($direction) {
		$ret = true;
		if ($direction == 'down') {
			try {
				$ret &= $this->dropForeignKey('advanced_filters', 'users', 'advanced_filters_ibfk_1');
				$ret &= $this->dropForeignKey('advanced_filter_values', 'advanced_filters', 'advanced_filter_values_ibfk_1');
				
				$ret &= $this->dropForeignKey('assets_related', 'assets', 'assets_related_ibfk_1');
				$ret &= $this->dropForeignKey('assets_related', 'assets', 'assets_related_ibfk_2');

				$ret &= $this->dropForeignKey('risk_calculation_values', 'risk_calculations', 'risk_calculation_values_ibfk_1');
			}
			catch (Exception $e) {
				return false;
			}
		}

		return $ret;
	}

/**
 * After migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function after($direction) {
		parent::after($direction);
		
		$ret = true;
		if ($direction == 'up') {
			try {
				$ret &= $this->addForeignKey(
					array('advanced_filters', 'user_id'),
					array('users', 'id'),
					null,
					'advanced_filters_ibfk_1'
				);

				$ret &= $this->addForeignKey(
					array('advanced_filter_values', 'advanced_filter_id'),
					array('advanced_filters', 'id'),
					null,
					'advanced_filter_values_ibfk_1'
				);

				$ret &= $this->addForeignKey(
					array('assets_related', 'asset_id'),
					array('assets', 'id'),
					null,
					'assets_related_ibfk_1'
				);

				$ret &= $this->addForeignKey(
					array('assets_related', 'asset_related_id'),
					array('assets', 'id'),
					null,
					'assets_related_ibfk_2'
				);

				$ret &= $this->addForeignKey(
					array('risk_calculation_values', 'risk_calculation_id'),
					array('risk_calculations', 'id'),
					null,
					'risk_calculation_values_ibfk_1'
				);
			}
			catch (Exception $e) {
				return false;
			}

			if (!$ret) {
				return false;
			}

			$ret &= $this->insertData();
		}
		
		return $ret;
	}

	private function insertData() {
		$ret = true;

		// add risk calculation data
		$ret &= $this->save('RiskCalculation', array(
			'model' => 'Risk',
			'method' => 'eramba'
		), false, true);

		$ret &= $this->save('RiskCalculation', array(
			'model' => 'ThirdPartyRisk',
			'method' => 'eramba'
		), false, true);

		$ret &= $this->save('RiskCalculation', array(
			'model' => 'BusinessContinuity',
			'method' => 'eramba'
		), false, true);

		$RiskClassificationType = $this->initModel('RiskClassificationType');
		$types = $RiskClassificationType->find('list', array(
			'fields' => array('id', 'id'),
			'recursive' => -1
		));

		// $RiskCalculationValue = $this->initModel('RiskCalculationValue');
		$RiskCalculation = $this->initModel('RiskCalculation');
		$calcs = $RiskCalculation->find('list', array(
			'fields' => array('id', 'id'),
			'recursive' => -1
		));

		foreach ($calcs as $calc) {
			foreach ($types as $type) {
				$ret &= $this->save('RiskCalculationValue', array(
					'risk_calculation_id' => $calc,
					'field' => 'default',
					'value' => $type
				), false, true);
			}
		}

		// getVariable

		// asset classification values set to 0
		$AssetClassification = $this->generateModel('AssetClassification');
		$class = $AssetClassification->find('list', array(
			'fields' => array('id', 'id'),
			'recursive' => -1
		));

		foreach ($class as $c) {
			$AssetClassification->id = $c;
			$ret &= $AssetClassification->saveField('value', '0', array(
				'validate' => false,
				'callbacks' => false
			));	
		}
		
		return $ret;
	}
}
