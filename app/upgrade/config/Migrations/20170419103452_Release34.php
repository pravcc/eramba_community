<?php
use Phinx\Migration\AbstractMigration;

class Release34 extends AbstractMigration
{
	protected function bumpVersion($value) {
		$this->query("UPDATE `settings` SET `value`='" . $value . "' WHERE `settings`.`variable`='DB_SCHEMA_VERSION'");

		if (class_exists('App')) {
			App::uses('Configure', 'Core');

			if (class_exists('Configure')) {
				Configure::write('Eramba.Settings.DB_SCHEMA_VERSION', $value);
			}
		}
	}
	public function up()
	{
		// update risk appetite max value possible to 6 digits
		$this->query("UPDATE `settings` SET `options`='{\"min\":0,\"max\":999999,\"step\":1}' WHERE `settings`.`variable`='RISK_APPETITE'");

		// update a workflow_status to approved on "Everyone" row in BU section
		$this->query("UPDATE `business_units` SET `workflow_status`=4 WHERE `business_units`.`id`=1");

		// update db schema version
		$this->bumpVersion('e1.0.1.017');

		/*$this->table('business_continuity_plan_audits')
			->dropForeignKey([], 'business_continuity_plan_audits_ibfk_2')
			->dropForeignKey([], 'business_continuity_plan_audits_ibfk_5')
			->update();
		$this->table('security_service_maintenances')
			->dropForeignKey([], 'security_service_maintenances_ibfk_2')
			->update();

		$this->table('business_continuity_plan_audits')
			->addForeignKey(
				'business_continuity_plan_id',
				'business_continuity_plans',
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
					'delete' => 'NO_ACTION'
				]
			)
			->update();

		$this->table('security_service_maintenances')
			->addForeignKey(
				'user_id',
				'users',
				'id',
				[
					'update' => 'CASCADE',
					'delete' => 'NO_ACTION'
				]
			)
			->update();*/
	}

	public function down()
	{
		$this->query("UPDATE `settings` SET `options`='{\"min\":0,\"max\":9999,\"step\":1}' WHERE `settings`.`variable`='RISK_APPETITE'");

		$this->query("UPDATE `business_units` SET `workflow_status`=0 WHERE `business_units`.`id`=1");

		$this->bumpVersion('e1.0.1.016');

		/*$this->table('business_continuity_plan_audits')
			->dropForeignKey(
				'business_continuity_plan_id'
			)
			->dropForeignKey(
				'user_id'
			);

		$this->table('security_service_maintenances')
			->dropForeignKey(
				'user_id'
			);

		$this->table('business_continuity_plan_audits')
			->addForeignKey(
				'user_id',
				'users',
				'id',
				[
					'update' => 'CASCADE',
					'delete' => 'RESTRICT'
				]
			)
			->addForeignKey(
				'business_continuity_plan_id',
				'business_continuity_plans',
				'id',
				[
					'update' => 'CASCADE',
					'delete' => 'CASCADE'
				]
			)
			->update();

		$this->table('security_service_maintenances')
			->addForeignKey(
				'user_id',
				'users',
				'id',
				[
					'update' => 'CASCADE',
					'delete' => 'RESTRICT'
				]
			)
			->update();*/
	}
}

