<?php
App::uses('SectionRenderProcessor', 'View/Renderer/Processor');
App::uses('OutputBuilder', 'ObjectRenderer.View/Renderer');
App::uses('FieldsIterator', 'ItemData.Lib');
App::uses('Project', 'Model');

class ProjectRenderProcessor extends SectionRenderProcessor
{
    public function objectStatusExpiredTasks(OutputBuilder $output, $subject)
    {
		$this->_statusFilter($output, $subject, __('Show'), $subject->view->AdvancedFilters->filterUrl('projectAchievements', [
			'project_id' => $subject->item->getPrimary(),
			'ObjectStatus_expired' => 1
		]));
    }

    public function ultimateCompletion(OutputBuilder $output, $subject)
    {
		$FieldsIterator = new FieldsIterator($subject->item, $subject->field);

        foreach ($FieldsIterator as $key => $value) {
        	$completion = 0;

        	if (!empty($value['item']->ProjectAchievement) && $value['item']->ProjectAchievement->count() > 0) {
        		foreach ($value['item']->ProjectAchievement as $ProjectAchievement) {
	        		$completion += $ProjectAchievement->completion;
	        	}

	        	$completion = round($completion / $value['item']->ProjectAchievement->count());
        	}
        	
			$output->label([
				$key => $completion . '%'
			]);
		}
    }

    public function projectStatusId(OutputBuilder $output, $subject)
    {
        $FieldsIterator = new FieldsIterator($subject->item, $subject->field);

        foreach ($FieldsIterator as $key => $value) {
            $output->label([
                $key => Project::statuses()[$value['raw']]
            ]);
        }
    }
}