
<?php
if (!empty($this->request->data['ComplianceAnalysisFinding']['ThirdParty'])) {
	$hiddenField = true;
    foreach ($this->request->data['ComplianceAnalysisFinding']['ThirdParty'] as $index => $package) {
        echo $this->element('../ComplianceAnalysisFindings/load_item', [
            'cpID'=>$package,
            'hiddenField' => $hiddenField
        ]);

        $hiddenField = false;
    }
}?>