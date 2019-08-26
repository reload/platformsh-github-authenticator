<?php

declare(strict_types=1);

namespace App\Controller;

use App\GitHub\EventHandler;
use Swop\Bundle\GitHubWebHookBundle\Annotation\GitHubWebHook;
use Swop\GitHubWebHook\Event\GitHubEvent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

        $this->messageBus->dispatch($gitHubEvent);
        return ['status' => 'success'];
    }
}
