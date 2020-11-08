<?php echo __('Hello!'); ?>
<br />
<br />
<?php
echo __('A notification <strong>%s</strong> has been triggered. You can find it <a href="%s">here</a>', $notificationTitle, $url);
?>
<br />
<br />

<strong><?php echo __('Plan details:'); ?></strong>
<br />
<strong><?php echo __('Objective:'); ?></strong> <?php echo $itemData['BusinessContinuityPlan']['objective']; ?>
<br />
<strong><?php echo __('Sponsor:'); ?></strong> <?php echo $itemData['BusinessContinuityPlan']['Sponsor']['name'] . ' ' . $itemData['BusinessContinuityPlan']['Sponsor']['surname']; ?>
<br />
<strong><?php echo __('Launch Criteria:'); ?></strong> <?php echo $itemData['BusinessContinuityPlan']['launch_criteria']; ?>
<br />
<br />

<strong><?php echo __('Task details:'); ?></strong>
<br />
<strong><?php echo __('Who:'); ?></strong> <?php echo $itemData['BusinessContinuityTask']['who']; ?>
<br />
<strong><?php echo __('When:'); ?></strong> <?php echo $itemData['BusinessContinuityTask']['when']; ?>
<br />
<strong><?php echo __('Does:'); ?></strong> <?php echo $itemData['BusinessContinuityTask']['does']; ?>
<br />
<strong><?php echo __('Where:'); ?></strong> <?php echo $itemData['BusinessContinuityTask']['where']; ?>
<br />
<strong><?php echo __('How:'); ?></strong> <?php echo $itemData['BusinessContinuityTask']['how']; ?>

<br />
<br />
<b><?php echo __('Cheers'); ?></b>
<br />
<b><?php echo __('Your friends at Eramba'); ?></b>