<?php
App::uses('AppHelper', 'View/Helper');

class ReviewsHelper extends AppHelper {
    public $helpers = ['Html', 'Ajax', 'Eramba', 'FieldData.FieldData', 'Limitless.Alerts', 'FormReload'];
    public $settings = array();
    
    public function __construct(View $view, $settings = array()) {
        parent::__construct($view, $settings);

        $this->settings = $settings;
    }

    public function foreignKeyField(FieldDataEntity $Field)
    {
        return $this->FieldData->input($Field, $this->FormReload->triggerOptions());
    }

    public function nextReviewDateField(FieldDataEntity $Field)
    {
        $edit = $this->_View->get('edit');
        $reviewModel = $this->_View->get('reviewModel');
        $sectionModel = ClassRegistry::init($reviewModel)->parentModel();
        $prevReview = $this->_View->get('prevReview');
        $mainItem = $this->_View->get('mainItem');
        $futureReview = $this->_View->get('futureReview');

        $options = [
            'inputName' => $sectionModel . '.' . $Field->getFieldName(),
            'disabled' => $this->isFieldDisabled()
        ];

        if (!empty($futureReview)) {
            $options['default'] = $futureReview[$reviewModel]['planned_date'];
        }
        else {
            $options['value'] = false;
        }

        $out = $this->FieldData->input($Field, $options);

        if (!empty($futureReview)) {
            $out .= $this->Alerts->info(__('We found another review with a future planned date, we suggest you use that same date in order to avoid creating another review.'));
        }

        return $out;
    }

    public function completedField(FieldDataEntity $Field)
    {
        $reviewCompleted = $this->_View->get('reviewCompleted');

        $options = [
            'toggleLabel' => false
        ];

        if ($reviewCompleted) {
            $options['readonly'] = true;
        }

        return $this->FieldData->input($Field, $options);
    }

    public function actualDateField(FieldDataEntity $Field)
    {
        $reviewCompleted = $this->_View->get('reviewCompleted');

        $options = [];

        if ($reviewCompleted) {
            $options['disabled'] = true;
        }

        return $this->FieldData->input($Field, $options);
    }

    public function descriptionField(FieldDataEntity $Field)
    {
        $reviewCompleted = $this->_View->get('reviewCompleted');

        $options = [];

        if ($reviewCompleted) {
            $options['disabled'] = true;
        }

        return $this->FieldData->input($Field, $options);
    }

    /**
     * By default fields are disabled with a single variable set in the View.
     * 
     * @return boolean
     */
    public function isFieldDisabled() {
    	$reviewCompleted = $this->_View->getVar('reviewCompleted');

    	return $reviewCompleted;
    }

    public function disableFieldParams($name = null) {
    	if ($this->isFieldDisabled() || ($this->_View->getVar('edit') && $name === 'planned_date')) {
    		return ['readonly' => true];
    	}

    	return [];
    }

    public function getLastReviewDate($item) {
        $emptyDate = '0000-00-00';
        $lastReviewDate = $emptyDate;

        foreach ($item['Review'] as $review) {
            $date = $review['planned_date'];
            $completed = $review['completed'] == Review::STATUS_COMPLETE;
            if (!empty($date) && $date > $lastReviewDate && !$completed && empty($review['deleted'])) {
                $lastReviewDate = $date;
            }
        }

        return ($lastReviewDate != $emptyDate) ? $lastReviewDate : $this->Eramba->getEmptyValue('');
    }

    //next review if exists, if not last review
    public function getReviewDate($item) {
        //next review
        $emptyDate = '9999-12-31';
        $nextReviewDate = $emptyDate;
        $label = __('Next Review');

        foreach ($item['Review'] as $review) {
            $date = $review['planned_date'];
            $completed = $review['completed'] == Review::STATUS_COMPLETE;
            if (!empty($date) && $date >= date('Y-m-d') && $date < $nextReviewDate && !$completed && empty($review['deleted'])) {
                $nextReviewDate = $date;
            }
        }

        if ($nextReviewDate != $emptyDate) {
            return [
                'label' => $label,
                'date' => $nextReviewDate
            ];
        }

        $emptyDate = '0000-00-00';
        $lastReviewDate = $emptyDate;
        $label = __('Last Review');

        foreach ($item['Review'] as $review) {
            $date = $review['actual_date'];
            $completed = $review['completed'] == Review::STATUS_COMPLETE;
            if (!empty($date) && $date <= date('Y-m-d') && $date > $lastReviewDate && $completed && empty($review['deleted'])) {
                $lastReviewDate = $date;
            }
        }

        $lastReviewDate = ($lastReviewDate != $emptyDate) ? $lastReviewDate : $this->Eramba->getEmptyValue('');

        return [
            'label' => $label,
            'date' => $lastReviewDate
        ];
    }

    public function getLastCompletedReview($item) {
        $orderedReviews = Hash::sort($item['Review'], '{n}.version', 'ASC');

        $emptyDate = '0000-00-00';
        $lastReviewDate = $emptyDate;
        $lastReview = null;

        foreach ($orderedReviews as $review) {
            $date = $review['actual_date'];
            $completed = $review['completed'] == Review::STATUS_COMPLETE;
            if (!empty($date) && $date <= date('Y-m-d') && $date >= $lastReviewDate && $completed && empty($review['deleted'])) {
                $lastReviewDate = $date;
                $lastReview = $review;
            }
        }

        return $lastReview;
    }
}