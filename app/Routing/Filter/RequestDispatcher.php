<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         CakePHP(tm) v 2.2
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

App::uses('DispatcherFilter', 'Routing');
App::uses('CakeText', 'Utility');

/**
 * This filter will check whether the response was previously cached in the file system
 * and served it back to the client if appropriate.
 *
 * @package Cake.Routing.Filter
 */
class RequestDispatcher extends DispatcherFilter {
	protected static $_requestId = null;

/**
 * Default priority for all methods in this filter
 * This filter should run before the request gets parsed by router
 *
 * @var int
 */
	public $priority = 9;

	public function beforeDispatch(CakeEvent $event) {
		$UuidClass = class_exists('CakeText') ? 'CakeText' : 'String';
		self::$_requestId = $UuidClass::uuid();
	}

	/**
	 * Get current request ID.
	 * 
	 * @return string Request ID.
	 */
	public static function requestId()
	{
		return self::$_requestId;
	}
	
/**
 * Checks whether the response was cached and set the body accordingly.
 *
 * @param CakeEvent $event containing the request and response object
 * @return CakeResponse with cached content if found, null otherwise
 */
	public function afterDispatch(CakeEvent $event) {
		// $request = $event->data['request'];
		// $response = $event->data['response'];

		// $data = [
		// 	'id' => self::$_requestId,
  //           'url' => $request->here(),
  //           'content_type' => $response->type(),
  //           'method' => $request->method(),
  //           'status_code' => $response->statusCode(),
  //           'requested_at' => env('REQUEST_TIME'),
  //           'panels' => []
  //       ];
		
		// $Request = ClassRegistry::init('Request');
		// $Request->create($data);

		// return $Request->save();
	}

}