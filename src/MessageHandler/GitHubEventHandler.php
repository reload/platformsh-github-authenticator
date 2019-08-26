<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\GitHub\EventHandler;
use Swop\GitHubWebHook\Event\GitHubEvent;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class GitHubEventHandler implements MessageHandlerInterface
{

    /* @var \App\GitHub\EventHandler */
    private $eventHandler;

    public function __construct(EventHandler $eventHandler)
    {
        $this->eventHandler = $eventHandler;
    }

    public function __invoke(GitHubEvent $event)
    {
        $this->eventHandler->handle($event->getPayload());
    }
}
