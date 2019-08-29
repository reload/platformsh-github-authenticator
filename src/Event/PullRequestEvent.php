<?php

declare(strict_types=1);

namespace App\Event;

use Lpdigital\Github\Entity\PullRequest;

abstract class PullRequestEvent
{

    private $pullRequest;

    public function __construct(PullRequest $pullRequest)
    {
        $this->pullRequest = $pullRequest;
    }

    public function getPullRequest(): PullRequest
    {
        return $this->pullRequest;
    }
}
