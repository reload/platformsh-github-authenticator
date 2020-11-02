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

use Lpdigital\Github\Entity\Release;
use Lpdigital\Github\Entity\User;

class ReleaseEvent extends RepositoryAwareEventType implements ActionableEventInterface
{
    public $action;

    /**
     * @var Release
     */
    public $release;

    public function getAction()
    {
        return $this->action;
    }

    public static function name()
    {
        return 'ReleaseEvent';
    }

    public static function fields()
    {
        return ['action', 'release', 'sender', 'repository'];
    }

    public function createFromData($data)
    {
        parent::createFromData($data);

        $this->action = $data['action'];
        $sender = User::createFromData($data['sender']);
        $this->release = Release::createFromData(
            $data['release'],
            $this->repository,
            $sender
        );

        return $this;
    }
}
