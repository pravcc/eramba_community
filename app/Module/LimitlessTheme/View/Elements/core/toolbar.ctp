<?php
if (!empty($useNewBreadcrumbs)) {
	echo $this->element('LimitlessTheme.toolbar/breadcrumbs_new');
}
else {
	echo $this->element('LimitlessTheme.toolbar/breadcrumbs');
}
?>

<?php
// $cacheKey = 'view_' . $currentModel . '_action_' . $this->request->params['action'] . '_user_' . $logged['id'];

// if (($layoutToolbar = Cache::read($cacheKey, 'layout_toolbar')) === false) {

    $subject = null;
    if (!empty($Section)) {
        $subject = $Section->getSubject();
    }

	$layoutToolbar = $this->LayoutToolbar->render(null, $subject);

	// Cache::write($cacheKey, $layoutToolbar, 'layout_toolbar');
// }

echo $layoutToolbar;
?>