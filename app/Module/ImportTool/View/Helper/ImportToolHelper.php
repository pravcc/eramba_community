<?php
App::uses('AppHelper', 'View/Helper');
App::uses('Inflector', 'Utility');
App::uses('ImportToolObject', 'ImportTool.Lib');

class ImportToolHelper extends AppHelper {
	public $helpers = array('Html', 'Eramba', 'LimitlessTheme.Icons', 'LimitlessTheme.Popovers');
	public $settings = array();

	public function getUrl($model) {
		return array(
			'plugin' => 'importTool',
			'controller' => 'importTool',
			'action' => 'index',
			$model
		);
	}

	public function getDownloadUrl($model, $getData = false) {
		return array(
			'plugin' => 'importTool',
			'controller' => 'importTool',
			'action' => 'downloadTemplate',
			$model,
			$getData
		);
	}

	public function getPreviewUrl() {
		return array(
			'plugin' => 'importTool',
			'controller' => 'importTool',
			'action' => 'preview'
		);
	}

	public function getIndexLink($model) {
		if (is_array($model)) {
			$list = array();
			foreach ($model as $alias => $name) {
				$list[] = $this->Html->link($name, $this->getUrl($alias), array(
					'escape' => false
				));
			}

			$ul = $this->Html->nestedList($list, array(
				'class' => 'dropdown-menu pull-right',
				'style' => 'text-align: left;'
			));

			$btn = '<button class="btn dropdown-toggle" data-toggle="dropdown"><i class="icon-file"></i>' . __('Import') . ' <span class="caret"></span></button>';

			return $this->Html->div("btn-group group-merge", $btn . $ul);
		}
		else {
			return $this->Html->div("btn-group group-merge",
				$this->Html->link( '<i class="icon-file"></i>' . __('Import'), $this->getUrl($model), array(
					'class' => 'btn',
					'escape' => false
				))
			);
		}		
	}

	/**
	 * Show a detailed information about validation errors for a certain item.
	 */
	public function getValidationErrorsContent($validationErrors = array(), $model) {
		$content = '';

		$Model = ClassRegistry::init($model);

		foreach ($validationErrors as $fieldName => $errors) {
			// fix for custom fields validation
			if ($fieldName === '') {
				continue;
			}
			
			if ($Model->hasFieldDataEntity($fieldName)) {
				$fieldLabel = $Model->getFieldDataEntity($fieldName)->getLabel();
			}
			else {
				$fieldLabel = Inflector::humanize($fieldName);
			}

			$content .= $fieldLabel;
			$content .= $this->Html->nestedList($errors);
		}

		return $content;
	}

	public function previewObjectValue($value)
	{
		$data = $value->getObject();
		$out = '';

		$label = h($value->getFindValue());

		if ($value->getStatus() == ImportToolObject::STATUS_MATCH) {
			$icon = $this->Icons->render('checkmark-circle', ['class' => 'text-success']);
			$out = $this->Html->tag('span', $label . '&nbsp;' . $icon, [
				'class' => ['import-object']
			]);
		}
		elseif ($value->getStatus() == ImportToolObject::STATUS_PARTIAL_MATCH) {
			$icon = $this->Icons->render('info22', ['class' => 'text-warning']);
			$out = $this->Html->tag('span', $label . '&nbsp;' . $icon, [
				'class' => ['import-object']
			]);
			$out = $this->Popovers->top($out, __('We found a similar object "%s" and we will use this for the import.', $data->{$data->getModel()->displayField}), __('Warning'), [
				'class' => 'import-object-popover'
			]);
		}
		else {
			$icon = $this->Icons->render('notification2', ['class' => 'text-danger']);
			$out = $this->Html->tag('span', $label . '&nbsp;' . $icon, [
				'class' => ['import-object']
			]);
			$out = $this->Popovers->top($out, __('We could not find this object and we won\'t import your input.'), __('Warning'), [
				'class' => 'import-object-popover'
			]);
		}

		return $out;
	}
}