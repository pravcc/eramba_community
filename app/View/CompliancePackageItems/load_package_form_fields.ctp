<?php
if (empty($this->request->data['CompliancePackageItem']['compliance_package_id'])) {
	echo $this->FieldData->input($CompliancePackageCollection->package_id);
	echo $this->FieldData->input($CompliancePackageCollection->name);
	echo $this->FieldData->input($CompliancePackageCollection->description);
} else {
	// echo $this->Alerts->info(__('You selected an existing Compliance Package instead of adding a new one.'));
}
?>