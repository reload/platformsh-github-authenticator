# Platform.sh GitHub Authenticator

Control who gets automated pull request environments on [Platform.sh](https://platform.sh) by using GitHub teams. Inspired by the [Jenkins GitHub pull request builder plugin](https://wiki.jenkins.io/display/JENKINS/GitHub+pull+request+builder+plugin).

One use case for this is to use Platform.sh for open source projects, where you want to use the pull request environment functionality to test changes - but only for certain users.

## Getting started

These instructions will get you a copy of the project up and running.

See [Development](#development) for getting the project on your local machine for development and testing purposes. See [Deployment](#serverless-deployment) for notes on how to deploy the project to AWS Lamdba.

### Prerequisites

To use this project you need the following:

1. PHP version 7.3 or newer
2. [Composer](https://getcomposer.org/)
3. A GitHub user for representing the application.
4. A GitHub repository contaning the code for the project you want to deploy to Platform.sh including sufficient permissions to manage teams and webhooks.
5. A Platform.sh project to deploy the project code to.
6. A Platform.sh user with permission to commit code to the project.
7. An environment capable of processing HTTP requests using PHP and running the git CLI application.

You may want to create user accounts on GitHub and Platform.sh specifically for the application.

### Setup

#### Checkout the GitHub Platform.sh Authenticator application codebase

```
git clone https://github.com/reload/platformsh-github-authenticator.git
```

#### Install third-party dependencies

```
cd platformsh-github-authenticator
composer install
```

#### Configure the environment 

Define the following values in your environment: 

1. `GITHUB_USERNAME`: The username of the GitHub user which will be used to represent the application.
2. `GITHUB_SECRET`: A [personal access token](https://github.blog/2013-05-16-personal-api-tokens/) for the GitHub user. The token must have the following scopes: `repo` and `read:org`.
3. `GITHUB_WEBHOOK_SECRET`: [A shared secret between GitHub and the application](https://developer.github.com/webhooks/securing/#setting-your-secret-token).
4. `GITHUB_ORGANIZATION`: The name of the GitHub organisation which users must be a member of to have pull request environments enabled.
5. `GITHUB_TEAM`: The name of the GitHub organisation tram which users must be a member of to have pull request environments enabled.
6. `PLATFORMSH_API_TOKEN`: The [Platform.sh API token](Platform.sh API) to use to access the Platform.sh API.
7. `PLATFORMSH_PROJECT`: The id of the Platform.sh project to use when publishing pull request environments. When you go to the project console the id is the trailing part of the url: `https://console.platform.sh/[organization]/[project_id]`.
8. `GIT_REPO_URL`: The url for the project repository on Platform.sh.
9. `GIT_PRIVATE_KEY`: The SSH private key to use when synchronizing commits from pull requests to Platform.sh. The corresponding [public key must be added to the Platform.sh user](https://docs.platform.sh/development/ssh.html#add-the-ssh-key-to-your-platform-account) which will be used to perform the synchronization.

These can be defined as environment variables or by creating an [`.env.local` file](https://symfony.com/doc/current/configuration.html#configuration-based-on-environment-variables) in the root of the checkout which contains the environment variables.

#### Run the application

Running the application consists of two parts:

1. Create an endpoint for receiving HTTP requests by exposing `public/index.php`. In production this can be done using PHP-FPM and a webserver like Apache or NGINX. Note the url for the endpoint.
2. Run `./bin/console messenger:consume` one or more times to process queue events. In production [this can be handled using Supervisor](https://symfony.com/doc/current/messenger.html#supervisor-configuration).

#### Configure GitHub

[Add the application as a webhook](https://developer.github.com/webhooks/creating/) for the project with the following configuration:

1. **Payload URL**: `[endpoint-url]/webhook`
2. **Content type**: `application/json`
3. **Secret**: Use the value you configured in your environment
4. **SSL verification**: Enabled (assuming your environment supports SSL)
5. **Which events would you like to trigger this webhook**
    1. Let me select individual events
    2. Pull requests
6. **Active**: Enabled

Click "Add webhook".

Under "Recent Deliveries" a successful delivery should appear soon after. This indicates that GitHub is able to call the application without problems and that the secret is setup correctly.

#### Test integration

To test that the integration between GitHub and Platform.sh works create a branch with a dummy commit in the project codebase:

```
git checkout -b platformsh-test
git commit --allow-empty -m "This is just a test commit"
git push origin platformsh-test
```

Then open a pull request from the branch against the default branch for the repository. The user opening the pull request should be member of the organisation and team configured in the environment.

Now the `continuous-integration/platformsh` should appear in the list of status checks for the pull request displaying the status of the deployment to Platform.sh.

## Architecture

The architecture of the system is based on the application:

 1. Responding to GitHub webhooks
 2. Using the GitHub API to determine group membership and update pull requests
 3. Using Platform.sh API and project git repository to manage environments

 The process is described in the following diagram:

![Architecture](/docs/architecture.svg)

A key element in the application is the fact that it will push code from the project pull request branch to a corresponding branch in the project Platform.sh repository. The Platform.sh git implementation will then automatically create an environment for the branch.

## Development

For development purposes you can run both parts of the application using [Docker](https://docs.docker.com/install/) and [Docker Compose](https://docs.docker.com/compose/):

```
docker-compose up
```

In this setup [ngrok](https://ngrok.com/) is used to expose the application externally. The url for the application can be identified by accessing the ngrok user interface.

In this setup the url for the webhook will be `https://[unique-id].ngrok.io/webhook`.

## Unit tests

Tests are implemented using [PHPUnit](https://phpunit.de/). Run the tests:

```
./bin/phpunit
```

## Serverless deployment

The application can be deployed to [Amazon AWS Lambda](https://aws.amazon.com/lambda/) and invoked there using [Bref](https://bref.sh/), the [Serverless framework](https://serverless.com/cli/) and a [Git layer](https://github.com/lambci/git-lambda-layer).

The configuration of the application is defined in the included `serverless.yml` file and deployment is handled automatically in the included `push.yaml` GitHub Actions workflow.

To use these features then complete the following steps:

1. [Create an AWS key/secret pair](https://bref.sh/docs/installation/aws-keys.html)

2. Add [your AWS key and secret used to setup the application](https://bref.sh/docs/installation/aws-keys.html) as [GitHub secrets for the repository](https://help.github.com/en/articles/virtual-environments-for-github-actions#creating-and-using-secrets-encrypted-variables).

3. Add the [environment variable values](#configure-the-environment) to the [Amazon SSM parameter store](https://docs.aws.amazon.com/systems-manager/latest/userguide/systems-manager-parameter-store.html).

If you have the [AWS cli](https://docs.aws.amazon.com/cli/latest/userguide/cli-chap-install.html) installed this can be achieved through the following commands:

```shell script
aws ssm put-parameter --region eu-central-1 --name /platformsh-github-authenticator/aws-target-region --type String --value 'eu-central-1'
aws ssm put-parameter --region eu-central-1 --name /platformsh-github-authenticator/aws-key --type String --value '[YOUR_AWS_KEY]'
aws ssm put-parameter --region eu-central-1 --name /platformsh-github-authenticator/aws-secret --type String --value '[YOUR_AWS_SECRET]'
aws ssm put-parameter --region eu-central-1 --name /platformsh-github-authenticator/github-username --type String --value '${GITHUB_USERNAME}'
aws ssm put-parameter --region eu-central-1 --name /platformsh-github-authenticator/github-secret --type String --value '${GITHUB_SECRET}'
aws ssm put-parameter --region eu-central-1 --name /platformsh-github-authenticator/github-webhook-secret --type String --value '${GITHUB_WEBHOOK_SECRET}'
aws ssm put-parameter --region eu-central-1 --name /platformsh-github-authenticator/github-organization --type String --value '${GITHUB_ORGANIZATION}'
aws ssm put-parameter --region eu-central-1 --name /platformsh-github-authenticator/github-team --type String --value '${GITHUB_TEAM}'
aws ssm put-parameter --region eu-central-1 --name /platformsh-github-authenticator/platformsh-api-token --type String --value '${PLATFORMSH_API_TOKEN}'
aws ssm put-parameter --region eu-central-1 --name /platformsh-github-authenticator/platformsh-project --type String --value '${PLATFORMSH_PROJECT}'
aws ssm put-parameter --region eu-central-1 --name /platformsh-github-authenticator/git-repo-url --type String --value '${GIT_REPO_URL}'
aws ssm put-parameter --region eu-central-1 --name /platformsh-github-authenticator/git-private-key --type String --value '${GIT_PRIVATE_KEY}'
```

Now, when you push to the `master` and `develop` branches for the repository then the application will de deployed to the `prod` and `dev` environments for the application respectively.

The resulting deployment should have the following output:

```
Serverless: Packaging service...
Serverless: Excluding development dependencies...
Serverless: Uploading CloudFormation file to S3...
Serverless: Uploading artifacts...
Serverless: Uploading service platformsh-github-authenticator.zip file to S3 (8.72 MB)...
Serverless: Validating template...
Serverless: Updating Stack...
Serverless: Checking Stack update progress...
..............................
Serverless: Stack update finished...
Service Information
service: platformsh-github-authenticator
stage: dev
region: eu-central-1
stack: platformsh-github-authenticator-[environment]
resources: 16
api keys:
  None
endpoints:
  ANY - https://[unique-id].execute-api.eu-central-1.amazonaws.com/[environment]
  ANY - https://[unique-id].execute-api.eu-central-1.amazonaws.com/[environment]/{proxy+}
functions:
  api: platformsh-github-authenticator-[environment]-api
  worker: platformsh-github-authenticator-[environment]-worker
layers:
  None
Serverless: Removing old service artifacts from S3...
Serverless: Run the "serverless" command to setup monitoring, troubleshooting and testing.
```

Here the endpoint should be used as `[endpoint-url]` when [configuring the Payload URL of the GitHub webhook](#configure-github).

## Built With

* [Symfony 4](https://symfony.com/4) - Web framework
