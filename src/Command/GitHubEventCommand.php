<?php

declare(strict_types=1);

namespace App\Command;

use App\Event\PullRequestAuthorized;
use App\Event\PullRequestSynchronized;
use App\GitHub\EventHandler;
use App\GitHub\MembershipValidator;
use Lpdigital\Github\EventType\PullRequestEvent;
use Lpdigital\Github\Parser\WebhookResolver;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use function Safe\file_get_contents as file_get_contents;
use function Safe\json_decode as json_decode;
use function Safe\sprintf as sprintf;

class GitHubEventCommand extends Command
{

    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:github-event';

    /* @var \Lpdigital\Github\Parser\WebhookResolver */
    private $resolver;

    /* @var \App\GitHub\MembershipValidator */
    private $validator;

    /* @var \Symfony\Component\Messenger\MessageBusInterface */
    private $messageBus;

    public function __construct(
        WebhookResolver $resolver,
        MembershipValidator $validator,
        MessageBusInterface $messageBus
    ) {
        $this->resolver = $resolver;
        $this->validator = $validator;
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
        $event = $this->resolver->resolve($json);
        if (!$event instanceof PullRequestEvent) {
            throw new \UnexpectedValueException(
                sprintf('Unexpected event type %s', $event::name())
            );
        }
        if (!$this->validator->isMember($event->sender->getLogin())) {
            throw new \UnexpectedValueException('User is not member of group');
        }
        $this->messageBus->dispatch(new PullRequestAuthorized($event->pullRequest));
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
