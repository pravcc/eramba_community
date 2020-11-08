<?php
App::uses('Component', 'Controller');
class TicketmasterComponent extends Component {
	public $hours = 24; //dlzka platnosti ticketu

	public function startup(Controller $controller) {
		$this->controller = $controller;
	}

	public function initialize(Controller $controller) {
		$this->controller = $controller;
	}

	/**
	  * Vrati cas do kedy je ticket platny.
	  */
	public function getExpirationDate() {
		$date = strftime('%c');
		$date = strtotime($date);
		$date += ($this->hours * 60 * 60);

		$expired = date('Y-m-d H:i:s', $date);

		return $expired;
	}

	/**
	  * Funkcia vytvori hash.
	  */
	public function createHash() {
		return sha1(date('mdY').rand(3000000,3999999).'mX5yxO1w');
	}

	/**
	 * Method marks the ticket as used.
	 *
	 * @param string $hash unique hash of the ticket
	 */
	public function useTicket($hash) {
		return $this->controller->Ticket->updateAll(
			array('Ticket.is_used' => true, 'Ticket.modified' => "'".date('Y-m-d H:i:s')."'"),
			array('Ticket.hash' => $hash)
		);
	}

	/**
	  * Overi spravnost hashu. Ci dany ticket existuje.
	  */
	public function checkTicket($hash) {
		//odstranime vsetky stare tickety
		//$this->purgeTickets();

		//najdeme ticket podla hashu
		$ticket = $this->controller->Ticket->find('first', array(
			'conditions' => array(
				'hash' => $hash,
				'is_used' => 0,
				'expires >= NOW()'
			),
			'recursive' => -1
		));

		if(empty($ticket)) {
			//no more ticket
			return false;
		}
		else {
			return $ticket;
		}
	}
}