<?php

declare(strict_types=1);

namespace App\Command;

use App\Git\Synchronizer;
use App\GitHub\MembershipValidator;
use App\GitHub\Status;
use App\GitHub\StatusUpdater;
use Lpdigital\Github\EventType\PullRequestEvent;
use Lpdigital\Github\Parser\WebhookResolver;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GitHubEvent extends Command
{

    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:github-event';

    /* @var \Lpdigital\Github\Parser\WebhookResolver */
    private $resolver;

    /* @var \App\GitHub\MembershipValidator */
    private $validator;

    /* @var \App\GitHub\StatusUpdater */
    private $statusUpdater;

    /* @var \App\Git\Synchronizer */
    private $synchronizer;

    /* @var string */
    private $eventData;

    public function __construct(
        WebhookResolver $resolver,
        MembershipValidator $validator,
        StatusUpdater $statusUpdater,
        Synchronizer $synchronizer,
        array $eventData
    ) {
        $this->resolver = $resolver;
        $this->validator = $validator;
        $this->statusUpdater = $statusUpdater;
        $this->synchronizer = $synchronizer;
        $this->eventData = $eventData;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /* @var \Lpdigital\Github\EventType\GithubEventInterface $event */
        $event = $this->resolver->resolve($this->eventData);
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
