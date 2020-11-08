<?php
App::uses('Ldap', 'Lib/Ldap');

class BaseConnector extends Ldap {
	const TYPE_GROUP = LDAP_CONNECTOR_TYPE_GROUP;
	const TYPE_AUTHENTICATOR = LDAP_CONNECTOR_TYPE_AUTHENTICATOR;

	protected $_config = array();

	public function __construct($config) {
		$this->_config = $config;
	}

	public function connect() {
		return $this->_doConnect($this->_config['host'], $this->_config['port'], $this->_config['ldap_bind_dn'], $this->_config['ldap_bind_pw']);
	}

	public function unbind() {
		if (!empty($this->_ldap)) {
			return ldap_unbind($this->_ldap);
		}

		return false;
	}

	public function config($field) {
		return $this->_config[$field];
	}

	public function connectorOk() {
		return $this->connected();
	}

	public function getAttribute($entry, $fieldAttribute) {
		return self::_getAttributeValue($entry, $this->config($fieldAttribute));
	}

	protected static function _getAttributeValue($entry, $attribute) {
		$attr = mb_strtolower($attribute);
		if (!in_array($attr, $entry)) {
			return false;
		}

		return $entry[$attr][0];
	}

	/**
	 * Helper method formats entries into array list of data based on attribute.
	 */
	public function formatEntries($entries, $attribute) {
		$list = array();
		if (!empty($entries)) {
			if (isset($entries['count'])) {
				array_shift($entries);
			}

			foreach ($entries as $entry) {
				$val = $this->getAttribute($entry, $attribute);
				if ($val) {
					$list[$val] = $val;
				}
			}
		}

		return $list;
	}

	/**
	 * Clean LDAP results returned from ldap_get_entries function.
	 */
	public function cleanUpEntry($entry) {
		$retEntry = array();
		for ( $i = 0; $i < $entry['count']; $i++ ) {
			if (is_array($entry[$i])) {
				$subtree = $entry[$i];

				//This condition should be superfluous so just take the recursive call
				//adapted to your situation in order to increase perf.
				if ( ! empty($subtree['dn']) and ! isset($retEntry[$subtree['dn']])) {
					$retEntry[$subtree['dn']] = $this->cleanUpEntry($subtree);
				}
				else {
					$retEntry[] = $this->cleanUpEntry($subtree);
				}
			}
			else {
				$attribute = $entry[$i];
				if ( $entry[$attribute]['count'] == 1 ) {
					$retEntry[$attribute] = $entry[$attribute][0];
				}
				else {
					for ( $j = 0; $j < $entry[$attribute]['count']; $j++ ) {
						$retEntry[$attribute][] = $entry[$attribute][$j];
					}
				}
			}
		}
		return $retEntry;
	}
}