<?php

declare(strict_types=1);

namespace App\Event;

use Lpdigital\Github\Entity\PullRequest;

class EnvironmentDeploymentCompleted extends PullRequestEvent
{

    /* @var string */
    private $environmentUri;

    public function __construct(PullRequest $pullRequest, string $environmentUri)
    {
        parent::__construct($pullRequest);
        $this->environmentUri = $environmentUri;
    }

    public function getEnvironmentUri(): string
    {
        return $this->environmentUri;
    }
}
