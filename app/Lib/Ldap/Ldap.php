<?php
App::uses('Debugger', 'Utility');

class Ldap {
	public $_ldap = null;

	protected $_lastSearch = null;
	protected $_connected = false;
	public $ldapError = false;

	private $_url;
	private $_port;
	private $_bindDn;

	/**
	 * Connect to ldap server.
	 */
	protected function _doConnect($url, $port, $bindDn, $bindPw) {
		try {
			$ldap = ldap_connect($url, $port) or die("Could not connect to $url");

			ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3) or die("Could not set ldap protocol");
			ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0) or die("Could not set ldap protocol");
			ldap_set_option($ldap, LDAP_OPT_NETWORK_TIMEOUT, 10) or die("Could not set ldap protocol");

			if (!$ldap) {
				throw new CakeException("Could not connect to LDAP server");
			}

			$bind = @ldap_bind($ldap, $bindDn, $bindPw);

			if (!$bind) {
				throw new CakeException("Could not bind to LDAP server - check your bind DN and password.");
			}

			$this->_ldap = $ldap;
			$this->_connected = true;

			$this->_url = $url;
			$this->_port = $port;
			$this->_bindDn = $bindDn;
		}
		catch (Exception $e) {
			$errorMsg = 'Error occured: ' . $e->getMessage();

			$this->ldapError = $errorMsg;
			return $errorMsg . "\n";
		}
		
		return true;
	}

	public function connected() {
		if (!empty($this->_ldap) && empty($this->_connected)) {
			return true;
		}
		
		return $this->_connected;
	}

	public function setSizeLimit($limit) {
		$this->_sizeLimit = $limit;
	}

	/**
	 * Perform a search with pagination.
	 * 
	 * @return Merged array of all results with count parameter.
	 */
	public function search($dn, $filter, $attributes, $options = array()) {
		if (!$this->connected()) {
			return false;
		}

		$options = am(array(
			'subtree' => true
		), $options);


		// attributes array must be formatted in incremental numbered keys (0,1,...)
		// otherwise ldap_search() fails with error
		$attributes = array_values($attributes);

		if ($this->_sizeLimit != 0) {
			// $pageSize = $this->_sizeLimit;
			ldap_control_paged_result($this->_ldap, $this->_sizeLimit, true);

			$sr = @ldap_search($this->_ldap, $dn, $filter, $attributes, 0, $this->_sizeLimit);
			if (!is_resource($sr)) {
				CakeLog::write('debug', 'LDAP testing failed, $dn:'.Debugger::exportVar($dn).' $filter: ' . Debugger::exportVar($filter) . ' Attributes: ' . Debugger::exportVar($attributes));

				$data = [
					'count' => 0
				];
			}
			else {
				$data = ldap_get_entries($this->_ldap, $sr);
			}
		}
		else {
			$cacheStr = 'ldap_results_'. $this->_url . '_' . $this->_port . '_' . $dn . '_' . $filter . '_' . (implode('_', $attributes));
			$cacheStr = sha1($cacheStr);

			if (($data = Cache::read($cacheStr, 'ldap')) === false) {

				$pageSize = 1000;
				$data = array();
				$cookie  = '';
				$count = 0;
				$i = 0;
				do {
					ldap_control_paged_result($this->_ldap, $pageSize, true, $cookie);

					if ($options['subtree']) {
						$sr  = ldap_search($this->_ldap, $dn, $filter, $attributes);
					}
					else {
						$sr  = ldap_list($this->_ldap, $dn, $filter, $attributes);
					}
					
					$entries = ldap_get_entries($this->_ldap, $sr);

					$count += array_shift($entries);
					$data = array_merge($data, $entries);

					ldap_control_paged_result_response($this->_ldap, $sr, $cookie);
					
					$i++;
				} while ($cookie !== null && $cookie != '');

				// $data = array('count' => $count) + $data;
				$data = array_merge([
					'count' => $count
				], $data);

				Cache::write($cacheStr, $data, 'ldap');
				Cache::write('last_cache_update', ['time' => time()], 'ldap');
			}
		}

		$this->_lastSearch = array(
			'dn' => $dn,
			'filter' => $filter,
			'attributes' => $attributes,
		);

		return $data;
	}

	/**
	 * Method escapes string for use inside filter query.
	 */
	public function escape($value, $ignore = null) {
		return ldap_escape($value, $ignore, LDAP_ESCAPE_FILTER);
	}

	/**
	 * Merges multiple performed ldap searches entries into fully usable array.
	 *
	 * @param boolean $unique Should array_unique() be performed after merging sets of results to remove duplicates.
	 */
	protected function mergeSearchResults($entries, &$data, $unique = true) {
		if (empty($data)) {
			$data = array();
			$count = 0;
		}
		else {
			$count = (int) array_shift($data);
		}
		
		$count += array_shift($entries);
		
		$results = array_merge($data, $entries);
		if ($unique) {
			$results = array_unique($results, SORT_REGULAR);
		}

		$data = array('count' => $count) + $results;
	}

	/**
	 * Information about last ldap search parameters.
	 */
	public function getLastSearch() {
		return $this->_lastSearch;
	}

}