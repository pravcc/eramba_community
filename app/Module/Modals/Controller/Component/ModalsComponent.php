<?php

App::uses('Component', 'Controller');
App::uses('Hash', 'Utility');
App::uses('CakeText', 'Utility');
App::uses('ClassRegistry', 'Utility');
App::uses('Inflector', 'Utility');
App::uses('Modal', 'Modals.Lib');

class ModalsComponent extends Component
{
	public $components = [];

	protected $_controller;

	protected $modal = null;

	protected $init = false;

	protected $modalId = 0;

	protected $friendlyName = "";

	protected $breadcrumbs = [];

	protected $types = [
		'normal' => [
			'headerBgClass' => ''
		],
		'warning' => [
			'headerBgClass' => 'bg-danger'
		],
		'info' => [
			'headerBgClass' => 'bg-primary'
		],
		'success' => [
			'headerBgClass' => 'bg-success'
		]
	];

    public function __construct(ComponentCollection $collection, $settings = array())
    {
		if (empty($this->settings)) {
			$this->settings = [
				'layout' => 'LimitlessTheme.modals/modal',
				'header' => [
					'class' => '',
					'heading' => '',
					'buttons' => [
						'close' => true
					]
				],
				'body' => '',
				'footer' => [
					'closeBtn' => true,
					'saveBtn' => false,
					'buttons' => [
						'closeBtn' => [
							'visible' => true,
							'text' => __('Close'),
							'tag' => 'button',
							'options' => [
								'class' => 'btn btn-link',
								'data-yjs-request' => 'app/closeModal', 
								'data-yjs-modal-id' => null,
								'data-yjs-event-on' => 'click',
								'data-yjs-use-loader' => false
							]
						],
						'saveBtn' => [
							'visible' => false,
							'text' => __('Save'),
							'tag' => 'button',
							'options' => [
								'class' => 'btn btn-primary',
								'data-yjs-request' => "crud/submitForm",
								'data-yjs-target' => "modal", 
								'data-yjs-modal-id' => null,
								'data-yjs-on-modal-success' => "close", 
								'data-yjs-datasource-url' => null, 
								'data-yjs-forms' => null, 
								'data-yjs-event-on' => "click",
								'data-yjs-on-success-reload' => "#main-toolbar|~.advanced-filter-object|#main-content"
							]
						]
					]
				],
				'type' => 'normal'
			];
		}

		$settings = array_merge($this->settings, (array)$settings);
		parent::__construct($collection, $settings);

		// Create modal class
		$this->modal = new Modal();
	}

	public function initialize(Controller $controller)
	{
		$this->_controller = $controller;

		// Set modalId
		if (isset($this->_controller->request->query['modalId'])) {
			$this->modalId = $this->_controller->request->query['modalId'];
		} else if (isset($this->_controller->request->data['modalId'])) {
			$this->modalId = $this->_controller->request->data['modalId'];
		}

		if (isset($this->_controller->request->query['modalBreadcrumbs']) ||
			isset($this->_controller->request->data['modalBreadcrumbs'])) {
			$modalBreadcrumbs = isset($this->_controller->request->query['modalBreadcrumbs']) ? $this->_controller->request->query['modalBreadcrumbs'] : $this->_controller->request->data['modalBreadcrumbs'];
			$parentModalHeaderBits = explode('|yjs-and|', $modalBreadcrumbs);
			$parentModalHeaderBits = array_reverse($parentModalHeaderBits);
			foreach ($parentModalHeaderBits as $pmhb) {
				$bit = explode('|yjs-pair|', $pmhb);
				$this->addBreadcrumb(count($bit) == 2 ? $bit : $bit[0], true);
			}
		}
	}

	public function setModalId($id)
	{
		$this->modalId = $id;
	}

	public function getModalId()
	{
		return $this->modalId;
	}

	public function setFriendlyName($name)
	{
		$this->friendlyName = $name;
	}

	public function getFriendlyName()
	{
		return $this->friendlyName;
	}

	public function init($val = true)
	{
		$this->init = $val;
	}

	public function initModal($force = false)
	{
		if (!$force && !$this->init) {
			return false;
		}

		$this->_handleLayout();
		$this->_handleHeader();
		$this->_handleBody();
		$this->_handleFooter();

		$this->modal->setModalId($this->getModalId());
		$this->modal->setFriendlyName($this->getFriendlyName());
		$this->_controller->set('modal', $this->modal);

		return true;
	}

	public function setLayout($layout)
	{
		$this->settings['layout'] = $layout;
	}

	public function setType($type)
	{
		if (array_key_exists($type, $this->types)) {
			$setting = $this->types[$type];
			$this->setHeaderClass($setting['headerBgClass']);
		}
	}

	public function setHeaderClass($class)
	{
		$this->settings['header']['class'] = $class;
	}

	public function setHeaderHeading($heading, $stripHtml = true)
	{
		$this->settings['header']['heading'] = $stripHtml ? strip_tags($heading) : $heading;
	}

	public function addBreadcrumb($bit, $prepend = false, $stripHtml = true)
	{
		if (!is_array($bit)) {
			$bit = [0, $bit];
		}

		if ($stripHtml) {
			$bit[1] = strip_tags($bit[1]);
		}

		$this->breadcrumbs[] = [
			'bit' => $bit,
			'place' => $prepend == true ? 'prepend' : 'append'
		];
	}

	public function getBreadcrumbs()
	{
		return $this->breadcrumbs;
	}

	public function showHeaderCloseButton($val = true)
	{
		$this->settings['header']['buttons']['close'] = $val == true ? true : false;
	}

	public function setFooterButtons($buttons)
	{
		$this->settings['footer']['buttons'] = $buttons;
	}

