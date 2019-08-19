<?php

declare(strict_types=1);

namespace App\GitHub;

use Github\Client;

class StatusUpdater
{

    /* @var \Github\Client */
    private $github;

    /* @var string */
    private $context;

    public function __construct(Client $github, string $context)
    {
        $this->github = $github;
        $this->context = $context;
    }

    public function createStatus(
        string $owner,
        string $repository,
        string $sha,
        Status $status
    ): void {
        $params = $status->withContext($this->context)->toParams();
        $this->github->repository()->statuses()->create($owner, $repository, $sha, $params);
    }
}
