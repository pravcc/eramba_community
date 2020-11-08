<?php
App::uses('AppHelper', 'View/Helper');
App::uses('Inflector', 'Utility');

class SettingsHelper extends AppHelper
{
	public $helpers = ['Html', 'Form'];

	public function input($setting, $options)
	{
		// error
		$error = $this->Form->error($setting['variable'], null, [
	        'wrap' => 'label',
	        'class' => 'validation-error-label',
	        'escape' => false
    	]);

		// helper text
    	$helpBlock = '';
	    if (!empty($setting['info'])) {
	        $helpBlock = $this->Html->tag('span', $setting['info'], ['class' => 'help-block']);
	    }

	    // default options
	    $options = array_merge([
	        'label' => [
	            'class' => 'control-label',
	            'text' => $setting['name']
	        ],
	        'div' => [
	            'class' => 'form-group',
	            'errorClass' => 'has-error',
	        ],
	        'class' => 'form-control',
	        'type' => $setting['type'],
	        'after' => $error . $helpBlock,
	        'default' => (isset($setting['defaultValue'])) ? $setting['defaultValue'] : null,
	        'error' => ''
	    ], $options);

	    if ($setting['type'] == 'select') {
	        $options = array_merge($options, [
	            'options' => call_user_func($setting['options']),
	            'class' => 'select2',
	        ]);
	    }
	    elseif ($setting['type'] == 'checkbox') {
	        $options = array_merge($options, [
	            'label' => false,
	            'class' => 'switchery',
	            'type' => 'checkbox',
	            'before' => '<div class="checkbox checkbox-switchery switchery-sm"><label>',
	            'after' =>  $setting['name'] . '</label></div>' . $error . $helpBlock,
	            'value' => 1
	        ]);
	    }

		return $this->Form->input($setting['variable'], $options);
	}

	public function defaultCurrency($setting, $options)
	{
		$options = array_merge($options, [
			'class' => 'form-control select2-currency',
			'data-placeholder' => __('Choose a currency for the system')
		]);

		return $this->input($setting, $options);
	}

	public function timezone($setting, $options)
	{
		$options = array_merge($options, [
			'default' => date_default_timezone_get(),
			'id' => 'timezone-field',
			'data-yjs-request' => 'app/submitForm',
			'data-yjs-event-on' => 'init|change',
			'data-yjs-datasource-url' => "/settings/getTimeByTimezone",
			'data-yjs-forms' => $this->_View->get('formName'),
			'data-yjs-form-fields' => 'data[Setting][TIMEZONE]',
			'data-yjs-target' => '#timezone-actual-time'
		]);

		return $this->input($setting, $options) . $this->Html->div('', '', ['id' => 'timezone-actual-time']);
	}

	public function cronUrl($setting, $options)
	{
		$options = array_merge($options, [
			'div' => [
	            'class' => 'form-group cron-type-cli',
	            'errorClass' => 'has-error',
	        ],
	        'default' => $setting['defaultValue'],
		]);

		// fake info cronUrlWeb input
		$cronUrlWebSetting = array_merge($setting, [
			'variable' => 'CRON_URL_WEB',
			'info' => __('This is the URL you are currently using on your browser and the one you should use to configure your crontab entries. If you change the URL of this system you must update the crontab jobs on the linux system.'),
		]);

		$cronUrlWebOptions = array_merge($options, [
			'disabled' => 'disabled',
			'value' => Configure::read('App.fullBaseUrl'),
			'div' => [
	            'class' => 'form-group cron-type-web',
	            'errorClass' => 'has-error',
	        ],
	        'id' => 'cron-type-web-url',
	        'disabled' => 'disabled',
		]);

		return $this->input($setting, $options) . $this->input($cronUrlWebSetting, $cronUrlWebOptions);
	}

	public function cronSecurityKey($setting, $options)
	{
		$options = array_merge($options, [
			'div' => [
	            'class' => 'form-group cron-type-web',
	            'errorClass' => 'has-error',
	        ],
		]);

		return $this->input($setting, $options);
	}
}
