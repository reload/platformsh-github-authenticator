<?php

namespace App\Tests\unit;

use App\GitHub\Status;
use App\GitHub\StatusUpdater;
use Github\Api\Repo;
use Github\Api\Repository\Statuses;
use Github\Client;
use PHPUnit\Framework\TestCase;

class StatusUpdaterTest extends TestCase
{

    public function testCreateStatus()
    {
        $status = (new Status('success'))
            ->withState('failed')
            ->withDescription('description')
            ->withTargetUrl('http://google.com');

        $github = $this->getMockBuilder(Client::class)
            ->setMethods(['api'])
            ->getMock();
        $github->method('api')->willReturnCallback(function () use ($status) {
            $repo = $this->getMockBuilder(Repo::class)
                ->disableOriginalConstructor()
                ->setMethods(['statuses'])
                ->getMock();
            $repo->method('statuses')->willReturnCallback(function () use ($status) {
                $statuses = $this->createMock(Statuses::class);
                $statuses
                   ->expects($this->once())
                   ->method('create')
                   ->with('owner', 'repo', 'sha', $status->toParams());
                return $statuses;
            });

            return $repo;
        });

        $statusUpdater = new StatusUpdater($github, 'context');
        $statusUpdater->createStatus('owner', 'repo', 'sha', $status);
    }
}
