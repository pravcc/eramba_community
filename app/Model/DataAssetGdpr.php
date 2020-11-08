<?php
App::uses('AppModel', 'Model');
App::uses('DataAsset', 'Model');
App::uses('DataAssetSettingsUser', 'Model');
App::uses('DataAssetGdprDataType', 'Model');
App::uses('DataAssetGdprCollectionMethod', 'Model');
App::uses('DataAssetGdprLawfulBase', 'Model');
App::uses('DataAssetGdprThirdPartyCountry', 'Model');
App::uses('DataAssetGdprArchivingDriver', 'Model');
App::uses('Country', 'Model');
App::uses('AbstractQuery', 'Lib/AdvancedFilters/Query');

class DataAssetGdpr extends AppModel
{
    public $useTable = 'data_asset_gdpr';

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
                'data_subject', 'volume', 'recived_data', 'contracts', 'retention', 'encryption', 'right_to_erasure', 'origin',
                'security', 'right_to_portability', 'stakeholders', 'accuracy', 'right_to_access', 'right_to_rectification',
                'right_to_decision', 'purpose', 'right_to_be_informed'
            ]
        ],
        'ModuleDispatcher' => [
            'behaviors' => [
                'Reports.Report',
            ]
        ],
        'Macros.Macro',
    ];

    public $validate = [
    ];

    public $validateGroups = [
        DataAsset::STATUS_COLLECTED => [
            'DataAssetGdprDataType' => [
                'minCount' => [
                    'rule' => ['multiple', ['min' => 1]],
                    'message' => 'You have to select at least one option',
                    'required' => true
                ]
            ],
            'purpose' => [
                'rule' => 'notBlank',
                'required' => true
            ],
            'right_to_be_informed' => [
                'rule' => 'notBlank',
                'required' => true
            ],
            'data_subject' => [
                'rule' => 'notBlank',
                'required' => true
            ],
            'DataAssetGdprCollectionMethod' => [
                'minCount' => [
                    'rule' => ['multiple', ['min' => 1]],
                    'message' => 'You have to select at least one option',
                    'required' => true
                ]
            ],
            'volume' => [
                'rule' => 'notBlank',
                'required' => true
            ],
            'recived_data' => [
                'rule' => 'notBlank',
                'required' => true
            ],
            'DataAssetGdprLawfulBase' => [
                'minCount' => [
                    'rule' => ['multiple', ['min' => 1]],
                    'message' => 'You have to select at least one option',
                    'required' => true
                ]
            ],
            'contracts' => [
                'rule' => 'notBlank',
                'required' => true
            ],
        ],
        DataAsset::STATUS_MODIFIED => [
            'stakeholders' => [
                'rule' => 'notBlank',
                'required' => true
            ],
            'accuracy' => [
                'rule' => 'notBlank',
                'required' => true
            ],
            'right_to_access' => [
                'rule' => 'notBlank',
                'required' => true
            ],
            'right_to_rectification' => [
                'rule' => 'notBlank',
                'required' => true
            ],
            'right_to_decision' => [
                'rule' => 'notBlank',
                'required' => true
            ],
            'right_to_object' => [
                'rule' => 'notBlank',
                'required' => true
            ],
        ],
        DataAsset::STATUS_STORED => [
            'retention' => [
                'rule' => 'notBlank',
                'required' => true
            ],
            'encryption' => [
                'rule' => 'notBlank',
                'required' => true
            ],
            'right_to_erasure' => [
                'rule' => 'notBlank',
                'required' => true
            ],
            'DataAssetGdprArchivingDriver' => [
                'minCount' => [
                    'rule' => ['checkEmptyCheckbox', 'archiving_driver_empty'],
                    'message' => 'You have to select at least one option',
                    'required' => true,
                ]
            ],
        ],
        DataAsset::STATUS_TRANSIT => [
            'origin' => [
                'rule' => 'notBlank',
                'required' => true
            ],
            'destination' => [
                'rule' => 'notBlank',
                'required' => true
            ],
            'DataAssetGdprThirdPartyCountry' => [
                'minCount' => [
                    'rule' => ['multiple', ['min' => 1]],
                    'message' => 'You have to select at least one option',
                    'required' => true
                ]
            ],
            'ThirdPartyInvolved' => [
                'minCount' => [
                    'rule' => ['multiple', ['min' => 1]],
                    'message' => 'You have to select at least one option',
                    'required' => true
                ]
            ],
            'security' => [
                'rule' => 'notBlank',
                'required' => true
            ],
            'right_to_portability' => [
                'rule' => 'notBlank',
                'required' => true
            ],
        ],
        DataAsset::STATUS_DELETED => [
        ],
    ];

    public $belongsTo = [
        'DataAsset'
    ];

    public $hasMany = [
        'DataAssetGdprDataType',
        'DataAssetGdprCollectionMethod',
        'DataAssetGdprLawfulBase',
        'DataAssetGdprThirdPartyCountry',
        'DataAssetGdprArchivingDriver',
        'ThirdPartyInvolved' => [
            'className' => 'Country',
            'foreignKey' => 'foreign_key',
            'conditions' => [
                'ThirdPartyInvolved.model' => 'DataAssetGdpr',
                'ThirdPartyInvolved.type' => Country::TYPE_DATA_ASSET_GDPR_THIRD_PARTY_INVOLVED
            ]
        ],
    ];

    public static $fieldGroups = [
        DataAsset::STATUS_COLLECTED => [
            'id', 'DataAssetGdprDataType', 'purpose', 'right_to_be_informed', 'data_subject', 'DataAssetGdprCollectionMethod', 'volume', 'recived_data', 'DataAssetGdprLawfulBase', 'contracts',
        ],
        DataAsset::STATUS_MODIFIED => [
            'id', 'stakeholders', 'accuracy', 'right_to_access', 'right_to_rectification', 'right_to_decision', 'right_to_object'
        ],
        DataAsset::STATUS_STORED => [
            'id', 'retention', 'encryption', 'right_to_erasure', 'DataAssetGdprArchivingDriver'
        ],
        DataAsset::STATUS_TRANSIT => [
            'id', 'origin', 'destination', 'transfer_outside_eea', 'ThirdPartyInvolved', 'DataAssetGdprThirdPartyCountry', 'security', 'right_to_portability',
        ],
        DataAsset::STATUS_DELETED => [
            'id'
        ],
    ];

    public function __construct($id = false, $table = null, $ds = null) {
        $this->label = __('Data Asset GDPR');

        $this->fieldGroupData = [
            'default' => [
                'label' => __('General')
            ],
        ];

        $this->fieldData = [
            'id' => [
                'label' => __('Id'),
                'editable' => true,
                'type' => 'hidden',
                'renderHelper' => ['DataAssetGdpr', 'idField']
            ],
            'data_asset_id' => [
                'label' => __('Data Asset'),
                'editable' => false,
            ],
            'DataAssetGdprDataType' => [
                'label' => __('Type of Data'),
                'editable' => true,
                'options' => [$this, 'dataTypes'],
                'description' => __('What type of data is being collected?<br><br>See Art.4(1), Rec.10, 34, 35, 51; Art.9(1), Rec. 19, 50, 73, 80, 91, 97; Art.10, Rec.26, 28-29, 75, 78, 156; Art.4(5), 6(4)(e), 25(1), 32(1)(a), 40(2)(d), 89(1), Rec. 35, 53-54; Art.4(15), Art.10, 23(1)(j), Rec.51-56; Art.9, Art.9(2)(a-j) and Art.9(4)'),
                'renderHelper' => ['DataAssetGdpr', 'dataAssetGdprDataTypeField'],
                'help' => __('
Art.4(1)
Personal data means any information relating to an identified or identifiable natural person (data subject); an identifiable person is one who can be identified, directly or indirectly, in particular by reference to an identifier such as a name, an identification number, location data, online identifier or to one or more factors specific to the physical, physiological, genetic, mental, economic, cultural or social identity of that person. 
<br><br>
Rec.10, 34, 35, 51; Art.9(1)
Sensitive Personal Data are personal data, revealing racial or ethnic origin, political opinions, religious or philosophical beliefs, trade-union membership; data concerning health or sex life and sexual orientation; genetic data or biometric data. Data relating to criminal offences and convictions are addressed separately (as criminal law lies outside the EUs legislative competence).
<br><br>
Rec. 19, 50, 73, 80, 91, 97; Art.10
Data relating to criminal offences and convictions may only be processed by national authorities. National law may provide derogations, subject to suitable safeguards. A comprehensive register of criminal offences may only be kept by the responsible national authority.
<br><br>
Rec.26, 28-29, 75, 78, 156; Art.4(5), 6(4)(e), 25(1), 32(1)(a), 40(2)(d), 89(1)
Pseudonymous data are still treated as personal data because they enable the identification of individuals (albeit via a key). However, provided that the key that enables re‑identification of individuals is kept separate and secure, the risks associated with pseudonymous data are likely to be lower, and so the levels of protection required for those data are likely to be lower.
<br><br>
Rec. 35, 53-54; Art.4(15)
Data concerning health means personal data relating to the physical or mental health of an individual, including the provision of health care services, which reveal information about his or her health status. It expressly covers both physical and mental health.
<br><br>
Art.10, 23(1)(j)
Personal data relating to criminal convictions and offences or related security measures may only be processed: under the control of an official authority; or when permitted under EU or Member State law.Any comprehensive register of criminal convictions may be kept only under the control of official authority.
Member States may impose restrictions on the processing of personal data for the purposes of enforcing civil law claims.
<br><br>
Rec.51-56; Art.9
The processing of Sensitive Personal Data is prohibited, unless:
<br>Art.9(2)(a) The data subject has given explicit consent.
<br>Art.9(2)(b) The processing is necessary in the context of employment law, or laws relating to social security and social protection.
<br>Art.9(2)(c) The processing is necessary to protect vital interests of the data subject (or another person) here the data subject is ncapable of giving consent.
<br>Art.9(2)(d) The processing s carried out in the course of the legitimate activities of a charity or not-for-profit body, with respect to its own members, former members, or persons with whom it has regular contact in connection with its purposes.
<br>Art.9(2)(e) The processing relates to personal data which have been manifestly made public by the data subject.
<br>Art.9(2)(f) The processing is necessary for the establishment, exercise or defence of legal claims, or for courts acting in their judicial capacity.
<br>Art.9(2)(g) The processing is necessary for reasons of substantial publicinterest, and occurs on the basis of a law that is, inter alia, proportionate to the aim pursued and protects the rights of data subjects.
<br>Art.9(2)(h), (3) The processing is required for the purpose of medical treatment undertaken by health professionals, including assessing the working capacity of employees and the management of health or social care systems and services.
<br>Art.9(2)(i) The processing is necessary for reasons of public interest in the area of public health (e.g., ensuring the safety of medicinal products).
<br>Art.9(2)(j) The processing is necessary for archiving purposes in the public interest, for historical, scientific, research or statistical purposes, subject to appropriate safeguards.
<br>Art.9(4) Member States may maintain or introduce further conditions, including limitations with regard to genetic data, biometric data or health data.
                '),
            ],
            'purpose' => [
                'label' => __('Purpose'),
                'editable' => true,
                'description' => __('Describe what business process requires this data.<br><br>See Rec.50; Art.5(1)(b), Rec.39; Art.5(1)(c)'),
            'help' => __('
ec.50; Art.5(1)(b)
Personal data may only be collected for specified, explicit and legitimate purposes and must not be further processed in a manner that is incompatible with those purposes. (Further processing of personal data for archiving purposes in the public interest, or scientific and historical research purposes or statistical purposes, in accordance with Art.89(1), is permitted—see Chapter 17).
<br><br>
Rec.39; Art.5(1)(c)
Personal data must be adequate, relevant and limited to what is necessary in relation to the purposes for which those data are processed.
                '),
            ],
            'right_to_be_informed' => [
                'label' => __('Right To be Informed'),
                'editable' => true,
                'description' => __('Describe how the right to be informed is fulfilled, bare in mind multiple constraints apply in regards of what needs to be communicated and how the identification of the data subject is performed. <br><br>See Rec.59; Art.12(2), Rec.57, 64; Art.12(2), (6), Rec.57; Art.11, 12(2), Rec.59; Art.12(3)-(4), Rec.58, 60; Art.13-14 and Rec.63; Art.15.'),
                'help' => __('
Rec.59; Art.12(2)
Controllers have a legal obligation to give effect to the rights of data subjects.
<br><br>
Rec.57, 64; Art.12(2), (6)
The controller must not refuse to give effect to the rights of a data subject unless the controller cannot identify the data subject. The controller must use all reasonable efforts to verify the identity of data subjects. Where the controller has reasonable doubts as to the identity of the data subject, the controller may request the provision of additional information necessary to confirm the identity of t  he data subject, but is not required to do so (see the row immediately below).
<br><br>

Rec.57; Art.11, 12(2)
To the extent that the controller can demonstrate that it is not in a position to identify the data subject, the controller is exempt from the application of the rights of data subjects in Art.15‑22. The controller is also not obliged to obtain further personal data in order to link data in its possession to a data subject.
<br><br>

Rec.59; Art.12(3)-(4)
A controller must, within one month of receiving a request made under those rights, provide any requested information in relation to any of the rights of data subjects. If the controller fails to meet this deadline, the data subject may complain to the relevant DPA and may seek a judicial remedy. Where a controller receives large numbers of requests, or especially complex requests, the time limit may be extended by a maximum of two further months.
<br><br>

Rec.58, 60; Art.13-14
Data subjects have the right to be provided with information on the identity of the controller, the reasons for processing their personal data and other relevant information necessary to ensure the fair and transparent processing of personal data.
                '),
            ],
            'data_subject' => [
                'label' => __('Data Subject'),
                'editable' => true,
                'description' => __('Who is the data Subject?'),
            ],
            'DataAssetGdprCollectionMethod' => [
                'label' => __('Collection Method'),
                'editable' => true,
                'options' => [$this, 'collectionMethods'],
                'description' => __('How is data collected? See Rec.15; Art.2(1)'),
                'help' => __('
Rec.15; Art.2(1)
The Directive applies to the processing of personal data:
by automatic means (e.g., a computerised system or database); and by other (non-automated) means that form part of a relevant filing system.The protection of individuals should be technologically neutral and should not depend on the techniques used.
                '),
            ],
            'volume' => [
                'label' => __('Volume'),
                'editable' => true,
                'description' => __('How much data is being created? Sometimes is also relevant to consider how much data is collected as part of the industry. If you are a telco and there are four big players on your market with equal share, you could describe 25% as the volume of data being collected.'),
            ],
            'recived_data' => [
                'label' => __('What exact data is received?'),
                'editable' => true,
                'description' => __('Describe in detail what data is being collected as part of this analysis'),
            ],
            'DataAssetGdprLawfulBase' => [
                'label' => __('What is the lawful base?'),
                'editable' => true,
                'options' => [$this, 'lawfulBases'],
                'description' => __('Choose one or more applicable lawful options for which this data is being collected.<br><br>See Rec.25; Art.4(11), Rec.39; Art.5(1)(a), Rec.50; Art.5(1)(b), Rec.39, 40, 41; Art.6(1), Rec.32, 42, 43; Art.6(1)(b), Rec.44; Art.6(1)(c), Rec.46; Art.6(1)(d), Rec.45; Art.6(1)(e), Rec.47, 48; Art.6(1)(f), Rec.40; Art.6(1), Rec.32; Art.4(11), 6(1)(a), 7, Rec.32, 43; Art.7(4), Rec.32; Art.6(1)(a), Rec.32, 42; Art.4(11), 7(1), Rec.32, Art.7(2), Rec.42; Art.7(1), Rec.42, 65; Art.7(3), Rec.111; Art.49(1)(a), (3) and Rec.171'),
                'renderHelper' => ['DataAssetGdpr', 'dataAssetGdprLawfulBaseField'],
                'help' => __('
Rec.25; Art.4(11)
The consent of the data subject means any freely given, specific, informed and unambiguous indication of his or her wishes by which the data subject, either by a statement or by a clear affirmative action, signifies agreement to personal data relating to them being processed.
<br><br>
Rec.39; Art.5(1)(a)
Personal data must be processed lawfully, fairly and in a transparent manner in relation to the data subject.
<br><br>
Rec.39, 40, 41; Art.6(1)
Personal may be processed only if, and to the extent that, at least one lawful basis applies.
<br><br>
Rec.32, 42, 43; Art.6(1)(a)
Processing is permitted if the data subject has consented to the processing.
<br><br>Rec.44; Art.6(1)(b)
Processing is permitted if it is necessary for the entry into, or performance of, a contract with the data subject or in order to take steps at his or her request prior to the entry into a contract.
<br><br>Rec.45; Art.6(1)(c)Processing is permitted if it is necessary for compliance with a legal obligation.<br><br>
Rec.46; Art.6(1)(d)
Processing is permitted if it is necessary in order to protect the vital interests of the data subject or of another natural person.
<br><br>
Rec.45; Art.6(1)(e)
Processing is permitted if it is necessary for the performance of a task carried out in the public interest or in the exercise of official authority vested in the controller.<br><br>Rec.47, 48; Art.6(1)(f)
Processing is permitted if it is necessary for the purposes of legitimate interests pursued by the controller (or by a third party), except where the controllers interests are overridden by the interests, fundamental rights or freedoms of the affected data subjects which require protection, particularly where the data subject is a child. This does not apply to processing carried out by public authorities in the performance of their duties.<br><br>
Rec.40; Art.6(1)
In order for the processing of personal data to be lawful, the controller requires either the consent of the data subject or another lawful basis.
<br><br>Rec.32; Art.4(11), 6(1)(a), 7
Consent means any freely given, specific, informed and unambiguous indication of the data subjects agreement to the processing of his or her personal data. Consent must be given by a statement or a clear affirmative action.
<br><br>Rec.32, 43; Art.7(4)
Consent will not be valid if the data subject has no genuine and free choice, or is unable to refuse or withdraw consent without detriment.
Where there is a clear imbalance between the controller and the data subject (e.g., between an employer and an employee), consent is presumed not to have been free
ly given. When assessing whether consent is freely given, utmost account must be taken of whether the performance of a contract is made conditional on the data subject consenting to processing activities that are not necessary for the performance of that contract.
<br><br>
Rec.32; Art.6(1)(a)
Consent must be specific. The GDPR does not explain this term further.
<br><br>
Rec.32, 42; Art.4(11), 7(1)
Consent must be informed. In order for consent to be informed:
the nature of the processing should be explained in an intelligible and easily accessible form, using clear and plain language which does not contain unfair terms; and the data subject should be aware at least of the identity of the controller and the purposes for which the personal data will be processed.
<br><br>
Rec.32
Consent must take the form of an affirmative action or statement. Consent can be provided by any appropriate method enabling a freely given, specific and informed indication of the data subjects wishes. For example, depending on the circumstances, valid consent could be provided verbally, in writing, by ticking a box on a web page, by choosing technical settings in an app, or by any other statement or conduct which clearly indicates in this context the data subjects acceptance of the proposed processing of their personal data.
<br><br>
Rec.32
Silence, pre-ticked boxes, inactivity, failure to opt-out, or passive acquiescence do not constitute valid consent.
<br><br>
Art.7(2)
If consent is given in the context of a written declaration which also concerns other matters, the request for consent must be presented in a manner which is clearly distinguishable from the other matters, in an intelligible and easily accessible form, using clear and plain language. If the data subject is asked to consent to something that is inconsistent with the requirements of the GDPR, that consent will not be binding.
<br><br>
Rec.42; Art.7(1)
Where any processing activity is performed on the basis of consent, the controller must be able to demonstrate that it has obtained valid consent from the affected data subjects.
<br><br>
Rec.42, 65; Art.7(3)
Data subjects have the right to withdraw their consent at any time. The withdrawal of consent does not affect the lawfulness of processing based on consent before its withdrawal. Prior to giving consent, the data subject must be informed of the right to withdraw consent. It must be as easy to withdraw consent as to give it.
<br><br>
Rec.111; Art.49(1)(a), (3)
In the absence of other safeguards, transfers may take place if the data subject has explicitly consented to the transfer, having previously been informed of its possible risks. This does not apply to public authorities in the exercise of their powers.
<br><br>
Rec.171
Where an organisation has already collected consent from data subjects (prior to the GDPR Effective Date) it is not necessary to collect that consent a second time in consequence of the GDPR, provided that the initial consent was compliant with the requirements of the GDPR.
                '),
            ],
            'contracts' => [
                'label' => __('Applicable Contracts, Code of Conducts and Privacy Notes'),
                'editable' => true,
                'description' => __('What contracts, policies, code of conducts and standards apply to this data collection process?<br><br>Code of Conducts - See Rec.74; Art.24, Rec.77, 81, 98-99; Art.40(2), Rec.98; Art.40(1), 57(1)(m), (p), (o), Rec.81; Art.40(1)(j), (3), 46(2)(e), Art.40(4), 41, Rec.77; Art.24(3), 28(5), 35(8), 46(2)(e), Art.83(2)(j), Art.40(5), 57(1)(p), 58(3)(d) and Art.40(8), 64(1)(b)'),
                'help' => __('
Rec.74; Art.24
The controller is responsible for implementing appropriate technical and organisational measures to ensure and to demonstrate that its processing activities are compliant with the requirements of the GDPR. These measures may include implementing an appropriate privacy policy. Adherence to approved Codes of Conduct (see Chapter 12) may provide evidence of compliance.
<br><br>
Rec.77, 81, 98-99; Art.40(2)
Associations and other industry bodies may prepare Codes of Conduct covering compliance with the GDPR, in respect of general or specific aspects of the GDPR.
<br><br>

Rec.98; Art.40(1), 57(1)(m), (p), (o)
Member States, DPAs and the EDPB are all obliged to encourage the drawing up of Codes of Conduct.
<br><br>

Rec.81; Art.40(1)(j), (3), 46(2)(e)
Controllers and processors that are outside the EEA, and that are not subject to the GDPR, may adhere to Codes of Conduct in order to create a framework for providing adequate protection to personal data in third countries. The GDPR specifically allows adherence of non-EEA controllers and processors to an approved Code of Conduct to provide the basis for Cross-Border Data Transfers (see Chapter 13).
<br><br>

Art.40(4), 41
An independent body may be appointed by the relevant DPA to monitor and enforce a Code of Conduct if it is:
<br><br>
independent and has demonstrated its expertise; has established procedures for reviewing and assessing compliance with a Code of Conduct; has established procedures for dealing with complaints or infringements of the Code of Conduct; and can demonstrate that it has no conflicts of interest in this role.Such a body may be appointed to monitor and enforce compliance with a Code of Conduct. DPAs still retain their own separate enforcement powers.

Rec.77; Art.24(3), 28(5), 35(8), 46(2)(e), Art.83(2)(j)
Adherence to an approved Code of Conduct:
<br><br>
may provide guidance on specific compliance issues; may provide evidence of compliance with the GDPR; is a positive factor in an Impact Assessment; may provide the basis for Cross-Border Data Transfers (see Chapter 13); and may affect any fines imposed upon the adherent controller or processor.

Art.40(5), 57(1)(p), 58(3)(d)
Draft Codes of Conduct must be submitted to the competent DPA, which must then:
<br><br>
approve the Code of Conduct if it provides sufficient protections in accordance with the GDPR, or amend it if it does not; register and publish approved Codes of Conduct; and publish the criteria for gaining such approval. If a Code of Conduct affects processing in several Member States, DPAs must review the Code of Conduct in accordance with the Consistency Mechanism (see Chapter 15) and refer it to the EDPB.

Art.40(8), 64(1)(b)
The EDPB is required to issue an opinion on any draft Code of Conduct before it is approved. The EDPB must also register and publish approved Codes of Conduct.

                '),
            ],
            'retention' => [
                'label' => __('Retention'),
                'editable' => true,
                'description' => __('Processors (and therefore controllers) must keep clear retention policies alligned with the lawfull base of data collection and purpose.<br><br>See Rec.82, 89; Art.30'),
                'help' => __('
Rec.82, 89; Art.30
There is no obligation to notify DPAs. Instead, each controller (and its representative, if any) must keep records of the controllers processing activities, including: the contact details of the controller/representative/ DPO; the purposes of the processing; the categories of data subjects and personal data processed; the categories of recipients with whom the data may be shared; information regarding Cross-Border Data Transfers; the applicable data retention periods; and a description of the security measures implemented in respect of the processed data.Upon request, these records must be disclosed to DPAs.
                '),
            ],
            'encryption' => [
                'label' => __('Encryption'),
                'editable' => true,
                'description' => __('How is data encrypted?<br><br>See Rec.83; Art.32,  Rec.73, 86-88; Art.34 and Art.28(1), (3)(e), (4), 32'),
            ],
            'right_to_erasure' => [
                'label' => __('Right to erasure'),
                'editable' => true,
                'description' => __('The right to erasure is also known as ‘the right to be forgotten’. The broad principle underpinning this right is to enable an individual to request the deletion or removal of personal data where there is no compelling reason for its continued processing. This right can be circumbented unless the personal data is no longer necessary in relation to the purpose for which it was originally collected/processed, the individual withdraws consent, the individual objects to the processing and there is no overriding legitimate interest for continuing the processing, The personal data was unlawfully processed, The personal data has to be erased in order to comply with a legal obligation, The personal data is processed in relation to the offer of information society services to a child.<br><br>See Rec.65-66, 68; Art.17.'),
                'help' => __('
Rec.65-66, 68; Art.17
Data subjects have the right to erasure of personal data (the right to be forgotten) if:
the data are no longerneeded for their original purpose (and no new lawful purpose exists); the lawful basis for theprocessing is the datasubjects consent, the data subject withdraws that consent, and no other lawful ground exists; the data subject exercises the right to object, and the controller has no overriding grounds for continuing the processing; the data have been processed unlawfully; or erasure is necessary forcompliance with EU law or the national law of the relevant Member State.
                '),
            ],
            'DataAssetGdprArchivingDriver' => [
                'label' => __('Archiving Drivers'),
                'editable' => true,
                'options' => [$this, 'archivingDrivers'],
                'description' => __('Personal data may be stored for longer periods insofar as the data will be processed solely for archiving purposes in the public interest, or scientific, historical, or statistical purposes.<br><br>See Rec.39; Art.5(1)(e).'),
                'renderHelper' => ['DataAssetGdpr', 'dataAssetGdprArchivingDriverField'],
                'help' => __(' 
Rec.39; Art.5(1)(e)
Personal data must be kept in a form that permits identification of data subjects for no longer than is necessary for the purposes for which the personal data are processed. Personal data may be stored for longer periods insofar as the data will be processed solely for archiving purposes in the public interest, or scientific, historical, or statistical purposes in accordance with Art.89(1) and subject to the implementation of appropriate safeguards.
                '),
            ],
            'archiving_driver_empty' => [
                'label' => __('Not applicable'),
                'editable' => false,
                'type' => 'toggle'
            ],
            'origin' => [
                'label' => __('Origin'),
                'editable' => true,
                'description' => __('Describe from which geographical region data will be obtained.'),
            ],
            'destination' => [
                'label' => __('Destination'),
                'editable' => true,
                'description' => __('Describe where data will be transferred'),
            ],
            'transfer_outside_eea' => [
                'label' => __('Data Transfers outside the EEA?'),
                'editable' => true,
                'type' => 'toggle',
                'description' => __('Cross-Border Data Transfers may only take place if the transfer is made to an Adequate Jurisdiction (see below) or the data exporter has implemented a lawful data transfer mechanism (or an exemption or derogation applies). <br><br>See Rec.101-116; Art.44, 45.'),
                'renderHelper' => ['DataAssetGdpr', 'transferOutsideEeaField'],
                'help' => __('
Rec.101-116; Art.44, 45
Cross-Border Data Transfers may only take place if the transfer is made to an Adequate Jurisdiction (see below) or the data exporter has implemented a lawful data transfer mechanism (or an exemption or derogation applies).
<br><br>
Rec.103-107; Art.44, 45
Cross-Border Data Transfers to a recipient in a third country may take place if the third country receives an Adequacy Decision from the Commission. Factors that may affect an Adequacy Decision include, inter alia:
the rule of law and legal protections for human rights and fundamental freedoms; access to transferred data by public authorities; existence and effective functioning of DPAs; and international commitments and other obligations in relation to the protection of personal data.The Commission may declare third countries (or a territory, a specified sector, or an international organisation) to be Adequate Jurisdictions.
                '),
            ],
            'ThirdPartyInvolved' => [
                'label' => __('Third Party Countries Involved'),
                'editable' => true,
                'options' => [$this->ThirdPartyInvolved, 'countries'],
                'description' => __('Select one or more countries where this data could be transfered to or simply checkbox the option above if data could move anywhere around the world.'),
                'renderHelper' => ['DataAssetGdpr', 'thirdPartyInvolvedField'],
            ],
            'third_party_involved_all' => [
                'label' => __('Anywhere in the world'),
                'type' => 'toggle',
                'editable' => false,
                'description' => __('Checkbox this option if data could be sent anywhere in the world.'),
            ],
            'DataAssetGdprThirdPartyCountry' => [
                'label' => __('Lawful Base for Transfers outside EEA'),
                'editable' => true,
                'options' => [$this, 'thirdPartyCountries'],
                'description' => __('If data will leave the European Economic Area (EEA) - what will be the legal base for that transfer?<br><br>See Rec.108; Art.46(2)(a), (3)(b), Rec.108, 110; Art.4(20) 46(2)(b), 47, Rec.108, 110; Art.47(1)-(3), Rec.108, 110; Art.47(1), 57(1)(s), Rec.81, 108-109; Art.28(6)-(8), 46(2)(c), 57(1)(j), (r), 93(2), Rec.108-109; Art.46(2)(d), 64(1)(d), 57(1)(j), (r), 93(2), Rec.108; Art.40, 41, 46(2)(e), Rec.108; Art.42, 43, 46(2)(f), Rec.108; Art.46(3)(a), (4), 63, Rec.108; Art.46(3)(b), (4), 63, Rec.115; Art.48, Rec.111; Art.49(1)(a), (3), Rec.111 Art.49(1)(b), (3), Rec.111; Art.49(1)(c), (3), Rec.111-112; Art.49(1)(d), (4), Rec.111; Art.49(1)(e), Rec.111-112; Art.49(1)(f), Rec.111; Art.49(1)(g), (2) and Rec.113; Art.49(1), (3), (6). '),
                'help' => __('
Rec.108; Art.46(2)(a), (3)(b)
Cross-Border Data Transfers between public authorities may take place on the basis of agreements between public authorities, which do not require any specific authorisation from a DPA. The public authorities must ensure compliance with GDPR requirements.
<br><br>

Rec.108, 110; Art.4(20) 46(2)(b), 47
The GDPR directly addresses the concept of BCRs. The competent DPA will approve BCRs as an appropriate mechanism for Cross-Border Data Transfers within a corporate group (including to members of that group that are established in third countries). If the BCRs meet the requirements set out in the GDPR, they will be approved, and no further DPA approval will be required for transfers of personal data made under the BCRs.
<br><br>

Rec.108, 110; Art.47(1)-(3)
BCRs must include a mechanism to make the BCRs legally binding on group companies. Among other things, the BCRs must: specify the purposes of the transfer and affected categories of data; reflect the requirements of the GDPR; confirm that the EU-based data exporters accept liability on behalf of the entire group; explain complaint procedures; and provide mechanisms for ensuring compliance (e.g., audits).
<br><br>

Rec.108, 110; Art.47(1), 57(1)(s)
The competent DPA must approve BCRs that fulfil the criteria set out in the GDPR. Where the BCRs are intended to cover data transfers from multiple Member States, the Consistency Mechanism applies (see Chapter 15).
<br><br>

Rec.81, 108-109; Art.28(6)-(8), 46(2)(c), 57(1)(j), (r), 93(2)
Cross-Border Data Transfers are permitted if the controller or processor adduces appropriate safeguards in the form of Model Clauses. These do not require any further authorisation from a DPA. The Commission may create new types of Model Clauses.
<br><br>

Rec.108-109; Art.46(2)(d), 64(1)(d), 57(1)(j), (r), 93(2)
A Cross-Border Data Transfer may take place on the basis of DPA Clauses, which offer a national alternative to the Commission-approved Model Clauses. Transfers made on the basis of DPA Clauses do not require further DPA approval. DPA Clauses may be included in a wider contract (e.g., from one processor to another), provided the original wording of the authorised DPA Clauses is not contradicted (directly or indirectly).
<br><br>

Rec.108; Art.40, 41, 46(2)(e)
A Cross-Border Data Transfer may take place on the basis of an approved Code of Conduct, together with binding and enforceable commitments to provide appropriate safeguards. Transfers made on this basis do not require DPA approval (although, as set out in Chapter 12, the Code of Conduct itself requires DPA approval).
<br><br>

Rec.108; Art.42, 43, 46(2)(f)
A Cross-Border Data Transfer may take place on the basis of certifications together with binding and enforceable commitments of the data importer to apply the certification to the transferred data. Transfers made on this basis do not require DPA approval (although, as set out in Chapter 12, the certification scheme itself requires DPA approval).
<br><br>
Rec.108; Art.46(3)(a), (4), 63
A Cross-Border Data Transfer may take place on the basis of ad hoc clauses. These clauses must conform to the requirements of the GDPR, and must be approved by the relevant DPA subject to the Consistency Mechanism, before transfers can begin.
<br><br>

Rec.108; Art.46(3)(b), (4), 63
Cross-Border Data Transfers may take place on the basis of administrative arrangements between public authorities (e.g., a Memorandum of Understanding), which include adequate protection for the rights of data subjects. Transfers made on this basis require DPA approval.
<br><br>

Rec.115; Art.48
A judgment from a third country, requiring a Cross-Border Data Transfer, only provides a lawful basis for such a transfer if the transfer is based on an appropriate international agreement, such as a Mutual Legal Assistance Treaty. However, this is without prejudice to other grounds for a transfer.
<br><br>

Rec.111; Art.49(1)(a), (3)
A Cross-Border Data Transfer may be made on the basis that the data subject, having been informed of the possible risks of such transfer, explicitly consents.
<br><br>

Rec.111 Art.49(1)(b), (3)
A Cross-Border Data Transfer may take place if the transfer is necessary for:
the performance of a contract between the data subject and the controller; or the implementation of pre‑contractual measures taken in response to the data subjects request.
<br><br>

Rec.111; Art.49(1)(c), (3)
A Cross-Border Data Transfer may take place if the transfer is necessary for the conclusion or performance of a contract between the controller and a third party, where it is in the interests of the data subject.
<br><br>

Rec.111-112; Art.49(1)(d), (4)
A Cross-Border Data Transfer may take place if the transfer is necessary for important reasons of public interest. Such interests must be recognised in EU law or in the law of the Member State to which the controller is subject.
<br><br>

Rec.111; Art.49(1)(e)
A Cross-Border Data Transfer may take place if the transfer is necessary for the establishment, exercise or defence of legal claims.
<br><br>

Rec.111-112; Art.49(1)(f)
A Cross-Border Data Transfer may take place if the transfer is necessary in order to protect the vital interests of the data subject or of other persons, where the data subject is physically or legally incapable of giving consent.
<br><br>
Rec.111; Art.49(1)(g), (2)
A Cross-Border Data Transfer may take place if the transferred data are taken from a register which is open to the public or, upon request, to any person who can demonstrate a legitimate interest in inspecting it. This does not permit a transfer of the entire register.
<br><br>
Rec.113; Art.49(1), (3), (6)
A Cross-Border Data Transfer may take place if: none of the other lawful bases applies; the transfer is not repetitive; it only concerns a limited number of data subjects; the transfer is necessary for the purposes of compelling legitimate interests pursued by the controller which are not overridden by those of the data subject; and the controller has adduced suitable safeguards for the transferred data.The controller must inform the relevant DPA and the data subjects about the transfer.
                '),
            ],
            'security' => [
                'label' => __('Security'),
                'editable' => true,
                'description' => __('Describe how data will be secured during transit to ensure its confidentiality, integrity and availability are not questioned.'),
            ],
            'right_to_portability' => [
                'label' => __('Right to Data Portability'),
                'editable' => true,
                'description' => __('The right to data portability allows individuals to obtain and reuse their personal data for their own purposes across different services.The right to data portability only applies to personal data an individual has provided to a controller, where the processing is based on the individual’s consent or for the performance of a contract and when processing is carried out by automated means.<br><br>See Rec.68, 73; Art.20.'),
                'help' => __('
Rec.68, 73; Art.20
Data subjects have a right to receive a copy of their personal data in a commonly used machine-readable format, and transfer their personal data from one controller to another or have the data transmitted directly between controllers.
                '),
            ],
            'stakeholders' => [
                'label' => __('Stakeholders'),
                'editable' => true,
                'description' => __('Who will have access to this data? Use BU, Third Parties options on the general tab to describe them if possible.'),
            ],
            'accuracy' => [
                'label' => __('Right to Restrict'),
                'editable' => true,
                'description' => __('Individuals have a right to ‘block’ or suppress processing of personal data. When processing is restricted, you are permitted to store the personal data, but not further process it. You can retain just enough information about the individual to ensure that the restriction is respected in future <br><br>See Rec.67; Art.18'),
                'help' => __('
ec.67; Art.18Data subjects have the right to restrict the processing of personal data (meaning that the data may only be held by the controller, and may only be used for limited purposes) if: the accuracy of the data is contested (and only for as long as it takes to verify that accuracy); the processing isunlawful and the datasubject requestsrestriction (as opposed to exercising the right to erasure); the controller no longerneeds the data for their original purpose, but the data are still required by the controller to establish, exercise or defend legal rights; or if verification ofoverriding grounds is pending, in the context of an erasure request.

Rec.62; Art.17(2), 19
Where a controller has disclosed personal data to any third parties, and the data subject has subsequently exercised any of the rights of rectification, erasure or blocking, the controller must notify those third parties of the data subjects exercising of those rights. The controller is exempt from this obligation if it is impossible or would require disproportionate effort. The data subject is also entitled to request information about the identities of those third parties. Where the controller has made the data public, and the data subject exercises these rights, the controller must take reasonable steps (taking costs into account) to inform third parties that the data subject has exercised those rights.
                '),
            ],
            'right_to_access' => [
                'label' => __('Right to Access'),
                'editable' => true,
                'description' => __('Under the GDPR, individuals will have the right to obtain confirmation that their data is being processed, access to their personal data and other supplementary information – this largely corresponds to the information that should be provided in a privacy notice. <br><br>See Rec.63; Art.15'),
                'help' => __('
Rec.63; Art.15
Data subjects have the right to obtain:
confirmation of whether, and where, the controller is processing their personal data; information about the purposes of the processing; information about the categories of data being processed; information about the categories of recipients with whom the data may be shared; information about theperiod for which thedata will be stored (or the criteria used to determine that period); information about theexistence of the rights to erasure, to rectification, to restriction ofprocessing and to object to processing; information about theexistence of the right to complain to the DPA; where the data were not collected from the data subject, information as to the source of the data; and information about theexistence of, and an explanation of the logic involved in, any automated processing that has a significant effect on data subjects.Additionally, data subjects may request a copy of the personal data being processed.
                '),
            ],
            'right_to_rectification' => [
                'label' => __('Right to Rectification'),
                'editable' => true,
                'description' => __('If you have disclosed the personal data in question to third parties, describe how you inform them of the rectification where possible and how you inform the individuals about the third parties to whom the data has been disclosed where appropriate. <br><br>See Rec. 59; Art.12(5), 15(3), (4)'),
                'help' => __('
Rec. 59; Art.12(5), 15(3), (4)
The controller must give effect to the rights of access, rectification, erasure and the right to object, free of charge. The controller may charge a reasonable fee for repetitive requests, manifestly unfounded or excessive requests or further copies.
<br><br>
Rec.39, 59, 65, 73; Art.5(1)(d), 16
Controllers must ensure that inaccurate or incomplete data are erased or rectified. Data subjects have the right to rectification of inaccurate personal data.
                '),
            ],
            'right_to_decision' => [
                'label' => __('Rights related to automated decision making and profiling'),
                'editable' => true,
                'description' => __('Identify whether any of your processing operations constitute automated decision making and consider whether you need to update your procedures to deal with the requirements of the GDPR.<br><br>See Rec.71, 75; Art.22'),
                'help' => __('
Rec.71, 75; Art.22
Data subjects have the right not to be subject to a decision based solely on automated processing which significantly affect them (including profiling). Such processing is permitted where:
it is necessary for entering into or performing a contract with the data subject provided that appropriate safeguards are in place; it is authorised by law; or the data subject hasexplicitly consented and appropriate safeguards are in place.
                '),
            ],
            'right_to_object' => [
                'label' => __('Right to Object'),
                'editable' => true,
                'description' => __('Individuals have the right to object to processing based on legitimate interests or the performance of a task in the public interest/exercise of official authority (including profiling); direct marketing (including profiling); and processing for purposes of scientific/historical research and statistics.<br><br>See Rec.70; Art.21(2)-(3) and Rec.156; Art.21(6), 83(1).'),
                'help' => __('
Rec.50, 59, 69-70, 73; Art.21Data subjects have the right to object, on grounds relating to their particular situation, to the processing of personal data, where the basis for that processing is either: public interest; or legitimate interests of the controller.The controller must cease such processing unless the controller:
demonstrates compelling legitimate grounds for the processing which override the interests, rights and freedoms of the data subject; or requires the data in order to establish, exercise or defend legal rights
<br><br>
Rec.70; Art.21(2)-(3)
Data subjects have the right to object to the processing of personal data for the purpose of direct marketing, including profiling.
<br><br>

Rec.156; Art.21(6), 83(1)
Where personal data are processed for scientific and historical research purposes or statistical purposes, the data subject has the right to object, unless the processing is necessary for the performance of a task carried out for reasons of public interest.
<br><br>

Art.13(2)(b), 14(2)(c), 15(1)(e), 21(4)
The right to object to processing of personal data noted above must be communicated to the data subject no later than the time of the first communication with the data subject.
This information should be provided clearly and separately from any other information provided to the data subject.
                '),
            ],
        ];

        parent::__construct($id, $table, $ds);
    }

    public function checkThirdPartyInvolved($check) {
        $result = true;

        if (!empty($this->data['DataAssetGdpr']['transfer_outside_eea']) 
            && empty($this->data['DataAssetGdpr']['ThirdPartyInvolved']) 
            && empty($this->data['DataAssetGdpr']['third_party_involved_all'])
        ) {
            $result = false;
        }

        return $result;
    }

    public function checkThirdPartyCountry($check) {
        $result = true;

        if (!empty($this->data['DataAssetGdpr']['transfer_outside_eea']) 
            && empty($this->data['DataAssetGdpr']['DataAssetGdprThirdPartyCountry'])
        ) {
            $result = false;
        }
        
        return $result;   
    }

    public function beforeValidate($options = array()) {
        // has many validation hot fix
        $fieldList = [
            'DataAssetGdprDataType',
            'DataAssetGdprCollectionMethod',
            'DataAssetGdprLawfulBase',
            'DataAssetGdprThirdPartyCountry',
            'DataAssetGdprArchivingDriver',
            'ThirdPartyInvolved',
        ];

        foreach ($fieldList as $field) {
            if (isset($this->data[$field])) {
                $this->data['DataAssetGdpr'][$field] = $this->data[$field];
                unset($this->data[$field]);
            }
        }

        if (!empty($this->data['DataAssetGdpr']['third_party_involved_all'])) {
            unset($this->validate['ThirdPartyInvolved']);
        }

        if (empty($this->data['DataAssetGdpr']['transfer_outside_eea'])) {
            unset($this->validate['DataAssetGdprThirdPartyCountry']);
            unset($this->validate['ThirdPartyInvolved']);
        }

        return true;
    }

    public function beforeSave($options = array()) {
        return true;
    }

    public function afterSave($created, $options = array()) {
        $this->saveHasMany();
    }

    /**
     * save hasMany associations
     */
    protected function saveHasMany() {
        if (isset($this->data['DataAssetGdprDataType'])) {
            $this->DataAssetGdprDataType->deleteAll(['data_asset_gdpr_id' => $this->id]);
            foreach ($this->data['DataAssetGdprDataType'] as $value) {
                $itme = [
                    'data_asset_gdpr_id' => $this->id,
                    'data_type' => $value
                ];
                $this->DataAssetGdprDataType->create();
                $this->DataAssetGdprDataType->save($itme);
            }
        }
        if (isset($this->data['DataAssetGdprCollectionMethod'])) {
            $this->DataAssetGdprCollectionMethod->deleteAll(['data_asset_gdpr_id' => $this->id]);
            foreach ($this->data['DataAssetGdprCollectionMethod'] as $value) {
                $itme = [
                    'data_asset_gdpr_id' => $this->id,
                    'collection_method' => $value
                ];
                $this->DataAssetGdprCollectionMethod->create();
                $this->DataAssetGdprCollectionMethod->save($itme);
            }
        }
        if (isset($this->data['DataAssetGdprLawfulBase'])) {
            $this->DataAssetGdprLawfulBase->deleteAll(['data_asset_gdpr_id' => $this->id]);
            foreach ($this->data['DataAssetGdprLawfulBase'] as $value) {
                $itme = [
                    'data_asset_gdpr_id' => $this->id,
                    'lawful_base' => $value
                ];
                $this->DataAssetGdprLawfulBase->create();
                $this->DataAssetGdprLawfulBase->save($itme);
            }
        }
        if (isset($this->data['DataAssetGdprThirdPartyCountry'])) {
            $this->DataAssetGdprThirdPartyCountry->deleteAll(['data_asset_gdpr_id' => $this->id]);
            foreach ($this->data['DataAssetGdprThirdPartyCountry'] as $value) {
                $itme = [
                    'data_asset_gdpr_id' => $this->id,
                    'third_party_country' => $value
                ];
                $this->DataAssetGdprThirdPartyCountry->create();
                $this->DataAssetGdprThirdPartyCountry->save($itme);
            }
        }
        if (isset($this->data['DataAssetGdprArchivingDriver']) || isset($this->data['DataAssetGdpr']['archiving_driver_empty'])) {
            $this->DataAssetGdprArchivingDriver->deleteAll(['data_asset_gdpr_id' => $this->id]);
            if (!empty($this->data['DataAssetGdprArchivingDriver'])) {
                foreach ($this->data['DataAssetGdprArchivingDriver'] as $value) {
                    $itme = [
                        'data_asset_gdpr_id' => $this->id,
                        'archiving_driver' => $value
                    ];
                    $this->DataAssetGdprArchivingDriver->create();
                    $this->DataAssetGdprArchivingDriver->save($itme);
                }
            }
        }

        if (isset($this->data['ThirdPartyInvolved']) || isset($this->data['DataAssetGdpr']['third_party_involved_all'])) {
            $this->ThirdPartyInvolved->deleteAll([
                'foreign_key' => $this->id,
                'model' => 'DataAssetGdpr',
                'type' => Country::TYPE_DATA_ASSET_GDPR_THIRD_PARTY_INVOLVED,
            ]);
            if (!empty($this->data['ThirdPartyInvolved'])) {
                foreach ($this->data['ThirdPartyInvolved'] as $value) {
                    $item = [
                        'model' => 'DataAssetGdpr',
                        'type' => Country::TYPE_DATA_ASSET_GDPR_THIRD_PARTY_INVOLVED,
                        'foreign_key' => $this->id,
                        'country_id' => $value
                    ];
                    $this->ThirdPartyInvolved->create();
                    $this->ThirdPartyInvolved->save($item);
                }
            }
        }
    }

    public function setValidation($dataAssetStatus) {
        $this->validate = $this->validateGroups[$dataAssetStatus];
    }

    public static function dataTypes($value = null) {
        return DataAssetGdprDataType::dataTypes($value);
    }

    public static function collectionMethods($value = null) {
        return DataAssetGdprCollectionMethod::collectionMethods($value);
    }

    public static function lawfulBases($value = null) {
        return DataAssetGdprLawfulBase::lawfulBases($value);
    }

    public static function thirdPartyCountries($value = null) {
        return DataAssetGdprThirdPartyCountry::thirdPartyCountries($value);
    }

    public static function archivingDrivers($value = null) {
        return DataAssetGdprArchivingDriver::archivingDrivers($value);
    }

    public function getBusinessUnits() {
        return $this->DataAsset->BusinessUnit->getList();
    }

    public function getDataAssets() {
        return $this->DataAsset->getList();
    }

    public function getUsers() {
        $this->DataAsset->DataAssetInstance->DataAssetSetting->Dpo->virtualFields['full_name'] = 'CONCAT(Dpo.name, " ", Dpo.surname)';
        $data = $this->DataAsset->DataAssetInstance->DataAssetSetting->Dpo->find('list', [
            'fields' => ['Dpo.id', 'Dpo.full_name'],
        ]);

        return $data;
    }

    public function getEuropeCountries() {
        return Country::europeCountries();
    }

    public function getCountries() {
        return Country::countries();
    }
}
