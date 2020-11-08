<?php
App::uses('AppModel', 'Model');
App::uses('AbstractQuery', 'Lib/AdvancedFilters/Query');
App::uses('UserFields', 'UserFields.Lib');
App::uses('Attachment', 'Attachments.Model');
App::uses('CakeSession', 'Model/Datasource');
App::uses('ReportTemplate', 'Reports.Model');
App::uses('ReportBlockChartSetting', 'Reports.Model');
App::uses('SecurityPoliciesHelper', 'View/Helper');

/**
 * NOTE: Optimized contain in AdvancedFilter fields: third_party_id, compliance_package_item_item_id, 
 * compliance_package_item_name, compliance_package_item_description
 */

class SecurityPolicy extends AppModel
{
	public $reviewAfterSave = false;
	public $displayField = 'index';

	protected $_appModelConfig = [
		'behaviors' => [
		],
		'elements' => [
		]
	];

	public $mapping = array(
		'titleColumn' => 'index',
		'notificationSystem' => array('index'),
		'logRecords' => true,
		'workflow' => false
	);

	public $actsAs = array(
		'Containable',
		'Search.Searchable',
		'AuditLog.Auditable' => array(
            'ignore' => array(
                'created',
                'modified',
            )
        ),
        'Utils.SoftDelete',
		'HtmlPurifier.HtmlPurifier' => array(
			'config' => 'Strict',
			'fields' => array(
				'index', 'short_description', 'use_attachments', 'url', 'security_policy_document_type_id', 'version', 'published_date', 'next_review_date', 'permission', 'ldap_connector_id', 'asset_label_id', 'status', 'hash'
			)
		),
		'Visualisation.Visualisation',
		'ReviewsPlanner.Reviews' => [
			'dateColumn' => 'next_review_date',
			'userFields' => [
				'Collaborator'
			],
			'autoCreatedReview' => 'This review was created by the system at the time the policy was created - If you used "attachments" as content, then dont forget to attach policies to this review.'
		],
		'ObjectStatus.ObjectStatus',
		'ModuleDispatcher' => [
			'behaviors' => [
				'CustomFields.CustomFields',
				'Reports.Report',
			]
		],
		'UserFields.UserFields' => [
			'fields' => [
				'Owner',
				'Collaborator' => [
					'mandatory' => true
				]
			]
		],
		'Comments.Comments',
		'Attachments.Attachments',
		'Widget.Widget',
		'Macros.Macro',
		'ImportTool.ImportTool',
		'SubSection' => [
			'childModels' => true
		],
		'AdvancedFilters.AdvancedFilters',
		'CustomLabels.CustomLabels'
	);

	public $validate = array(
		'index' => array(
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false
		),
		'description' => array(
			'notEmpty' => array(
				'rule' => 'notBlank',
				'required' => false,
				'allowEmpty' => true
			)
		),
		'url' => array(
			'notEmpty' => array(
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => true
			),
			'url' => array(
				'rule' => array('url', true),
				'allowEmpty' => true,
				'message' => 'Please enter a valid URL'
			)
		),
		'version' => array(
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false
		),
		'published_date' => array(
			'notEmpty' => array(
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'This field cannot be empty'
			),
			'date' => array(
				'rule' => 'date',
				'message' => 'Enter a valid date'
			)
		),
		'next_review_date' => array(
			'notEmpty' => array(
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'This field cannot be empty'
			),
			'date' => array(
				'rule' => 'date',
				'message' => 'Enter a valid date'
			),
			'future' => array(
				'rule' => 'validateFutureDate',
				'message' => 'Choose a date in the future'
			)
		),
		'status' => array(
			'notEmpty' => array(
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'This field cannot be empty'
			),
			'inList' => array(
				'rule' => array('inList', array(SECURITY_POLICY_DRAFT, SECURITY_POLICY_RELEASED)),
				'message' => 'Status is invalid'
			)
		),
		// 'document_type' => array(
		// 	'notEmpty' => array(
		// 		'rule' => 'notBlank',
		// 		'required' => true,
		// 		'allowEmpty' => false,
		// 		'message' => 'This field cannot be empty'
		// 	),
		// 	'inList' => array(
		// 		'rule' => array('inList', array(SECURITY_POLICY_PROCEDURE, SECURITY_POLICY_POLICY, SECURITY_POLICY_STANDARD)),
		// 		'message' => 'Document Type is invalid'
		// 	)
		// ),
		'security_policy_document_type_id' => [
			'notEmpty' => [
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'This field cannot be empty'
			],
		],
		'use_attachments' => array(
			'notEmpty' => array(
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'This field cannot be empty'
			),
			'inList' => array(
				'rule' => array('inList', array(SECURITY_POLICY_USE_CONTENT, SECURITY_POLICY_USE_ATTACHMENTS, SECURITY_POLICY_USE_URL)),
				'message' => 'Document Content is invalid'
			)
		),
		'permission' => array(
			'notEmpty' => array(
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'This field cannot be empty'
			),
			'inList' => array(
				'rule' => array('inList', array(SECURITY_POLICY_PUBLIC, SECURITY_POLICY_PRIVATE, SECURITY_POLICY_LOGGED, SECURITY_POLICY_LDAP_GROUP)),
				'message' => 'Permission is invalid'
			)
		),
		'ldap_connector_id' => array(
			'notEmpty' => array(
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'LDAP Connector must be selected for this permission type'
			)
		),
		'ldap_groups' => array(
			'multiple' => array(
				'rule' => array('multiple', array('min' => 1)),
				'required' => true,
				'allowEmpty' => false,
				'message' => 'Choose one or more LDAP groups'
			)
		)
	);

	public $belongsTo = array(
		'AssetLabel',
		'LdapConnector',
		'SecurityPolicyDocumentType'
	);

	public $hasMany = array(
		'Tag' => array(
			'className' => 'Tag',
			'foreignKey' => 'foreign_key',
			'conditions' => array(
				'Tag.model' => 'SecurityPolicy'
			)
		),
		// prefer to use this
		'SecurityPolicyReview' => array(
			'className' => 'SecurityPolicyReview',
			'foreignKey' => 'foreign_key',
			'conditions' => array(
				'SecurityPolicyReview.model' => 'SecurityPolicy'
			)
		),
		'Review' => array(
			'className' => 'Review',
			'foreignKey' => 'foreign_key',
			'conditions' => array(
				'Review.model' => 'SecurityPolicy'
			)
		),
		'LogSecurityPolicy',
		'SecurityPolicyLdapGroup'
	);

