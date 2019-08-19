<?php

namespace App\Tests\unit;

use App\GitHub\MembershipValidator;
use Github\Api\Organization\Teams;
use Github\Client;
use Github\Exception\RuntimeException;
use PHPUnit\Framework\TestCase;

class MembershipValidatorTest extends TestCase
{

    /* @var \PHPUnit\Framework\MockObject\MockObject|\Github\Client */
    private $github;

    protected function setUp()
    {
        $this->github = $this->getMockBuilder(Client::class)
            ->setMethods(['api'])
            ->getMock();
        $this->github->method('api')->with('teams')->willReturnCallback(function () {
            $teams = $this->createMock(Teams::class);
            $teams->method('all')->willReturn([
                [
                    'id' => 1,
                    'name' => 'team'
                ],
                [
                    'id' => 2,
                    'name' => 'other team'
                ]
            ]);

            $teams->method('check')->willReturnCallback(function ($team_id, $username) {
                if ($username == 'member' && $team_id == 1) {
                    return [];
                } else {
                    throw new RuntimeException();
                }
            });

            return $teams;
        });
    }

    public function testMember()
    {
        $validator = new MembershipValidator($this->github, 'org', 'team');
        $this->assertTrue($validator->isMember('member'));
    }

    public function testNotMember()
    {
        $validator = new MembershipValidator($this->github, 'org', 'team');
        $this->assertFalse($validator->isMember('not-member'));
    }
}
