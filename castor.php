<?php

use Castor\Attribute\AsTask;

use function Castor\context;
use function Castor\guard_min_version;
use function Castor\import;
use function Castor\io;
use function Castor\notify;
use function Castor\run;
use function docker\about;
use function docker\build;
use function docker\docker_compose_run;
use function docker\up;

guard_min_version('1.5.0');

import(__DIR__ . '/.castor');

/**
 * @return array{project_name: string, root_domain: string}
 */
function create_default_variables(): array
{
    $projectName = 'weekend';
    $tld = 'test';

    return [
        'project_name' => $projectName,
        'root_domain' => "{$projectName}.{$tld}",
    ];
}

#[AsTask(description: 'Builds and starts the infrastructure, then install the application (composer, yarn, ...)')]
function start(): void
{
    io()->title('Starting the stack');

    build();
    install();
    up(profiles: ['default']);

    notify('The stack is now up and running.');
    io()->success('The stack is now up and running.');

    about();
}

#[AsTask(description: 'Installs the application (composer, yarn, ...)', namespace: 'app', aliases: ['install'])]
function install(): void
{
    io()->title('Installing the application');

    io()->section('Installing PHP dependencies');
    docker_compose_run('composer install -n --prefer-dist --optimize-autoloader');

    qa\install();
}

#[AsTask(description: 'Update dependencies')]
function update(bool $withTools = false): void
{
    io()->title('Updating dependencies...');

    docker_compose_run('composer update -o');

    if ($withTools) {
        qa\update();
    }
}

#[AsTask(description: 'Clears the application cache', namespace: 'app', aliases: ['cache-clear'])]
function cache_clear(bool $warm = true): void
{
    io()->title('Clearing the application cache');

    docker_compose_run('rm -rf var/cache/');

    if ($warm) {
        cache_warmup();
    }
}

#[AsTask(description: 'Warms the application cache', namespace: 'app', aliases: ['cache-warmup'])]
function cache_warmup(): void
{
    io()->title('Warming the application cache');

    docker_compose_run('bin/console cache:warmup', c: context()->withAllowFailure());
}

#[AsTask(description: 'Deploy the application to production')]
function deploy(): void
{
    io()->title('Deploying the application to production');

    $host = 'ternel@ternel.net';
    $srvdir = '/home/ternel/www/estcequecestbientotleweekend.fr/prod/current/';
    $branch = 'master';

    run(sprintf('ssh %s "cd %s && git checkout %s && git pull && composer install --no-dev -o"', $host, $srvdir, $branch));
}
