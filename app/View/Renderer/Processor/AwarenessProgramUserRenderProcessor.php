<?php
App::uses('SectionRenderProcessor', 'View/Renderer/Processor');
App::uses('FieldDataEntity', 'FieldData.Model/FieldData');
App::uses('OutputBuilder', 'ObjectRenderer.View/Renderer');

class AwarenessProgramUserRenderProcessor extends SectionRenderProcessor
{
	public function render(OutputBuilder $output, $subject)
	{
		parent::render($output, $subject);
	}

	public function reminder(OutputBuilder $output, $subject)
	{
		$model = $subject->field->getModelName();
		$itemKey = $output->getKey($subject->item, $subject->field);

		$url = ClassRegistry::init('AwarenessReminder')->getMappedRoute([
			// 'ActiveUser',
			'?' => [
				'advanced_filter' => 1,
				'awareness_program_id' => $subject->item->AwarenessProgram->id,
				'uid' => $subject->item->{$model}['uid']
			]
		]);

		$link = $subject->view->Html->link(OutputBuilder::CONTENT, $url);

		$output->label([
			$itemKey => __('List Reminders')
		]);
		$output->itemTemplate([
			$itemKey => $link
		]);
	}

	public function training(OutputBuilder $output, $subject)
	{
		$model = $subject->field->getModelName();
		$itemKey = $output->getKey($subject->item, $subject->field);

		$url = ClassRegistry::init('AwarenessTraining')->getMappedRoute([
			// 'ActiveUser',
			'?' => [
				'advanced_filter' => 1,
				'awareness_program_id' => $subject->item->AwarenessProgram->id,
				'login' => $subject->item->{$model}['uid']
			]
		]);

		$link = $subject->view->Html->link(OutputBuilder::CONTENT, $url);

		$output->label([
			$itemKey => __('List Trainings')
		]);
		$output->itemTemplate([
			$itemKey => $link
		]);
	}

}