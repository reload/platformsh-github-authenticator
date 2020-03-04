<?php

declare(strict_types=1);

namespace App\GitHub;

use GitWrapper\GitWrapper;
use Spatie\TemporaryDirectory\TemporaryDirectory;
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
        $repoUrl = Url::fromString($repoUrl);
        $authorizedRepoUrl = (string) $repoUrl->withUserInfo($this->username, $this->password);

        $directory = $this->getWorkDirectory();
        $repository = $this->git->cloneRepository(
            $authorizedRepoUrl,
            $directory->path()
        );
        $repository->checkout($branch);

        $repository->addRemote('target', $this->targetRepoUrl);

        $repository->push('--force', 'target', $branch);

        $directory->delete();
    }

    public function deleteBranch(string $branch)
    {
        $directory = $this->getWorkDirectory();
        $repository = $this->git->cloneRepository(
            $this->targetRepoUrl,
            $directory->path()
        );
        $repository->push('--delete', 'origin', $branch);

        $directory->delete();
    }

    protected function getWorkDirectory(): TemporaryDirectory
    {
        return (new TemporaryDirectory($this->workingDirectory))->create();
    }
}
