<?php

declare(strict_types=1);

namespace App\Event;

use App\Platformsh\EnvironmentManager;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class PlatformshEnvironmentPublisher implements MessageHandlerInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /* @var \Symfony\Component\Messenger\MessageBusInterface */
    private $messageBus;

    private $environmentManager;

    public function __construct(EnvironmentManager $environmentManager, MessageBusInterface $messageBus)
    {
        $this->environmentManager = $environmentManager;
        $this->messageBus = $messageBus;
    }

    public function __invoke(PullRequestSynchronized $event)
    {
        $head = $event->getPullRequest()->getHead();
        $activity = $this->environmentManager->activate($head['ref']);
        if ($activity) {
            $activity->wait(null, function ($message) {
                $this->logger->info($message);
            });
        }

        $this->messageBus->dispatch(
            new PullRequestEnvironmentReady($event->getPullRequest())
        );
    }
}
