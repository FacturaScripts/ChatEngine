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

use FacturaScripts\Plugins\ChatEngine\Model\ChatKnowledge;

/**
 * Description of ChatEngine
 *
 * @author Carlos García Gómez
 */
class ChatEngine
{

    /**
     * 
     * @param string $question
     *
     * @return array
     */
    public function ask($question)
    {
        $response = [
            'link' => '',
            'match' => 0,
            'text' => 'Lo siento, no puedo entenderte.',
            'unknown' => true,
        ];

        $chatKnowledge = new ChatKnowledge();
        foreach ($chatKnowledge->all() as $knowledge) {
            $match = $knowledge->match($question);
            if ($match > $response['match']) {
                $response['match'] = $match;
                $response['text'] = $knowledge->answer;
                $response['unknown'] = false;
            }
        }

        if ($response['unknown']) {
            $this->findAlternativeKnowledge($response, $question);
        }

        return $response;
    }

    /**
     * 
     * @param array  $response
     * @param string $question
     */
    protected function findAlternativeKnowledge(&$response, $question)
    {
        ;
    }
}
