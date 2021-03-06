<?php
/**
 * This file is part of ChatEngine plugin for FacturaScripts.
 * Copyright (C) 2018 Carlos Garcia Gomez  <carlos@facturascripts.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
namespace FacturaScripts\Plugins\ChatEngine\Controller;

use FacturaScripts\Core\Base\DataBase\DataBaseWhere;
use FacturaScripts\Core\Lib\ExtendedController;

/**
 * Description of EditChatSession
 *
 * @author Carlos García Gómez
 */
class EditChatSession extends ExtendedController\EditController
{

    /**
     * 
     * @return string
     */
    public function getModelClassName()
    {
        return 'ChatSession';
    }

    /**
     * Returns basic page attributes
     *
     * @return array
     */
    public function getPageData()
    {
        $pageData = parent::getPageData();
        $pageData['title'] = 'chat-session';
        $pageData['menu'] = 'web';
        $pageData['showonmenu'] = false;
        $pageData['icon'] = 'fas fa-comment-dots';

        return $pageData;
    }

    /**
     * Load views
     */
    protected function createViews()
    {
        parent::createViews();
        $this->setTabsPosition('bottom');

        /// messages
        $this->addEditListView('ListChatMessage', 'ChatMessage', 'chat-messages', 'fas fa-comments');
        $this->views['ListChatMessage']->disableColumn('code', true);
    }

    /**
     * Load data view procedure
     *
     * @param string $keyView
     * @param ExtendedController\BaseView $view
     */
    protected function loadData($keyView, $view)
    {
        switch ($keyView) {
            case 'ListChatMessage':
                $idchat = $this->getViewModelValue('EditChatSession', 'idchat');
                $view->loadData(false, [new DataBaseWhere('idchat', $idchat)], ['creationtime' => 'ASC']);
                break;

            default:
                parent::loadData($keyView, $view);
                break;
        }
    }
}
