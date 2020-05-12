<?php

declare(strict_types=1);

namespace App\Event;

use App\GitHub\Status;
use App\GitHub\StatusUpdater;
use App\GitHub\UpdatesPullRequestStatus;
use App\Platformsh\EnvironmentManager;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use function Safe\sprintf as sprintf;

class GitHubDeploymentFinishedNotifier implements MessageHandlerInterface, LoggerAwareInterface
{
    use UpdatesPullRequestStatus, LoggerAwareTrait;

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
        $this->environmentManager->waitForReady($id, function (string $log) {
            $this->logger->info($log);
        });

        // Even though the environment may not have any activities pending
        // urls might still not be ready. Retry up to 10 times waiting 1000ms
        // between each attempt.
        $environmentUrl = backoff(function () use ($id) {
            return $this->environmentManager->getEnvironmentUrl($id);
        }, 10, 1000);

        $status = (new Status('success'))
            ->withDescription('Deployment completed')
            ->withTargetUrl($environmentUrl);
        $this->updateStatus($event->getPullRequest(), $status);
    }
}
