<?php
App::uses('ModuleBase', 'Lib');
App::uses('AdvancedFiltersNamesMigration', 'AdvancedFilters.Lib');

class AdvancedFiltersModule extends ModuleBase {
	const QUERY_PARAM = 'advanced_filter';
	const ELEMENT_PATH = 'advancedFilters/';


	public function migrateFilterArgs()
	{
		$Migration = new AdvancedFiltersNamesMigration();

		return $Migration->migrate();
	}

	public function dueDateFiltersFix()
	{
		$ret = true;

		$migrationMap = [
			[
				'model' => 'SecurityServiceAudit',
				'slug' => 'due-in-14-days',
				'fields' => [
					'planned_date' => '_plus_14_days_',
					'planned_date__comp_type' => 2
				]
			],
			[
				'model' => 'SecurityServiceMaintenance',
				'slug' => 'due-in-14-audits',
				'fields' => [
					'planned_date' => '_plus_14_days_',
					'planned_date__comp_type' => 2
				]
			],
			[
				'model' => 'SecurityPolicyReview',
				'slug' => 'due-in-14-days',
				'fields' => [
					'planned_date' => '_plus_14_days_',
					'planned_date__comp_type' => 2
				]
			],
		];

		$AdvancedFilters = ClassRegistry::init('AdvancedFilters.AdvancedFilter');
		$AdvancedFilterValues = ClassRegistry::init('AdvancedFilters.AdvancedFilterValue');

		foreach ($migrationMap as $migration) {
			$filterIds = $AdvancedFilters->find('list', [
				'conditions' => [
					'AdvancedFilter.model' => $migration['model'],
					'AdvancedFilter.slug' => $migration['slug']
				],
				'fields' => [
					'AdvancedFilter.id'
				],
				'recursive' => -1
			]);

			if (empty($filterIds)) {
				continue;
			}

			foreach ($migration['fields'] as $field => $value) {
				$ret &= (bool) $AdvancedFilterValues->updateAll(['value' => '"' . $value . '"'], [
					'AdvancedFilterValue.advanced_filter_id' => $filterIds,
					'AdvancedFilterValue.field' => $field
				]);
			}
		}

		return $ret;
	}
}
