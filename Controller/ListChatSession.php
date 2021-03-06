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

use FacturaScripts\Core\Lib\ExtendedController;

/**
 * Description of ListChatSession
 *
 * @author Carlos García Gómez
 */
class ListChatSession extends ExtendedController\ListController
{

    /**
     * Returns basic page attributes
     *
     * @return array
     */
    public function getPageData()
    {
        $pageData = parent::getPageData();
        $pageData['title'] = 'chat-messages';
        $pageData['menu'] = 'web';
        $pageData['icon'] = 'fas fa-comments';

        return $pageData;
    }

    /**
     * Load views
     */
    protected function createViews()
    {
        /// sessions
        $this->addView('ListChatSession', 'ChatSession', 'chat-sessions', 'fas fa-comment-dots');
        $this->addOrderBy('ListChatSession', ['idchat'], 'code');
        $this->addOrderBy('ListChatSession', ['creationtime'], 'date', 2);
        $this->addOrderBy('ListChatSession', ['certainty'], 'certainty');
        $this->addOrderBy('ListChatSession', ['messagesnumber'], 'messages-number');
        $this->addSearchFields('ListChatSession', ['idchat']);

        /// messages
        $this->addView('ListChatMessage', 'ChatMessage', 'chat-messages', 'fas fa-comments');
        $this->addSearchFields('ListChatMessage', ['content']);
        $this->addOrderBy('ListChatMessage', ['idchat'], 'code');
        $this->addOrderBy('ListChatMessage', ['creationtime'], 'date', 2);
        $this->addFilterCheckbox('ListChatMessage', 'unmatched', 'unmatched', 'unmatched');
        $this->addFilterCheckbox('ListChatMessage', 'ischatbot', 'chatbot', 'ischatbot');
        $this->addFilterCheckbox('ListChatMessage', 'nochatbot', 'human', 'ischatbot', true);

        /// knowledge
        $this->addView('ListChatKnowledge', 'ChatKnowledge', 'chat-knowledge', 'fas fa-database');
        $this->addSearchFields('ListChatKnowledge', ['keywords', 'bannedwords', 'answer']);
        $this->addOrderBy('ListChatKnowledge', ['creationdate'], 'date', 2);
        $this->addOrderBy('ListChatKnowledge', ['certainty'], 'certainty');
    }
}
