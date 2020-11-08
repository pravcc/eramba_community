<?php
echo nl2br($body);
?>

<br />
<br />

<?php
if (!empty($feedbackUrl)) {
	echo __('Provide feedback <a href="%s">here</a>.', $feedbackUrl);
}
?>