	public $hasAndBelongsToMany = array(
		'SecurityService' => array(
			'with' => 'SecurityPoliciesSecurityService'
		),
		'PolicyException',
		'ComplianceManagement' => array(
			'with' => 'ComplianceManagementsSecurityPolicy'
		),
		'Project' => array(
			'with' => 'ProjectsSecurityPolicy'
		),
		'Risk' => array(
			'with' => 'RisksSecurityPolicy',
			'joinTable' => 'risks_security_policies',
			'foreignKey' => 'security_policy_id',
			'associationForeignKey' => 'risk_id',
			'conditions' => array(
				'RisksSecurityPolicy.risk_type' => 'asset-risk',
				'RisksSecurityPolicy.type' => RISKS_SECURITY_POLICIES_INCIDENT
			)
		),
		'ThirdPartyRisk' => array(
			'with' => 'RisksSecurityPolicy',
			'joinTable' => 'risks_security_policies',
			'foreignKey' => 'security_policy_id',
			'associationForeignKey' => 'risk_id',
			'conditions' => array(
				'RisksSecurityPolicy.risk_type' => 'third-party-risk',
				'RisksSecurityPolicy.type' => RISKS_SECURITY_POLICIES_TREATMENT
			)
		),
		'BusinessContinuity' => array(
			'with' => 'RisksSecurityPolicy',
			'joinTable' => 'risks_security_policies',
			'foreignKey' => 'security_policy_id',
			'associationForeignKey' => 'risk_id',
			'conditions' => array(
				'RisksSecurityPolicy.risk_type' => 'business-risk',
				'RisksSecurityPolicy.type' => RISKS_SECURITY_POLICIES_TREATMENT
			)
		),
		'RiskIncident' => array(
			'className' => 'Risk',
			'with' => 'RisksSecurityPolicy',
			'joinTable' => 'risks_security_policies',
			'foreignKey' => 'security_policy_id',
			'associationForeignKey' => 'risk_id',
			'conditions' => array(
				'RisksSecurityPolicy.risk_type' => 'asset-risk',
				'RisksSecurityPolicy.type' => RISKS_SECURITY_POLICIES_INCIDENT
			)
		),
		'RiskTreatment' => array(
			'className' => 'Risk',
			'with' => 'RisksSecurityPolicy',
			'joinTable' => 'risks_security_policies',
			'foreignKey' => 'security_policy_id',
			'associationForeignKey' => 'risk_id',
			'conditions' => array(
				'RisksSecurityPolicy.risk_type' => 'asset-risk',
				'RisksSecurityPolicy.type' => RISKS_SECURITY_POLICIES_TREATMENT
			)
		),
		'ThirdPartyRiskIncident' => array(
			'className' => 'ThirdPartyRisk',
			'with' => 'RisksSecurityPolicy',
			'joinTable' => 'risks_security_policies',
			'foreignKey' => 'security_policy_id',
			'associationForeignKey' => 'risk_id',
			'conditions' => array(
				'RisksSecurityPolicy.risk_type' => 'third-party-risk',
				'RisksSecurityPolicy.type' => RISKS_SECURITY_POLICIES_INCIDENT
			)
		),
		'ThirdPartyRiskTreatment' => array(
			'className' => 'ThirdPartyRisk',
			'with' => 'RisksSecurityPolicy',
			'joinTable' => 'risks_security_policies',
			'foreignKey' => 'security_policy_id',
			'associationForeignKey' => 'risk_id',
			'conditions' => array(
				'RisksSecurityPolicy.risk_type' => 'third-party-risk',
				'RisksSecurityPolicy.type' => RISKS_SECURITY_POLICIES_TREATMENT
			)
		),
		'BusinessContinuityIncident' => array(
			'className' => 'BusinessContinuity',
			'with' => 'RisksSecurityPolicy',
			'joinTable' => 'risks_security_policies',
			'foreignKey' => 'security_policy_id',
			'associationForeignKey' => 'risk_id',
			'conditions' => array(
				'RisksSecurityPolicy.risk_type' => 'business-risk',
				'RisksSecurityPolicy.type' => RISKS_SECURITY_POLICIES_INCIDENT
			)
		),
		'BusinessContinuityTreatment' => array(
			'className' => 'BusinessContinuity',
			'with' => 'RisksSecurityPolicy',
			'joinTable' => 'risks_security_policies',
			'foreignKey' => 'security_policy_id',
			'associationForeignKey' => 'risk_id',
			'conditions' => array(
				'RisksSecurityPolicy.risk_type' => 'business-risk',
				'RisksSecurityPolicy.type' => RISKS_SECURITY_POLICIES_TREATMENT
			)
		),
		'RelatedDocuments' => array(
			'className' => 'SecurityPolicy',
			'with' => 'SecurityPoliciesRelated',
			'joinTable' => 'security_policies_related',
			'foreignKey' => 'security_policy_id',
			'associationForeignKey' => 'related_document_id'
		),
		'AwarenessProgram',
		'DataAsset'
	);

	public function __construct($id = false, $table = null, $ds = null)
	{
		//
		// Init helper Lib for UserFields Module
		$UserFields = new UserFields();
		//
		
		$this->label = __('Security Policies');
        $this->_group = parent::SECTION_GROUP_CONTROL_CATALOGUE;

		$this->fieldGroupData = array(
            'default' => array(
                'label' => __('General')
            ),
            'policy-content' => array(
                'label' => __('Policy Content')
            ),
            'related-documents' => array(
                'label' => __('Related Documents')
            ),
            'access-restrictions' => array(
                'label' => __('Access Restrictions')
            ),
        );

		$this->fieldData = array(
			'index' => array(
				'label' => __('Title'),
				'description' => __('This is usually the title of the policy, for example: "Network Policies".'),
				'editable' => true,
				'inlineEdit' => true,
				'macro' => [
					'name' => 'title'
				]
			),
			'short_description' => array(
				'type' => 'textarea',
				'label' => __('Short Description'),
				'description' => __('OPTIONAL: Describe the scope of this document'),
				'editable' => true,
				'inlineEdit' => true,
				'macro' => [
					'name' => 'description'
				]
			),
			'Owner' => $UserFields->getFieldDataEntityData($this, 'Owner', [
				'label' => __('Owner'), 
				'description' => __('The owner is the GRC team member that will be responsible to ensure this policy is well documented in eramba and its always reviewed (by the collaborators)'),
				'quickAdd' => true,
				'inlineEdit' => true,
			]),
			'Collaborator' => $UserFields->getFieldDataEntityData($this, 'Collaborator', [
				'label' => __('Reviewer'), 
				'description' => __('Reviewers are those that are actually responsible for reviewing policies at set intervals. If this is a network policy, then is likely that the reviewers are those that work on that team.'),
				'quickAdd' => true,
				'inlineEdit' => true,
			]),
			'Tag' => array(
                'label' => __('Tags'),
				'editable' => true,
				'inlineEdit' => true,
				'type' => 'tags',
				'description' => __('OPTIONAL: Select existing or create new tags for this document. Tags are useful for filtering policies and also are used on the policy portal.'),
				'empty' => __('Add a tag')
            ),
			'published_date' => array(
				'label' => __('Published Date'),
				'inlineEdit' => true,
				'description' => __('Select the date when the document was made public'),
				'editable' => true
			),
			'next_review_date' => array(
				'label' => __('Next Review Date'),
				'description' => __('Select a date in the future when this policy will be reviewed'),
				'editable' => false,
				'renderHelper' => ['SecurityPolicies', 'disabledFields']
			),
			'asset_label_id' => array(
				'label' => __('Label'),
				'description' => __('OPTIONAL: Choose one of the Asset Labels, this labels are originated at Asset Management / Asset Identification / Settings / Labels).'),
				'inlineEdit' => true,
				'editable' => true,
				'macro' => [
					'name' => 'label'
				]
			),
			'Project' => array(
				'label' => __('Projects'),
				'description' => __('OPTIONAL: Select one or more improvement project (Security Operations / Project Management) for this document'),
				'editable' => true,
				'options' => array($this, 'getProjectsNotCompleted'),
				'quickAdd' => true,
				'inlineEdit' => true,
			),
			'status' => array(
				'label' => __('Status'),
				'description' => __('Only those policies tagged as "Published" will be used across other parts of the system.'),
				'editable' => true,
				'options' => array($this, 'getSecurityPolicyStatuses'),
			),
			// 'document_type' => array(
			// 	'label' => __('Document Type'),
			// 	'group' => 'policy-content',
			// 	'editable' => true,
			// 	'options' => array($this, 'getPoliciesDocumentTypes'),
			// ),
			'security_policy_document_type_id' => array(
				'label' => __('Document Type'),
				'group' => 'policy-content',
				'description' => __('The document type is used to categorise the type of document you are creating in the Policy Portal page. Some documents are a mix of policies, standards and procedures and a single option does not represent accurately the type of document, in such cases we recommend you choosing the label that you find more appropiate.'),
				'editable' => true,
				'inlineEdit' => true,
				'quickAdd' => true,
				'macro' => [
					'name' => 'document_type'
				]
			),
			'version' => array(
				'label' => __('Version'),
				'description' => __('The document version'),
				'group' => 'policy-content',
				'editable' => false,
				'renderHelper' => ['SecurityPolicies', 'versionField']
			),
			'use_attachments' => array(
				'label' => __('Document Content'),
				'description' => __('Select where you want to store the actual document:<br>- Use Content: you can use the content editor below to write and mantain your policies.<br>- Use Attachments: you will attach PDF, Word Files, Etc to this policy once you have saved it. Remember that those attachments must be uploaded to the reviews of the policy (Manage / Reviews), not the policy itself.<br>- Use URL: if your policies are on Sharepoints, Wikis, Etc.'),
				'group' => 'policy-content',
				'editable' => true,
				'options' => array($this, 'getPoliciesDocumentContentTypes'),
				'renderHelper' => ['SecurityPolicies', 'documentContentField'],
				'macro' => [
					'name' => 'document_content_type'
				]
			),
			'description' => array(
				'type' => 'editor',
				'label' => __('Description'),
				'description' => __('Use the editor above to document your policy'),
				'group' => 'policy-content',
				'editable' => false,
				'macro' => [
					'name' => 'document_content_description'
				]
			),
			'url' => array(
				'type' => 'text',
				'label' => __('URL'),
				'group' => 'policy-content',
				'editable' => false,
				'macro' => [
					'name' => 'document_url'
				]
			),
			'attachment' => [//this is only virtual field
				'label' => __('Attachment'),
				'description' => __('Upload attachments to this policy'),
				'group' => 'policy-content',
				'editable' => false,
				'renderHelper' => ['SecurityPolicies', 'attachmentField'],
				'hidden' => true
			],
			'permission' => array(
				'label' => __('Policy Portal Permissions for this Document'),
				'description' => __('Choose if you wish yo show this policy on the policy portal or not. Remember that the policy portal is by default disabled (System / Settings / Authentication) and is accesible by using the same URL you use for eramba with /policy/ on the end. For example: http://eramba.com/policy/<br><br>Public: Anyone can see this document on the policy portal<br>Private: No one can see this document on the policy portal<br>Authorized Users Only: Only authorized users can see this document. This is controlled by selecting groups from your AD where only those users will be able to access this document.'),
				'group' => 'access-restrictions',
				'editable' => true,
				'options' => array($this, 'getPoliciesDocumentPermissions'),
				'empty' => __('Choose a permission'),
				'renderHelper' => ['SecurityPolicies', 'permissionField']
			),
			'ldap_connector_id' => array(
				'label' => __('LDAP Connector'),
				'description' => __('Choose an LDAP Connector to show groups.'),
				'hidden' => true,
				'options' => [$this, 'getConnectors'],
				'empty' => __('Choose an LDAP Connector'),
			),
			'expired_reviews' => array(
				'label' => __('Expired Reviews'),
				'type' => 'toggle',
				'hidden' => true
			),
			'hash' => array(
				'label' => __('Hash'),
				'hidden' => true
			),
			'Review' => array(
				'label' => __('Review'),
				'hidden' => true
			),
			'LogSecurityPolicy' => array(
				'label' => __('Security Policy Logs'),
				'hidden' => true
			),
			'SecurityPolicyLdapGroup' => array(
				'label' => __('Security Policy Ldap Groups'),
				'hidden' => true
			),
			'SecurityService' => array(
				'label' => __('Internal Controls'),
				'hidden' => true
			),
			'PolicyException' => array(
				'label' => __('Policy Exceptions'),
				'hidden' => true
			),
			'ComplianceManagement' => array(
				'label' => __('Compliance Managements'),
				'hidden' => true
			),
			'Risk' => array(
				'label' => __('Asset Risks'),
				'hidden' => true
			),
			'ThirdPartyRisk' => array(
				'label' => __('Third Party Risks'),
				'hidden' => true
			),
			'BusinessContinuity' => array(
				'label' => __('Business Continuities'),
				'hidden' => true
			),
			'RiskIncident' => array(
				'label' => __('Asset Risk Incidents'),
				'hidden' => true
			),
			'RiskTreatment' => array(
				'label' => __('Asset Risk Treatments'),
				'hidden' => true
			),
			'ThirdPartyRiskIncident' => array(
				'label' => __('Third Party Risk Incidents'),
				'hidden' => true
			),
			'ThirdPartyRiskTreatment' => array(
				'label' => __('Third Party Risk Treatments'),
				'hidden' => true
			),
			'BusinessContinuityIncident' => array(
				'label' => __('Business Continuity Incidents'),
				'hidden' => true
			),
			'BusinessContinuityTreatment' => array(
				'label' => __('Business Continuity Treatments'),
				'hidden' => true
			),
			'RelatedDocuments' => [
				'label' => __('Related Documents'),
				'group' => 'related-documents',
				'editable' => true,
				'description' => __('OPTIONAL: Choose one or more similar Documents, they will be shown on the portal if a user selects this policy.'),
				'options' => array($this, 'getRelatedDocuments'),
				'quickAdd' => true,
			],
			'AwarenessProgram' => array(
				'label' => __('Awareness Programs'),
				'editable' => false,
			),
			'DataAsset' => array(
				'label' => __('Data Asset Flow'),
				'editable' => false,
			),
		);

		$this->notificationSystem = array(
			'macros' => array(
				'POLICY_ID' => array(
					'field' => 'SecurityPolicy.id',
					'name' => __('Security Policy ID')
				),
				'POLICY_NAME' => array(
					'field' => 'SecurityPolicy.index',
					'name' => __('Security Policy Name')
				),
				'POLICY_DESCRIPTION' => array(
					'field' => 'SecurityPolicy.short_description',
					'name' => __('Security Policy Description')
				),
				'POLICY_OWNER' => $UserFields->getNotificationSystemData('Owner', [
					'name' => __('Security Policy Author')
				]),
				'POLICY_REVIEW_DATE' => array(
					'field' => 'SecurityPolicy.next_review_date',
					'name' => __('Security Policy Review')
				),
				'POLICY_VERSION' => array(
					'field' => 'SecurityPolicy.version',
					'name' => __('Security Policy Version')
				),
			),
			'customEmail' =>  true
		);

		$this->advancedFilterSettings = array(
			'pdf_title' => __('Security Policies'),
			'pdf_file_name' => __('security_policy'),
			'csv_file_name' => __('security_policy'),
			'additional_actions' => array(
				'SecurityPolicyReview' => array(
					'label' => __('Reviews'),
					'url' => array(
						'controller' => 'reviews',
						'action' => 'filterIndex',
						'SecurityPolicyReview',
						'?' => array(
							'advanced_filter' => 1
						)
					)
				),
			),
			'history' => true,
            'bulk_actions' => true,
            'trash' => true,
            // 'view_item' => false,
            'use_new_filters' => true,
            'add' => true,
		);

		parent::__construct($id, $table, $ds);
	}

