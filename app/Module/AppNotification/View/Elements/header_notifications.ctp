<?php
if (!isset($appNotification)) {
	return;
}
?>
<li>
	<a href="#"
        id="app-notification-list-toggle"
        class="dropdown-toggle"
        data-toggle="dropdown"
        data-yjs-request="AppNotification/toggleClick"
        data-yjs-event-on="click"
        data-yjs-use-loader="false"
    >
		<i class="icon-bell2"></i>
		<span class="visible-xs-inline-block position-right"><?= __('Calendar') ?></span>
		<?php if ($appNotification['alertsCount']) : ?>
			<span class="badge bg-warning-400 app-notification-list-unseen-count"><?= $appNotification['alertsCount'] ?></span>
		<?php endif; ?>
	</a>
    <div class="dropdown-menu dropdown-content app-notification-list">
        <div
            data-yjs-request="AppNotification/init"
            data-yjs-event-on="init"
            data-yjs-use-loader="false"
        >
        </div>
        <div class="dropdown-content-heading">
            <?= __('Notifications') ?>
        </div>
        <ul class="media-list dropdown-content-body width-350"
            id="app-notification-list-ul"
            data-yjs-request="crud/load"
            data-yjs-target="#app-notification-list-ul"
            data-yjs-target-placement="append"
            data-yjs-event-on=""
            data-yjs-use-loader="false"
            data-yjs-datasource-url="<?= Router::url(['admin' => false, 'plugin' => 'app_notification', 'controller' => 'appNotifications', 'action' => 'list', strtotime($appNotification['lastViewDatetime'])]) ?>"
        >
            <li class="text-center app-notification-list-loader">
                <i class="icon-spinner4 spinner"></i>
            </li>
        </ul>
    </div>
</li>
