<?php
/**
 * Application level View Helper
 *
 * This file is application-wide helper file. You can put all
 * application-wide helper-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Helper
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('Helper', 'View');

/**
 * Application helper
 *
 * Add your application-wide methods in the class below, your helpers
 * will inherit them.
 *
 * @package       app.View.Helper
 */
define('INHERIT_CONFIG_KEY', '_inherit');
class AppHelper extends Helper {
	public $fallbackHelperList = array(
		'RiskExceptions' => 'PolicyExceptions'
	);
	/**
	 * Calculate residual risk.
	 * @param  int $residual_score Residual Score.
	 * @param  int $risk_score     Risk Score.
	 * @return int                 Residual Risk.
	 */
	public function getResidualRisk( $residual_score, $risk_score ) {
		return CakeNumber::precision( getResidualRisk($residual_score, $risk_score), 2 );
	}

	/**
	 * @deprecated
	 * 
	 * Quickly get select options with percentages.
	 */
	public function getPercentageOptions( $multiplier = 10 ) {
		return getPercentageOptions($multiplier);
	}

	/**
	 * Checks if date is expired.
	 */
	public function isExpired( $date = null, $status = null ) {
		return isExpired($date, $status);
	}

	/**
	 * Returns expired label based on date.
	 */
	public function getExpiredLabel( $date = null, $status = null ) {
		if ( $status !== null && (int) $status === 0 ) {
			return '<span class="label label-success">' . __( 'Closed' ) . '</span>';
		}

		$notification = '<span class="label label-success">' . __( 'Not Expired' ) . '</span>';
		if ( $this->isExpired( $date ) ) {
			$notification = '<span class="label label-danger">' . __( 'Expired' ) . '</span>';
		}

		return $notification;
	}

	public function getIncidentsLabel($incidents = null) {
		if ($incidents) {
			$notification = '<span class="label label-warning">' . __('Incident Mapped') . '</span>';
		}
		else {
			$notification = '<span class="label label-success">' . __('No Incident') . '</span>';
		}

		return $notification;
	}

	protected function processHeaderType($type) {
		if (!empty($this->Status)) {
			return $this->Status->processHeaderType($type);
		}
	}
	protected function getInheritedStatuses($item, $inheritOptions = array()) {
		if (!empty($this->Status)) {
			return $this->Status->getInheritedStatuses($item, $inheritOptions);
		}
	}
	protected function getColorType($statuses = array()) {
		if (!empty($this->Status)) {
			return $this->Status->getColorType($statuses);
		}
	}
	/*protected function inheritItemStatus($item, $inheritModel, $configs = '*') {
		if (!empty($this->Status)) {
			return $this->Status->inheritItemStatus($item, $inheritModel, $configs);
		}
	}*/
	protected function inheritItemStatus($item, $inheritModel, $configs = '*') {
		if (!isset($item[$inheritModel])) {
			appError("This item is missing status array data for: " . $inheritModel);
		}

		if (!empty($item[$inheritModel])) {
			if (isset($item[$inheritModel][0])) {
				$statuses = array();
				foreach ($item[$inheritModel] as $i) {
					$statuses = array_merge($statuses, $this->getStatusArr($i, $configs, $inheritModel));
				}

				return $statuses;
			}
			else {
				return $this->getStatusArr($item, $configs, $inheritModel);
			}
		}

		return array();
	}

	public function getStatusClass($item, $modelName = null, $allow = '*') {
		$statuses = $this->getStatusArr($item, $allow, $modelName);
		$type = $this->getColorType($statuses);

		return $type;
	}

	public function getFilterSingleStatus($item, $model, $column) {
		$fallbackHelperList = $this->fallbackHelperList;

		$helperName = Inflector::pluralize($model);
		if (isset($fallbackHelperList[$helperName])) {
			$helperName = $fallbackHelperList[$helperName];
		}

		if (empty($this->{$helperName})) {
			$this->{$helperName} = $this->_View->loadHelper($helperName);
		}

		$ret = 0;
		if (isset($item[$model][0])) {
			foreach ($item[$model] as $i) {

				$s = $this->{$helperName}->getStatusArr($i, $column);
				if (!empty($s)) {
					$ret = 1;
				}
			}
		}

		return $ret;
	}

	public function getFilterStatusesValue($item, $model, $column = '*', $justLabels = false) {
		$fallbackHelperList = $this->fallbackHelperList;

		$helperName = Inflector::pluralize($model);
		if (isset($fallbackHelperList[$helperName])) {
			$helperName = $fallbackHelperList[$helperName];
		}

		if (empty($this->{$helperName})) {
			$this->{$helperName} = $this->_View->loadHelper($helperName);
		}

		$statuses = $this->{$helperName}->getStatusArr($item, $column, $model);
		if (!empty($justLabels)) {
			$labels = array();
			foreach ($statuses as $status) {
				$labels[] = $status['label'];
			}

			$statuses = $labels;
		}

		return $statuses;

		/*$ret = 0;
		if (isset($item[$model][0])) {
			foreach ($item[$model] as $i) {

				$s = $this->{$helperName}->getStatusArr($i, $column);
				if (!empty($s)) {
					$ret = 1;
				}
			}
		}

		return $ret;*/
	}

	public function buildData($data) {
		$attributes = [];

		foreach ($data as $key => $value) {
			$attributes["data-$key"] = $value;
		}

		return $attributes;
	}

}
