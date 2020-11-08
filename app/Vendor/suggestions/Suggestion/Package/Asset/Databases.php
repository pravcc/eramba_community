<?php
namespace Suggestion\Package\Asset;
use Suggestion\Package\BusinessUnit\It;
use Suggestion\Package\Legal\ISO27001;
use Suggestion\Package\AssetLabel\Confidentiality;

class Databases extends BasePackage {
	public $alias = 'Databases';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Databases');

		$this->data = array(
			'name' => $this->name,
			'business_unit_id' => array(
				new It(),
			),
			'asset_label_id' => new Confidentiality,
			'asset_owner_id' => new It(),
			'asset_guardian_id' => new It(), 
			'asset_user_id' => BU_EVERYONE,
			'description' => __('Database engines such as MySQL, Oracle and others used to support application stacks.'),
			'asset_media_type_id' => ASSET_MEDIA_TYPE_SOFTWARE,
			'asset_classification_id' => '',
			'legal_id' => array( new ISO27001() ),
			'review' => date('Y-m-d')
		);
	}
}
