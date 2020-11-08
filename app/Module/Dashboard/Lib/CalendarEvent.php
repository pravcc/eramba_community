<?php
/**
 * Calendar Event Library Class.
 */

App::uses('ClassRegistry', 'Utility');
App::uses('CakeObject', 'Core');

class CalendarEvent extends CakeObject
{
	/**
	 * Title for the event.
	 * 
	 * @var string
	 */
	public $title = null;

	/**
	 * Start date for the event.
	 * 
	 * @var string
	 */
	public $start = null;

	/**
	 * End date for the event.
	 * 
	 * @var null|string
	 */
	public $end = null;

	/**
	 * URL for the event.
	 * 
	 * @var null|string
	 */
	public $model, $foreignKey;

	public function __construct()
	{
	}

	/**
	 * Build event array for the calendar entry.
	 * 
	 * @return array
	 */
	public function buildEvent()
	{
		return [
			'title' => $this->title,
			'start' => $this->start,
			'end' => $this->end,
			'model' => $this->model,
			'foreign_key' => $this->foreignKey,
		];
	}
}