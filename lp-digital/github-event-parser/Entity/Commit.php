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

namespace Lpdigital\Github\Entity;

/**
 * Partial representation of Commit GitHub API.
 *
 * @doc https://developer.github.com/v3/git/commits/
 */
class Commit
{
    private $sha;
    private $url;

    /**
     * @var CommitUser
     */
    private $author;

    /**
     * @var CommitUser
     */
    private $committer;
    private $message;
    private $tree;

    public static function createFromData(array $data)
    {
        return new static($data);
    }

    public function __construct($data)
    {
        $this->url = $data['url'];
        $this->author = CommitUser::createFromData($data['author']);
        $this->committer = CommitUser::createFromData($data['committer']);
        $this->message = $data['message'];
        $this->tree = $data['tree'];
    }

    public function setSha($sha)
    {
        $this->sha = $sha;

        return $this;
    }

    public function getSha()
    {
        return $this->sha;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function getCommitter()
    {
        return $this->committer;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getTree()
    {
        return $this->tree;
    }
}