	public function getAdvancedFilterConfig()
	{
		$advancedFilterConfig = $this->createAdvancedFilterConfig()
			->group('general', [
				'name' => __('General')
			])
				->nonFilterableField('id')
				->textField('index', [
					'showDefault' => true
				])
				->textField('short_description', [
					'showDefault' => true
				])
				->userField('Owner', 'Owner', [
					'showDefault' => true
				])
				->userField('Collaborator', 'Collaborator', [
					'showDefault' => true
				])
				->multipleSelectField('Tag-title', [$this, 'getTags'])
				->dateField('published_date', [
					'showDefault' => true
				])
				->dateField('next_review_date', [
					'showDefault' => true
				])
				->multipleSelectField('status', [$this, 'getSecurityPolicyStatuses'])
				->objectStatusField('ObjectStatus_expired_reviews', 'expired_reviews')
				->selectField('asset_label_id', [$this, 'getLabels'], [
					'showDefault' => true
				])
			->group('PolicyContent', [
				'name' => __('Policy Content')
			])
				->multipleSelectField('security_policy_document_type_id', [$this, 'getPoliciesDocumentTypes'], [
					'showDefault' => true
				])
				->numberField('version', [
					'showDefault' => true
				])
				->multipleSelectField('use_attachments', 'getPoliciesDocumentContentTypesWithoutUse', [
					'label' => __('Document Content Type'),
				])
			->group('LdapConnector', [
				'name' => __('Policy Portal Permissions')
			])
				->multipleSelectField('permission', [$this, 'getPoliciesDocumentPermissions'])
				->multipleSelectField('ldap_connector_id', [$this, 'getLdapConnectors']);

		$this->ComplianceManagement->relatedFilters($advancedFilterConfig);
		$this->RiskTreatment->relatedFilters($advancedFilterConfig);
		$this->ThirdPartyRiskTreatment->relatedFilters($advancedFilterConfig);
		$this->BusinessContinuityTreatment->relatedFilters($advancedFilterConfig);

		$advancedFilterConfig
			->group('DataAsset', [
				'name' => __('Data Flow Analysis')
			])
				->multipleSelectField('DataAssetInstance-asset_id', [ClassRegistry::init('Asset'), 'getList'], [
					'label' => __('Asset'),
					'findField' => 'DataAsset.DataAssetInstance.asset_id',
					'fieldData' => 'DataAsset.DataAssetInstance.asset_id'
				])
				->multipleSelectField('DataAsset', [ClassRegistry::init('Risk'), 'getList'], [
					'label' => __('Data Asset Flow')
				])
				->multipleSelectField('DataAsset-data_asset_status_id', [ClassRegistry::init('DataAsset'), 'statuses'], [
					'label' => __('Data Asset Flow Type')
				]);

		$this->SecurityService->relatedFilters($advancedFilterConfig);
		$this->Project->relatedFilters($advancedFilterConfig);

		if (AppModule::loaded('CustomFields')) {
			$this->customFieldsFilters($advancedFilterConfig);
		}

		$this->otherFilters($advancedFilterConfig);

		return $advancedFilterConfig->getConfiguration()->toArray();
	}

	public function relatedFilters($advancedFilterConfig)
	{
		$advancedFilterConfig
			->group('SecurityPolicy', [
				'name' => __('Security Policy')
			])
				->multipleSelectField('SecurityPolicy', [$this, 'getList'], [
					'label' => __('Security Policy')
				]);

		return $advancedFilterConfig;
	}

