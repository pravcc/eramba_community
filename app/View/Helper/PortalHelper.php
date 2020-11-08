<?php
App::uses('AppHelper', 'View/Helper');

/**
 * Dispatcher class for any Section Helper class that can route methods using a model name as argument. 
 */
class PortalHelper extends AppHelper {
	public $helpers = ['Html', 'Form'];
	
	/**
	 * Call section's helper methods throught this dispatcher using a Model name.
	 * Can be used in functionalities that can manage all sections in one place under one view.
	 * 
	 * @param  string $name Method to call.
	 * @param  array  $args Arguments, first argument should always be a model name.
	 */
	public function beforeRender($viewFile) {
		
	}

	public function floatingHeaderScript() {
		return $this->Html->scriptBlock("
			$(function() {
				function togglePortalHeader() {
					var scrollTop = $(document).scrollTop();
					var headerTop = $('.portal-header-holder').offset().top;

					if (scrollTop > headerTop) {
						$('.portal-header-holder').css('height', $('.portal-header').outerHeight() + 'px');
						$('.portal-header').addClass('fixed');
						
					}
					else {
						$('.portal-header-holder').css('height', 'auto');
						$('.portal-header').removeClass('fixed');
					}
				}

				$(document).on('scroll', function() {
					togglePortalHeader();
				});
			});
		");
	}

	public function submitBtn($incompleteSubmit = false) {
		$submitLabel = __('Submit!');
		$submitClass = 'hidden portal-submit-btn-toggle';

		if ($incompleteSubmit) {
			$submitLabel = __('Submit at any time');
			$submitClass = '';
		}

		return $this->Form->submit($submitLabel, [
			'id' => 'portal-submit-btn',
			'class' => 'btn btn-success ' . $submitClass,
			'div' => false
		]);
	}
}