<?php

declare(strict_types=1);

namespace App\Platformsh;

use Platformsh\Client\Model\Activity;
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

    public function activate(string $id)
    {
        $environment = $this->getEnvironment($id);

        if (!$environment->isActive()) {
            $activity = $environment->activate();
        }
    }

    public function deactivate(string $id)
    {
        $this->getEnvironment($id)->deactivate();
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

    public function isReady($id) : bool
    {
        $environment = $this->getEnvironment($id);
        $activities = array_merge(
            $environment->getActivities(1, 'environment.push'),
            $environment->getActivities(1, 'environment.activate')
        );
        $incomplete_activities = array_filter(
            $activities,
            function (Activity $activity) {
                return $activity->isComplete();
            }
        );
        return !empty($incomplete_activities);
    }

    public function getEnvironmentUrl(string $id)
    {
        $environment = $this->getEnvironment($id);
        $urls = $environment->getRouteUrls();
        return array_shift($urls);
    }
}
