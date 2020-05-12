<?php

namespace App\Tests\unit;

use App\GitHub\Status;
use App\GitHub\StatusUpdater;
use App\GitHub\UpdatesPullRequestStatus;
use Lpdigital\Github\Entity\PullRequest;
use PHPUnit\Framework\TestCase;

class UpdatesPullRequestStatusTest extends TestCase
{

    public function testPullRequestUpdate()
    {
        $status = new Status('success');

        $statusUpdater = $this->createMock(StatusUpdater::class);
        $statusUpdater->expects($this->once())->method('createStatus')->with(
            'owner',
            'repository',
            'sha1234',
            $status
        );
        $pullRequest = $this->createMock(PullRequest::class);
        $pullRequest->method('getHead')->willReturn([
            'repo' => ['full_name' => 'pullrequester/repository'],
            'sha' => 'sha1234'
        ]);
        $pullRequest->method('getBase')->willReturn([
            'repo' => ['full_name' => 'owner/repository'],
            'sha' => 'sha1234'
        ]);

        $object = new class($statusUpdater) {
            use UpdatesPullRequestStatus;
            public function __construct(StatusUpdater $updater)
            {
                $this->statusUpdater = $updater;
            }
            public function test(PullRequest $pullRequest, Status $status)
            {
                $this->updateStatus($pullRequest, $status);
            }
        };

        $object->test($pullRequest, $status);
    }
}
