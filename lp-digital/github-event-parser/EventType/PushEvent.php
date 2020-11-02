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

class PushEvent extends RepositoryAwareEventType
{
    public $before;
    public $commits;
    public $distinctSize;
    public $head;
    public $pusher;
    public $ref;

    /**
     * @var User
     */
    public $sender;
    public $size;

    public static function name()
    {
        return 'PushEvent';
    }

    public static function fields()
    {
        return ['ref', 'head', 'before', 'commits'];
    }

    public function createFromData($data)
    {
        parent::createFromData($data);

        $this->before = $data['before'];
        $this->commits = $data['commits'];
        $this->distinctSize = $data['distinct_size'];
        $this->head = $data['head'];
        $this->pusher = $data['pusher']['name'];
        $this->ref = $data['ref'];
        $this->sender = User::createFromData($data['sender']);
        $this->size = $data['size'];

        return $this;
    }
}