	public function getImportToolConfig()
	{
		return [
			'SecurityPolicy.index' => [
				'name' => __('Title'),
				'headerTooltip' => __('This field is mandatory')
			],
			'SecurityPolicy.short_description' => [
				'name' => __('Short Description'),
				'headerTooltip' => __('This field is optional, you can leave it blank if you want to')
			],
			'SecurityPolicy.use_attachments' => [
				'name' => __('Document Content'),
				'headerTooltip' => __(
					'Mandatory, set one of the following values: %s',
					ImportToolModule::formatList(self::importToolContentTypes(), false)
				)
			],
			'SecurityPolicy.description' => [
				'name' => __('Content Editor Text'),
				'headerTooltip' => __('This field is mandatory only if Document Content is set to "Use Content"')
			],
			'SecurityPolicy.url' => [
				'name' => __('URL'),
				'headerTooltip' => __('This field is mandatory only if Document Content is set to "URL"')
			],
			'SecurityPolicy.Owner' => UserFields::getImportArgsFieldData('Owner', [
				'name' => __('Owner')
			]),
			'SecurityPolicy.Collaborator' => UserFields::getImportArgsFieldData('Collaborator', [
				'name' => __('Reviewer')
			], true),
			'SecurityPolicy.Tag' => [
				'name' => __('Tags'),
				'model' => 'Tag',
				'callback' => [
					'beforeImport' => [$this, 'convertTagsImport']
				],
				'headerTooltip' => __('Optional and accepts tags separated by "|". For example "Critical|SOX|PCI"')
			],
			'SecurityPolicy.published_date' => [
				'name' => __('Published Date'),
				'headerTooltip' => __('Mandatory, it must follow the format YYYY-MM-DD, not the "-" character is used as delimiter')
			],
			'SecurityPolicy.next_review_date' => [
				'name' => __('Next Review Date'),
				'headerTooltip' => __('Mandatory, it must follow the format YYYY-MM-DD, not the "-" character is used as delimiter')
			],
			'SecurityPolicy.asset_label_id' => [
				'name' => __('Label'),
				'model' => 'AssetLabel',
				'headerTooltip' => __('Optional, set the value of the name of the label you want to use. Label names can be obtained from Asset Management / Asset Identification / Settings / Labels'),
				'objectAutoFind' => true
			],
			'SecurityPolicy.Project' => [
				'name' => __('Projects'),
				'model' => 'Project',
				'headerTooltip' => __('Optional and accepts multiple names separated by "|". You need to enter the name of a project, you can find them at Security Operations / Project Management'),
				'objectAutoFind' => true
			],
			'SecurityPolicy.status' => [
				'name' => __('Status'),
				'headerTooltip' => __(
					'Mandatory, set value: %s',
					ImportToolModule::formatList($this->getSecurityPolicyStatuses(), false)
				)
			],
			'SecurityPolicy.security_policy_document_type_id' => [
				'name' => __('Type'),
				'model' => 'SecurityPolicyDocumentType',
				'headerTooltip' => __('You need to provide the name of the policy type, you can obtain the name of type from Control Catalogue / Security Policies / Settings / Document Type'),
				'objectAutoFind' => true
			],
			'SecurityPolicy.version' => [
				'name' => __('Version'),
				'headerTooltip' => __('This field is mandatory')
			],
			'SecurityPolicy.permission' => [
				'name' => __('Permissions'),
				'headerTooltip' => __(
					'Mandatory, set one of the following values: %s',
					"\n" . implode("\n", self::importToolPermissionTypes())

				)
			],
		];
	}

	public function getObjectStatusConfig() {
        return [
            'expired_reviews' => [
            	'title' => __('Review Expired'),
                'callback' => [$this, '_statusExpiredReviews'],
                'trigger' => [
                    [
                        'model' => $this->DataAsset,
                        'trigger' => 'ObjectStatus.trigger.policies_with_missing_reviews'
                    ],
                    [
                        'model' => $this->ComplianceManagement,
                        'trigger' => 'ObjectStatus.trigger.security_policy_expired_reviews'
                    ],
                ],
                'regularTrigger' => true,
            ],
            'current_review_trigger' => [
                'trigger' => [
                    [
                        'model' => $this->SecurityPolicyReview,
                        'trigger' => 'ObjectStatus.trigger.current_review'
                    ],
                ],
                'hidden' => true
            ],
            'project_expired' => [
            	'title' => __('Project Expired'),
                'inherited' => [
                	'Project' => 'expired'
            	],
            	'storageSelf' => false,
            	'regularTrigger' => true,
            ],
            'project_expired_tasks' => [
            	'title' => __('Project Task Expired'),
                'inherited' => [
                	'Project' => 'expired_tasks'
            	],
            	'storageSelf' => false,
            	'regularTrigger' => true,
            ],
            'project_ongoing' => [
            	'title' => __('Project Ongoing'),
                'inherited' => [
                	'Project' => 'ongoing'
            	],
            	'type' => 'success',
                'storageSelf' => false
            ],
            'project_planned' => [
            	'title' => __('Project Planned'),
                'inherited' => [
                	'Project' => 'planned'
            	],
            	'type' => 'success',
                'storageSelf' => false
            ],
            'project_closed' => [
            	'title' => __('Project Closed'),
                'inherited' => [
                	'Project' => 'closed'
            	],
            	'type' => 'success',
                'storageSelf' => false
            ],
            'project_no_updates' => [
            	'title' => __('Project Missing Updates'),
                'inherited' => [
                	'Project' => 'no_updates'
            	],
                'storageSelf' => false,
                'regularTrigger' => true,
            ],
        ];
    }

    public function getReportsConfig()
    {
    	$controlsByMitigation = [
			'title' => __('Policies by Mitigation'),
			'description' => __('This ven diagram shows the proportion on how policies are used against Internal Controls, Asset Risks, Third Party Risks, Business Risks, Compliance and Data Flow Analysis.'),
			'type' => ReportBlockChartSetting::TYPE_RADAR,
			'dataFn' => 'associationsChart',
		];

		$reviewsOverTime = [
			'title' => __('Policy Reviews Over Time'),
			'description' => __('This chart shows all policy review records over time: completed, missing and pending. It also shows the quantity based on the size of the circle.'),
			'type' => ReportBlockChartSetting::TYPE_PUNCH_CARD,
			'dataFn' => 'reviewsTimelineChart'
		];

		return [
			'finder' => [
				'options' => [
					'contain' => [
						'AssetLabel',
						'LdapConnector',
						'SecurityPolicyDocumentType',
						'Tag',
						'SecurityPolicyReview' => [
							'SecurityPolicy',
							'User'
						],
						'Review',
						'LogSecurityPolicy',
						'SecurityPolicyLdapGroup',
						'CustomFieldValue',
						'SecurityService',
						'ComplianceManagement',
						'ComplianceManagement' => [
							'CompliancePackageItem' => [
								'CompliancePackage' => [
									'CompliancePackageRegulator'
								]
							]
						],
						'Project',
						'Risk',
						'ThirdPartyRisk',
						'BusinessContinuity',
						'RiskIncident',
						'RiskTreatment',
						'ThirdPartyRiskIncident',
						'ThirdPartyRiskTreatment',
						'BusinessContinuityIncident',
						'BusinessContinuityTreatment',
						'RelatedDocuments',
						'PolicyException' => [
							'Classification',
							'CustomFieldValue',
							'SecurityPolicy',
							'ThirdParty',
							'Asset',
							'Requestor',
							'RequestorGroup'
						],
						'AwarenessProgram',
						'DataAsset' => [
							'DataAssetInstance' => [
								'Asset'
							]
						],
						'Owner',
						'OwnerGroup',
						'Collaborator',
						'CollaboratorGroup'
					]
				]
			],
			'table' => [
				'model' => [
					'SecurityPolicyReview', 'PolicyException',
				]
			],
			'chart' => [
				1 => array_merge($controlsByMitigation, [
					'templateType' => ReportTemplate::TYPE_ITEM
				]),
				2 => array_merge($controlsByMitigation, [
					'templateType' => ReportTemplate::TYPE_SECTION
				]),
				3 => array_merge($reviewsOverTime, [
					'templateType' => ReportTemplate::TYPE_ITEM
				]),
				4 => array_merge($reviewsOverTime, [
					'templateType' => ReportTemplate::TYPE_SECTION
				]),
				5 => [
					'title' => __('Related Compliance Items'),
					'description' => __('This tree chart shows all related compliance requirements linked to this item.'),
					'type' => ReportBlockChartSetting::TYPE_TREE,
					'templateType' => ReportTemplate::TYPE_ITEM,
					'dataFn' => 'relatedComplianceItemsChart',
				],
				6 => [
					'title' => __('Related Risk Items'),
					'description' => __('This tree chart shows all related risk items linked.'),
					'type' => ReportBlockChartSetting::TYPE_TREE,
					'templateType' => ReportTemplate::TYPE_ITEM,
					'dataFn' => 'relatedRiskItemsChart',
				]
			]
		];
	}

