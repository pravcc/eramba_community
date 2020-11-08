<?php
App::uses('ErambaHelper', 'View/Helper');
class PolicyHelper extends ErambaHelper {
	public $settings = array();
	
	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);
		$this->settings = $settings;
	}

	/**
	 * Get URL array for a policy document.
	 */
	public function getDocumentUrl($id, $external = false) {
		$url = array(
			'controller' => 'policy',
			'action' => 'document',
			$id
		);

		if ($external) {
			$url['?'] = array(
				'allowForLogged' => true
			);
		}

		return $url;
	}

	public function getDocumentAttrs($id) {
		return array(
			'data-document-link' => true,
			'data-document-id' => $id,
			'escape' => false
			//'data-doc-title' => $title
		);
	}

	/**
	 * Returns link element for a document.
	 */
	public function getDocumentLink($title, $id, $external = false, $shortDesc = false) {
		/*$url = array(
			'controller' => 'policy',
			'action' => 'document',
			$id
		);

		if ($external) {
			$url['?'] = array(
				'allowForLogged' => true
			);
		}*/

		$url = $this->getDocumentUrl($id, $external);

		$attrs = $this->getDocumentAttrs($id);

		if (!empty($shortDesc)) {
			$attrs['class'] = 'bs-popover';
			$attrs['data-trigger'] = 'hover';
			$attrs['data-placement'] = 'top';
			$attrs['data-original-title'] = __('Description');
			$attrs['data-content'] = $shortDesc;
		}

		return $this->Html->link($title, $url, $attrs);
	}

}