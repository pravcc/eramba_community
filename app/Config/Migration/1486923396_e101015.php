<?php
App::uses('AppMigration', 'Lib');
class E101015 extends AppMigration {

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
	public $description = 'e1.0.1.015';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'queue' => array(
					'description' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'queue_id'),
				),
				'third_party_risks' => array(
					'risk_score_formula' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'risk_score'),
				),
			),
			'alter_field' => array(
				'cron' => array(
					'type' => array('type' => 'string', 'null' => false, 'length' => 128, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'id'),
				),
				'queue' => array(
					'queue_id' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
				)
			),
		),
		'down' => array(
			'drop_field' => array(
				'queue' => array('description'),
				'third_party_risks' => array('risk_score_formula'),
			),
			'alter_field' => array(
				'queue' => array(
					'queue_id' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
				)
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
		$ret = parent::before($direction);

		if ($direction == 'down') {
			$SettingGroup = $this->generateModel('SettingGroup');
			$ret &= $SettingGroup->deleteAll(array(
				'slug' => 'QUEUE'
			));
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
		$ret = parent::after($direction);
		
		if ($direction == 'up') {
			try {
				// awareness program foreign key correction to be CASCADE instead of SET NULL
				$ret &= $this->dropForeignKey('awareness_trainings', 'awareness_programs', 'awareness_trainings_ibfk_3');
				$ret &= $this->addForeignKey(
					array('awareness_trainings', 'awareness_program_id'),
					array('awareness_programs', 'id'),
					null,
					'awareness_trainings_ibfk_3'
				);

				$ret &= $this->dropForeignKey('awareness_program_missed_recurrences', 'awareness_programs', 'awareness_program_missed_recurrences_ibfk_3');
				$ret &= $this->addForeignKey(
					array('awareness_program_missed_recurrences', 'awareness_program_id'),
					array('awareness_programs', 'id'),
					null,
					'awareness_program_missed_recurrences_ibfk_3'
				);

				// cron foreign key forrection to be CASCADE instead of RESTRICT
				$ret &= $this->dropForeignKey('advanced_filter_crons', 'cron', 'advanced_filter_cron_ibfk_2');
				$ret &= $this->addForeignKey(
					array('advanced_filter_crons', 'cron_id'),
					array('cron', 'id'),
					null,
					'advanced_filter_cron_ibfk_2'
				);
			}
			catch (Exception $e) {
				return false;
			}

			// add email queue to settings
			$ret &= $this->save('SettingGroup', array(
				'slug' => 'QUEUE',
				'parent_slug' => 'MAIL',
				'name' => 'Emails In Queue',
				'url' => '{"controller":"queue", "action":"index", "?" :"advanced_filter=1"}'
				
			), false, true);
		}

		return $ret;
	}
}
