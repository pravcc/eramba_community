<?php
namespace Suggestion\Package\SecurityPolicy;

class RiskManagement extends BasePackage {
	public $alias = 'RiskManagement';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Risk Management');

		$this->data = array(
			$this->model => array(
				'index' => $this->name,
				'short_description' => __('This policy governs asset based Risk Management practices across the organization.'),
				// 'description' => __('Default'),
				'description' => $this->readDocument('RiskManagement'),
				'decurity_policy_document_type_id' => 1,
				'version' => '1',
				'published_date' => $this->now(),
				'next_review_date' => $this->oneYear(),
				'asset_label_id' => '',
				'procedure_id' => '',
				'policy_id' => '',
				'standard_id' => '',
				'status' => SECURITY_POLICY_RELEASED,
				'project_id' => '',
				'author_id' => ADMIN_ID,
				'collaborator_id' => array(ADMIN_ID),
				'permission' => SECURITY_POLICY_PUBLIC
			),
			'Tag' => array(
				'tags' => 'suggested'
			)
		);

	}
}