	public function getSectionInfoConfig()
	{
		return [
			'map' => [
				'SecurityPolicyReview',
				'Risk',
				'ThirdPartyRisk',
				'BusinessContinuity',
				'AwarenessProgram',
				'SecurityService' => [
					'SecurityServiceAudit',
					'SecurityServiceIssue',
					'SecurityServiceMaintenance',
				],
				'PolicyException',
			]
		];
	}

	public function getMacrosConfig()
	{
		return [
			'assoc' => [
				'ComplianceManagement.CompliancePackageItem',
			],
			'seed' => [
				[$this, 'customMacros']
			]
		];
	}

	public function customMacros($Collection)
	{
		$groupSettings = $this->getMacroGroupModelSettings();

		$Macro = new Macro($this->getMacroAlias('compliance_items_list'), __('List of Related Compliance Items'), null, ['SecurityPoliciesHelper', 'complianceItemsList']);
		$Collection->add($Macro);

		$Macro = new Macro($this->getMacroAlias('risk_items_list'), __('List of Related Risk Items'), null, ['SecurityPoliciesHelper', 'riskItemsList']);
		$Collection->add($Macro);

		$Macro = new Macro($this->getMacroAlias('data_flow_items_list'), __('List of Related Data Flow Items'), null, ['SecurityPoliciesHelper', 'dataAssetItemsList']);
		$Collection->add($Macro);
	}

	/**
	 * Adjust permission types for valid use in import tools.
	 */
	public static function importToolPermissionTypes() {
		$types = self::humanizedPermissionTypes();

		// unset 'logged' value for import tools because that value requires additional field to pass validation
		// and that field is not part of import tool for policies at the moment
		unset($types[self::PERMISSION_LOGGED]);

		return $types;
	}

	/**
	 * Adjust permission types for valid use in import tools.
	 *
	 * @deprecated e1.0.6.039 Import tool has added support for "Buil-in Editor" content type.
	 */
	public static function importToolContentTypes() {
		$types = self::contentTypes();

		// unset 'logged' value for import tools because that value requires additional field to pass validation
		// and that field is not part of import tool for policies at the moment
		// unset($types[self::CONTENT_TYPE_EDITOR]);

		return $types;
	}

	/**
	 * Humanized permission types as short sentences.
	 */
	public static function humanizedPermissionTypes($value = null) {
		$options = array(
			self::PERMISSION_LOGGED => __('"%s" for authenticated users', self::PERMISSION_LOGGED),
			self::PERMISSION_PRIVATE => __('"%s" to avoid the policy being published', self::PERMISSION_PRIVATE),
			self::PERMISSION_PUBLIC => __('"%s" if you want anyone to see it', self::PERMISSION_PUBLIC),
		);
		return parent::enum($value, $options);
	}
	// this will be also used for non-humanized version of permission types when its moved from bootstrap_functions.php
	const PERMISSION_PUBLIC = SECURITY_POLICY_PUBLIC;
	const PERMISSION_PRIVATE = SECURITY_POLICY_PRIVATE;
	const PERMISSION_LOGGED = SECURITY_POLICY_LOGGED;

	/**
	 * Wrapper method for the list of content types but with humanized values, used in import tools now.
	 */
	public static function contentTypes($value = null) {
		$options = array(
			self::CONTENT_TYPE_EDITOR => __('Built-in editor'),
			self::CONTENT_TYPE_ATTACHMENTS => __('Attachments'),
			self::CONTENT_TYPE_URL => __('URL')
		);
		return parent::enum($value, $options);
	}
	// this will be also used for non-humanized version of content types when its moved from bootstrap_functions.php
	const CONTENT_TYPE_EDITOR = SECURITY_POLICY_USE_CONTENT;
	const CONTENT_TYPE_ATTACHMENTS = SECURITY_POLICY_USE_ATTACHMENTS;
	const CONTENT_TYPE_URL = SECURITY_POLICY_USE_URL;

	public function validateAttachment()
	{
		$valid = true;

		if (isset($this->data['SecurityPolicy']['use_attachments'])
			&& $this->data['SecurityPolicy']['use_attachments'] == SECURITY_POLICY_USE_ATTACHMENTS
		) {
			$attachmentHash = CakeSession::read('SecurityPolicy.Attachment.hash');

			if ($attachmentHash !== null) {
				$Attachment = ClassRegistry::init('Attachments.Attachment');

				$valid = (boolean) $Attachment->find('count', [
					'conditions' => [
						'Attachment.hash' => $attachmentHash
					]
				]);
			}
		}

		return $valid;
	}

	public function beforeValidate($options = array()) {
		// set default validation rules before single item is processed as some fields may be modified during the process
		$this->validator()->getField('description')->setRules($this->validate['description']);
		$this->validator()->getField('url')->setRules($this->validate['url']);

		$disableFieldValidation = isset($this->data['SecurityPolicy']['_disableReviewFields']);
		$disableFieldValidation = $disableFieldValidation && !empty($this->data['SecurityPolicy']['_disableReviewFields']);

		// for bulk edits and cases where document type is needed for proper validation
		if (!isset($this->data['SecurityPolicy']['use_attachments']) && !empty($this->id)) {
			$this->data['SecurityPolicy']['use_attachments'] = $this->field('use_attachments');
		}

		if (isset($this->data['SecurityPolicy']['use_attachments']) && !$disableFieldValidation) {
			if ($this->data['SecurityPolicy']['use_attachments'] == SECURITY_POLICY_USE_CONTENT) {
				$this->validator()->getField('description')->setRule('notEmpty', array(
					'rule' => 'notBlank',
					'required' => true,
					'allowEmpty' => false,
					'message' => __('This field cannot be left blank while Document Content type is set as "%s"', getPoliciesDocumentContentTypes(SECURITY_POLICY_USE_CONTENT))
				));
			}
			if ($this->data['SecurityPolicy']['use_attachments'] == SECURITY_POLICY_USE_URL) {
				$this->validator()->getField('url')->setRule('notEmpty', array(
					'rule' => 'notBlank',
					'required' => true,
					'allowEmpty' => false,
					'message' => __('This field cannot be left blank while Document Content type is set as "%s"', getPoliciesDocumentContentTypes(SECURITY_POLICY_USE_URL))
				));
			}
		}

		$ldapConnectorField = $this->validator()->getField('ldap_connector_id');
		if ($ldapConnectorField !== null) {
			$ldapConnectorField->setRules($this->validate['ldap_connector_id']);
		}

		$ldapGroupsField = $this->validator()->getField('ldap_groups');
		if ($ldapGroupsField !== null) {
			$ldapGroupsField->setRules($this->validate['ldap_groups']);
		}

		if (isset($this->data['SecurityPolicy']['permission'])) {
			if ($this->data['SecurityPolicy']['permission'] != SECURITY_POLICY_LOGGED) {
				$this->validator()->remove('ldap_connector_id');
				$this->validator()->remove('ldap_groups');
			}
			else {
				$this->validator()->add('ldap_connector_id', $this->validate['ldap_connector_id']);
				$this->validator()->add('ldap_groups', $this->validate['ldap_groups']);

				$_data =  $this->data['SecurityPolicy'];
				// bulk edit case
				if (!isset($_data['ldap_connector_id']) && !isset($_data['ldap_groups'])) {
					$this->invalidate('permission', __('Authorized permission cannot be set with bulk actions, use standard edit form to update this value'));
				}
			}
		}

		if (isset($this->data['SecurityPolicy']['Project'])) {
			$this->invalidateRelatedNotExist('Project', 'Project', $this->data['SecurityPolicy']['Project']);
		}

		if (isset($this->data['SecurityPolicy']['asset_label_id'])) {
			$this->invalidateRelatedNotExist('AssetLabel', 'asset_label_id', $this->data['SecurityPolicy']['asset_label_id']);
		}

		if (isset($this->data['SecurityPolicy']['security_policy_document_type_id'])) {
			$this->invalidateRelatedNotExist('SecurityPolicyDocumentType', 'security_policy_document_type_id', $this->data['SecurityPolicy']['security_policy_document_type_id']);
		}

		if (empty($options['import']) && empty($this->data['SecurityPolicy']['id']) && !$this->validateAttachment()) {
			$this->invalidate('attachment', __('This field cannot be left blank.'));
		}
	}

