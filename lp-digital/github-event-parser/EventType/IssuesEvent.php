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

use Lpdigital\Github\Entity\Issue;
use Lpdigital\Github\Entity\Label;
use Lpdigital\Github\Entity\Repository;
use Lpdigital\Github\Entity\User;

class IssuesEvent extends RepositoryAwareEventType implements ActionableEventInterface
{
    public $action;

    /**
     * @var User|null
     */
    public $assignee;

    /**
     * @var Issue
     */
    public $issue;

    /**
     * @var Label|null
     */
    public $label;

    /**
     * @var User
     */
    public $sender;

    public function getAction()
    {
        return $this->action;
    }

    public static function name()
    {
        return 'IssuesEvent';
    }

    public static function fields()
    {
        return ['action', 'issue'];
    }

    public function createFromData($data)
    {
        parent::createFromData($data);

        $this->action = $data['action'];
        $this->assignee = isset($data['assignee']) ? User::createFromData($data['assignee']) : null;
        $this->label = isset($data['label']) ? Label::createFromData($data['label']) : null;
        $this->issue = Issue::createFromData($data['issue']);
        $this->repository = Repository::createFromData($data['repository']);
        $this->sender = User::createFromData($data['sender']);

        return $this;
    }

    public function isAssigned()
    {
        return 'assigned' === $this->action;
    }

    public function isUnassigned()
    {
        return 'unassigned' === $this->action;
    }

    public function isLabeled()
    {
        return 'labeled' === $this->action;
    }

    public function isUnlabeled()
    {
        return 'unlabeled' === $this->action;
    }

    public function isOpened()
    {
        return 'opened' === $this->action;
    }

    public function isClosed()
    {
        return 'closed' === $this->action;
    }

    public function isReopened()
    {
        return 'reopened' === $this->action;
    }
}
