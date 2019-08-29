<?php

declare(strict_types=1);

namespace App\GitHub;

use GitWrapper\GitWrapper;
use Spatie\Url\Url;

class Synchronizer
{

    /* @var \GitWrapper\GitWrapper */
    private $git;

    /* @var string */
    private $workingDirectory;

    /* @var string */
    private $targetRepoUrl;

    /* @var string */
    private $username;

    /* @var string */
    private $password;

    public function __construct(
        GitWrapper $git,
        string $username,
        string $password,
        string $workingDirectory,
        string $targetRepoUrl
    ) {
        $this->git = $git;
        $this->username = $username;
        $this->password = $password;
        $this->workingDirectory = $workingDirectory;
        $this->targetRepoUrl = $targetRepoUrl;
    }

    public function synchronizeBranch(string $repoUrl, string $branch)
    {
        $targetDirectory = $this->workingDirectory . '/' . uniqid();

        $repoUrl = Url::fromString($repoUrl);
        $authorizedRepoUrl = (string) $repoUrl->withUserInfo($this->username, $this->password);

        $repository = $this->git->cloneRepository($authorizedRepoUrl, $targetDirectory);
        $repository->checkout($branch);

        $repository->addRemote('target', $this->targetRepoUrl);

        $repository->push('--force', 'target', $branch);
    }
}