	public function beforeSave($options = array()) {
		$ret = true;

		// $this->transformDataToHabtm(array('Project', 'RelatedDocuments'));

		// return true;
		// $ret = $this->createReview();
		// $ret &= $this->logVersion();
		$this->purifyDescription();
		// $ret &= $this->createReview();
		// $this->storeReviewDate();
		
		if (!empty($this->id)) {
			$ret &= $this->logPolicy($this->id);
		}

		return $ret;
	}

	public function afterSave($created, $options = array()) {
		$ret = true;

		// if ($this->reviewAfterSave && !empty($this->data['SecurityPolicy']['next_review_date'])) {
		// 	$ret = true;

		// 	// additional data for all types
		// 	// if ($this->data['SecurityPolicy']['use_attachments'] == SECURITY_POLICY_USE_ATTACHMENTS) {
		// 		$additionalData = [
		// 			'planned_date' => CakeTime::format('Y-m-d', CakeTime::fromString('now')),
		// 			'actual_date' => CakeTime::format('Y-m-d', CakeTime::fromString('now')),
		// 			'description' => __('This review was created by the system at the time the policy was created - If you used "attachments" as content, then dont forget to attach policies to this review.'),
		// 			'completed' => REVIEW_COMPLETE,
		// 			'version' => $this->data['SecurityPolicy']['version']
		// 		];

		// 		$ret &= $this->saveReviewRecord(null, $additionalData);
		// 	// }

		// 	$ret &= $this->saveReviewRecord($this->data['SecurityPolicy']['next_review_date']);
		// }

		if (!empty($this->id)) {

			if (isset($this->data['SecurityPolicy']['ldap_groups'])) {
				$ret &= $this->deleteJoins($this->id);
				$ret &= $this->saveLdapGroups($this->data['SecurityPolicy']['ldap_groups'], $this->id);
			}
		}
		
		return $ret;
	}

	public function getNotificationSystemConfig()
	{
		$config = parent::getNotificationSystemConfig();
		// $config['notifications'] = array_merge($config['notifications'], [
		// 	'security_policy_review_-1day' => [
		// 		'type' => NOTIFICATION_TYPE_WARNING,
		// 		'className' => '.SecurityPolicyReview',
		// 		'days' => -1,
		// 		'label' => __('Policy Scheduled Review in (-1 day)'),
		// 		'description' => __('Notifies 1 day before an Security Policy Review begins')
		// 	],
		// 	'security_policy_review_-5day' => [
		// 		'type' => NOTIFICATION_TYPE_WARNING,
		// 		'className' => '.SecurityPolicyReview',
		// 		'days' => -5,
		// 		'label' => __('Policy Scheduled Review in (-5 days)'),
		// 		'description' => __('Notifies 5 days before an Security Policy Review begins')
		// 	],
		// 	'security_policy_review_-10day' => [
		// 		'type' => NOTIFICATION_TYPE_WARNING,
		// 		'className' => '.SecurityPolicyReview',
		// 		'days' => -10,
		// 		'label' => __('Policy Scheduled Review in (-10 days)'),
		// 		'description' => __('Notifies 10 days before an Security Policy Review begins')
		// 	],
		// 	'security_policy_review_-20day' => [
		// 		'type' => NOTIFICATION_TYPE_WARNING,
		// 		'className' => '.SecurityPolicyReview',
		// 		'days' => -20,
		// 		'label' => __('Policy Scheduled Review in (-20 days)'),
		// 		'description' => __('Notifies 20 days before an Security Policy Review begins')
		// 	],
		// 	'security_policy_review_-30day' => [
		// 		'type' => NOTIFICATION_TYPE_WARNING,
		// 		'className' => '.SecurityPolicyReview',
		// 		'days' => -30,
		// 		'label' => __('Policy Scheduled Review in (-30 days)'),
		// 		'description' => __('Notifies 30 days before an Security Policy Review begins')
		// 	],
		// 	'security_policy_review_+1day' => [
		// 		'type' => NOTIFICATION_TYPE_WARNING,
		// 		'className' => '.SecurityPolicyReview',
		// 		'days' => 1,
		// 		'label' => __('Policy Scheduled Review in (+1 day)'),
		// 		'description' => __('Notifies 1 day after an Security Policy Review begins')
		// 	],
		// 	'security_policy_review_+5day' => [
		// 		'type' => NOTIFICATION_TYPE_WARNING,
		// 		'className' => '.SecurityPolicyReview',
		// 		'days' => 5,
		// 		'label' => __('Policy Scheduled Review in (+5 days)'),
		// 		'description' => __('Notifies 5 days after an Security Policy Review begins')
		// 	],
		// 	'security_policy_review_+10day' => [
		// 		'type' => NOTIFICATION_TYPE_WARNING,
		// 		'className' => '.SecurityPolicyReview',
		// 		'days' => 10,
		// 		'label' => __('Policy Scheduled Review in (+10 days)'),
		// 		'description' => __('Notifies 10 days after an Security Policy Review begins')
		// 	],
		// 	'security_policy_review_+20day' => [
		// 		'type' => NOTIFICATION_TYPE_WARNING,
		// 		'className' => '.SecurityPolicyReview',
		// 		'days' => 20,
		// 		'label' => __('Policy Scheduled Review in (+20 days)'),
		// 		'description' => __('Notifies 20 days after an Security Policy Review begins')
		// 	],
		// 	'security_policy_review_+30day' => [
		// 		'type' => NOTIFICATION_TYPE_WARNING,
		// 		'className' => '.SecurityPolicyReview',
		// 		'days' => 30,
		// 		'label' => __('Policy Scheduled Review in (+30 days)'),
		// 		'description' => __('Notifies 30 days after an Security Policy Review begins')
		// 	]
		// ]);

		return $config;
	}

	/**
	 * Recalculate ducument version from reviews.
	 */
	public function updateDocumentVersion($id) {
		$ret = true;

		$review = $this->SecurityPolicyReview->getLastCompletedReview($id);

		if (!empty($review)) {
			$data = [
				'id' => $id,
				'version' => $review['SecurityPolicyReview']['version']
			];

			$this->create();
			$ret &= (boolean) $this->save($data, [
				'fieldList' => ['version'],
			]);
		}
		
		return $ret;
	}

	public function getListWithType($conditions = []) {
		$data = $this->find('all', [
			'conditions' => $conditions,
			'fields' => ['SecurityPolicy.id', 'SecurityPolicy.index', 'SecurityPolicyDocumentType.name'],
			'order' => ['SecurityPolicy.index' => 'ASC'],
			'contain' => ['SecurityPolicyDocumentType']
		]);

		$data = Hash::combine(
		    $data,
		    '{n}.SecurityPolicy.id',
		    ['%s [%s]', '{n}.SecurityPolicy.index', '{n}.SecurityPolicyDocumentType.name']
		);

		return $data;
	}

	/**
	 * save hasMany SecurityPolicyLdapGroup associated with SecurityPolicy
	 * 
	 * @param  array $list list of groups
	 * @param  int $security_policy_id
	 * @return boolean $result
	 */
	public function saveLdapGroups($list, $security_policy_id) {
		if (!is_array($list) || empty($list)) {
			return true;
		}

		$data = array();

		foreach ($list as $group) {
			if (!$group)
				continue;

			$data[] = array(
				'security_policy_id' => $security_policy_id,
				'name' => $group
			);
		}

		$result = $this->SecurityPolicyLdapGroup->saveAll($data, array(
			'validate' => false,
			'atomic' => false
		));

		return (bool) $result;
	}

	public function saveJoins($data = null) {
		$this->data = $data;

		$ret = true;

		/*$ret &= $this->joinProcedures($this->request->data['SecurityPolicy']['procedure_id'], $this->SecurityPolicy->id);
		$ret &= $this->joinPolicies($this->request->data['SecurityPolicy']['policy_id'], $this->SecurityPolicy->id);
		$ret &= $this->joinStandards($this->request->data['SecurityPolicy']['standard_id'], $this->SecurityPolicy->id);*/
		/*if (isset($this->request->data['SecurityPolicy']['ldap_groups'])) {
			$ret &= $this->joinLdapGroups($this->request->data['SecurityPolicy']['ldap_groups'], $this->SecurityPolicy->id);
		}*/
		//$ret &= $this->SecurityPolicy->Tag->saveTags($this->request->data, 'SecurityPolicy', $this->SecurityPolicy->id);

		$ret &= $this->joinHabtm('Project', 'project_id');

		$this->data = false;
		
		return $ret;
	}

	public function deleteJoins($id) {
		// $ret = $this->ProjectsSecurityPolicy->deleteAll(array(
		// 	'ProjectsSecurityPolicy.security_policy_id' => $id
		// ));

		// $ret &= $this->SecurityPoliciesUser->deleteAll(array(
		// 	'SecurityPoliciesUser.security_policy_id' => $id
		// ));
		
		$ret = $this->SecurityPolicyLdapGroup->deleteAll(array(
			'SecurityPolicyLdapGroup.security_policy_id' => $id
		));

		return $ret;
	}

