<?php
App::uses('ItemDataEntity', 'FieldData.Model/FieldData');

class ComplianceManagementItemData extends ItemDataEntity
{
	public function __construct(Model $Model, $data)
	{
		parent::__construct($Model, $data);
	}

	public function getCompliancePackageRegulatorId()
	{
		return $this->CompliancePackageItem->CompliancePackage->compliance_package_regulator_id;
	}
}