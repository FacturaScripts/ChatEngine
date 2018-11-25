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
namespace FacturaScripts\Plugins\ChatEngine;

use FacturaScripts\Core\Base\CronClass;
use FacturaScripts\Core\Base\DataBase\DataBaseWhere;
use FacturaScripts\Plugins\ChatEngine\Model\ChatSession;

/**
 * Define the tasks of ChatEngine's crons.
 * 
 * @author Carlos Garcia Gomez <carlos@facturascripts.com>
 */
class Cron extends CronClass
{

    public function run()
    {
        if ($this->isTimeForJob('remove-empty-chats', '1 day')) {
            $this->removeEmptyChats();
            $this->jobDone('remove-empty-chats');
        }
    }

    protected function removeEmptyChats()
    {
        $session = new ChatSession();
        $where = [new DataBaseWhere('messagesnumber', 0)];
        foreach ($session->all($where, [], 0, 0) as $ses) {
            $ses->delete();
        }
    }
}
