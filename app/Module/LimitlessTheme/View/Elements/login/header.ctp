<div class="text-center mb-20">
	<div>
		<?= $this->Eramba->getLogo(DEFAULT_LOGO_URL); ?>
	</div>
	<?php if (!empty($loginTitle)) : ?>
		<h3 class="pt0 pb-10">
			<?= $loginTitle ?>
		</h3>
	<?php endif; ?>
</div>