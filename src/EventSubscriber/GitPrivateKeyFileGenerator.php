<?php

namespace App\EventSubscriber;

use GitWrapper\Event\GitEvent;
use GitWrapper\Event\GitEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use function Safe\chmod as chmod;
use function Safe\file_put_contents as file_put_contents;

class GitPrivateKeyFileGenerator implements EventSubscriberInterface
{

    /* @var string */
    private $privateKey;

    /* @var $string */
    private $directory;

    public function __construct(string $privateKey, string $directory)
    {
        $this->privateKey = $privateKey;
        $this->directory = $directory;
    }

    public static function getSubscribedEvents() : array
    {
        return [
          GitEvents::GIT_PREPARE => [
              'generatePrivateKey'
          ]
        ];
    }

    public function generatePrivateKey(GitEvent $e)
    {
        $privateKeyFile = $this->directory . DIRECTORY_SEPARATOR . 'git_private_key';
        if (!file_exists($privateKeyFile)) {
            file_put_contents($privateKeyFile, $this->privateKey);
            // We need to prevent public access to the key before SSH accepts it.
            chmod($privateKeyFile, 0600);
        }
        $e->getWrapper()->setPrivateKey($privateKeyFile);
    }
}
