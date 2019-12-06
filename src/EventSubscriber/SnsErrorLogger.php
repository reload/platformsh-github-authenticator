<?php

namespace App\EventSubscriber;

use Bref\MessengerSns\Event\SnsMessageDecodeFailed;
use Bref\MessengerSns\Event\SnsMessageFailed;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SnsErrorLogger implements EventSubscriberInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    public static function getSubscribedEvents() : array
    {
        return [
            SnsMessageFailed::class => [
                'logError'
            ],
            SnsMessageDecodeFailed::class => [
                'logError'
            ]
        ];
    }

    /**
     * @param \Bref\MessengerSns\Event\SnsMessageFailed|\Bref\MessengerSns\Event\SnsMessageDecodeFailed $error
     */
    public function logError($error)
    {
        $this->logger->error(
            'SNS error',
            [
                'error' => get_class($error),
                'exception' => get_class($error->getThrowable())
            ]
        );
    }
}
