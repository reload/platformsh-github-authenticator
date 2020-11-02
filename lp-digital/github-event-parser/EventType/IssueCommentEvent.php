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

use Lpdigital\Github\Entity\Comment;
use Lpdigital\Github\Entity\Issue;
use Lpdigital\Github\Entity\User;

class IssueCommentEvent extends RepositoryAwareEventType implements ActionableEventInterface
{
    public $action;

    /**
     * @var Issue
     */
    public $issue;

    /**
     * @var User
     */
    public $user;

    /**
     * @var Comment
     */
    public $comment;

    public function getAction()
    {
        return $this->action;
    }

    public static function name()
    {
        return 'IssueCommentEvent';
    }

    public static function fields()
    {
        return ['action', 'issue', 'comment'];
    }

    public function createFromData($data)
    {
        parent::createFromData($data);

        $this->action = $data['action'];
        $this->issue = Issue::createFromData($data['issue']);
        $this->comment = Comment::createFromData($data['comment']);
        $this->user = $this->comment->getUser();

        return $this;
    }
}
