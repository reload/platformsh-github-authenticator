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
        $sha = $head['sha'];
        $base = $pullRequest->getBase();
        list($owner, $repository) = explode('/', $base['repo']['full_name']);
        $this->statusUpdater->createStatus($owner, $repository, $sha, $status);
    }
}
