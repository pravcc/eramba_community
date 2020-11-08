<?php
App::uses('DefaultNotification', 'NotificationSystem.Lib/NotificationSystem');

class WidgetObjectNotification extends DefaultNotification
{
    public function initialize()
    {
        $this->_label = __('New Comment or Attachment');

        $this->emailSubject = __('New Comment or Attachment for item: %s', $this->_displayFieldMacro());
        $this->emailBody = __('Hello,

            A new %%WIDGET_OBJECT_TYPE%% has been included on for the item "%s":

            <i>%%WIDGET_OBJECT_CONTENT%%</i>

            To respond the %%WIDGET_OBJECT_TYPE%% please follow the link below, login in eramba with your credentials and once you see the item click on the menu options and then "Comments & Attachments".

            %%ITEM_URL%%

            Regards
        ', $this->_displayFieldMacro());
    }

    public function getMacros()
    {
        return parent::getMacros() + [
            'WIDGET_OBJECT_TYPE' => __('Object Type'),
            'WIDGET_OBJECT_CONTENT' => __('Comment Content / Attachment Filename'),
        ];
    }
}