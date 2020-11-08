<?php
abstract class StatusTemplatesLib {

	public static function getTemplate(Model $Model, $configName, $options = array()) {
		$options = array_merge(
			array(
				'model' => $Model->alias,
				'niceModel' => parseModelNiceName($Model->alias),
				'titleColumn' => $Model->mapping['titleColumn'],
				'migrateRecords' => array(),
				'auditLabel' => __('Audit')
			),
			(array) $options
		);

		return self::{$configName}($options);
	}

	private static function auditsLastFailed($options) {
		$config = array(
			'column' => 'audits_last_passed',
			'fn' => array('statusProcess', 'audits_last_passed'),
			'migrateRecords' => $options['migrateRecords'],
			'toggles' => array(
				'fail' => array(
						'value' => 0,
						'message' => __('The Audit for the date %s on the %s %s was tagged as Fail'),
						'messageArgs' => array(
							0 => array(
								'type' => 'fn',
								'fn' => array('lastAuditDate', $options['model'], 0),
							),
							1 => array(
								'type' => 'value',
								$options['niceModel']
							),
							2 => '%Current%.' . $options['titleColumn']
						)
					),
					'pass' => array(
						'value' => 1,
						'message' => __('The Audit for the date %s on the %s %s was tagged as Pass'),
						'messageArgs' => array(
							0 => array(
								'type' => 'fn',
								'fn' => array('lastAuditDate', $options['model']),
							),
							1 => array(
								'type' => 'value',
								$options['niceModel']
							),
							2 => '%Current%.' . $options['titleColumn']
						)
					),
			),
			'custom' => array(
				'toggles' => array(
					
					'AuditDelete' => array(
						'value' => 1,
						'message' => __('The Audit for the date %s that has been tagged as Fail on the %s %s has been deleted'),
						'messageArgs' => array(
							0 => array(
								'type' => 'custom',
								'failedAuditDateBeforeDelete'
							),
							1 => array(
								'type' => 'value',
								$options['niceModel']
							),
							2 => '%Current%.' . $options['titleColumn']
						)
					),
				)
			)
		);

		return $config;
	}

	private static function auditsLastMissing($options) {
		$config = array(
			'column' => 'audits_last_missing',
			'fn' => array('statusProcess', 'audits_last_missing'),
			'migrateRecords' => $options['migrateRecords'],
			'customValues' => array(
				'before' => array(
					'lastMissingAudit' => array('lastMissingAudit', $options['model'])
				)
			),
			'toggles' => array(
				'missing' => array(
					'value' => 1,
					// i.e. The Security Service $title has a missing Audit $date
					'message' => __('The %s %s has a missing %s %s'),
					'messageArgs' => array(
						0 => array(
							'type' => 'value',
							$options['niceModel']
						),
						1 => '%Current%.' . $options['titleColumn'],
						2 => array(
							'type' => 'value',
							$options['auditLabel']
						),
						3 => array(
							'type' => 'fn',
							'fn' => array('lastMissingAudit', $options['model']),
						)
					)
				),
				'notMissing' => array(
					'value' => 0,
					'message' => __('The %s planned for the date %s on the %s %s has been set to %s'),
					'messageArgs' => array(
						0 => array(
							'type' => 'value',
							$options['auditLabel']
						),
						1 => array(
							'type' => 'custom',
							'lastMissingAudit',
						),
						2 => array(
							'type' => 'value',
							$options['niceModel']
						),
						3 => '%Current%.' . $options['titleColumn'],
						4 => array(
							'type' => 'fn',
							'fn' => array('lastMissingAuditResult', $options['model'])
						)
					)
				)
			),
			'custom' => array(
				'toggles' => array(
					'AuditDelete' => array(
						'value' => 0,
						'message' => __('The %s for the date %s that has been missing on the %s %s has been deleted'),
						'messageArgs' => array(
							0 => array(
								'type' => 'value',
								$options['auditLabel']
							),
							1 => array(
								'type' => 'custom',
								'missingAuditDateBeforeDelete'
							),
							2 => array(
								'type' => 'value',
								$options['niceModel']
							),
							3 => '%Current%.' . $options['titleColumn']
						)
					),
				)
			)
		);

		return $config;
	}

}