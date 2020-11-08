<?php if (!empty($data)) : ?>
	<?php
	$policyNames = array();
	foreach ($data as $policy) {
		$policyNames[] = $policy['SecurityPolicy']['index'];
	}
	$names = implode(', ', $policyNames);
	$warning = __('The Risk you have selected contains the following incident handling procedure: %s', $names);

	echo $this->Alerts->danger($warning);
	?>
<?php endif; ?>
