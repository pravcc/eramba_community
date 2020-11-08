<?php
App::uses('ErambaHelper', 'View/Helper');
App::uses('OauthConnector', 'Model');

class SamlConnectorsHelper extends ErambaHelper
{
	public $settings = array();
	public $helpers = ['Html', 'Text', 'FieldData.FieldData'];
	
	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);

		$this->settings = $settings;
	}

	public function signSamlRequestField(FieldDataEntity $Field)
	{
		$options = [
            'id' => 'sign-saml-request',
            'data-custom-id' => 'sign-saml-request'
        ];

        $out = $this->FieldData->input($Field, $options);

        $out .= $this->Html->scriptBlock("
            jQuery(function($) {
                $('[data-custom-id=\"sign-saml-request\"]').on(\"change\", function(e) {
                    if ($(this).is(\":checked\")) {
                        $(\"#sp-certificate, #sp-private-key\").removeAttr(\"disabled\");
                    }
                    else {
                        $(\"#sp-certificate, #sp-private-key\").attr(\"disabled\", \"disabled\");
                    }
                }).trigger(\"change\");
            });
        ");

        return $out;
	}

	public function spCertificateField(FieldDataEntity $Field)
	{
		return $this->FieldData->input($Field, [
			'id' => 'sp-certificate'
		]);
	}

	public function spPrivateKeyField(FieldDataEntity $Field)
	{
		return $this->FieldData->input($Field, [
			'id' => 'sp-private-key'
		]);
	}

	public function loginRedirectUrlsField(FieldDataEntity $Field)
    {
    	$redirectUrls = $this->_View->viewVars['loginRedirectUrls'];

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
