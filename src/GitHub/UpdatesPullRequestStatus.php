<?php

declare(strict_types=1);

namespace App\GitHub;

use Lpdigital\Github\Entity\PullRequest;

trait UpdatesPullRequestStatus
{

    /* @var \App\GitHub\StatusUpdater */
    private $statusUpdater;

    protected function updateStatus(PullRequest $pullRequest, Status $status)
    {
        $head = $pullRequest->getHead();
        list($owner, $repository) = explode('/', $head['repo']['full_name']);
        $sha = $head['sha'];
        $this->statusUpdater->createStatus($owner, $repository, $sha, $status);
    }
}
