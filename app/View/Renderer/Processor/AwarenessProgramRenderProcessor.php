<?php
App::uses('SectionRenderProcessor', 'View/Renderer/Processor');
App::uses('FieldDataEntity', 'FieldData.Model/FieldData');
App::uses('OutputBuilder', 'ObjectRenderer.View/Renderer');

class AwarenessProgramRenderProcessor extends SectionRenderProcessor
{
	public function render(OutputBuilder $output, $subject)
	{
		parent::render($output, $subject);
	}

	public function recurrence(OutputBuilder $output, $subject)
	{
		$this->_printDays($output, $subject, 'recurrence');
	}

	public function reminderApart(OutputBuilder $output, $subject)
	{
		$this->_printDays($output, $subject, 'reminder_apart');
	}

	protected function _printDays(OutputBuilder $output, $subject, $field)
	{
		$value = $subject->item->AwarenessProgram[$field];

		$itemKey = $output->getKey($subject->item, $subject->field);
		$output->label([$itemKey => sprintf(__n('%d day', '%d days', $value), $value)]);
	}

	public function activeUsers(OutputBuilder $output, $subject)
	{
		$this->_printUsers($output, $subject, 'active_users', 'AwarenessProgramActiveUser');	
	}

	public function ignoredUsers(OutputBuilder $output, $subject)
	{
		$this->_printUsers($output, $subject, 'ignored_users', 'AwarenessProgramIgnoredUser');	
	}

	public function compliantUsers(OutputBuilder $output, $subject)
	{
		$this->_printUsers($output, $subject, 'compliant_users', 'AwarenessProgramCompliantUser');	
	}

	public function notCompliantUsers(OutputBuilder $output, $subject)
	{
		$this->_printUsers($output, $subject, 'not_compliant_users', 'AwarenessProgramNotCompliantUser');	
	}

	public function activeUsersPercentage(OutputBuilder $output, $subject)
	{
		$this->_printUsers($output, $subject, 'active_users_percentage', 'AwarenessProgramActiveUser');	
	}

	public function ignoredUsersPercentage(OutputBuilder $output, $subject)
	{
		$this->_printUsers($output, $subject, 'ignored_users_percentage', 'AwarenessProgramIgnoredUser');	
	}

	public function compliantUsersPercentage(OutputBuilder $output, $subject)
	{
		$this->_printUsers($output, $subject, 'compliant_users_percentage', 'AwarenessProgramCompliantUser');	
	}

	public function notCompliantUsersPercentage(OutputBuilder $output, $subject)
	{
		$this->_printUsers($output, $subject, 'not_compliant_users_percentage', 'AwarenessProgramNotCompliantUser');	
	}

	protected function _printUsers(OutputBuilder $output, $subject, $field, $relatedModel)
	{
		$itemKey = $output->getKey($subject->item, $subject->field);
		if ($subject->item->stats_update_status) {
			$url = ClassRegistry::init($relatedModel)->getMappedRoute([
				'?' => [
					'advanced_filter' => 1,
					'awareness_program_id' => $subject->item->getPrimary()
				]
			]);

			$output->itemAction([
				$itemKey => [
					'label' => __('Show'),
					'url' => $url,
					'options' => [
						'escape' => false
					]
				]
			]);
		}
		else {
			$output->label([$itemKey => __('Available after daily CRON runs')]);
		}
	}

}