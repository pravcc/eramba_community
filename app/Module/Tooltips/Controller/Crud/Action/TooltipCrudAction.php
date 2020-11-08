<?php
App::uses('CrudAction', 'Crud.Controller/Crud');
App::uses('CrudActionTrait', 'Controller/Crud/Trait');
App::uses('TooltipsModule', 'Tooltips.Lib');

class TooltipCrudAction extends CrudAction {
	const ACTION_SCOPE = CrudAction::SCOPE_MODEL;

	use CrudActionTrait;

	public function __construct(CrudSubject $subject, array $defaults = array()) {
		$defaults = am([
        ], $defaults);

        parent::__construct($subject, $defaults);
	}

	protected function _get($modelAlias, $type = 'large', $dataset = '', $id = null) {
		$controller = $this->_controller();
		$request = $this->_request();

		$path = '';
		if ($type === 'large') {
			if (empty($dataset)) {
				$dataset = 'Initial';
			}
			$path = ucFirst($modelAlias) . DS . $dataset;
		} elseif ($type === 'small') {
			$path = ucFirst($modelAlias);
		}

		$tooltipsModule = new TooltipsModule();
		$tooltipsModule->setPath($path);
		$tooltipsModule->setModelAlias($modelAlias);
		$tooltipsModule->setType($type);
		$tooltipsModule->setDataset($dataset);
		$tooltipsModule->setFileId($id);
		$tooltip = $tooltipsModule->getTooltip();

		$controller->Modals->setLayout('clean');

		// Set model alias to view
		$controller->set('modelAlias', $modelAlias);

		if ($type === 'large') {
			$this->view($tooltipsModule->getTooltipTemplate());

			$controller->set('header', $tooltip->getHeader());
			$controller->set('paragraphs', $tooltip->getParagraphs());
			$controller->set('images', $tooltip->getImages());
			$controller->set('videos', $tooltip->getVideos());
			$controller->set('youtubeIds', $tooltip->getYoutubeIds());
			$controller->set('buttons', $tooltipsModule->getTooltipButtons());
		} elseif ($type === 'small') {
			$this->view('');

			$yjsData = [
				'tooltipHeader' => $tooltip->getHeader(),
				'tooltipParagraphs' => $tooltip->getParagraphs(),
				'tooltipButtons' => $tooltipsModule->getTooltipButtons()
			];
			foreach ($yjsData as $key => $val) {
				$controller->YoonityJSConnector->addData($key, $val);
			}
		}

		$this->_trigger('beforeRender');
	}
}
