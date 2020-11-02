<?php

/*
 * Copyright (c) 2015 Lp digital system
 *
 * This file is part of Github Parser library.
 *
 * Github Parser is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Github Parser is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Github Parser. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Tests\Parser;

use Lpdigital\Github\Parser\WebhookResolver;

class WebhookResolverTest extends \PHPUnit_Framework_TestCase
{
    /** @var $userAgent string */
    public static $userAgent;

    /** @var $resolver \Lpdigital\Github\Parser\WebhookResolver */
    private $resolver;

    /** @var $jsonDataFolder string */
    private $jsonDataFolder;

    public static function setUpBeforeClass()
    {
        self::$userAgent = 'MyClient/1.0.0';
    }

    public function setUp()
    {
        ini_set('user_agent', self::$userAgent);
        $this->resolver = new WebhookResolver();
        $this->jsonDataFolder = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'json-data'.DIRECTORY_SEPARATOR;
    }

    public function tearDown()
    {
        $this->resolver = null;
    }

    public function testResolveIssuesEvent()
    {
        $jsonReceived = json_decode(file_get_contents($this->jsonDataFolder.'issue_event.json'), true);
        $event = $this->resolver->resolve($jsonReceived);

        $this->assertInstanceOf("Lpdigital\Github\EventType\IssuesEvent", $event);

        $this->assertEquals('opened', $event->action);
        $this->assertEquals('Spelling error in the README file', $event->issue->getTitle());
        $this->assertEquals('35129377', $event->repository->getId());
    }

    public function testResolveIssueCommitEvent()
    {
        $jsonReceived = json_decode(file_get_contents($this->jsonDataFolder.'issue_commit_event.json'), true);
        $event = $this->resolver->resolve($jsonReceived);

        $this->assertInstanceOf("Lpdigital\Github\EventType\IssueCommentEvent", $event);

        $this->assertEquals('Spelling error in the README file', $event->issue->getTitle());
        $this->assertEquals('baxterthehacker', $event->user->getLogin());
        $this->assertEquals("You are totally right! I'll get this fixed right away.", $event->comment->getBody());
    }

    public function testResolveForkEvent()
    {
        $jsonReceived = json_decode(file_get_contents($this->jsonDataFolder.'fork_event.json'), true);
        $event = $this->resolver->resolve($jsonReceived);

        $this->assertInstanceOf("Lpdigital\Github\EventType\ForkEvent", $event);

        $this->assertEquals('baxterandthehackers/public-repo', $event->forkedRepository->getFullName());
        $this->assertEquals('7649605', $event->owner->getId());
        $this->assertEquals('https://api.github.com/repos/baxterthehacker/public-repo', $event->repository->getUrl());
    }

    public function testResolveDeploymentStatusEvent()
    {
        $jsonReceived = json_decode(file_get_contents($this->jsonDataFolder.'deployment_status_event.json'), true);
        $event = $this->resolver->resolve($jsonReceived);

        $this->assertInstanceOf("Lpdigital\Github\EventType\DeploymentStatusEvent", $event);

        $this->assertEquals('production', $event->deployment->getEnvironment());
        $this->assertEquals('public-repo', $event->repository->getName());
        $this->assertEquals('User', $event->sender->getType());
    }

    public function testResolvePullRequestEvent()
    {
        $jsonReceived = json_decode(file_get_contents($this->jsonDataFolder.'pull_request_event.json'), true);
        $event = $this->resolver->resolve($jsonReceived);

        $this->assertInstanceOf("Lpdigital\Github\EventType\PullRequestEvent", $event);

        $this->assertEquals('Update the README with new information', $event->pullRequest->getTitle());
        $this->assertEquals('opened', $event->action);
        $this->assertEquals('1', $event->number);
        $this->assertEquals('baxterthehacker', $event->sender->getLogin());
        $this->assertInstanceOf('Lpdigital\Github\Entity\Repository', $event->repository);
    }

    public function testResolveStatusEvent()
    {
        $jsonReceived = json_decode(file_get_contents($this->jsonDataFolder.'status_event.json'), true);
        $event = $this->resolver->resolve($jsonReceived);

        $this->assertInstanceOf("Lpdigital\Github\EventType\StatusEvent", $event);

        $this->assertEquals('baxterthehacker', $event->committer->getLogin());
        $this->assertEquals('public-repo', $event->repository->getName());
        $this->assertEquals('success', $event->state);
        $this->assertEquals('9049f1265b7d61be4a8904a9a27120d2064dab3b', $event->sha);
    }

    public function testResolveReleaseEvent()
    {
        $jsonReceived = json_decode(file_get_contents($this->jsonDataFolder.'release_event.json'), true);
        $event = $this->resolver->resolve($jsonReceived);

        $this->assertInstanceOf("Lpdigital\Github\EventType\ReleaseEvent", $event);

        $this->assertInstanceOf("Lpdigital\Github\Entity\Release", $event->release);
        $this->assertEquals('published', $event->action);
        $this->assertEquals('https://api.github.com/repos/baxterthehacker/public-repo/releases/1261438', $event->release->getUrl());
        $this->assertEquals('https://github.com/baxterthehacker/public-repo/releases/tag/0.0.1', $event->release->getHtmlUrl());
    }

    public function testResolveWatchEvent()
    {
        $jsonReceived = json_decode(file_get_contents($this->jsonDataFolder.'watch_event.json'), true);
        $event = $this->resolver->resolve($jsonReceived);

        $this->assertInstanceOf("Lpdigital\Github\EventType\WatchEvent", $event);
        $this->assertInstanceOf("Lpdigital\Github\Entity\Repository", $event->repository);
        $this->assertInstanceOf("Lpdigital\Github\Entity\User", $event->user);

        $this->assertEquals('started', $event->action);
    }

    public function testResolvePullRequestReviewCommentEvent()
    {
        $jsonReceived = json_decode(file_get_contents($this->jsonDataFolder.'pull_request_review_comment_event.json'), true);
        $event = $this->resolver->resolve($jsonReceived);

        $this->assertInstanceOf("Lpdigital\Github\EventType\PullRequestReviewCommentEvent", $event);
        $this->assertInstanceOf("Lpdigital\Github\Entity\Repository", $event->repository);
        $this->assertInstanceOf("Lpdigital\Github\Entity\User", $event->sender);
        $this->assertInstanceOf("Lpdigital\Github\Entity\Comment", $event->comment);
    }

    public function testResolveGollumEvent()
    {
        $jsonReceived = json_decode(file_get_contents($this->jsonDataFolder.'gollum_event.json'), true);
        $event = $this->resolver->resolve($jsonReceived);

        $this->assertInstanceOf("Lpdigital\Github\EventType\GollumEvent", $event);
        $this->assertInstanceOf("Lpdigital\Github\Entity\Repository", $event->repository);
        $this->assertInstanceOf("Lpdigital\Github\Entity\User", $event->sender);
        $this->assertInternalType('array', $event->pages);
        $this->assertCount(2, $event->pages);

        $this->assertInstanceOf("Lpdigital\Github\Entity\Page", current($event->pages));
        $this->assertInstanceOf("Lpdigital\Github\Entity\Page", next($event->pages));

        $this->assertEquals('Home', $event->pages[0]->getTitle());
        $this->assertEquals('Home2', $event->pages[1]->getTitle());
    }

    public function testPushEvent()
    {
        $jsonReceived = json_decode(file_get_contents($this->jsonDataFolder.'push_event.json'), true);
        $event = $this->resolver->resolve($jsonReceived);

        $this->assertInstanceOf("Lpdigital\Github\EventType\PushEvent", $event);
    }

    public function testResolvePullRequestEventCommitsOk()
    {
        $jsonReceived = json_decode(file_get_contents($this->jsonDataFolder.'pull_request_event.json'), true);
        $event = $this->resolver->resolve($jsonReceived);

        $this->assertInstanceOf("Lpdigital\Github\EventType\PullRequestEvent", $event);
        $pullRequest = $event->pullRequest;

        $this->assertInstanceOf("Lpdigital\Github\Entity\PullRequest", $pullRequest);

        $commits = $pullRequest->getCommits();
        $this->assertTrue(is_array($commits));
        $commit = $commits[0];
        $this->assertInstanceOf("Lpdigital\Github\Entity\Commit", $commit);
    }

    public function testResolvePullRequestHasIntegration()
    {
        $jsonReceived = json_decode(file_get_contents($this->jsonDataFolder.'pull_request_event.json'), true);
        $event = $this->resolver->resolve($jsonReceived);

        $this->assertInstanceOf("Lpdigital\Github\Entity\Integration", $event->integration);
    }

    public function testResolvePullRequestEventCommitThrowsException()
    {
        ini_set('user_agent', '');
        $this->setExpectedException('Lpdigital\Github\Exception\UserAgentNotFoundException');

        $jsonReceived = json_decode(file_get_contents($this->jsonDataFolder.'pull_request_event.json'), true);
        $event = $this->resolver->resolve($jsonReceived);
        $event->pullRequest->getCommits();
    }

    public function testResolveWithMissingRepositoryThrowsException()
    {
        $this->setExpectedException('Lpdigital\Github\Exception\RepositoryNotFoundException');

        $jsonReceived = json_decode(file_get_contents($this->jsonDataFolder.'repository_not_found.json'), true);
        $this->resolver->resolve($jsonReceived);
    }

    public function testResolveWithMalformedRepositoryThrowsException()
    {
        $this->setExpectedException('Lpdigital\Github\Exception\RepositoryNotFoundException');

        $jsonReceived = json_decode(file_get_contents($this->jsonDataFolder.'repository_malformed.json'), true);
        $this->resolver->resolve($jsonReceived);
    }

    public function testResolveIntegrationInstallationEvent()
    {
        $jsonReceived = json_decode(file_get_contents($this->jsonDataFolder.'integration_installation_event.json'), true);
        $event = $this->resolver->resolve($jsonReceived);

        $this->assertInstanceOf("Lpdigital\Github\EventType\IntegrationInstallationEvent", $event);
        $this->assertInstanceOf("Lpdigital\Github\Entity\Integration", $event->integration);
        $this->assertInstanceOf("Lpdigital\Github\Entity\User", $event->sender);

        $this->assertInternalType('string', $event->integration->getAccessTokenUrl());
        $this->assertInternalType('string', $event->integration->getRepositoriesUrl());
    }

    public function testResolveIntegrationInstallationRepositoriesEvent()
    {
        $jsonReceived = json_decode(file_get_contents($this->jsonDataFolder.'integration_installation_repositories_event.json'), true);
        $event = $this->resolver->resolve($jsonReceived);

        $this->assertInstanceOf("Lpdigital\Github\EventType\IntegrationInstallationRepositoriesEvent", $event);
        $this->assertInstanceOf("Lpdigital\Github\Entity\Integration", $event->integration);
        $this->assertInstanceOf("Lpdigital\Github\Entity\User", $event->sender);
        $this->assertInternalType('string', $event->repositorySelection);
        $this->assertInternalType('array', $event->repositoryAdded);
        $this->assertInternalType('array', $event->repositoryRemoved);

        $this->assertInternalType('string', $event->integration->getAccessTokenUrl());
        $this->assertInternalType('string', $event->integration->getRepositoriesUrl());
        $this->assertInternalType('string', $event->integration->getHtmlUrl());
    }
}
