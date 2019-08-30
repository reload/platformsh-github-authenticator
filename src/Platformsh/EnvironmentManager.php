<?php

declare(strict_types=1);

namespace App\Platformsh;

use Platformsh\Client\PlatformClient;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use function Safe\sprintf as sprintf;

class EnvironmentManager implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /* @var \Platformsh\Client\PlatformClient */
    private $platform;

    /* @var string */
    private $project;

    public function __construct(PlatformClient $platform, string $project)
    {
        $this->platform = $platform;
        $this->project = $project;
    }

    public function activate(string $id) : string
    {
        $environment = $this->getEnvironment($id);

        if (!$environment->isActive()) {
            $activity = $environment->activate();
            $activity->wait(null, $this->activityLogger());
            $this->logger->info($activity->getProperty('log'));
        }
        return $environment->getUri();
    }

    public function deactivate(string $id)
    {
        $activity = $this->getEnvironment($id)->deactivate();
        $activity->wait(null, $this->activityLogger());
        $this->logger->info($activity->getProperty('log'));
    }

    private function getEnvironment(string $id)
    {
        $project = $this->platform->getProject($this->project);
        if (!$project) {
            throw new \UnexpectedValueException(
                sprintf('Unknown Platform.sh project %s', $this->project)
            );
        }
        $environment = $project->getEnvironment($id);
        if (!$environment) {
            throw new \UnexpectedValueException(
                sprintf('Unknown Platform.sh environment %s', $id)
            );
        }
        return $environment;
    }

    private function activityLogger(): callable
    {
        return function (string $message) {
            $this->logger->info($message);
        };
    }
}
