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

class Release
{
    private $url;
    private $assetsUrl;
    private $uploadUrl;
    private $htmlUrl;
    private $id;
    private $tagName;
    private $targetCommitish;
    private $name;
    private $isDraft;
    private $author;
    private $isPreRelease;
    private $createdAt;
    private $publishedAt;
    private $assets;
    private $tarballUrl;
    private $zipballUrl;
    private $body;
    private $repository;
    private $sender;

    public static function createFromData(array $data, Repository $repository, User $sender)
    {
        return new static($data, $repository, $sender);
    }

    public function __construct(array $data, Repository $repository, User $sender)
    {
        $this->url = $data['url'];
        $this->assetsUrl = $data['assets_url'];
        $this->uploadUrl = $data['upload_url'];
        $this->htmlUrl = $data['html_url'];
        $this->id = $data['id'];
        $this->tagName = $data['tag_name'];
        $this->targetCommitish = $data['target_commitish'];
        $this->name = $data['name'];
        $this->isDraft = $data['draft'];
        $this->author = User::createFromData($data['author']);
        $this->isPreRelease = $data['prerelease'];
        $this->createdAt = $data['created_at'];
        $this->publishedAt = $data['published_at'];
        $this->assets = $data['assets'];
        $this->tarballUrl = $data['tarball_url'];
        $this->zipballUrl = $data['zipball_url'];
        $this->body = $data['body'];
        $this->repository = $repository;
        $this->sender = $sender;
    }

    /**
     * @return mixed
     */
    public function getAssetsUrl()
    {
        return $this->assetsUrl;
    }

    /**
     * @return mixed
     */
    public function getUploadUrl()
    {
        return $this->uploadUrl;
    }

    /**
     * @return mixed
     */
    public function getHtmlUrl()
    {
        return $this->htmlUrl;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getTagName()
    {
        return $this->tagName;
    }

    /**
     * @return mixed
     */
    public function getTargetCommitish()
    {
        return $this->targetCommitish;
    }

    /**
     * @return mixed
     */
    public function isDraft()
    {
        return $this->isDraft;
    }

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @return mixed
     */
    public function isPreRelease()
    {
        return $this->isPreRelease;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return mixed
     */
    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    /**
     * @return mixed
     */
    public function getAssets()
    {
        return $this->assets;
    }

    /**
     * @return mixed
     */
    public function getTarballUrl()
    {
        return $this->tarballUrl;
    }

    /**
     * @return mixed
     */
    public function getZipballUrl()
    {
        return $this->zipballUrl;
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return mixed
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @return mixed
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Gets the value of url.
     *
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Gets the value of name.
     *
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }
}
