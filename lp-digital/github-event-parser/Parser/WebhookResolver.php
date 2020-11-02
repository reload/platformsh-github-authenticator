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

namespace Lpdigital\Github\Parser;

use Lpdigital\Github\Exception\EventNotFoundException;

class WebhookResolver
{
    public function resolve(array $data)
    {
        foreach ($this->eventsType() as $eventType) {
            if ($eventType['class']::isValid($data)) {
                return (new $eventType['class']())->createFromData($data);
            }
        }

        throw new EventNotFoundException();
    }

    public function eventsType()
    {
        $classes = [
            'Lpdigital\Github\EventType\IssuesEvent',
            'Lpdigital\Github\EventType\IssueCommentEvent',
            'Lpdigital\Github\EventType\ForkEvent',
            'Lpdigital\Github\EventType\DeploymentStatusEvent',
            'Lpdigital\Github\EventType\GollumEvent',
            'Lpdigital\Github\EventType\IntegrationInstallationEvent',
            'Lpdigital\Github\EventType\IntegrationInstallationRepositoriesEvent',
            'Lpdigital\Github\EventType\PullRequestEvent',
            'Lpdigital\Github\EventType\PullRequestReviewCommentEvent',
            'Lpdigital\Github\EventType\PushEvent',
            'Lpdigital\Github\EventType\ReleaseEvent',
            'Lpdigital\Github\EventType\StatusEvent',
            'Lpdigital\Github\EventType\WatchEvent',
        ];

        $eventTypes = [];

        foreach ($classes as $className) {
            $name = $className::name();
            $fields = $className::fields();

            $eventTypes[$name] = ['class' => $className, 'priority' => count($fields)];
        }

        usort($eventTypes, function ($a, $b) {
            if ($a['priority'] == $b['priority']) {
                return 0;
            }

            return ($a['priority'] < $b['priority']) ? 1 : -1;
        });

        return $eventTypes;
    }
}
