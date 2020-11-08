<?php
App::uses('AppHelper', 'View/Helper');
App::uses('ClassRegistry', 'Utility');
App::uses('FormReloadListener', 'Controller/Crud/Listener');

class AwarenessProgramsHelper extends AppHelper {
	public $helpers = array('Html', 'Form', 'AdvancedFilters', 'FieldData.FieldData', 'LimitlessTheme.Buttons', 'Macros.Macro', 'Attachments.Attachments', 'FormReload');
	public $settings = array();
	
	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);

		$this->settings = $settings;
	}

	public function textFileField(FieldDataEntity $Field)
	{
		$AwarenessProgram = ClassRegistry::init('AwarenessProgram');

		$out = $this->FieldData->input($Field);
		$out .= $this->getExampleLink('txt', __('Download Example (txt)')) . ' ';
		$out .= $this->getExampleLink('html', __('Download Example (html)'));
		$out .= '<br><br>';
		$out .= $this->_uploadedFile($Field);

		return $out;
	}

	public function questionnaireField(FieldDataEntity $Field)
	{
		$out = $this->FieldData->input($Field);
		$out .= $this->getExampleLink('csv', __('Download Example (csv)'));
		$out .= '<br><br>';
		$out .= $this->_uploadedFile($Field);

		return $out;
	}

	public function videoField(FieldDataEntity $Field)
	{
		$out = $this->FieldData->input($Field);
		$out .= $this->_uploadedFile($Field);

		return $out;
	}

	public function emailBodyField(FieldDataEntity $Field)
	{
		$MacroCollection = $this->_View->get('MacroCollection');

		$options = $this->Macro->editorOptions($MacroCollection);

		return $this->FieldData->input($Field, $options['options']) . $options['script'];
	}

	public function emailReminderBodyField(FieldDataEntity $Field)
	{
		$MacroCollection = $this->_View->get('MacroCollection');

		$options = $this->Macro->editorOptions($MacroCollection);

		return $this->FieldData->input($Field, $options['options']) . $options['script'];
	}

	protected function _uploadedFile(FieldDataEntity $Field)
	{
		$mapDeleteActions = [
			'text_file' => 'deleteTextFile',
			'questionnaire' => 'deleteQuestionnaire',
			'video' => 'deleteVideo',
		];

		$type = $Field->getFieldName();

		$edit = $this->_View->get('edit');
		$data = $this->_View->get('data');

		$out = null;
		if (isset($edit) && !empty($data['AwarenessProgram'][$type])) {
			$url = Router::url(array('controller' => 'awareness', 'action' => 'downloadStepFile', $data['AwarenessProgram']['id'], $type));
			$link = $this->Html->link($data['AwarenessProgram'][$type], $url, array(
				'target' => '_blank'
			));

			$out .= '<p>' . __('Uploaded text file: <strong>%s</strong>', $link) . '</p>';
			$out .= $this->Html->link(__('Delete file'), array(
				'controller' => 'awarenessPrograms',
				'action' => $mapDeleteActions[$type],
				$data['AwarenessProgram']['id']
			), array(
				'class' => 'btn btn-danger btn-sm delete-file-confirm',
				'data-yjs-request' => 'app/showForm',
				'data-yjs-event-on' => 'click',
				'data-yjs-target' => 'modal',
				'data-yjs-datasource-url' => Router::url([
					'controller' => 'awarenessPrograms',
					'action' => $mapDeleteActions[$type],
					$data['AwarenessProgram']['id']
				]),
			));

			$out .= '<br><br>';
		}

		return $out;
	}

	public function ldapConnectorField(FieldDataEntity $Field)
	{
		$out = '';
		$options = array_merge([
			'readonly' => !empty($this->_View->get('edit')) ? true : false
		], $this->FormReload->triggerOptions([
			'field' => $Field,
			'url' => Router::url(['controller' => 'awarenessPrograms', 'action' => 'add', '?' => [FormReloadListener::REQUEST_PARAM => true]])
		]));

		$out .= $this->FieldData->input($Field, $options);

		return $out;
	}

	public function ldapGroupField(FieldDataEntity $Field)
	{
		$out = '';

		$options = array_merge([
			'readonly' => !empty($this->_View->get('edit')) ? true : false
		], $this->FormReload->triggerOptions([
			'field' => $Field,
			'url' => Router::url(['controller' => 'awarenessPrograms', 'action' => 'add', '?' => [FormReloadListener::REQUEST_PARAM => true]])
		]));

		$out .= $this->FieldData->input($Field, $options);

		return $out;
	}

	public function emailReminderCustomField(FieldDataEntity $Field)
	{
		$AwarenessProgram = ClassRegistry::init('AwarenessProgram');

		$options = [
			'id' => 'reminder-customize-toggle'
		];

		$out = $this->FieldData->input($Field, $options);

		$emailOptions = [
			'div' => [
				// @todo form-group class is not dry!
				'class' => 'form-group reminder-customize-group'
			]
		];

		$out .= $this->FieldData->input($AwarenessProgram->getFieldDataEntity('email_reminder_subject'), $emailOptions);
		$out .= $this->FieldData->input($AwarenessProgram->getFieldDataEntity('email_reminder_body'), $emailOptions);

		return $out;
	}

	public function getStatuses($item) {
		$statuses = array();

		if ($item['AwarenessProgram']['status'] == AWARENESS_PROGRAM_STARTED) {
			$statuses[] = $this->getLabel(__('Started'), 'success');
		}

		if ($item['AwarenessProgram']['status'] == AWARENESS_PROGRAM_STOPPED) {
			$statuses[] = $this->getLabel(__('Paused'), 'warning');
		}

		return $this->processStatuses($statuses);
	}

	public function getExampleLink($type, $title = null) {
		if (empty($title)) {
			$title = __('Download Example');
		}

		return $this->Buttons->default($title, [
			'href' => [
				'controller' => 'awarenessPrograms',
				'action' => 'downloadExample',
				$type
			]
		]);
	}

	/**
	 * Get part of a pulled statistics of a program.
	 * @updated e1.0.6.016 Statistics are now hold as values in database, not pulled and calculated on the fly.
	 */
	public function getStatisticPart($program, $part = null) {
		if (!in_array($part, array('active', 'ignored', 'compliant', 'not_compliant'))) {
			return false;
		}

		$key = $part . '_users';

		$usersCount = $program[$key];

		$usersPercentage = $program[$key . '_percentage'];
		$usersPercentage = CakeNumber::toPercentage($usersPercentage, 0, array('multiply' => false));

		return sprintf(__n('%d user', '%d users', $usersCount), $usersCount) . ' ' . sprintf('(%s)', $usersPercentage);
	}

	public function outputActiveUsersLink($data, $options = array()) {
        $link = $this->AdvancedFilters->getItemFilteredLink(__('List Users'), 'AwarenessProgramUser', $data, array(
            'key' => 'awareness_program_id',
            'param' => 'ActiveUser'
        ), $options);
        return $link;
    }

    public function outputIgnoredUsersLink($data, $options = array()) {
        $link = $this->AdvancedFilters->getItemFilteredLink(__('List Users'), 'AwarenessProgramUser', $data, array(
            'key' => 'awareness_program_id',
            'param' => 'IgnoredUser'
        ), $options);
        return $link;
    }

    public function outputCompliantUsersLink($data, $options = array()) {
        $link = $this->AdvancedFilters->getItemFilteredLink(__('List Users'), 'AwarenessProgramUser', $data, array(
            'key' => 'awareness_program_id',
            'param' => 'CompliantUser'
        ), $options);
        return $link;
    }

    public function outputNotCompliantUsersLink($data, $options = array()) {
        $link = $this->AdvancedFilters->getItemFilteredLink(__('List Users'), 'AwarenessProgramUser', $data, array(
            'key' => 'awareness_program_id',
            'param' => 'NotCompliantUser'
        ), $options);
        return $link;
    }
}