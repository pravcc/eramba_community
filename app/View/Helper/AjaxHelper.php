<?php
App::uses('AppModule', 'Lib');

class AjaxHelper extends AppHelper {
	public $helpers = array('Html', 'Session', 'Flash', 'Paginator', 'Eramba', 'ObjectVersion.ObjectVersionHistory', 'Workflows.Workflows');
	public $settings = array();
	protected $_actions = array();

	private $flashMsgSuccess = null;

	/**
	 * @deprecated for FlashComponent
	 */
	private $replaceFlashMsg = array(
		FLASH_OK => 'Flash/success',
		FLASH_ERROR => 'Flash/error',
		FLASH_WARNING => 'Flash/warning',
		FLASH_INFO => 'Flash/default'
	);

	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);

		$this->settings = $settings;
	}

	/**
	 * We use native way of flashing a message but rendered with a special ajax element.
	 *
	 * @deprecated for FlashComponent
	 */
	public function flash() {
		return $this->Flash->render();

		/*if ($this->Session->check('Message.flash')) {
			$element = $this->Session->read('Message.flash.element');
			$this->setFlashType($element);

			return $this->Session->flash('flash', array('element' => $this->replaceFlashMsg[$element]));
		}

		return false;*/
	}

	private function setFlashType($element) {
		$this->flashMsgSuccess = $element == FLASH_OK ? true : false;
	}

	public function isFlashSuccess() {
		return $this->flashMsgSuccess;
	}

	public function addActionNoAjax() {
		$defaults = array(
			'url' => array('controller' => $this->settings['controller'], 'action' => 'add')
		);

		$params = array_merge($defaults, []);

		return $this->Html->link('<i class="icon-plus-sign"></i>' . __('Add New'), $params['url'], array(
			'class' => 'btn',
			'escape' => false
		));
	}

	public function addAction($params = array(), $options = array()) {
		$defaultOptions = [
			'class' => 'btn',
			'data-ajax-action' => 'add',
			'escape' => false,
			'label' => __('Add New')
		];
		$options = array_merge($defaultOptions, $options);

		$label = $options['label'];
		unset($options['label']);

		$defaults = array(
			'url' => array('controller' => $this->settings['controller'], 'action' => 'add')
		);

		$params = array_merge($defaults, $params);

		return $this->Html->link('<i class="icon-plus-sign"></i>' . $label, $params['url'], $options);
	}

	public function workflowEditAction($params = array()) {
		$defaults = array(
			'id' => null
		);

		$params = array_merge($defaults, $params);

		if (empty($params['id'])) {
			return false;
		}

		return $this->Html->link('<i class="icon-cog"></i>' . __('Workflow'), array(
			'controller' => 'workflows',
			'action' => 'edit',
			$params['id']
		), array(
			'class' => 'btn ajax-workflow',
			'data-ajax-action' => 'workflow',
			'escape' => false
		));
	}

	public function cancelBtn($model = null, $foreignKey = null, $options = array()) {
		$defaults = array(
			'title' => __('Close'),
			'class' => 'btn btn-inverse'
		);
		$options = array_merge($defaults, $options);

		if (empty($model)) {
			$model = $this->settings['model'];
		}

		$cancelBtn = $this->Html->link($options['title'], array(
			'action' => 'cancelAction',
			$model
		), array(
			'data-dismiss' => 'modal',
			'class' => $options['class']
		));

		return $cancelBtn;
	}

	/**
	 * Extended getActionList method to generates UL element containing ONLY custom user-added actions (addToActionList) into the list.
	 *
	 * @param array $options Customized options.
	 */
	public function getUserDefinedActionList($options = array()) {
		$defaults = array(
			'style' => 'icons',
			'listClass' => false,
			'edit' => false,
			'trash' => false,
			'comments' => false,
			'records' => false,
			'attachments' => false
		);

		$options = array_merge($defaults, $options);

		return $this->getActionList(PHP_INT_MAX, $options);
	}

	/**
	 * Generates UL element containing available actions for an item.
	 *
	 * @param int $id Foreign key.
	 */
	public function getActionList($id, $options = array()) {
		if (empty($id)) {
			AppError("Action list is missing ID value");
			return false;
		}

		$Visualisation = AppModule::instance('Visualisation');

		$defaults = array(
			'style' => 'normal', // or icons
			'listClass' => false,

			'edit' => true,
				'disableEditAjax' => false, // tempororay until everything is not moved to ajax ui
			'trash' => true,
			'comments' => true,
			'records' => true,
			'attachments' => true,
			'notifications' => false,
			'history' => false,

			// naming convention is the name of a module (camelcase)
			WorkflowsModule::alias() => false,
			$Visualisation->getAlias() => false,

			'editTitle' => __('Edit'),
			'trashTitle' => __('Delete'),
			'commentsTitle' => __('Comments'),
			'recordsTitle' => __('Records'),
			'attachmentsTitle' => __('Attachments'),
			'notificationsTitle' => __('Notifications'),
			'historyTitle' => __('History'),
			WorkflowsModule::alias() . 'Title' => WorkflowsModule::name(),
			$Visualisation->getAlias() . 'Title' => __('Share'),

			'commentsCount' => null,
			'attachmentsCount' => null,

			'controller' => isset($this->settings['controller']) ? $this->settings['controller'] : null,
			'model' => isset($this->settings['model']) ? $this->settings['model'] : null,
			'item' => null
		);

		$options = array_merge($defaults, $options);

		if (empty($id) || empty($options['controller']) || empty($options['model'])) {
			AppError("ID, controller or model is not defined!");
			return false;
		}
		
		$style = $options['style'];

		// custom user-added list items before getting the list from a view file.
		$customListItems = $this->prepareActionList($style);

		if (!empty($options['edit'])) {
			$url = array(
				'controller' => $options['controller'],
				'action' => 'edit',
				$id
			);

			// temporary solution
			$editAjaxAction = 'edit';
			if (!empty($options['disableEditAjax'])) {
				$editAjaxAction = false;
			}

			$this->addToActionList($options['editTitle'], $url, 'pencil', $editAjaxAction);
		}

		$trashUrl = array(
			'controller' => $options['controller'],
			'action' => 'delete',
			$id
		);

		if (!empty($options['trash']) && $style != 'icons') {
			$this->addToActionList($options['trashTitle'], $trashUrl, 'trash', 'delete');

			$addDividerCond = empty($options['comments']);
			$addDividerCond &= empty($options['records']);
			$addDividerCond &= empty($options['attachments']);
			$addDividerCond &= empty($options['notifications']);

			// lets add divider only in case at least one of the modules above is enabled
			if (empty($addDividerCond)) {
				$this->_actions[] = 'divider';
			}
		}

		if (!empty($options['comments'])) {
			$url = array(
				'controller' => 'ajax',
				'action' => 'modalSidebarWidget',
				$options['model'],
				$id,
				'plugin' => null
			);

			$attrs = null;
			$cond = (!empty($options['item']) && (!empty($options['item']['Comment']) || !empty($options['item'][$options['model']]['Comment'])));
			if (!empty($options['commentsCount']) || $cond || $this->getWidgetData('Comment', $options['model'], $id)) {
				$attrs['class'] = 'has-items';
			}

			$this->addToActionList($options['commentsTitle'], $url, 'comments', 'sidebar-widget', $attrs);
			unset($attrs);
		}

		if (!empty($options['records'])) {
			$url = array(
				'controller' => 'ajax',
				'action' => 'modalSidebarWidget',
				$options['model'],
				$id,
				'records',
				'plugin' => null
			);

			$this->addToActionList($options['recordsTitle'], $url, 'cog', 'sidebar-widget');
		}

		if (!empty($options['attachments'])) {
			$this->addAttachmentsToActionList($id, $options);
		}

		if (!empty($options['notifications'])) {
			$url = array(
				'controller' => 'ajax',
				'action' => 'modalSidebarWidget',
				$options['model'],
				$id,
				'notifications',
				'plugin' => null
			);

			$attrs = null;
			$cond = (!empty($options['item']) && (!empty($options['item']['NotificationObject']) || !empty($options['item'][$options['model']]['NotificationObject'])));
			if ($cond || $this->getWidgetData('NotificationObject', $options['model'], $id)) {
				$attrs['class'] = 'has-items';
			}

			$this->addToActionList($options['notificationsTitle'], $url, 'info-sign', 'sidebar-widget', $attrs);
		}

		// history
		if (!empty($options['history'])) {
			$url = $this->ObjectVersionHistory->getUrl($options['model'], $id);

			$this->addToActionList($options['historyTitle'], $url, 'retweet', 'history');
		}

		// workflows
		if (!empty($options[WorkflowsModule::alias()])) {
			$url = $this->Workflows->getItemUrl($options['model'], $id);
			$this->addToActionList($options[WorkflowsModule::alias() . 'Title'], $url, 'retweet', false);
		}

		$visItems = $this->_View->get('visualisationSectionShared');
		if (!empty($options[$Visualisation->getAlias()])) {
			$visAttrs = null;
			if (isset($visItems[$options['model']])) {
				$visualModelItems = $visItems[$options['model']];
				if (in_array($id, $visualModelItems)) {
					$visAttrs = ['class' => 'has-items'];
				}
			}
			
			$url = $Visualisation->getItemUrl($options['model'], $id);
			$this->addToActionList($options[$Visualisation->getAlias() . 'Title'], $url, 'group', 'edit', $visAttrs);
		}

		if (!empty($options['trash']) && $style == 'icons') {
			$this->addToActionList($options['trashTitle'], $trashUrl, 'trash', 'delete');
		}

		if (!empty($customListItems) && $id != PHP_INT_MAX) {
			$this->_actions[] = 'divider';
		}

		$primaryActions = $this->prepareActionList($style);

		$list = array_merge($primaryActions, $customListItems);

		$listClass = ($style == 'icons') ? 'table-controls nested-actions' : 'dropdown-menu manage-dropdown-menu pull-right';
		if (!empty($options['listClass'])) {
			$listClass = $options['listClass'];
		}

		return $this->Html->nestedList($list, array(
			'class' => $listClass,
			'data-action-list-menu' => true,
			'data-action-list-model' => $options['model'],
			'data-action-list-id' => $id,
		));
	}

	/**
	 * Widget check function to make icons glow in action buttons.
	 */
	public function getWidgetData($widget, $model, $id) {
		$ret = true;
		$ret &= $data = $this->_View->get('WidgetData');

		$ret = $ret && $data['_model'] == $model;
		$ret = $ret && isset($data[$widget][$id]);

		return $ret;
	}

	/**
	 * Generic method that adds Attachments ajax link into the action list with possibility of customized parameters.
	 */
	public function addAttachmentsToActionList($id, $options = array(), $attrs = array()) {
		$url = array(
			'controller' => 'ajax',
			'action' => 'modalSidebarWidget',
			$options['model'],
			$id,
			'attachments',
			'plugin' => null
		);

		// $attrs = null;
		$cond = (!empty($options['item']) && (!empty($options['item']['Attachment']) || !empty($options['item'][$options['model']]['Attachment'])));
		if (!empty($options['attachmentsCount']) || $cond || $this->getWidgetData('Attachment', $options['model'], $id)) {
			$attrs['class'] = 'has-items';
		}

		$this->addToActionList($options['attachmentsTitle'], $url, 'cloud-upload', 'sidebar-widget', $attrs);
	}

	/**
	 * Generates action links html from current actions array data and cleans the array.
	 */
	private function prepareActionList($style) {
		$actions = $this->_actions;

		foreach ($actions as $key => $action) {
			if (is_array($action)) {
				// if (!isset($action['options']['order'])) {
					$actions[$key] = $this->getActionListItem($action, $style);
				// }
			}
			elseif ($action == 'divider' && $style != 'icons') {
				$actions[$key] = '<div class="divider"></div>';
			}
			else {
				unset($actions[$key]);
			}
		}

		$this->_actions = array();

		return $actions;
	}

	/**
	 * Link for ajax action link.
	 */
	private function getActionListItem($action, $style) {
		$attrs = array(
			// 'data-ajax-action' => $action['actionType'],
			'escape' => false
		);

		if ($action['actionType'] !== false) {
			$attrs['data-ajax-action'] = $action['actionType'];
		}

		if (!empty($action['options']) && is_array($action['options'])) {
			$attrs = array_merge($attrs, $action['options']);
		}

		$icon = '<i class="icon-' . $action['icon'] . '"></i>';
		if ($style == 'icons') {
			$attrs['class'] = isset($attrs['class']) ? ($attrs['class'] . ' bs-tooltip') : 'bs-tooltip';
			$attrs['title'] = $action['title'];
			// $attrs['data-container'] = '#eramba-modal';
			$linkTitle = $icon;
		}
		else {
			$linkTitle = $icon . ' ' . $action['title'];
		}

		if (!empty($action['options']['tooltip'])) {
			$tooltipOpts = am(array(
				'placement' => 'left',
				'container' => 'body'
			), $action['options']['tooltip']);

			$linkTitle .= $this->Eramba->getTruncatedTooltip(false, $tooltipOpts);
		}

		return $this->Html->link($linkTitle, $action['url'], $attrs);
	}

	/**
	 * Adds a link to the ajax actions array.
	 */
	public function addToActionList($title, $url = null, $icon, $actionType = 'index', $options = null) {
		$this->_actions[] = array(
			'title' => $title,
			'url' => $url,
			'icon' => $icon,
			'actionType' => $actionType,
			'options' => $options
		);

		return $this;
	}

	public function setPagination($updateClass = '#eramba-modal .modal-body', $url = null){
		$options['update'] = $updateClass;
		if($url){
			$options['url'] = $url;
		}

		$options['before'] = 'Eramba.Ajax.UI.paginationBefore();';
		$options['complete'] = 'Eramba.Ajax.UI.paginationComplete();';

		//posible options
		// 'before' => "$('#order-list-table').fadeTo('fast',0.2);$('.loader').fadeIn();",
		//'complete' => "$('#order-list-table').fadeTo('fast',1);$('.loader').fadeOut();$('html, body').animate({scrollTop: $('#order-list-table').offset().top}, 400);",
		return  $this->Paginator->options($options);
	}

	public function quickAddAction($params = array()) {
		$defaults = array(
			//'url' => array('controller' => $this->settings['controller'], 'action' => 'add')
			'text' => 'Quick Add'
		);

		$params = array_merge($defaults, $params);

		return $this->Html->link('<i class="icon-plus-sign"></i>', $params['url'], array(
			'class' => 'btn btn-success bs-popover',
			'data-ajax-action' => 'quick-create',
			'data-trigger' => 'hover',
			'data-placement' => 'top',
			'data-content' => $params['text'],
			'data-container' => '#eramba-modal',
			'escape' => false
		));
	}

	public function popupLink($text, $url) {
		return $this->Html->link($text, $url, array(
			'data-ajax-action' => 'popup'
		));
	}
}