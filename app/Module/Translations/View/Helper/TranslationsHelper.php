<?php
App::uses('AppHelper', 'View/Helper');

class TranslationsHelper extends AppHelper
{
	public $helpers = ['Html', 'Form', 'LimitlessTheme.Icons'];

	public function loginLanguageSelect()
	{
		$availableTranslations = $this->_View->get('availableTranslations');

		if ($availableTranslations <= 1) {
			return '';
		}

		$input = $this->Form->input('language', [
			'label' => false, 
			'div' => false,
			'options' => $availableTranslations,
			'default' => Configure::read('Config.translation_id'),
			'id' => 'language-selector',
			'class' => 'form-control select2'
		]);

		$icon = $this->Html->div('form-control-feedback', $this->Icons->render('flag3', ['class' => 'text-muted']), ['escape' => false]);

		$wrapper = $this->Html->div('form-group has-feedback has-feedback-left form-group-language-select', $input . $icon, ['escape' => false]);

		$url = Router::url(['plugin' => false, 'controller' => 'users', 'action' => 'changeLanguage']);

		$script = $this->Html->scriptBlock('
			jQuery(function($) {
				var $langSelector = $("#language-selector");
				var langUrl = "' . $url . '";
				
				$langSelector.on("change", function(e) {
					if (typeof eramba !== "undefined") {
						eramba.setPseudoNProgress();
					}

					var lang = $(this).val();
					window.location.href = langUrl + "/" + lang;
				});

				$(".select2").select2({minimumResultsForSearch: -1});
			});
		');

		return $wrapper . $script;
	}
}
