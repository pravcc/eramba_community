<?php
$url = array('plugin' => null, 'controller' => 'users', 'action' => 'useticket', $token);
if (!empty($redirect)) {
	$url['?']['redirect'] = $redirect;
}
$url = Router::url($url, true);
?>
<div style="color: #505050; font-family: Arial; font-size: 14px; line-height: 150%; text-align: left;" align="left">
	<?php 
	echo __('We have received a password recovery request for your account. Change your password '); 
	
	echo $this->Html->link(__('here'), $url) .' '. __('or use the link below within 24 hours.')
	?> 
	<br />
	<br />
	<?php echo $this->Html->link($url, $url); ?>
	<br />
	<br />
	<?php echo __('If you have received this email by mistake, please ignore it!'); ?>
	<br />
	<?php echo __('If you need more help, just reply to this email.'); ?>
	<br />
	<br />
	<b><?php echo __('Cheers'); ?></b>
	<br />
	<b><?php echo __('Your friends at Eramba'); ?></b>
</div>