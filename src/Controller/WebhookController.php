<?php

declare(strict_types=1);

namespace App\Controller;

use App\Event\PullRequestAuthorized;
use App\Event\PullRequestClosed;
use App\GitHub\EventHandler;
use Swop\Bundle\GitHubWebHookBundle\Annotation\GitHubWebHook;
use Swop\GitHubWebHook\Event\GitHubEvent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class WebhookController extends AbstractController
{

    /* @var \App\GitHub\EventHandler */
    private $eventHandler;

    /* @var \Symfony\Component\Messenger\MessageBusInterface */
    private $messageBus;

    public function __construct(EventHandler $handler, MessageBusInterface $messageBus)
    {
        $this->eventHandler = $handler;
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
            $event = $this->eventHandler->parseMessage($gitHubEvent->getPayload());
        } catch (\UnexpectedValueException $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }

        if (in_array($event->getAction(), ['opened', 'synchronize', 'reopened'])) {
            if (!$this->eventHandler->isAuthorized($event)) {
                return ['status' => 'user not authorized'];
            }
            $this->messageBus->dispatch(new PullRequestAuthorized($event->pullRequest));
            return ['status' => 'event dispatched'];
        } elseif ($event->getAction() == 'closed') {
            $this->messageBus->dispatch(new PullRequestClosed($event->pullRequest));
            return ['status' => 'event dispatched'];
        }

        return ['status' => 'no action'];
    }
}
