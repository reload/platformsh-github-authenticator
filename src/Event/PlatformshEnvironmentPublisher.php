<?php

declare(strict_types=1);

namespace App\Event;

use App\Platformsh\EnvironmentManager;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class PlatformshEnvironmentPublisher implements MessageHandlerInterface
{

    private $environmentManager;

    public function __construct(EnvironmentManager $environmentManager)
    {
        $this->environmentManager = $environmentManager;
    }

    public function __invoke(PullRequestSynchronized $event)
    {
        $head = $event->getPullRequest()->getHead();
        $this->environmentManager->activate($head['ref']);
    }
}
