<?php echo __( 'Hello!' ); ?>
<br />
<br />
<?php echo __('You should come and complete feedback for every item associated to your name. Click <a href="%s">here</a> to view items.', $url); ?>
<br>
<br>
<?php if (!empty($items)) : ?>
    <?php echo __('Your list of items:'); ?>
    <br>
    <ul>
    	<?php foreach ($items as $item) : ?>
    		<li><?php echo $item; ?></li>
    	<?php endforeach; ?>
    </ul>
    <br />
    <br />
<?php endif; ?>
<b><?php echo __('Cheers'); ?></b>
<br />
<b><?php echo __('Your friends at Eramba'); ?></b>