	public function addFooterButton($text, $options, $name = null, $visible = true, $tag = 'button', $placement = null)
	{
		$placementType = 'after';
		$placementBtn = '';
		if (!empty($placement)) {
			if (strpos($placement, 'before-') === 0) {
				$placementType = 'before';
				$placementBtn = substr($placement, strlen('before-'));
			} else if (strpos($placement, 'after-') === 0) {
				$placementType = 'after';
				$placementBtn = substr($placement, strlen('after-'));
			}
		}

		$name = empty($name) ? 'btn' . count(Hash::get($this->settings, 'footer.buttons', [])) : $name;
		$newBtn = [
			'visible' => $visible,
			'text' => $text,
			'tag' => $tag,
			'options' => $options
		];

		$buttons = $this->settings['footer']['buttons'];
		$newButtons = [];
		$inserted = false;
		foreach ($buttons as $btnName => $btn) {
			if (!$inserted && $placementBtn === $btnName && $placementType == 'before') {
				$newButtons[$name] = $newBtn;
				$inserted = true;
			}

			// Insert one of original buttons to new array
			$newButtons[$btnName] = $btn;

			if (!$inserted && $placementBtn === $btnName && $placementType == 'after') {
				$newButtons[$name] = $newBtn;
				$inserted = true;
			}
		}

		if (!$inserted) {
			$newButtons[$name] = $newBtn;
		}

		$this->settings['footer']['buttons'] = $newButtons;		
	}

	public function changeConfig($path, $val)
	{
		$this->settings = Hash::insert($this->settings, $path, $val);
	}

	public function getConfig($path, $def = null)
	{
		return Hash::get($this->settings, $path, $def);
	}

	public function showFooterCloseButton($val = true)
	{
		$this->settings = Hash::insert($this->settings, 'footer.buttons.closeBtn.visible', $val == true ? true : false);
	}

	public function showFooterSaveButton($val = true)
	{
		$this->settings = Hash::insert($this->settings, 'footer.buttons.saveBtn.visible', $val == true ? true : false);
	}

	protected function _handleLayout()
	{
		$this->_controller->layout = $this->settings['layout'];
	}

	protected function _handleHeader()
	{
		$heading = Hash::get($this->settings, 'header.heading', '');
		if ($heading === '' && isset($this->_controller->viewVars['title_for_layout'])) {
			$heading = $this->_controller->viewVars['title_for_layout'];
		}

		//
		// Set modal's friendly name
		if ($this->getFriendlyName() === "") {
			$this->setFriendlyName($heading);
		}
		//
		
		$maxCrumbLen = 50;
		$headingClean = $heading;

		//
		// If any of breadcrumbs goes after heading, truncate heading as any other breadcrumb
		foreach ($this->breadcrumbs as $bit) {
			if ($bit['place'] === 'append') {
				$heading = CakeText::truncate($heading, $maxCrumbLen, [
					'elipsis' => '...'
				]);
				break;
			}
		}
		//

		$counter = 0;
		foreach ($this->breadcrumbs as $bit) {
			$modalId = $bit['bit'][0];
			$modalFriendlyName = $bit['bit'][1] != "" ? $bit['bit'][1] : "-";
			$modalShortFriendlyName = $modalFriendlyName;
			if ($counter != count($this->breadcrumbs) - 1 || $bit['place'] === 'prepend') {
				$modalShortFriendlyName = CakeText::truncate($modalFriendlyName, $maxCrumbLen, [
					'elipsis' => '...'
				]);
			}

			if ($modalId > 0) {
				$modalShortFriendlyName = '<a href="#" style="color: inherit !important" data-yjs-request="app/showModal" data-yjs-modal-id="' . $modalId . '" data-yjs-event-on="click">' . $modalShortFriendlyName . '</a>';
			}
			if ($bit['place'] === 'prepend') {
				$heading = $modalShortFriendlyName . ' / ' . $heading;
				$headingClean = $modalFriendlyName . ' / ' . $headingClean;
			} else if ($bit['place'] === 'append') {
				$heading .= ' / ' . $modalShortFriendlyName;
				$headingClean .= ' / ' . $modalFriendlyName;
			}

			$counter++;
		}
		$header = [];
		$header['headingClean'] = $headingClean;
		$header['heading'] = $heading;
		$header['class'] = Hash::get($this->settings, 'header.class');
		$header['buttons'] = Hash::get($this->settings, 'header.buttons');
		$this->modal->setHeader($header);
	}

	protected function _handleBody()
	{
		$this->modal->setBody($this->settings['body']);
	}

	protected function _handleFooter()
	{
		$buttons = [];
		foreach (Hash::get($this->settings, 'footer.buttons', []) as $name => $button) {
			if ($button['visible'] == true) {
				if (array_key_exists('data-yjs-modal-id', $button['options']) && 
					$button['options']['data-yjs-modal-id'] === null) {
					$button['options']['data-yjs-modal-id'] = $this->modalId;
				}
				if (array_key_exists('data-yjs-datasource-url', $button['options']) && 
					$button['options']['data-yjs-datasource-url'] === null) {
					$button['options']['data-yjs-datasource-url'] = $this->_controller->viewVars['formUrl'];
				}
				if (array_key_exists('data-yjs-forms', $button['options']) && 
					$button['options']['data-yjs-forms'] === null) {
					$button['options']['data-yjs-forms'] = $this->_controller->viewVars['formName'];
				}

				$buttons[$name] = [
					'text' => $button['text'],
					'tag' => !empty($button['tag']) ? $button['tag'] : 'button',
					'options' => $button['options']
				];
			}
		}

		$footer  = [];
		$footer['buttons'] = $buttons;
		$this->modal->setFooter($footer);
	}
}