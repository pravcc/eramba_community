<?php
App::uses('AppModel', 'Model');
App::uses('DataAssetSettingsUser', 'Model');
App::uses('Country', 'Model');
App::uses('DataAssetSettingsThirdParty', 'Model');
App::uses('Hash', 'Utility');
App::uses('UserFields', 'UserFields.Lib');

class DataAssetSetting extends AppModel
{
    public $displayField = 'name';

    protected $_appModelConfig = [
        'behaviors' => [
        ],
        'elements' => [
        ]
    ];

    public $actsAs = [
        'Containable',
        'Search.Searchable',
        'HtmlPurifier.HtmlPurifier' => [
            'config' => 'Strict',
            'fields' => [
                'name',
            ]
        ],
        'ModuleDispatcher' => [
            'behaviors' => [
                'Reports.Report',
            ]
        ],
        'AuditLog.Auditable',
        'Visualisation.Visualisation',
        'ObjectStatus.ObjectStatus',
        'UserFields.UserFields' => [
            'fields' => [
                'DataOwner' => [
                    // 'customRolesInit' => false
                ]
            ]
        ],
        'Comments.Comments',
        'Attachments.Attachments',
        'Widget.Widget',
        'Macros.Macro',
        'CustomLabels.CustomLabels'
    ];

    public $validate = [
    ];

    public $validateGdpr = [
        'driver_for_compliance'  => [
            'rule' => 'notBlank',
            'required' => true
        ],
        'Dpo' => [
            'minCount' => [
                'rule' => ['checkEmptyCheckbox', 'dpo_empty'],
                'message' => 'You have to select at least one option',
                'required' => true
            ]
        ],
        'Processor' => [
            'minCount' => [
                'rule' => ['checkEmptyCheckbox', 'processor_empty'],
                'message' => 'You have to select at least one option',
                'required' => true
            ]
        ],
        'Controller' => [
            'minCount' => [
                'rule' => ['checkEmptyCheckbox', 'controller_empty'],
                'message' => 'You have to select at least one option',
                'required' => true
            ]
        ],
        'ControllerRepresentative' => [
            'minCount' => [
                'rule' => ['checkEmptyCheckbox', 'controller_representative_empty'],
                'message' => 'You have to select at least one option',
                'required' => true
            ]
        ],
        'SupervisoryAuthority' => [
            'minCount' => [
                'rule' => ['multiple', ['min' => 1]],
                'message' => 'You have to select at least one option',
                'required' => true
            ]
        ],
    ];

    public $belongsTo = [
        'DataAssetInstance'
    ];

    public $hasMany = [
        'SupervisoryAuthority' => [
            'className' => 'Country',
            'foreignKey' => 'foreign_key',
            'conditions' => [
                'SupervisoryAuthority.model' => 'DataAssetSetting',
                'SupervisoryAuthority.type' => Country::TYPE_DATA_ASSET_SETTING_SUPERVISORY_AUTHORITY
            ]
        ],
    ];

    public $hasAndBelongsToMany = [
        'Dpo' => [
            'className' => 'User',
            'with' => 'DataAssetSettingsUser',
            'joinTable' => 'data_asset_settings_users',
            'foreignKey' => 'data_asset_setting_id',
            'associationForeignKey' => 'user_id',
            'conditions' => [
                'DataAssetSettingsUser.type' => DataAssetSettingsUser::TYPE_DPO
            ]
        ],
        'Processor' => [
            'className' => 'ThirdParty',
            'with' => 'DataAssetSettingsThirdParty',
            'joinTable' => 'data_asset_settings_third_parties',
            'foreignKey' => 'data_asset_setting_id',
            'associationForeignKey' => 'third_party_id',
            'conditions' => [
                'DataAssetSettingsThirdParty.type' => DataAssetSettingsThirdParty::TYPE_PROCESSOR
            ]
        ],
        'Controller' => [
            'className' => 'ThirdParty',
            'with' => 'DataAssetSettingsThirdParty',
            'joinTable' => 'data_asset_settings_third_parties',
            'foreignKey' => 'data_asset_setting_id',
            'associationForeignKey' => 'third_party_id',
            'conditions' => [
                'DataAssetSettingsThirdParty.type' => DataAssetSettingsThirdParty::TYPE_CONTROLLER
            ]
        ],
        'ControllerRepresentative' => [
            'className' => 'User',
            'with' => 'DataAssetSettingsUser',
            'joinTable' => 'data_asset_settings_users',
            'foreignKey' => 'data_asset_setting_id',
            'associationForeignKey' => 'user_id',
            'conditions' => [
                'DataAssetSettingsUser.type' => DataAssetSettingsUser::TYPE_CONTROLLER_REPRESENTATIVE
            ]
        ],
    ];

