<?php
namespace Suggestion\Package\TeamRole;

class SecurityOfficerPackage extends BasePackage {
	public $alias = 'SecurityOfficerPackage';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Security Officer');
		//$this->description = __('Description');

		$this->data = array(
			'user_id' => ADMIN_ID,
			'role' => $this->name,
			'responsibilities' => __('Secures premises and personnel by patrolling property; monitoring surveillance equipment; inspecting buildings, equipment, and access points; permitting entry. Obtains help by sounding alarms.'),
			'competences' => __('Good physical shape, knowledgable in personal defense'),
			'status' => TEAM_ROLE_ACTIVE
		);
	}
}
