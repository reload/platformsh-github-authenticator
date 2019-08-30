<?php

declare(strict_types=1);

namespace App\GitHub;

use App\Platformsh\EnvironmentManager;
use Lpdigital\Github\Entity\PullRequest;
use Lpdigital\Github\EventType\PullRequestEvent;
use Lpdigital\Github\Exception\EventNotFoundException;
use Lpdigital\Github\Parser\WebhookResolver;
use Platformsh\Client\PlatformClient;

class EventHandler
{

    /* @var \Lpdigital\Github\Parser\WebhookResolver */
    private $resolver;

    /* @var \App\GitHub\MembershipValidator */
    private $validator;

    /* @var \App\GitHub\StatusUpdater */
    private $statusUpdater;

    /* @var \App\GitHub\Synchronizer */
    private $synchronizer;

    /* @var \App\Platformsh\EnvironmentManager */
    private $environmentManager;

    public function __construct(
        WebhookResolver $resolver,
        MembershipValidator $validator,
        Synchronizer $synchronizer,
        EnvironmentManager $environmentManager
    ) {
        $this->resolver = $resolver;
        $this->validator = $validator;
        $this->synchronizer = $synchronizer;
        $this->environmentManager = $environmentManager;
    }

    public function handle(array $eventData)
    {
        $event = $this->parseMessage($eventData);
        if ($this->isAuthorized($event)) {
            $this->synchronize($event->pullRequest);
            $this->publishEnvironment($event->pullRequest);
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

    public function isAuthorized(PullRequestEvent $event): bool
    {
        return $this->validator->isMember($event->sender->getLogin());
    }

    public function synchronize(PullRequest $pullRequest): void
    {
        $head = $pullRequest->getHead();
        $this->synchronizer->synchronizeBranch(
            $head['repo']['clone_url'],
            $head['ref']
        );
    }

    public function delete(PullRequest $pullRequest): void
    {
        $head = $pullRequest->getHead();
        $this->synchronizer->deleteBranch($head['ref']);
    }

    public function publishEnvironment(PullRequest $pullRequest): string
    {
        $head = $pullRequest->getHead();
        return $this->environmentManager->activate($head['ref']);
    }

    public function deactivateEnvironment(PullRequest $pullRequest): void
    {
        $head = $pullRequest->getHead();
        $this->environmentManager->deactivate($head['ref']);
    }
}
