<?php
if (!empty($this->request->data['CompliancePackage']['compliance_package_regulator_id'])) {
	echo $this->FieldData->input($FieldDataCollection->compliance_package_id);
} else {
	echo $this->Alerts->info(__('First you have to choose a Third Party to proceed in the form.'));
}
?>