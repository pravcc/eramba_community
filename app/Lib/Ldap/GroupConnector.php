<?php
App::uses('BaseConnector', 'Lib/Ldap');

class GroupConnector extends BaseConnector {
	protected $_sizeLimit = 0;

	public function getTest($options = array()) {
		$results = array();

		if ($options['testType'] == 'listUsers') {
			$members = $this->getMembersOfGroup($options['groupName']);

			$results[] = $this->getLastSearch();

			$cleanMembers = $this->cleanUpEntry($members);
			if ($this->isEmailValueBuilt()) {
				$i = 0;
				foreach ($cleanMembers as &$m) {
					$m['__email'] = $this->buildEmailValue($members[$i]);

					$i++;
				}
			}

			$results[] = $cleanMembers;
		}

		if ($options['testType'] == 'listGroups') {
			$groups = $this->getAllGroups();

			$results[] = $this->getLastSearch();
			$results[] = $this->cleanUpEntry($groups);
		}

		return $results;
	}

	public function getGroupList() {
		if (!$this->connectorOk()) {
			return false;
		}

		$groups = $this->getAllGroups();
		return $this->formatGroups($groups);
	}

	public function getMemberList($groups = array()) {
		if (!$this->connectorOk()) {
			return false;
		}

		$users = $this->getMembersOfGroup($groups, array(
			'fetchEmail' => false
		));

		return $this->formatMembers($users);
	}

	public function getMemberEmailList($groups = array()) {
		if (!$this->connectorOk()) {
			return false;
		}
		
		$results = array();
		// foreach ($groups as $group) {
			$members = $this->getMembersOfGroup($groups, array(
				'fetchEmail' => true
			));
			array_shift($members);
			
			foreach ($members as $member) {
				$email = $this->buildEmailValue($member);
				$results[$this->getMemberName($member)] = $email;
			}
		// }

		return $results;
	}

	
	public function getMemberArray($groups = array(), $attributes = array()) {
		if (!$this->connectorOk()) {
			return false;
		}
		
		$results = array();

		$members = $this->getMembersOfGroup($groups, array(
			'fetchEmail' => true,
			'attributes' => $attributes
		));
		array_shift($members);
		
		foreach ($members as $member) {
			$arr = array(
				'email' => $this->buildEmailValue($member),
				'uid' => $this->getMemberName($member)
			);

			$email = $this->buildEmailValue($member);
			$groupName = $this->getMemberName($member);

			foreach ($attributes as $_a) {
				$arr[$_a] = parent::_getAttributeValue($member, $_a);
			}

			$results[] = $arr;
		}

		return $results;
	}

	public function getGrouppedEmailsList($groups = array()) {
		if (!$this->connectorOk()) {
			return false;
		}
		
		$results = array();
		foreach ($groups as $group) {
			$members = $this->getMembersOfGroup($group, array(
				'fetchEmail' => true
			));
			array_shift($members);
			
			foreach ($members as $member) {
				$email = $this->buildEmailValue($member);

				$conds = $email !== false;
				$conds &= (!isset($results[$email])) || (isset($results[$email]) && !in_array($group, $results[$email]));

				if ($conds) {
					$results[$email][] = $group;
				}
			}
		}

		return $results;
	}

	/**
	 * Lists all groups based on options.
	 */
	public function getAllGroups($options = array(), $searchOpts = array()) {
		$options = am(array(
		), $options);

		$attrs = array($this->_config['ldap_grouplist_name']);

		return $this->search(
			$this->_config['ldap_base_dn'],
			$this->_config['ldap_grouplist_filter'],
			$attrs,
			$searchOpts
		);
	}

