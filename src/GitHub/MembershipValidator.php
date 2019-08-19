<?php

namespace App\GitHub;

use Github\Client;
use Github\Exception\RuntimeException;

class MembershipValidator
{

    /* @var \Github\Client */
    private $github;

    /* @var string */
    private $organizationName;

    /* @var string */
    private $teamName;

    public function __construct(Client $github, string $organizationName, string $teamName)
    {
        $this->github = $github;
        $this->organizationName = $organizationName;
        $this->teamName = $teamName;
    }

    public function isMember(string $username): bool
    {
        $teams = $this->github->teams()->all($this->organizationName);
        $teams = array_filter($teams, function (array $teamData) {
            return strcasecmp($teamData['name'], $this->teamName) === 0;
        });
        $member = array_reduce($teams, function ($membership, array $teamData) use ($username) {
            try {
                $membership = $this->github->teams()->check($teamData['id'], $username);
                return true;
            } catch (RuntimeException $e) {
                // This will usually occur when the user is not a member of the
                // team but we error on the side of caution and handle all
                // errors the same by not indicating membership.
            }
            return $membership;
        }, false);

        return $member;
    }
}
