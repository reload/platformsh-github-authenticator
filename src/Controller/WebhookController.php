<?php

declare(strict_types=1);

namespace App\Controller;

use App\Event\PullRequestAuthorized;
use App\Event\PullRequestSynchronized;
use App\Event\PullRequestClosed;
use App\GitHub\EventHandler;
use App\GitHub\MembershipValidator;
use Lpdigital\Github\EventType\PullRequestEvent;
use Lpdigital\Github\Exception\EventNotFoundException;
use Lpdigital\Github\Parser\WebhookResolver;
use Swop\Bundle\GitHubWebHookBundle\Annotation\GitHubWebHook;
use Swop\GitHubWebHook\Event\GitHubEvent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use function Safe\sprintf as sprintf;

class WebhookController extends AbstractController
{

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
    }

    /**
     * @Route("/webhook", name="webhook")
     *
     * @GitHubWebHook(eventType="ping")
     * @GitHubWebHook(eventType="pull_request")
     */
    public function webhook(GitHubEvent $gitHubEvent)
    {
        if ($gitHubEvent->getType() == 'ping') {
            return ['status' => 'success'];
        }

        try {
            /* @var \Lpdigital\Github\EventType\GithubEventInterface $event */
            $event = $this->resolver->resolve($gitHubEvent->getPayload());
        } catch (EventNotFoundException $e) {
            throw new BadRequestHttpException('Unable to determine event type', $e);
        }
        if (!$event instanceof PullRequestEvent) {
            throw new BadRequestHttpException(
                sprintf('Unsupported event type: ', $event::name())
            );
        }

        if (in_array($event->getAction(), ['opened', 'synchronize', 'reopened'])) {
            if (!$this->validator->isMember($event->sender->getLogin())) {
                return ['status' => 'user not authorized'];
            } else {
                $this->messageBus->dispatch(new PullRequestAuthorized($event->pullRequest));
                return ['status' => 'event dispatched'];
            }
        } elseif ($event->getAction() == 'closed') {
            $this->messageBus->dispatch(new PullRequestClosed($event->pullRequest));
            return ['status' => 'event dispatched'];
        }

        return ['status' => 'no action'];
    }
}
