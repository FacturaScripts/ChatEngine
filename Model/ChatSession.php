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
use FacturaScripts\Dinamic\Model\Contacto;

/**
 * Description of ChatSession
 *
 * @author Carlos García Gómez
 */
class ChatSession extends Base\ModelClass
{

    use Base\ModelTrait;

    /**
     * Message content.
     *
     * @var string
     */
    public $content;

    /**
     * Creation time, in seconds.
     *
     * @var int
     */
    public $creationtime;

    /**
     * Primary key.
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
     *
     * @var int
     */
    public $lastmodtime;

    /**
     * Reset the values of all model properties.
     */
    public function clear()
    {
        parent::clear();
        $this->creationtime = date('d-m-Y H:i:s');
        $this->lastmodtime = date('d-m-Y H:i:s');
    }

    public function install()
    {
        new Contacto();
        return parent::install();
    }

    /**
     * Returns the name of the column that is the primary key of the model.
     *
     * @return string
     */
    public static function primaryColumn()
    {
        return 'idchat';
    }

    /**
     * Returns the name of the table that uses this model.
     *
     * @return string
     */
    public static function tableName()
    {
        return 'chatsessions';
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
        $time = time() - $this->lastmodtime;
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
}
