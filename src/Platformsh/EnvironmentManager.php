<?php

declare(strict_types=1);

namespace App\Platformsh;

use Platformsh\Client\PlatformClient;
use function Safe\sprintf as sprintf;

class EnvironmentManager
{

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
        $project = $this->platform->getProject($this->project);
        if (!$project) {
            throw new \UnexpectedValueException(sprintf('Unknown Platform.sh project %s', $this->project));
        }
        $environment = $project->getEnvironment($id);
        if (!$environment) {
            throw new \UnexpectedValueException(sprintf('Unknown Platform.sh environment %s', $id));
        }

        if (!$environment->isActive()) {
            $activity = $environment->activate();
            $activity->wait();
        }
        return $environment->getUri();
    }
}
