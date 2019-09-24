# Platform.sh GitHub Authenticator

Control who gets automated pull request environments on [Platform.sh](https://platform.sh) by using GitHub teams. Inspired by the [Jenkins GitHub pull request builder plugin]() for Jenkins.

One use case for this is to use Platform.sh for open source projects, where you want to use the pull request environment functionality to test changes - but only for certain users.

## Getting started

These instructions will get you a copy of the project up and running. See [Development](#development) for getting the project on your local machine for development and testing purposes. See [Deployment](#deployment) for notes on how to deploy the project on a live system.

### Prerequisites

To use this project you need the following:

1. PHP version 7.3 or newer
2. [Composer](https://getcomposer.org/)
3. A GitHub repository contaning the code for the project you want to deploy to Platform.sh including sufficient permissions to manage teams and webhooks.
4. A Platform.sh project to deploy the project code to.
5. A Platform.sh user with permission to commit code to the project.
6. An environment capable of processing HTTP requests using PHP. 

### Setup

####Checkout the GitHub Platform.sh Authenticator application codebase

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

1. `GITHUB_USERNAME`: The username of the GitHub user which will be used to represent the application. You may want to create a user specifically for this.
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

Explain how the application is structured.

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

## Integration testing

## Serverless deployment

Add additional notes about how to deploy this to Lambda.

## Built With

* [Symfony 4](https://symfony.com/4) - Web framework
* [Bref](https://bref.sh/) - Serverless framework
