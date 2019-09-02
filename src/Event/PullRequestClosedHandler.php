<?php

declare(strict_types=1);

namespace App\Event;

use App\GitHub\EventHandler;
use App\GitHub\Synchronizer;
use App\Platformsh\EnvironmentManager;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class PullRequestClosedHandler implements MessageHandlerInterface
{

    /* @var \App\GitHub\EventHandler */
    private $synchronizer;

    /* @var \App\Platformsh\EnvironmentManager */
    private $environmentManager;

    public function __construct(Synchronizer $synchronizer, EnvironmentManager $environmentManager)
    {
        $this->synchronizer = $synchronizer;
        $this->environmentManager = $environmentManager;
    }

    public function __invoke(PullRequestClosed $event)
    {
        $head = $event->getPullRequest()->getHead();
        $this->environmentManager->deactivate($head['ref']);
        $this->synchronizer->deleteBranch($head['ref']);
    }
}
