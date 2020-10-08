# Forum One Code Quality Utilities

These utilities serve as a packaging method for common PHP code quality project
dependencies and Docker configuration for usage.

## Tools Included

### [Reviewdog](https://github.com/reviewdog/reviewdog)

Reviewdog is used as a utility for filtering testing results and reporting
relevant results for a subset of changes. Reporting may be output in a number
of formats including local output and publishing to GitHub PRs.

### [Robo](https://github.com/consolidation/Robo)

Robo is a PHP task-runner used to automate project-level task execution. This
package includes a variety of commands to be detected and run within a given
project. These are primarily focused on usage locally and within Forum One CI
processes to produce code quality reports using various tools.

### [wodby/php](https://github.com/wodby/php)

The [wodby/php](https://github.com/wodby/php) image is used as the base for the
utility image configured here. This image is very flexible through the use of
environment variables configured at build and runtime. Documentation for the
[PHP extensions](https://github.com/wodby/php#php-extensions) included in this
image and the [environment variables](https://github.com/wodby/php#environment-variables)
available for customization may be found within the
[documentation for this image](https://github.com/wodby/php/blob/master/README.md).

## Project Usage

Usage of this toolset within a project will require incorporation at two levels:

* Composer
* Docker Compose

### Install Composer Dependencies

Within your PHP project, add this package as a dev dependency by adding this
repository within the `repositories` section of your `composer.json` file:

```json
{
  "type": "vcs",
  "url": "https://github.com/forumone/code-quality"
}
```

Once this is added so the package is discoverable, require the package as dev
dependency via Composer:

```bash
composer require --dev forumone/code-quality
```

Note additional dependencies may be suggested within the [composer.json][] file
during installation and should be added based on the CMS being used within your
project.

#### Drupal Projects

```
composer require --dev drupal/coder mglaman/phpstan-drupal
```

#### WordPress Projects

* [ ] Identify and add dependencies for WordPress projects.

### Docker-Compose Configuration

Making the Docker image available for usage within your project is easiest to configure within a `docker-compose.yml` file. For most Forum One projects, this should be added to the `docker-compose.cli.yml` file specifically, and may be added using the following snippet within the `services` key:
```yaml
  code-quality:
    build:
      context: https://github.com/forumone/code-quality.git
    volumes:
      - ./:/var/www/html:cached
#    environment:
#      PHP_XDEBUG: 1
#      PHP_XDEBUG_DEFAULT_ENABLE: 1
#      PHP_XDEBUG_REMOTE_CONNECT_BACK: 0
#      PHP_IDE_CONFIG: serverName=my-ide
#      PHP_XDEBUG_IDEKEY: "my-ide"
#      PHP_XDEBUG_REMOTE_HOST: host.docker.internal # Docker 18.03+ Mac/Win
#      PHP_XDEBUG_REMOTE_HOST: 172.17.0.1 # Linux
#      PHP_XDEBUG_REMOTE_HOST: 10.254.254.254 # macOS, Docker < 18.03
#      PHP_XDEBUG_REMOTE_HOST: 10.0.75.1 # Windows, Docker < 18.03
#      PHP_XDEBUG_REMOTE_LOG: /tmp/php-xdebug.log
```
Some pre-defined configuration variables are commented out within this snippet to enable Xdebug within the image for debugging. These may be uncommented as needed, and additional environment variables are available as documented for the [wodby/php image](https://github.com/wodby/php#environment-variables).

## Usage
Assuming all Composer dependencies have been installed and the proper Docker Compose configuration is in place, the Robo tasks added by this project should be discovered and listed via this command assuming usage of the [forumone/forumone-cli utility](https://github.com/forumone/forumone-cli):
```bash
f1 run code-quality robo
```
At this point, any of the provided Robo tasks may be run using the format:
```bash
f1 run code-quality robo <task>
```
### Available Tasks
- [ ] Document provided tasks

#### `run:code-sniffer`

#### `run:phpcs`

#### `run:phpstan`

### Configuration
- [ ] Document complete configuration options available per tool.

Execution of the provided tasks may also be customized dynamically using a `robo.yml` file placed within the application directory alongside the `RoboFile.php` file.

Example configuration:
```yaml
task:
  Phpcs:
    settings:
      # Preset options are "drupal8" or "wordpress".
      preset: "drupal8"
      # If a non-standard CMS path covered by the preset is used,
      # it may be specified using this parameter.
      path: "services/drupal"
```
## Development
Customization of the base PHP image may be handled through environment variables defined within the project's `docker-compose.yml` file as described above. Configuration to enable Xdebug is documented [here](https://wodby.com/docs/1.0/stacks/php/local/#xdebug).
