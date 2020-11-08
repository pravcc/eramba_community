<?php
class AwarenessProgramDemo extends AppModel {
	public $actsAs = array(
		'Containable'
	);

	public $belongsTo = array(
		'AwarenessProgram'
	);

	/*
     * static enum: Model::function()
     * @access static
     */
    public static function statuses($value = null) {
        $options = array(
            self::STATUS_NOT_COMPLETED => __('Not Completed'),
            self::STATUS_COMPLETED => __('Completed'),
        );
        return parent::enum($value, $options);
    }

    const STATUS_NOT_COMPLETED = 0;
    const STATUS_COMPLETED = 1;

    /**
     * Check if we have live not completed demo for given input data.
     */
    public function liveDemoExists($awarenessProgramId, $uid) {
    	$data = $this->find('count', [
			'conditions' => [
				'AwarenessProgramDemo.awareness_program_id' => $awarenessProgramId,
				'AwarenessProgramDemo.uid' => $uid,
				'AwarenessProgramDemo.completed' => self::STATUS_NOT_COMPLETED,
			]
		]);

		return (boolean) $data;
    }
}