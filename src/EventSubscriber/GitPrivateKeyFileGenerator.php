<?php

namespace App\EventSubscriber;

use App\Platformsh\RsaPrivateKey;
use Codeaken\SshKey\SshPrivateKey;
use GitWrapper\Event\GitEvent;
use GitWrapper\Event\GitEvents;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use function Safe\chmod as chmod;
use function Safe\file_put_contents as file_put_contents;
use function Safe\substr as substr;

class GitPrivateKeyFileGenerator implements EventSubscriberInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

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

    public function generatePrivateKey(GitEvent $event)
    {
        $privateKeyFile = $this->directory . DIRECTORY_SEPARATOR . 'git_private_key';
        try {
            RsaPrivateKey::fromFile($privateKeyFile);
        } catch (\Exception $e) {
            $key = new RsaPrivateKey($this->privateKey);
            $key->toFile($privateKeyFile);
            $this->logger->info(
                'Generated private key',
                [
                    'path' => $privateKeyFile,
                    'key' => substr($this->privateKey, 0, 100)
                ]
            );
        }
        $event->getWrapper()->setPrivateKey($privateKeyFile);
    }
}
