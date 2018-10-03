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
namespace FacturaScripts\Plugins\ChatEngine\Lib;

use FacturaScripts\Core\Base\DataBase\DataBaseWhere;
use FacturaScripts\Dinamic\Lib\WebPortal\SearchEngine;
use FacturaScripts\Plugins\ChatEngine\Model\ChatKnowledge;
use FacturaScripts\Plugins\webportal\Model\WebSearch;

/**
 * Description of ChatEngine
 *
 * @author Carlos García Gómez
 */
class ChatEngine
{

    const ALTERNATIVE_MESSAGE = "No tengo respuesta para eso en mi base de datos, pero he encontrado esto:\n\n";

    /**
     * 
     * @param string $question
     *
     * @return array
     */
    public function ask($question)
    {
        $responses = [];
        $this->findKnowledge($responses, $question);

        if (empty($responses)) {
            $this->findAlternativeKnowledge($responses, $question);
        }

        /// sort by certainty and score
        usort($responses, function($item1, $item2) {
            if ($item1['certainty'] == $item2['certainty']) {
                if ($item1['score'] == $item2['score']) {
                    return 0;
                } else if ($item1['score'] > $item2['score']) {
                    return -1;
                }

                return 1;
            } else if ($item1['certainty'] > $item2['certainty']) {
                return -1;
            }

            return 1;
        });

        return empty($responses) ? $this->newResponse() : $responses[0];
    }

    /**
     * 
     * @param array  $responses
     * @param string $question
     */
    protected function findAlternativeKnowledge(&$responses, $question)
    {
        $searchEngine = new SearchEngine();
        foreach ($this->matchWebSearches($question) as $key) {
            foreach ($searchEngine->search($key) as $result) {
                $html = self::ALTERNATIVE_MESSAGE . $result['title'] . ' ' . $result['description'];

                $response = $this->newResponse();
                $response['link'] = $result['link'];
                $response['score'] -= $result['position'];
                $response['text'] = $html;
                $responses[] = $response;
            }
        }
    }

    /**
     * 
     * @param array  $responses
     * @param string $question
     */
    protected function findKnowledge(&$responses, $question)
    {
        $chatKnowledge = new ChatKnowledge();
        foreach ($chatKnowledge->all([], [], 0, 0) as $knowledge) {
            $match = $knowledge->match($question);
            if ($match === 0) {
                continue;
            }

            $response = $this->newResponse();
            $response['certainty'] = $knowledge->certainty;
            $response['score'] += $match;
            $response['text'] = $knowledge->answer;
            $responses[] = $response;
        }
    }

    protected function matchWebSearches($question)
    {
        $keys = [];

        $webSearch = new WebSearch();
        $where = [new DataBaseWhere('numresults', 0, '>')];
        foreach ($webSearch->all($where, ['visitcount' => 'DESC']) as $search) {
            if (false !== stripos($question, $search->query)) {
                $keys[] = $search->query;
            }
        }

        return $keys;
    }

    /**
     * 
     * @return array
     */
    protected function newResponse()
    {
        return [
            'certainty' => 0,
            'link' => '',
            'score' => 0,
            'text' => 'Lo siento, no puedo entenderte.',
        ];
    }
}
