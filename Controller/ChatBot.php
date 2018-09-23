<?php
/**
 * This file is part of ChatEngine plugin for FacturaScripts.
 * Copyright (C) 2018 Carlos Garcia Gomez <carlos@facturascripts.com>
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
use FacturaScripts\Plugins\webportal\Lib\WebPortal\PortalController;
use FacturaScripts\Plugins\ChatEngine\Model\ChatMessage;
use FacturaScripts\Plugins\ChatEngine\Model\ChatSession;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * Description of ChatBot
 *
 * @author Carlos García Gómez
 */
class ChatBot extends PortalController
{

    /**
     * All messages in current chat session.
     * 
     * @var ChatMessage[]
     */
    public $messages = [];

    /**
     *
     * @var ChatSession
     */
    public $session;

    /**
     * Returns basic page attributes
     *
     * @return array
     */
    public function getPageData()
    {
        $pageData = parent::getPageData();
        $pageData['title'] = 'chatbot';
        $pageData['menu'] = 'web';
        $pageData['icon'] = 'fa-commenting-o';

        return $pageData;
    }

    /**
     * Runs the controller's private logic.
     *
     * @param \Symfony\Component\HttpFoundation\Response      $response
     * @param \FacturaScripts\Dinamic\Model\User              $user
     * @param \FacturaScripts\Core\Base\ControllerPermissions $permissions
     */
    public function privateCore(&$response, $user, $permissions)
    {
        parent::privateCore($response, $user, $permissions);
        $this->setTemplate('ChatBot');
        $this->getChatMessages();
        $this->processChat();
    }

    /**
     * Execute the public part of the controller.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     */
    public function publicCore(&$response)
    {
        parent::publicCore($response);
        $this->setTemplate('ChatBot');
        $this->getChatMessages();
        $this->processChat();
    }

    protected function answerUnknown()
    {
        ;
    }

    /**
     * Return all chat messages with this user.
     */
    protected function getChatMessages()
    {
        $chatMessage = new ChatMessage();
        $chatSession = $this->getChatSession();
        $where = [new DataBaseWhere('idchat', $chatSession->idchat)];
        $this->messages = array_reverse($chatMessage->all($where, ['creationtime' => 'DESC']));
    }

    protected function getChatSession()
    {
        if (isset($this->session)) {
            return $this->session;
        }

        $this->session = new ChatSession();
        $sessionId = $this->request->cookies->get('chatSessionId', '');
        if (!empty($sessionId) && $this->session->loadFromCode($sessionId)) {
            return $this->session;
        }

        if ($this->contact) {
            $this->session->idcontacto = $this->contact->idcontacto;
        }

        if ($this->session->save()) {
            $expire = time() + self::PUBLIC_COOKIES_EXPIRE;
            $this->response->headers->setCookie(new Cookie('chatSessionId', $this->session->idchat, $expire));
        }

        return $this->session;
    }

    /**
     * Saves new chat message (answer or reply).
     *
     * @param string $content
     * @param bool   $unmatched
     * @param bool   $isChatbot
     */
    protected function newChatMessage(string $content, bool $unmatched = false, bool $isChatbot = false)
    {
        $chatMessage = new ChatMessage();
        $chatMessage->content = $content;
        $chatMessage->idchat = $this->session->idchat;
        $chatMessage->ischatbot = $isChatbot;
        $chatMessage->unmatched = $unmatched;

        if ($isChatbot) {
            $chatMessage->creationtime++;
        } else {
            $chatMessage->idcontacto = is_null($this->contact) ? null : $this->contact->idcontacto;
            $this->session->content = $content;
        }

        if ($chatMessage->save()) {
            $this->messages[] = $chatMessage;

            $this->session->lastmodtime = time();
            $this->session->save();
        }
    }

    /**
     * Process answer and reply.
     */
    protected function processChat()
    {
        $userInput = $this->request->request->get('question', '');
        if (empty($userInput)) {
            /// no message
            return;
        }

        $botMessage = 'XXXX';
        if ('' === $botMessage) {
            $this->newChatMessage($userInput, true);
            $this->answerUnknown();
        } else {
            $this->newChatMessage($userInput);
            $this->newChatMessage($botMessage, false, true);
        }
    }
}
