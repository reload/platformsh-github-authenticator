<?php

declare(strict_types=1);

namespace App\Event;

use App\GitHub\Status;
use App\GitHub\StatusUpdater;
use App\GitHub\UpdatesPullRequestStatus;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class GitHubDeploymentStartedNotificer implements MessageHandlerInterface
{
    use UpdatesPullRequestStatus;

    public function __construct(StatusUpdater $statusUpdater)
    {
        $this->statusUpdater = $statusUpdater;
    }

    public function __invoke(PullRequestAuthorized $event)
    {
        $status = (new Status('pending'))
            ->withDescription('Deployment started');
        $this->updateStatus($event->getPullRequest(), $status);
    }
}
