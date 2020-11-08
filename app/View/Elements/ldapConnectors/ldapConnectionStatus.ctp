<?php
if (isset($ldapConnection) && $ldapConnection !== true) {
	echo $this->element('not_found', array(
		'message' => $ldapConnection
	));

	return false;
}
?>