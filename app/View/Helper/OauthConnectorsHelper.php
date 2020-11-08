<?php
App::uses('ErambaHelper', 'View/Helper');
App::uses('OauthConnector', 'Model');

class OauthConnectorsHelper extends ErambaHelper
{
	public $settings = array();
	public $helpers = ['Html', 'Text', 'FieldData.FieldData'];
	
	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);

		$this->settings = $settings;
	}

	public function getStatuses($item) {
		$statuses = array();

		if ($item['OauthConnector']['status'] == 1) {
			$statuses[] = $this->getLabel(OauthConnector::statuses($item['OauthConnector']['status']), 'success');
			
		}
		elseif ($item['OauthConnector']['status'] == 0) {
			$statuses[] = $this->getLabel(OauthConnector::statuses($item['OauthConnector']['status']), 'warning');
		}

		return $this->processStatuses($statuses);
	}

	public function redirectUrlsField(FieldDataEntity $Field)
    {
    	$redirectUrls = $this->_View->viewVars['redirectUrls'];

    	$out = '<div class="form-group">
				<label class="control-label">' . __('Redirect URLs') . ':</label>';

		foreach ($redirectUrls as $redirectUrl) {
			$out .= '<div>' . $redirectUrl . '</div>';
		}

		$out .= '<span class="help-block">' . $Field->getDescription() . '</span>
				</div>';

		return $out;
    }
}