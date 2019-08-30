<?php

declare(strict_types=1);

namespace App\Event;

use App\GitHub\EventHandler;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class PullRequestClosedHandler implements MessageHandlerInterface
{

    /* @var \App\GitHub\EventHandler */
    private $eventHandler;

    /* @var \Symfony\Component\Messenger\MessageBusInterface */
    private $messageBus;

    public function __construct(EventHandler $eventHandler, MessageBusInterface $messageBus)
    {
        $this->eventHandler = $eventHandler;
        $this->messageBus = $messageBus;
    }

    public function __invoke(PullRequestClosed $event)
    {
        $this->eventHandler->deactivateEnvironment($event->getPullRequest());
        $this->eventHandler->delete($event->getPullRequest());
    }
}