    const GDPR_ENABLED = 1;
    const GDPR_DISABLED = 0;

    public function __construct($id = false, $table = null, $ds = null)
    {
        //
        // Init helper Lib for UserFields Module
        $UserFields = new UserFields();
        //
        
        $this->label = __('Data Asset Settings');
        $this->_group = parent::SECTION_GROUP_ASSET_MGT;

        $this->fieldGroupData = [
            'default' => [
                'label' => __('General')
            ],
            'gdpr' => [
                'label' => __('GDPR')
            ]
        ];

        $this->fieldData = [
            'name' => [
                'label' => __('Name'),
                'editable' => true,
                'description' => __('The name of the asset as described under Asset Management / Asset identification'),
                'renderHelper' => ['DataAssetSettings', 'nameField'],
            ],
            'DataOwner' => $UserFields->getFieldDataEntityData($this, 'DataOwner', [
                'label' => __('Data Owner'),
                'editable' => true,
                'description' => __('Select one or more accounts (System / Settings / User Management) that will be used as owners'),
                'quickAdd' => true,
            ]),
            'data_asset_instance_id' => [
                'label' => __('Data Asset Instance'),
                'editable' => false
            ],
            'gdpr_enabled' => [
                'label' => __('Enable GDPR Analysis?'),
                'editable' => true,
                'type' => 'toggle',
                'group' => 'gdpr',
                'renderHelper' => ['DataAssetSettings', 'gdprEnabledField'],
                'description' => __('Enabling GDPR will include a set of pre-defined questionaires on each flow realted to the legislation'),
            ],
            'driver_for_compliance' => [
                'label' => __('Driver for Compliance'),
                'editable' => true,
                'group' => 'gdpr',
                'description' => __('Describe why your organisation requires to be compliance with GDPR'),
            ],
            'Dpo' => [
                'label' => __('DPO Role'),
                'editable' => true,
                'group' => 'gdpr',
                'renderHelper' => ['DataAssetSettings', 'dpoField'],
                'description' => __('Select one or more individuals (System / Settings / User Management) that act as Data Protection Officer (DPO) - <br><br>See Art.37, Rec.97; Art.37, Rec.97; Art.37(5)-(6), Art.38(1)-(2), (4)-(5), Art.38(3) and Art.38(6), 39'),
                'quickAdd' => true,
                'help' => __('
Art.37
To the extent that the GDPR requires the appointment of a DPO (see Chapter 12), that requirement applies to processors.

Rec.97; Art.37
A controller or processor must appoint a DPO if local laws require it to do so, or if its data processing activities involve:regular and systematic monitoring of data subjects on a large scale; or processing Sensitive Personal Data on a large scale.A corporate group may collectively appoint a single DPO. Organisations that are not required to appoint a DPO are free to do so voluntarily. If a DPO is appointed, the organisation must publish the details of the DPO, and communicate those details to the relevant DPA.
<br><br>
Rec.97; Art.37(5)-(6)
A DPO should have expert knowledge of data protection law and practice, and should be capable of performing the functions of a DPO (outlined below). A DPO can be an employee or an outside consultant.
<br><br>
Art.38(1)-(2), (4)-(5)
The DPO must deal with all data protection matters affecting the controller or processor properly and in a timely manner. The controller or processor must provide the DPO with the necessary resources and support. Data subjects may contact the DPO (e.g., to exercise their rights under the GDPR). The DPO must be bound by a confidentiality obligation in relation to his or her work.
<br><br>
Art.38(3)
The organisation cannot instruct the DPO in the performance of his or her duties, and cannot terminate the DPOs employment (or take any other disciplinary action) as a result of the performance of the DPOs duties.
<br><br>
Art.38(6), 39
A DPO must fulfil at least the following tasks:
informing and advising the relevant controller or processor (and any employees who process personal data) about their obligations under the GDPR; monitor compliance with the GDPR by the controller or processor; advise on Impact Assessments and prior consultation with DPAs; and cooperate with DPAs and act as a point

                '),
            ],
            'dpo_empty' => [
                'label' => __('Not applicable'),
                'type' => 'toggle',
                'editable' => false,
                'group' => 'gdpr',
                'description' => __(''),
            ],
            'Processor' => [
                'label' => __('Processor Role'),
                'editable' => true,
                'group' => 'gdpr',
                'renderHelper' => ['DataAssetSettings', 'processorField'],
                'description' => __('Select one or more Third Parties (Organisation / Third Parties) that will be taking the role of Processor for this data. <br><br>See Art.4(8), Rec.81; Art.28(1)-(3), Rec.82, 89; Art.30, Art.4(8), Rec.81; Art.28(1)-(3), Rec.22; Art.3(1), Rec.22; Art.3(1), Art.28(3)(h), Art.28(2), (4), Art.28(3)(b), 29, Art.29, Art.28(10), Rec.82; Art.30(2), Art.31, Art.28(1), (3)(e), (4), 32, Art.33(2), Art.44 and Rec.146; Art.82(1)-(2) '),
                'quickAdd' => true,
                'help' => __('
Art.4(8)
Processor means a natural or legal person, public authority, agency or any other body which processes personal data on behalf of the controller.
<br><br>
Rec.81; Art.28(1)-(3)
A controller that wishes to appoint a processor must only use processors that guarantee compliance with the GDPR. The controller must appoint the processor in the form of a binding agreement in writing, which states that the processor must:
only act on the controllers documented instructions; impose confidentialityobligations on allpersonnel who processthe relevant data; ensure the security of the personal data that it processes; abide by the rulesregarding appointmentof sub-processors (see Chapter 11); implement measuresto assist the controllerin complying with therights of data subjects; assist the controller inobtaining approval from DPAs where required; at the controllerselection, either returnor destroy the personaldata at the end of therelationship (except as required by EU orMember State law); and provide the controllerwith all informationnecessary to demonstratecompliance with the GDPR.
<br><br>
Art.4(8)
In summary, a processor is an entity that processes personal data on behalf of the controller. A full definition is set out in Chapter 5.
<br><br>
Rec.81; Art.28(1)-(3)
A controller that wishes to appoint a processor must only use processors that guarantee compliance with the GDPR. The controller must appoint the processor in the form of a binding written agreement, which states that the processor must:
only act on the controllers documented instructions; impose confidentiality obligations on all personnel who process the relevant data; must ensure the security of the personal data that it processes; abide by the rules regarding appointment of sub-processors; implement measures to assist the controller in complying with the rights of data subjects; assist the controller in obtaining approval from DPAs where required; at the controllers election, either return or destroy the personal data at the end of the relationship (except as required by EU or Member State law); and provide the controller with all information necessary to demonstrate compliance with the GDPR.
<br><br>
Rec.22; Art.3(1)
The GDPR applies to the processing of personal data by a controller or a processor that falls within the scope of the GDPR (regardless of whether the relevant processing takes place in the EU or not).
<br><br>
Art.28(3)(h)
In the event that a processor believes that the controllers instructions conflict with the requirements of the GDPR or other EU or Member State laws, the processor must immediately inform the controller.
<br><br>
Art.28(2), (4)
The processor must not appoint a sub-processor without the prior written consent of the controller. Where the controller agrees to the appointment of sub-processors, those sub‑processors must be appointed on the same terms as are set out in the contract between the controller and the processor, and in any case in accordance with Art.28(1)‑(2) (see above).
<br><br>
Art.28(3)(b), 29
The processor must ensure that any personal data that it processes are kept confidential. The contract between the controller and the processor must require the processor to ensure that all persons authorised to process the personal data are under an appropriate obligation of confidentiality.
<br><br>
                ')
            ],
            'processor_empty' => [
                'label' => __('Not applicable'),
                'type' => 'toggle',
                'editable' => false,
                'group' => 'gdpr',
                'description' => __(''),
            ],
            'Controller' => [
                'label' => __('Controller Role'),
                'editable' => true,
                'group' => 'gdpr',
                'renderHelper' => ['DataAssetSettings', 'controllerField'],
                'description' => __('Select one or more Third Parties (Organisation / Third Parties) that will be taking the role of Controller for this data. <br><br>See Art.4(7), Rec.79; Art.4(7), 26 and Rec.79, 146; Art.26(3), 82(3)-(5)'),
                'quickAdd' => true,
                'help'=>__('
Art.4(7)
Controller means the natural or legal person, public authority, agency or any other body which alone or jointly with others determines the purposes and means of the processing of personal data; where the purposes and means of processing are determined by EU or Member State laws, the controller (or the criteria for nominating the controller) may be designated by those laws.
In summary, a controller is an entity that, alone or jointly with others, determines how and why personal data are processed. A full definition is set out in Chapter 5.
<br><br>
Rec.79; Art.4(7), 26
Where two or more controllers jointly determine the purposes and means of the processing of personal data, they are joint controllers. Joint controllers must, by means of an arrangement between them, apportion data protection compliance responsibilities between themselves (e.g., the responsibility for providing clear information to data subjects – see Chapter 9). A summary of the arrangement must be made available for the data subject. The arrangement may designate a contact point for data subjects.
<br><br>
Rec.79, 146; Art.26(3), 82(3)-(5)
Data subjects are entitled to enforce their rights against any of the joint controllers. Each joint controller is liable for the entirety of the damage, although national law may apportion liability between them. A controller may be exempted from liability if it proves that it is not in any way responsible for the damage. If one joint controller has paid full compensation, it may then bring proceedings against the other joint controllers to recover their portions of the damages.
                '),
            ],
            'controller_empty' => [
                'label' => __('Not applicable'),
                'type' => 'toggle',
                'editable' => false,
                'group' => 'gdpr',
                'description' => __(''),
            ],
            'ControllerRepresentative' => [
                'label' => __('Controller Representative'),
                'editable' => true,
                'group' => 'gdpr',
                'renderHelper' => ['DataAssetSettings', 'controllerRepresentativeField'],
                'description' => __('If the controller is outside the European Union, select a one or more individuals (System / Settings / User Management) that will act as representative.<br><br>See Rec.25; Art.3(3), Rec.80; Art.4(17), 27'),
                'quickAdd' => true,
                'help'=>__('
Rec.25; Art.3(3)
An organisation that is not established in any Member State, but is subject to the laws of a Member State by virtue of public international law is also subject to the GDPR.
<br><br>
Rec.80; Art.4(17), 27
A controller established outside the EU must appoint a representative in one of the Member States in which the controller offers goods or services or monitors EU residents, unless the processing is occasional, small-scale and does not involve Sensitive Personal Data. The appointment of the representative is without prejudice to legal actions which could be initiated against the controller. The representative must be mandated by the controller or processor to be addressed in addition to or instead of the controller or the processor by supervisory authorities and data subjects, on all issues related to data protection. A representative may be subject to enforcement actions by DPAs in the event of non-compliance by the controller.
                '),
            ],
            'controller_representative_empty' => [
                'label' => __('Not applicable'),
                'type' => 'toggle',
                'editable' => false,
                'group' => 'gdpr',
                'description' => __(''),
            ],
            'SupervisoryAuthority' => [
                'label' => __('Supervisory Authority'),
                'editable' => true,
                'options' => [$this->SupervisoryAuthority, 'europeCountries'],
                'group' => 'gdpr',
                'description' => __('Select one or more countries from the European Economic Area (EEA) that will act as Supervisor Authority. <br><br>See Rec.22; Art.3(1)'),
                'help'=>__('
Rec.22; Art.3(1)
The GDPR applies to organisations that:
are established in one or more Member State(s); and process personal data (either as controller or processor, and regardless of whether or not the processing takes place in the EU) in the context of that establishment.
<br><br>
Rec.73, 85-88; Art.33
In the event of a data breach, the controller must report the breach to the DPA without undue delay, and in any event within 72 hours of becoming aware of it. There is an exception where the data breach is unlikely to result in any harm to data subjects. The notification must include at least:
a description of the data breach, including the numbers of data subjects affected and the categories of data affected; the name and contact details of the DPO (or other relevant point of contact); the likely consequences of the data breach; and any measures taken by the controller to remedy or mitigate the breach.The controller must keep records of all data breaches, comprising the facts and effects of the breach and any remedial action taken.
                '),
            ],
        ];

        parent::__construct($id, $table, $ds);
    }

    public function getObjectStatusConfig() {
        return [
            'asset_missing_review' => [
                'trigger' => [
                    $this->DataAssetInstance,
                ],
                'hidden' => true
            ],
            'incomplete_analysis' => [
                'trigger' => [
                    $this->DataAssetInstance,
                ],
                'hidden' => true
            ],
            'incomplete_gdpr_analysis' => [
                'trigger' => [
                    $this->DataAssetInstance,
                ],
                'hidden' => true
            ],
        ];
    }

    public function beforeValidate($options = array()) {
        if (!empty($this->data['DataAssetSetting']['gdpr_enabled'])) {
            $this->validate = $this->validateGdpr;
        }

        return true;
    }

    public function beforeSave($options = array()) {
        $ret = true;

        // $this->transformDataToHabtm(['Dpo', 'Processor', 'Controller', 'ControllerRepresentative']);

        // $this->setHabtmConditionsToData(['Dpo', 'Processor', 'Controller', 'ControllerRepresentative']);

        return $ret;
    }

    public function afterSave($created, $options = array()) {
        $this->SupervisoryAuthority->deleteAll([
            'foreign_key' => $this->id,
            'model' => 'DataAssetSetting',
            'type' => Country::TYPE_DATA_ASSET_SETTING_SUPERVISORY_AUTHORITY,
        ]);

        if (!empty($this->data['DataAssetSetting']['SupervisoryAuthority'])) {
            foreach ($this->data['DataAssetSetting']['SupervisoryAuthority'] as $value) {
                $item = [
                    'model' => 'DataAssetSetting',
                    'type' => Country::TYPE_DATA_ASSET_SETTING_SUPERVISORY_AUTHORITY,
                    'foreign_key' => $this->id,
                    'country_id' => $value
                ];
                $this->SupervisoryAuthority->create();
                $this->SupervisoryAuthority->save($item);
            }
        }

        if ($created === true) {
            $this->unlockAnalysis($this->id);
        }
    }

    private function unlockAnalysis($id) {
        $data = $this->find('first', [
            'conditions' => [
                'DataAssetSetting.id' => $id
            ],
            'contain' => []
        ]);

        if (empty($data)) {
            return false;
        }

        return $this->DataAssetInstance->unlockAnalysis($data['DataAssetSetting']['data_asset_instance_id']);
    }
}
