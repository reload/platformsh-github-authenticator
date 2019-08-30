<?php

declare(strict_types=1);

namespace App\Event;

use App\GitHub\EventHandler;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class PullRequestAuthorizedHandler implements MessageHandlerInterface
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

    public function __invoke(PullRequestAuthorized $event)
    {
        $this->messageBus->dispatch(new EnvironmentDeploymentStarted($event->getPullRequest()));
        $this->eventHandler->synchronize($event->getPullRequest());
        $environmentUri = $this->eventHandler->publishEnvironment($event->getPullRequest());
        $this->messageBus->dispatch(new EnvironmentDeploymentCompleted($event->getPullRequest(), $environmentUri));
    }
}
