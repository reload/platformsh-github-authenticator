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

use Lpdigital\Github\Entity\Deployment;
use Lpdigital\Github\Entity\User;

class DeploymentStatusEvent extends RepositoryAwareEventType
{
    /**
     * @var Deployment
     */
    public $deployment;

    /**
     * @var User
     */
    public $sender;

    public static function name()
    {
        return 'DeploymentStatusEvent';
    }

    public static function fields()
    {
        return ['deployment_status', 'deployment', 'repository'];
    }

    public function createFromData($data)
    {
        parent::createFromData($data);

        $this->deployment = Deployment::createFromData($data['deployment']);
        $this->sender = User::createFromData($data['sender']);

        return $this;
    }
}
