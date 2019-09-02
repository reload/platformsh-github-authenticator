<?php

declare(strict_types=1);

namespace App\Event;

use App\GitHub\Synchronizer;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class PullRequestSynchronizer implements MessageHandlerInterface
{

    private $synchronizer;

    /* @var \Symfony\Component\Messenger\MessageBusInterface */
    private $messageBus;

    public function __construct(Synchronizer $synchronizer, MessageBusInterface $messageBus)
    {
        $this->synchronizer = $synchronizer;
        $this->messageBus = $messageBus;
    }

    public function __invoke(PullRequestAuthorized $event)
    {
        $head = $event->getPullRequest()->getHead();
        $this->synchronizer->synchronizeBranch(
            $head['repo']['clone_url'],
            $head['ref']
        );

        $this->messageBus->dispatch(new PullRequestSynchronized($event->getPullRequest()));
    }
}
