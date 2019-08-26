<?php

declare(strict_types=1);

namespace App\GitHub;

use App\Git\Synchronizer;
use Lpdigital\Github\EventType\PullRequestEvent;
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
        StatusUpdater $statusUpdater,
        Synchronizer $synchronizer
    ) {
        $this->resolver = $resolver;
        $this->validator = $validator;
        $this->statusUpdater = $statusUpdater;
        $this->synchronizer = $synchronizer;
    }

    public function handle(array $eventData)
    {
        /* @var \Lpdigital\Github\EventType\GithubEventInterface $event */
        $event = $this->resolver->resolve($eventData);
        if (!$event instanceof PullRequestEvent) {
            throw new \UnexpectedValueException('Unsupported event type: ' . $event::name());
        }

        if ($this->validator->isMember($event->sender->getLogin())) {
            $head = $event->pullRequest->getHead();

            $status = new Status('pending');

            $this->statusUpdater->createStatus(
                $event->getRepository()->getOwner()->getLogin(),
                $event->getRepository()->getName(),
                $head['sha'],
                $status
            );

            $this->synchronizer->synchronizeBranch(
                $head['repo']['git_url'],
                $head['ref']
            );

            $status->withState('success');

            $this->statusUpdater->createStatus(
                $event->getRepository()->getOwner()->getLogin(),
                $event->getRepository()->getName(),
                $head['sha'],
                $status
            );
        }
    }
}
