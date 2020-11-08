<?php
App::uses('LayoutToolbarHelper', 'LimitlessTheme.View/Helper');
App::uses('CakeEvent', 'Event');

class ItemDropdownHelper extends LayoutToolbarHelper
{
    /**
     * Render of all items in nested toolbar navigation.
     *
     * @param mixed $data Event data.
     * @param mixed $subject Event subject.
     * @return string Toolbar nested navigation.
     */
    public function render($data = null, $subject = null)
    {
        $event = new CakeEvent('ItemDropdown.beforeRender', $subject, $data);

        $this->_View->getEventManager()->dispatch($event);

        $items = $this->removeForbiddenItems($this->_toolbarItems);

        if (empty($items)) {
            return false;
        }

        $isSubsection = $this->_View->get('isSubsection');

        return $this->renderList($items, [
            'class' => ['dropdown-menu', 'dropdown-menu-left', 'dropdown-menu-filter-item'],
            'style' => ($isSubsection === true) ? 'z-index: 1051;' : ''
        ]);
    }

    /**
     * HTML render of item.
     *
     * @param array $item Item configuration.
     * @return string Toolbar item/link.
     */
    public function renderItem($item)
    {
        $content = $item->name;

        $elemOptions = array_merge([
            'class' => [],
        ], $item->liOptions);

        $linkOptions = array_merge([
            'escape' => false
        ], $item->options);

        //icon
        if (!empty($item->options['icon'])) {
            $content = $this->Icon->icon($item->options['icon'], [
                'class' => ['position-left']
            ]) . ' ' . $content;
        }

        //notification badge
        if (isset($item->options['notification'])) {
            $content .= $this->Html->tag('span', $item->options['notification'], [
                'class' => ['badge', 'badge-counter', 'position-right'],
            ]);
        }

        //submenu
        $subnav = '';
        if (!empty($item->children)) {
            $linkOptions['data-toggle'] = 'dropdown';
            $linkOptions['class'][] = 'dropdown-toggle';

            $elemOptions['class'][] = 'dropdown-submenu dropdown-submenu-right';
            
            $subnav = $this->renderList($item->children, [
                'class' => ['dropdown-menu', 'dropdown-menu-right']
            ]);
        }

        unset($linkOptions['icon']);
        unset($linkOptions['notification']);

        if ($item->url === false) {
            $item->url = '#';
            $linkOptions['class'][] = 'cursor-not-allowed';
        }

        //link
        if (!empty($linkOptions['post'])) {
            $link = $this->Form->postLink($content, $item->url, $linkOptions);
        }
        else {
            $link = $this->Html->link($content, $item->url, $linkOptions);
        }

        //li element
        $elem = $this->Html->tag('li', $link . $subnav, $elemOptions);

        return $elem;
    }
}
