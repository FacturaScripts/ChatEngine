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
 * Description of ChatKnowledge
 *
 * @author Carlos García Gómez
 */
class ChatKnowledge extends Base\ModelClass
{

    use Base\ModelTrait;

    const MAX_LEVENSHTEIN_DISTANCE = 1;
    const MAX_LEVENSHTEIN_LEN = 150;

    /**
     *
     * @var string
     */
    public $answer;

    /**
     *
     * @var string
     */
    public $bannedwords;

    /**
     *
     * @var int
     */
    public $certainty;

    /**
     * Creation date.
     *
     * @var int
     */
    public $creationdate;

    /**
     * Chat knowledge identifier.
     *
     * @var int
     */
    public $idknowledge;

    /**
     *
     * @var string
     */
    public $keywords;

    /**
     * Reset the values of all model properties.
     */
    public function clear()
    {
        parent::clear();
        $this->certainty = 100;
        $this->creationdate = date('d-m-Y');
    }

    /**
     * 
     * @param string $question
     *
     * @return int
     */
    public function match($question)
    {
        $keywords = $this->getKeywords($this->keywords);
        if (empty($keywords)) {
            return 0;
        }

        $match = 0;
        foreach ($keywords as $keys) {
            $found = false;
            foreach ($keys as $key) {
                /// same or similar word
                if (false !== stripos($question, $key) || $this->deepMatch($question, $key)) {
                    $found = true;
                    break;
                }
            }

            $match = $found ? $match + 1 : 0;
        }

        /// banned words
        foreach ($this->getKeywords($this->bannedwords) as $banwords) {
            foreach ($banwords as $banned) {
                if (false !== stripos($question, $banned)) {
                    $match = 0;
                    break;
                }
            }
        }

        return $match;
    }

    /**
     * Returns the name of the column that is the primary key of the model.
     *
     * @return string
     */
    public static function primaryColumn()
    {
        return 'idknowledge';
    }

    /**
     * Returns the name of the table that uses this model.
     *
     * @return string
     */
    public static function tableName()
    {
        return 'chatknowledge';
    }

    /**
     * Returns True if there is no errors on properties values.
     *
     * @return bool
     */
    public function test()
    {
        $this->answer = Utils::noHtml($this->answer);
        $this->bannedwords = Utils::noHtml($this->bannedwords);
        $this->keywords = Utils::noHtml($this->keywords);
        return parent::test();
    }

    /**
     * 
     * @param string $type
     * @param string $list
     *
     * @return string
     */
    public function url(string $type = 'auto', string $list = 'ListChatSession?activetab=List')
    {
        return parent::url($type, $list);
    }

    /**
     * 
     * @param string $question
     * @param string $word
     *
     * @return boolean
     */
    protected function deepMatch($question, $word)
    {
        $changes = array('/à/' => 'a', '/á/' => 'a', '/â/' => 'a', '/ã/' => 'a', '/ä/' => 'a',
            '/å/' => 'a', '/æ/' => 'ae', '/ç/' => 'c', '/è/' => 'e', '/é/' => 'e', '/ê/' => 'e',
            '/ë/' => 'e', '/ì/' => 'i', '/í/' => 'i', '/î/' => 'i', '/ï/' => 'i', '/ð/' => 'd',
            '/ñ/' => 'n', '/ò/' => 'o', '/ó/' => 'o', '/ô/' => 'o', '/õ/' => 'o', '/ö/' => 'o',
            '/ő/' => 'o', '/ø/' => 'o', '/ù/' => 'u', '/ú/' => 'u', '/û/' => 'u', '/ü/' => 'u',
            '/ű/' => 'u', '/ý/' => 'y', '/þ/' => 'th', '/ÿ/' => 'y',
            '/&quot;/' => '-'
        );
        $text = preg_replace(array_keys($changes), $changes, strtolower($question));
        $key = preg_replace(array_keys($changes), $changes, strtolower($word));

        if (false !== strpos($text, $key)) {
            return true;
        }

        /// string too long
        if (strlen($key) > self::MAX_LEVENSHTEIN_DISTANCE || strlen($text) > self::MAX_LEVENSHTEIN_DISTANCE) {
            return false;
        }

        $distance = levenshtein($text, $key);
        return $distance == self::MAX_LEVENSHTEIN_DISTANCE;
    }

    /**
     * 
     * @param string $string
     *
     * @return array
     */
    protected function getKeywords($string)
    {
        $keys = [];
        foreach (explode(',', $string) as $split) {
            $keys[] = explode('|', $split);
        }

        return $keys;
    }
}