	public function convertTagsImport($value) {
		if (!empty($value)) {
			// App::uses('Tag', 'Model');
			// return implode(Tag::VALUE_SEPARATOR, $value);
			return $value;
		}

		return false;
	}

	// private function createReview() {
	// 	$ret = true;
	// 	if (!isset($this->data['SecurityPolicy']['next_review_date'])) {
	// 		return true;
	// 	}

	// 	if (!empty($this->id)) {
	// 		$originalId = $this->id;

	// 		$data = $this->find('first', array(
	// 			'conditions' => array(
	// 				'SecurityPolicy.id' => $this->id
	// 			),
	// 			'fields' => array('SecurityPolicy.next_review_date'),
	// 			'recursive' => -1
	// 		));

	// 		if ($data['SecurityPolicy']['next_review_date'] == $this->data['SecurityPolicy']['next_review_date']) {
	// 			return true;
	// 		}
			
	// 		$ret &= $this->saveReviewRecord($this->data['SecurityPolicy']['next_review_date']);
	// 		$this->id = $originalId;
	// 		return $ret;
	// 	}

	// 	$this->reviewAfterSave = true;
	// 	return true;
	// }

	/**
	 * Save an actual review item.
	 *
	 * @param  string $review Date.
	 */
	// private function saveReviewRecord($review, $additionalData = []) {
	// 	$user = $this->currentUser();
	// 	$saveData = array(
	// 		'model' => 'SecurityPolicy',
	// 		'foreign_key' => $this->id,
	// 		'planned_date' => $review,
	// 		'user_id' => $user['id'],
	// 		'workflow_status' => WORKFLOW_APPROVED
	// 	);

	// 	if (!empty($additionalData)) {
	// 		$saveData = am($saveData, $additionalData);
	// 	}
		
	// 	$this->Review->create();
	// 	$this->Review->set($saveData);
	// 	return $this->Review->save($saveData, false);
	// }

	public function getStatuses() {
		if (isset($this->data['SecurityPolicy']['status'])) {
			$statuses = getSecurityPolicyStatuses();

			return $statuses[$this->data['SecurityPolicy']['status']];
		}

		return false;
	}

	public function getPermissions() {
		if (isset($this->data['SecurityPolicy']['permission'])) {
			return getPoliciesDocumentPermissions($this->data['SecurityPolicy']['permission']);
		}

		return false;
	}

	public function getLdapConnectors() {
		$data = $this->LdapConnector->find('list', array(
			'fields' => array('LdapConnector.name'),
			'order' => array('LdapConnector.name' => 'ASC'),
		));
		return $data;
	}

	/**
	 * Create a log of a policy after each save.
	 */
	public function logPolicy($id) {
		$data = $this->find('first', array(
			'conditions' => array(
				'SecurityPolicy.id' => $id
			),
			'recursive' => -1
		));
		
		if (!$this->exists($id)) {
			return true;
		}

		$saveData = $data['SecurityPolicy'];
		unset($saveData['id']);

		$user = $this->currentUser();
		$saveData['security_policy_id'] = $id;
		$saveData['user_edit_id'] = $user['id'];

		$this->LogSecurityPolicy->set($saveData);
		return $this->LogSecurityPolicy->save();
	}

	/**
	 * Clean unwanted HTML from description field.
	 */
	private function purifyDescription() {
		if (isset($this->data['SecurityPolicy']['description'])) {
			$this->data['SecurityPolicy']['description'] = $this->purifyHtml($this->data['SecurityPolicy']['description'], 'Editor');
		}
	}

	// private function storeReviewDate() {
	// 	if (isset($this->data['SecurityPolicy']['next_review_date'])) {
	// 		if (!empty($this->id)) {
	// 			$data = $this->find('first', array(
	// 				'conditions' => array(
	// 					'id' => $this->id
	// 				),
	// 				'fields' => array('next_review_date'),
	// 				'recursive' => -1
	// 			));

	// 			$this->lastReviewDate = $data['SecurityPolicy']['next_review_date'];
	// 		}
	// 		else {
	// 			$this->lastReviewDate = null;
	// 		}
	// 	}
	// }

	
	private function logVersion() {
		if (!isset($this->data['SecurityPolicy']['version'])) {
			return true;
		}

		if (!empty($this->id)) {
			$data = $this->find('first', array(
				'conditions' => array(
					'id' => $this->id
				),
				'fields' => array('version'),
				'recursive' => -1
			));

			if ($data['SecurityPolicy']['version'] == $this->data['SecurityPolicy']['version']) {
				return true;
			}
		}

		$user = $this->currentUser();
		$saveData = array(
			'security_policy_id' => $this->id,
			'user_id' => $user['id'],
			'version' => $this->data['SecurityPolicy']['version'],
			'short_description' => $this->data['SecurityPolicy']['short_description']
		);

		$this->SecurityPolicyVersion->set($saveData);
		return $this->SecurityPolicyVersion->save();
	}

	/**
	 * Validate date in the future.
	 */
	public function validateFutureDate($check) {
		$today = CakeTime::format('Y-m-d', CakeTime::fromString('now'));
		if ($check['next_review_date'] > $today) {
			return true;
		}

		return false;
	}

	/**
	 * Checks if there is one or more public documents available.
	 */
	public function hasPublicDocuments() {
		return $this->find('all', array(
			'conditions' => array(
				'SecurityPolicy.status' => 1,
				'SecurityPolicy.permission' => SECURITY_POLICY_PUBLIC
			)
		));
	}

	// is there only public documents in the entire app, used for determining if policy portal can be used with guest login only
	public function hasOnlyPublicDocuments() {
		$count = $this->find('count', array(
			'conditions' => array(
				'SecurityPolicy.status' => 1,
				'SecurityPolicy.permission' => [SECURITY_POLICY_PRIVATE, SECURITY_POLICY_LOGGED]
			),
			'recursive' => -1
		));

		return $count == 0;
	}

	public function findByPackageItem($data) {
		$this->ComplianceManagementsSecurityPolicy->Behaviors->attach('Containable', array('autoFields' => false));
		$this->ComplianceManagementsSecurityPolicy->Behaviors->attach('Search.Searchable');

		$query = $this->ComplianceManagementsSecurityPolicy->getQuery('all', array(
			'conditions' => array(
				'CompliancePackageItem.id' => $data['compliance_package_item_id']
			),
			'joins' => array(
		        array(
		        	'table' => 'compliance_package_items',
		            'alias' => 'CompliancePackageItem',
		            'type' => 'INNER',
		        )
			),
			'fields' => array(
				'ComplianceManagementsSecurityPolicy.security_policy_id'
			)
		));

		return $query;
	}

	public function findByDataAssetStatus($data) {
		$this->SecurityService->DataAsset->Behaviors->attach('Containable', array('autoFields' => false));
		$this->SecurityService->DataAsset->Behaviors->attach('Search.Searchable');

		$subSubQuery = $this->SecurityService->DataAsset->getQuery('all', array(
			'conditions' => array(
				'DataAsset.data_asset_status_id' => $data['data_asset_status_id']
			),
			'contain' => array(),
			'fields' => array(
				'DataAsset.id'
			)
		));

		$this->SecurityService->DataAssetsSecurityService->Behaviors->attach('Containable', array('autoFields' => false));
		$this->SecurityService->DataAssetsSecurityService->Behaviors->attach('Search.Searchable');

		$subQuery = $this->SecurityService->DataAssetsSecurityService->getQuery('all', array(
			'conditions' => array(
				'DataAssetsSecurityService.data_asset_id IN (' . $subSubQuery . ')' 
			),
			'contain' => array(),
			'fields' => array(
				'DataAssetsSecurityService.security_service_id'
			)
		));

		$this->SecurityPoliciesSecurityService->Behaviors->attach('Containable', array('autoFields' => false));
		$this->SecurityPoliciesSecurityService->Behaviors->attach('Search.Searchable');

		$query = $this->SecurityPoliciesSecurityService->getQuery('all', array(
			'conditions' => array(
				'SecurityPoliciesSecurityService.security_service_id IN (' . $subQuery . ')' 
			),
			'contain' => array(),
			'fields' => array(
				'SecurityPoliciesSecurityService.security_policy_id'
			)
		));

		return $query;
	}

	public function findByTag($data = array()) {
		$query = $this->Tag->find('list', array(
			'conditions' => array(
				'Tag.title' => $data['tag'],
				'Tag.model' => 'SecurityPolicy'
			),
			'fields' => array(
				'Tag.foreign_key'
			)
		));

		return $query;
	}