	/**
	 * Searches for members of a single given group based on options.
	 *
	 * @param mixed $groupName Group name or array of groups.
	 */
	public function getMembersOfGroup($groupName, $options = array())
	{
		$options = am(array(
			'fetchEmail' => true,
			'attributes' => array()
		), $options);

		$attrs = array($this->_config['ldap_group_account_attribute']);
		if ($options['fetchEmail']) {
			$attrs[] = $this->getEmailAttribute();
		}

		$attrs = am($attrs, $options['attributes']);

		if (is_array($groupName)) {
			$data = '';
			foreach ($groupName as $group) {
				if (!$this->groupExists($group)) {
					throw new NotFoundException(__('LDAP group %s which you\'re trying to use does not exists', $group));
				}

				$members = $this->getMembersOfGroup($group, $options);
				$this->mergeSearchResults($members, $data);
			}

			$this->_lastSearch['filter'] = $this->_config['ldap_grouplist_filter'];
			
			return $data;
		}

		if (!$this->groupExists($groupName)) {
			throw new NotFoundException(__('LDAP group %s which you\'re trying to use does not exists', $groupName));
		}

		return $this->search(
			$this->_config['ldap_base_dn'],
			$this->buildGroupMembersFilter($groupName),
			array_unique($attrs)//,
			// array('subtree' => true)
		);
	}

	/**
	 * Get email attribute for use in ldap search.
	 * @return [type] [description]
	 */
	protected function getEmailAttribute() {
		$type = $this->getEmailType();

		if ($type == LDAP_CONNECTOR_EMAIL_FETCH_EMAIL_ATTRIBUTE) {
			return $this->_config['ldap_group_email_attribute'];
		}

		if ($type == LDAP_CONNECTOR_EMAIL_FETCH_ACCOUNT_DOMAIN) {
			return $this->_config['ldap_group_account_attribute'];
		}

		return false;
	}

	public function getEmailType() {
		if (empty($this->_config['ldap_group_fetch_email_type'])) {
			return false;
		}

		return $this->_config['ldap_group_fetch_email_type'];
	}

	/**
	 * If email value of a member is not fetched directly.
	 */
	public function isEmailValueBuilt() {
		return $this->getEmailType() == LDAP_CONNECTOR_EMAIL_FETCH_ACCOUNT_DOMAIN;
	}

	/**
	 * Create email value for a member based on current connector settings.
	 */
	public function buildEmailValue($member) {
		if (empty($this->_config['ldap_group_fetch_email_type'])) {
			return false;
		}

		$attr = strtolower($this->getEmailAttribute());
		if (!in_array($attr, $member)) {
			return false;
		}

		$type = $this->_config['ldap_group_fetch_email_type'];
		if ($type == LDAP_CONNECTOR_EMAIL_FETCH_EMAIL_ATTRIBUTE) {
			return $member[$attr][0];
		}

		if ($type == LDAP_CONNECTOR_EMAIL_FETCH_ACCOUNT_DOMAIN) {
			return $member[$attr][0] . '@' . $this->_config['ldap_group_mail_domain'];
		}

		return false;
	}

	/**
	 * Create search filter for members of a group.
	 */
	public function buildGroupMembersFilter($groupName) {
		if (is_array($groupName)) {
			$groupName = $this->getGroupName($groupName);
		}

		if (!$this->groupExists($groupName)) {
			throw new NotFoundException(__('LDAP group %s which you\'re trying to use does not exists', $groupName));
		}

		// escape user input
		$groupName = $this->escape($groupName);

		// build the filter ldap query
		$filter = $this->_config['ldap_groupmemberlist_filter'];
		$filter = str_replace("%GROUP%", $groupName, $filter);

		return $filter;
	}

	/**
	 * Helper method formats group entries into a simple array of group names.
	 */
	public function formatGroups($groups) {
		return $this->formatEntries($groups, 'ldap_grouplist_name');
	}

	/**
	 * Helper method formats member entries into a simple array of member names.
	 */
	public function formatMembers($members) {
		return $this->formatEntries($members, 'ldap_group_account_attribute');
	}

	protected function getGroupName($group) {
		return $this->getAttribute($group, 'ldap_grouplist_name');
	}

	public function getMemberName($member) {
		return $this->getAttribute($member, 'ldap_group_account_attribute');
	}

	protected function groupExists($group)
	{
		$groups = $this->getGroupList();

		if ($groups == false || !in_array($group, $groups, true)) {
			return false;
		}

		return true;
	}
}