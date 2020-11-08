<?php
App::uses('RisksHelper', 'View/Helper');

class ThirdPartyRisksHelper extends RisksHelper {
	public $helpers = ['Html', 'FieldData.FieldData', 'Form', 'FormReload'];

	public function thirdPartyField(FieldDataEntity $Field)
	{
		// $options = [
		// 	'class' => [
		// 		'related-risk-item-input',
		// 		'risk-classifications-trigger'
		// 	],
		// 	'id' => 'risk-tp-id'
		// ];

		$script = $this->Html->scriptBlock("
			$(function() {
				$('#risk-tp-id').erambaAutoComplete({
					url: '/risks/getThreatsVulnerabilities',
					requestKey: ['assocIds'],
					requestType: 'POST',
					responseKey: ['threats', 'vulnerabilities'],
					assocInput: '#risk-threat-id, #risk-vulnerability-id'
				});
			});
		");
		
		$options = [
			'id' => 'risk-tp-id',
			'data-yjs-request' => 'app/triggerRequest/.risk-classification-reload',
			'data-yjs-event-on' => 'change',
			'data-yjs-use-loader' => 'false'
		];

		return $this->FieldData->input($Field, $options) . $script;
	}

	public function assetField(FieldDataEntity $Field)
	{
		$script = $this->_getAssetScript();

		$options = [
			'id' => 'risk-asset-id'
		];	

		return $this->FieldData->input($Field, $options) . $script;
	}

}