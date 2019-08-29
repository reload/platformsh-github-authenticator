<?php

declare(strict_types=1);

namespace App\Command;

use App\Event\PullRequestAuthorized;
use App\GitHub\EventHandler;
use Lpdigital\Github\EventType\PullRequestEvent;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use function Safe\file_get_contents as file_get_contents;
use function Safe\json_decode as json_decode;

class GitHubEventCommand extends Command
{

    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:github-event';

    /* @var \App\GitHub\EventHandler */
    private $eventHandler;

    /* @var \Symfony\Component\Messenger\MessageBusInterface */
    private $messageBus;

    public function __construct(
        EventHandler $eventHandler,
        MessageBusInterface $messageBus
    ) {
        $this->eventHandler = $eventHandler;
        $this->messageBus = $messageBus;

        parent::__construct();
    }

    protected function configure()
    {
        $this->addArgument('event', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = file_get_contents($this->getStringArgument($input, 'event'));
        $json = json_decode($file, true);
        $event = $this->eventHandler->parseMessage($json);
        if ($this->eventHandler->isAuthorized($event)) {
            $this->messageBus->dispatch(new PullRequestAuthorized($event->pullRequest));
        }
    }

    private function getStringArgument(InputInterface $input, string $argument): string
    {
        $value = $input->getArgument($argument);
        if (is_array($value)) {
            $value = array_shift($value);
        }
        return (string) $value;
    }
}
