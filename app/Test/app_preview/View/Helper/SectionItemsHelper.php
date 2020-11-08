<?php
App::uses('SectionBaseHelper', 'View/Helper');
App::uses('AppModule', 'Lib');
App::uses('SectionItem', 'Model');

class SectionItemsHelper extends SectionBaseHelper {
	public $settings = [];
	public $helpers = ['Html', 'LimitlessTheme.Labels'];

	public function actionList($item, $options = []) {
		$exportUrl = array(
			'action' => 'exportPdf',
			$item['SectionItem']['id']
		);

		$this->Ajax->addToActionList(__('Export PDF'), $exportUrl, 'file', false);
		
		$options = am([
			'notifications' => true,
			'history' => true,
			WorkflowsModule::alias() => true,
			AppModule::instance('Visualisation')->getAlias() => true
		], $options);

		return parent::actionList($item, $options);
	}

	public function renderTinyintStatus($value)
	{
		$out = SectionItem::statuses($value);
		switch ($value) {
			case 0:
				$class = 'danger';
				break;
			case 1:
				$class = 'primary';
				break;
			case 2:
				$class = 'success';
				break;
			
			default:
				$class = 'default';
				break;
		}

		$out = $this->Labels->{$class}($out);

		return $out;
	}

	public function getStatuses($item) {
		return 'statuses';
	}

	public function getDate($item) {
		return $this->Ux->date($item['SectionItem']['date']);
	}

	public function getBelongsTo($item) {
		return $this->Ux->text($item['BelongsTo']['full_name']);
	}

	public function getHasAndBelongsToMany($item) {
		return $this->Users->listNames($item, 'HasAndBelongsToMany');
	}

	public function getText($item) {
		return $this->Ux->text($item['SectionItem']['text']);
	}

	public function getTags($item) {
		return $this->Taggable->showList($item, [
			'notFoundCallback' => [$this->Taggable, 'notFoundBlank']
		]);
	}

}
