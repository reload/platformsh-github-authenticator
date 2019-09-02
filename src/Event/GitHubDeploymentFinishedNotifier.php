<?php

declare(strict_types=1);

namespace App\Event;

use App\GitHub\Status;
use App\GitHub\StatusUpdater;
use App\GitHub\UpdatesPullRequestStatus;
use App\Platformsh\EnvironmentManager;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use function Safe\sprintf as sprintf;

class GitHubDeploymentFinishedNotifier implements MessageHandlerInterface
{
    use UpdatesPullRequestStatus;

    /* @var \App\Platformsh\EnvironmentManager */
    private $environmentManager;

    public function __construct(StatusUpdater $statusUpdater, EnvironmentManager $environmentManager)
    {
        $this->statusUpdater = $statusUpdater;
        $this->environmentManager = $environmentManager;
    }

    public function __invoke(PullRequestSynchronized $event)
    {
        $id = $event->getPullRequest()->getHead()['ref'];
        if (!$this->environmentManager->isReady($id)) {
            throw new \RuntimeException(sprintf('Environment %s not ready', $id));
        }
        $status = (new Status('success'))
            ->withDescription('Deployment completed')
            ->withTargetUrl($this->environmentManager->getEnvironmentUrl($id));
        $this->updateStatus($event->getPullRequest(), $status);
    }
}
