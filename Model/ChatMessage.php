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
namespace FacturaScripts\Plugins\ChatEngine\Model;

use FacturaScripts\Core\Base\Utils;
use FacturaScripts\Core\Model\Base;

/**
 * Description of ChatMessage
 *
 * @author Carlos García Gómez
 */
class ChatMessage extends Base\ModelClass
{

    use Base\ModelTrait;

    /**
     *
     * @var int
     */
    public $certainty;

    /**
     *
     * @var string
     */
    public $chatvars;

    /**
     * Message content.
     *
     * @var string
     */
    public $content;

    /**
     * Creation time.
     *
     * @var string
     */
    public $creationtime;

    /**
     * Chat session identifier.
     *
     * @var int
     */
    public $idchat;

    /**
     *
     * @var int
     */
    public $idcontacto;

    /**
     * Primary key.
     *
     * @var int
     */
    public $idmessage;

    /**
     * To identify chatbot messages,
     *
     * @var bool
     */
    public $ischatbot;

    /**
     * Reset the values of all model properties.
     */
    public function clear()
    {
        parent::clear();
        $this->certainty = 0;
        $this->creationtime = date('d-m-Y H:i:s');
        $this->ischatbot = false;
    }

    /**
     * 
     * @return ChatSession
     */
    public function getChatSession()
    {
        $chatSession = new ChatSession();
        $chatSession->loadFromCode($this->idchat);
        return $chatSession;
    }

    /**
     * 
     * @return array
     */
    public function getChatVars()
    {
        return json_decode($this->chatvars, true);
    }

    /**
     * 
     * @return string
     */
    public function install()
    {
        /// needed dependencies
        new ChatSession();

        return parent::install();
    }

    /**
     * Returns the name of the column that is the primary key of the model.
     *
     * @return string
     */
    public static function primaryColumn()
    {
        return 'idmessage';
    }

    /**
     * 
     * @param array $vars
     */
    public function setChatVars($vars)
    {
        $this->chatvars = json_encode($vars);
    }

    /**
     * Returns the name of the table that uses this model.
     *
     * @return string
     */
    public static function tableName()
    {
        return 'chatmessages';
    }

    /**
     * Returns True if there is no errors on properties values.
     *
     * @return bool
     */
    public function test()
    {
        $this->content = Utils::noHtml($this->content);
        return parent::test();
    }

    /**
     * Return time since now.
     *
     * @return string
     */
    public function timesince()
    {
        $time = time() - strtotime($this->creationtime);
        $finalTime = ($time < 1) ? 1 : $time;
        $tokens = array(
            31536000 => 'year',
            2592000 => 'month',
            604800 => 'week',
            86400 => 'day',
            3600 => 'hour',
            60 => 'minute',
            1 => 'second'
        );

        foreach ($tokens as $unit => $text) {
            if ($finalTime < $unit) {
                continue;
            }

            $numberOfUnits = floor($finalTime / $unit);
            return $numberOfUnits . ' ' . $text . (($numberOfUnits > 1) ? 's' : '');
        }
    }

    /**
     * 
     * @param string $type
     * @param string $list
     *
     * @return string
     */
    public function url(string $type = 'auto', string $list = 'List')
    {
        $chatSession = $this->getChatSession();
        return $chatSession->url($type, $list);
    }
}
