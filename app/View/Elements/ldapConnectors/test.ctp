<div class="pb-20">
	<?php
	if ($ldapConnection !== true) {
		echo $this->element('not_found', array(
			'message' => $ldapConnection
		));
	}
	?>

	<?php if (!empty($results)) : ?>
		<p>
			<strong>
				<?php
				echo __('Size limit set to max %s results.', $limit);
				?>
			</strong>
		</p>
		<?php if (is_array($results)) : ?>
			<?php foreach ($results	as $result) : ?>
				<pre><?php print_r($result); ?></pre>
			<?php endforeach; ?>
		<?php else : ?>
			<pre><?php print_r($results); ?></pre>
		<?php endif; ?>
	<?php else : ?>
		<?php
		echo $this->element('not_found', array(
			'message' => __('No results returned')
		));
		?>
	<?php endif; ?>
</div>