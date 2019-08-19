<?php

declare(strict_types=1);

namespace App\Command;

use App\Git\Synchronizer;
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

    /* @var \App\Git\Synchronizer */
    private $synchronizer;

    /* @var string */
    private $eventData;

    public function __construct(
        WebhookResolver $resolver,
        Synchronizer $synchronizer,
        array $eventData
    ) {
        $this->resolver = $resolver;
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

        $head = $event->pullRequest->getHead();

        $this->synchronizer->synchronizeBranch(
            $head['repo']['git_url'],
            $head['ref']
        );
    }
}
