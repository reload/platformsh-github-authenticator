<?php

declare(strict_types=1);

namespace App\GitHub;

use App\Git\Synchronizer;
use Lpdigital\Github\EventType\PullRequestEvent;
use Lpdigital\Github\Exception\EventNotFoundException;
use Lpdigital\Github\Parser\WebhookResolver;

class EventHandler
{

    /* @var \Lpdigital\Github\Parser\WebhookResolver */
    private $resolver;

    /* @var \App\GitHub\MembershipValidator */
    private $validator;

    /* @var \App\GitHub\StatusUpdater */
    private $statusUpdater;

    /* @var \App\Git\Synchronizer */
    private $synchronizer;

    public function __construct(
        WebhookResolver $resolver,
        MembershipValidator $validator,
        Synchronizer $synchronizer
    ) {
        $this->resolver = $resolver;
        $this->validator = $validator;
        $this->synchronizer = $synchronizer;
    }

    public function handle(array $eventData)
    {
        $event = $this->parseMessage($eventData);
        if ($this->isAuthorized($event)) {
            $this->synchronize($event);
        }
    }

    public function parseMessage(array $eventData): PullRequestEvent
    {
        /* @var \Lpdigital\Github\EventType\GithubEventInterface $event */
        try {
            $event = $this->resolver->resolve($eventData);
        } catch (EventNotFoundException $e) {
            throw new \UnexpectedValueException('Unable to determine event type', 0, $e);
        }
        if (!$event instanceof PullRequestEvent) {
            throw new \UnexpectedValueException('Unsupported event type: ' . $event::name());
        }
        return $event;
    }

    public function isAuthorized($event): bool
    {
        return $this->validator->isMember($event->sender->getLogin());
    }

    public function synchronize(PullRequestEvent $event): void
    {
        $head = $event->pullRequest->getHead();
        $this->synchronizer->synchronizeBranch(
            $head['repo']['git_url'],
            $head['ref']
        );
    }
}
