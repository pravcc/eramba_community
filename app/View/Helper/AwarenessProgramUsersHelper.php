<?php
class AwarenessProgramUsersHelper extends AppHelper {
    public $helpers = array('Html', 'AdvancedFilters');
    public $settings = array();
    
    public function __construct(View $view, $settings = array()) {
        parent::__construct($view, $settings);

        $this->settings = $settings;
    }

    public function outputReminders($data, $options = array()) {
        $query = array();
        if (!empty($this->request->query['awareness_program_id'])) {
            $query['awareness_program_id'] = $this->request->query['awareness_program_id'];
        }

        $link = $this->AdvancedFilters->getItemFilteredLink(__('List Reminders'), 'AwarenessReminder', $data, array(
            'key' => 'uid',
            'query' => $query
        ), $options);
        return $link;
    }

    public function outputTrainings($data, $options = array()) {
        $query = array();
        if (!empty($this->request->query['awareness_program_id'])) {
            $query['awareness_program_id'] = $this->request->query['awareness_program_id'];
        }
        
        $link = $this->AdvancedFilters->getItemFilteredLink(__('List Trainings'), 'AwarenessTraining', $data, array(
            'key' => 'login',
            'query' => $query
        ), $options);
        return $link;
    }
}