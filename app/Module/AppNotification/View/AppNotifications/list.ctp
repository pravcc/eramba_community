<?php if (!empty($appNotifications)) : ?>
    <?php
    foreach ($appNotifications as $item) {
        echo $item->renderListItem($this);
    }
    ?>
    <?php if ($this->Paginator->hasNext()) : ?>
        <li class="text-center app-notification-list-loader app-notification-list-next"
            data-yjs-request="AppNotification/unlockLoad"
            data-yjs-event-on="init"
            data-yjs-use-loader="false"
        >
            <div class="app-notification-list-next-request"
                data-yjs-request="crud/load"
                data-yjs-target="#app-notification-list-ul"
                data-yjs-target-placement="append"
                data-yjs-event-on=""
                data-yjs-use-loader="false"
                data-yjs-datasource-url="<?= Router::url(['admin' => false, 'plugin' => 'app_notification', 'controller' => 'appNotifications', 'action' => 'list', $viewTimestamp,  'page' => ($this->request->params['paging']['AppNotification']['page']+1)]) ?>"
            >
            </div>
            <i class="icon-spinner4 spinner"></i>
        </li>
    <?php endif; ?>
<?php else : ?>
    <li class="text-center app-notification-list-empty">
        <?= __('No notifications') ?>
    </li>
<?php endif; ?>
