<?php
class RiskClassificationsRisk extends AppModel {
	public $actsAs = array(
		'Containable'
	);

	public $belongsTo = array(
		'RiskClassification',
		'Risk'
	);


}
