<?php

declare(strict_types=1);

namespace App\Command;

use App\Git\Synchronizer;
use App\GitHub\MembershipValidator;
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

    /* @var \App\Git\Synchronizer */
    private $synchronizer;

    /* @var string */
    private $eventData;

    public function __construct(
        WebhookResolver $resolver,
        MembershipValidator $validator,
        Synchronizer $synchronizer,
        array $eventData
    ) {
        $this->resolver = $resolver;
        $this->validator = $validator;
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

            $this->synchronizer->synchronizeBranch(
                $head['repo']['git_url'],
                $head['ref']
            );
        }
    }
}
