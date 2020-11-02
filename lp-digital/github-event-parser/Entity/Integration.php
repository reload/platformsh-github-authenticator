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

class Integration
{
    /**
     * @var int
     */
    private $installationId;

    /**
     * @var User
     */
    private $account;

    /**
     * @var string
     */
    private $accessTokenUrl;

    /**
     * @var string
     */
    private $repositoriesUrl;

    /**
     * @var string
     */
    private $htmlUrl;

    /**
     * @param array $data
     *
     * @return self
     */
    public static function createFromData(array $data)
    {
        return new static($data);
    }

    public function __construct(array $data)
    {
        $this->installationId = $data['id'];
        $this->account = isset($data['account']) ? User::createFromData($data['account']) : null;
        $this->accessTokenUrl = isset($data['access_tokens_url']) ? $data['access_tokens_url'] : null;
        $this->repositoriesUrl = isset($data['repositories_url']) ? $data['repositories_url'] : null;
        $this->htmlUrl = isset($data['html_url']) ? $data['html_url'] : null;
    }

    /**
     * @return int
     */
    public function getInstallationId()
    {
        return $this->installationId;
    }

    /**
     * @return User
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @return string
     */
    public function getAccessTokenUrl()
    {
        return $this->accessTokenUrl;
    }

    /**
     * @return string
     */
    public function getRepositoriesUrl()
    {
        return $this->repositoriesUrl;
    }

    /**
     * @return string
     */
    public function getHtmlUrl()
    {
        return $this->htmlUrl;
    }
}
