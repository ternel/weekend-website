# AGENTS.md

Instructions for AI agents working on this project.

## Fundamental rule: never run PHP/Node/Composer on the host machine

Everything goes through the `builder` container, via Castor:

```bash
castor builder -- bin/console cache:clear    # one-off command
```

Host prerequisites only: Docker, Bash, Castor.

## Essential commands

```bash
castor start                                 # build + install + up
castor stop                                  # stop the stack
castor logs [--service=service]              # logs (frontend, builder, ...)
castor app:install                           # composer install + qa:install
castor update [--with-tools]                 # composer update of the application
```

Docker:

```bash
castor docker:build [--service=service]
castor docker:up [--service=service]
castor docker:ports                          # ports of the current project / worktree
```

## Castor contexts

The context changes how tasks are executed (`APP_ENV`, compose files, etc.):

```bash
castor --context=test qa:phpunit             # APP_ENV=test, for tests
castor --context=ci ...                      # like test, tuned for CI
```

Always run tests with `--context=test`.
Without option, the `default` context applies.

## Stack

- Symfony in `application/` (docroot = `application/public`), no database
- nginx + php-fpm (service `frontend`), Traefik router, HTTPS on `<root_domain>` (see `castor.php`)
- Node/yarn only inside the `builder` container
- Production is deployed with `castor deploy` (SSH + git pull + composer install)

## QA — before considering a task done

Tools run inside the builder.

```bash
castor qa                                    # everything: cs + phpstan + twig-cs + phpunit
castor qa:cs [--dry-run]                     # PHP-CS-Fixer (.php-cs-fixer.php)
castor qa:phpstan [-b]                       # PHPStan level 8 (phpstan.neon)
castor qa:twig-cs                            # Twig-CS-Fixer
castor qa:phpunit                            # PHPUnit (if present in application/)
```

After any PHP/Twig code change: `castor qa:cs --dry-run`,
`castor qa:phpstan`, then `castor qa:phpunit`.

## Conventions

1. **Never invoke `docker compose` by hand**: use the `docker_compose()` /
   `docker_compose_run()` functions from `.castor/docker.php` to write new tasks.
2. **Never hardcode ports or project names**: git worktree support automatically
   isolates project/volumes/ports. Use `variable('project_name')` etc.
3. New recurring task? Make it a Castor task (`castor.php` or `.castor/*.php`),
   not a shell script.
4. QA tool dependencies live in `tools/<tool>/composer.json`
   (not in `application/composer.json`).
