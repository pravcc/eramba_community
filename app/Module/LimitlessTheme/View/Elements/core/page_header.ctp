<?php if (!isset($hidePageHeader)): ?>
	<!-- Page header -->
	<div class="page-header page-header-default">
		<div class="page-header-content">
			<div class="page-title">
				<h4><i class="icon-stack position-left"></i> <?= $title_for_layout; ?></h4>
				<p class="text-muted"><?= isset( $subtitle_for_layout ) ? $subtitle_for_layout : ''; ?>
			</div>
		</div>

		<div id="main-toolbar"
			class="breadcrumb-line"
			data-yjs-request="crud/showForm"
            data-yjs-target="#main-toolbar"
            data-yjs-datasource-url="<?= Router::url(['?' => ['toolbar' => true]]) ?>"
		>
			<?= $this->element($layout_toolbarPath); ?>
		</div>
	</div>
	<!-- /page header -->
<?php else: ?>
	<div class="margin-top"></div>
<?php endif; ?>