	public function searchPolicyCondition($data = array()) {
		$conditions = array();
		$filter = $data['policy_search'];

		$data['tag'] = $filter;
		$tagIds = $this->findByTag($data);

	    $conditions = array(
	        'OR' => array(
	            $this->alias . '.index LIKE' => '%' . $this->formatLike($filter) . '%',
	            $this->alias . '.description LIKE' => '%' . $this->formatLike($filter) . '%',
	            $this->alias . '.short_description LIKE' => '%' . $this->formatLike($filter) . '%',
	            $this->alias . '.id ' => $tagIds
	        )
	    );

	    return $conditions;
	}

	/**
	 * Create system log when notifications are sent.
	 */
	public function saveNotificationLog($id, $users = array()) {
		if (empty($users)) {
			return true;
		}

		$ret = true;
		$this->addNoteToLog(__('Notification sent to: %s', implode(', ', $users)));
		$ret &= $this->setSystemRecord($id, 2);

		return $ret;
	}

	public function getLabels() {
		$data = $this->AssetLabel->find('list', array(
			'order' => array('AssetLabel.name' => 'ASC'),
			'fields' => array('AssetLabel.id', 'AssetLabel.name'),
			'recursive' => -1
		));

		return $data;
	}

	public function getProjects() {
		return $this->Project->getList(false);
	}

	public function getProjectsNotCompleted() {
		return $this->Project->getList();
	}

	public function getSecurityServices() {
		$data = $this->SecurityService->find('list', array(
			'order' => array('SecurityService.name' => 'ASC'),
			'fields' => array('SecurityService.id', 'SecurityService.name'),
			'recursive' => -1
		));

		return $data;
	}

	public function getRisks() {
		$data = $this->Risk->find('list', array(
			'order' => array('Risk.title' => 'ASC'),
			'fields' => array('Risk.id', 'Risk.title'),
			'recursive' => -1
		));

		return $data;
	}

	public function getThirdPartyRisks() {
		$data = $this->ThirdPartyRisk->find('list', array(
			'order' => array('ThirdPartyRisk.title' => 'ASC'),
			'fields' => array('ThirdPartyRisk.id', 'ThirdPartyRisk.title'),
			'recursive' => -1
		));

		return $data;
	}

	public function getDataAssetStatuses() {
		$data = $this->SecurityService->DataAsset->DataAssetStatus->find('list', array(
			'order' => array('DataAssetStatus.name' => 'ASC'),
			'fields' => array('DataAssetStatus.id', 'DataAssetStatus.name'),
			'recursive' => -1
		));

		return $data;
	}

	public function getBusinessContinuities() {
		$data = $this->BusinessContinuity->find('list', array(
			'order' => array('BusinessContinuity.title' => 'ASC'),
			'fields' => array('BusinessContinuity.id', 'BusinessContinuity.title'),
			'recursive' => -1
		));

		return $data;
	}

	public function getCompliancePackageItems() {
		$data = $this->ComplianceManagement->CompliancePackageItem->find('list', array(
			'order' => array('CompliancePackageItem.name' => 'ASC'),
			'fields' => array('CompliancePackageItem.id', 'CompliancePackageItem.name'),
			'recursive' => -1
		));

		return $data;
	}

	public function getSecurityPolicyStatuses() {
		return getSecurityPolicyStatuses();
	}

	public function getPoliciesDocumentTypes() {
		$data = $this->SecurityPolicyDocumentType->find('list', [
			'order' => ['SecurityPolicyDocumentType.name' => 'ASC']
		]);
		return $data;
	}

	public function getPoliciesDocumentPermissions() {
		return getPoliciesDocumentPermissions();
	}

	public function getPoliciesDocumentContentTypes() {
		return getPoliciesDocumentContentTypes();
	}

	/**
	 * Connectors possible for authorization setup.
	 */
	public function getConnectors() {
		return $this->LdapConnector->find('list', array(
			'conditions' => array(
				'LdapConnector.type' => LDAP_CONNECTOR_TYPE_GROUP
			),
			'order' => array('LdapConnector.name' => 'ASC')
		));
	}

	public function getThirdParties() {
		return $this->ComplianceManagement->getThirdParties();
	}

	public function findByCompliancePackage($data = array(), $filterParams = array()) {
		$this->ComplianceManagementsSecurityPolicy->Behaviors->attach('Containable', array(
				'autoFields' => false
			)
		);
		$this->ComplianceManagementsSecurityPolicy->Behaviors->attach('Search.Searchable');

		$value = $data[$filterParams['name']];

		$joins = array(
			array(
				'table' => 'compliance_managements',
				'alias' => 'ComplianceManagement',
				'type' => 'LEFT',
				'conditions' => array(
					'ComplianceManagementsSecurityPolicy.compliance_management_id = ComplianceManagement.id'
				)
			),
			array(
				'table' => 'compliance_package_items',
				'alias' => 'CompliancePackageItem',
				'type' => 'LEFT',
				'conditions' => array(
					'ComplianceManagement.compliance_package_item_id = CompliancePackageItem.id'
				)
			),
		);

		$conditions = array();
		if ($filterParams['findByField'] == 'ThirdParty.id') {
			$conditions = array(
				$filterParams['findByField'] => $value
			);
			$joins[] = array(
				'table' => 'compliance_packages',
				'alias' => 'CompliancePackage',
				'type' => 'LEFT',
				'conditions' => array(
					'CompliancePackageItem.compliance_package_id = CompliancePackage.id'
				)
			);
			$joins[] = array(
				'table' => 'third_parties',
				'alias' => 'ThirdParty',
				'type' => 'LEFT',
				'conditions' => array(
					'ThirdParty.id = CompliancePackage.third_party_id'
				)
			);
		}
		else {
			$conditions = array(
				$filterParams['findByField'] . ' LIKE' => '%' . $value . '%'
			);
		}

		$query = $this->ComplianceManagementsSecurityPolicy->getQuery('all', array(
			'conditions' => $conditions,
			'joins' => $joins,
			'fields' => array(
				'ComplianceManagementsSecurityPolicy.security_policy_id'
			),
			// 'group' => 'ThirdParty.id'
		));

		return $query;
	}

	/**
	 * Return policy reviews containing a version for that review.
	 */
	public function getPolicyReviews($id) {
		$assoc = $this->getAssociated('Review');

		$reviewConds = $assoc['conditions'];
		$reviewConds['Review.foreign_key'] = $id;
		$reviewConds[] = 'Review.actual_date IS NOT NULL';
		$reviewConds['Review.actual_date <='] = date('Y-m-d');
		$reviewConds['Review.completed'] = REVIEW_COMPLETE;

		$reviews = $this->Review->find('all', array(
			'conditions' => $reviewConds,
			'fields' => array(
				'Review.*',
				'COUNT(LogSecurityPolicy.id) as policy_log_count',
				'COUNT(SecurityPolicy.id) as policy_count',
				'LogSecurityPolicy.version',
				'SecurityPolicy.version',
				'User.*'
			),
			'contain' => array(
				'User' => array(
					// 'fields' => array('User.*')
				)
			),
			'order' => array('Review.actual_date' => 'DESC', 'Review.id' => 'DESC'),
			'group' => array('Review.id'),
			'joins' => array(
				array(
					'table' => 'log_security_policies',
					'alias' => 'LogSecurityPolicy',
					'type' => 'LEFT',
					'conditions' => array(
						'DATE(Review.planned_date) = DATE(LogSecurityPolicy.next_review_date)'
					)
				),
				array(
					'table' => 'security_policies',
					'alias' => 'SecurityPolicy',
					'type' => 'LEFT',
					'conditions' => array(
						'DATE(Review.planned_date) = DATE(SecurityPolicy.next_review_date)'
					)
				),
			),
			'recursive' => -1
		));

		foreach ($reviews as &$review) {
			$review['Review']['User'] = $review['User'];

			$log_count = $review[0]['policy_log_count'];
			$count = $review[0]['policy_count'];
			if (!empty($review['Review']['version'])) {
				continue;
			}

			if (empty($log_count) && empty($count) && empty($review['Review']['version'])) {
				$review['Review']['version'] = false;
				continue;
			}

			if (!empty($count)) {
				$review['Review']['version'] = $review['SecurityPolicy']['version'];
			}

			if (!empty($log_count)) {
				$review['Review']['version'] = $review['LogSecurityPolicy']['version'];
			}
		}
		
		return $reviews;
	}

	public function getRelatedDocuments() {
		return $this->getListWithType();
	}

	public function getDataAssets() {
        return $this->DataAsset->getList();
    }

    public function hasSectionIndex()
	{
		return true;
	}
	
}
