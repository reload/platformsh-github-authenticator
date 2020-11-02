<?php

/*
 * Copyright (c) 2015 Lp digital system
 *
 * This file is part of Github Parser library.
 *
 * Github Parser is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Github Parser is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Github Parser. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Lpdigital\Github\EventType;

use Lpdigital\Github\Entity\User;

class IntegrationInstallationRepositoriesEvent extends AbstractEventType implements ActionableEventInterface
{
    public $action;

    /**
     * @var User
     */
    public $sender;

    /**
     * @var string
     */
    public $repositorySelection;

    /**
     * @var array
     */
    public $repositoryAdded;

    /**
     * @var array
     */
    public $repositoryRemoved;

    /**
     * @return string action name
     */
    public function getAction()
    {
        return $this->action;
    }

    public static function name()
    {
        return 'IntegrationInstallationRepositoriesEvent';
    }

    public static function fields()
    {
        return ['action', 'installation', 'repository_selection', 'repositories_added', 'repositories_removed'];
    }

    public function createFromData($data)
    {
        parent::createFromData($data);

        $this->action = $data['action'];
        $this->sender = User::createFromData($data['sender']);

        $this->repositorySelection = $data['repository_selection'];
        $this->repositoryAdded = $data['repositories_added'];
        $this->repositoryRemoved = $data['repositories_removed'];

        return $this;
    }
}
