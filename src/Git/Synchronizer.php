<?php

declare(strict_types=1);

namespace App\Git;

use GitWrapper\GitWrapper;

class Synchronizer
{

    /* @var \GitWrapper\GitWrapper */
    private $git;

    /* @var string */
    private $workingDirectory;

    /* @var string */
    private $targetRepoUrl;

    public function __construct(GitWrapper $git, string $workingDirectory, string $targetRepoUrl)
    {
        $this->git = $git;
        $this->workingDirectory = $workingDirectory;
        $this->targetRepoUrl = $targetRepoUrl;
    }

    public function synchronizeBranch(string $repoUrl, string $branch)
    {
        $targetDirectory = $this->workingDirectory . '/' . uniqid();
        $repository = $this->git->cloneRepository($repoUrl, $targetDirectory);
        $repository->checkout($branch);

        $repository->addRemote('target', $this->targetRepoUrl);

        $repository->push('--force', 'target', $branch);
    }
}
