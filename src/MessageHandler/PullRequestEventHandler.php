<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\GitHub\EventHandler;
use Lpdigital\Github\EventType\PullRequestEvent;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class PullRequestEventHandler implements MessageHandlerInterface
{

    /* @var \App\GitHub\EventHandler */
    private $eventHandler;

    public function __construct(EventHandler $eventHandler)
    {
        $this->eventHandler = $eventHandler;
    }

    public function __invoke(PullRequestEvent $event)
    {
        $this->eventHandler->synchronize($event);
    }
}
