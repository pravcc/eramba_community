<?php
App::uses('BaseConnector', 'Lib/Ldap');

class AuthConnector extends BaseConnector {
	protected $_sizeLimit = 0;

	public function getTest($options = array()) {
		$results = array();

		$user = $this->getUser($this->_config['_ldap_auth_filter_username_value']);
		$results[] = $this->getLastSearch();
		$results[] = $this->cleanUpEntry($user);

		return $results;
	}

	public function getUser($user) {
		$attributes = array($this->_config['ldap_auth_attribute']);

		if (!empty($this->_config['ldap_name_attribute'])) {
			$attributes[] = $this->_config['ldap_name_attribute'];
		}

		if (!empty($this->_config['ldap_email_attribute'])) {
			$attributes[] = $this->_config['ldap_email_attribute'];
		}

		if (!empty($this->_config['ldap_memberof_attribute'])) {
			$attributes[] = $this->_config['ldap_memberof_attribute'];
		}

		$filter = $this->getUserFilter($user);

		return $this->search(
			$this->_config['ldap_base_dn'],
			$filter,
			$attributes
		);
	}

	public function getUserFilter($user) {
		$user = $this->escape($user);
		return str_replace("%USERNAME%", $user, $this->_config['ldap_auth_filter']);
	}
	
	/**
	 * Get a list of LDAP users based on keyword search.
	 */
	public function searchUsers($keyword = null) {
		if (empty($keyword)) {
			return false;
		}

		$attrVal = '*' . $keyword . '*';
		$authAttr = $this->_config['ldap_auth_attribute'];

		$filter = '(| (' . $authAttr . '=' . $attrVal . ') )';

		$results = $this->search(
			$this->_config['ldap_base_dn'],
			$filter,
			array($authAttr)
		);

		return $this->formatEntries($results, 'ldap_auth_attribute');
	}

}