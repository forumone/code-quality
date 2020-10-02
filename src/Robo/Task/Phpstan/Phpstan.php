<?php

namespace ForumOne\CodeQuality\Robo\Task\Phpstan;

use ForumOne\CodeQuality\Robo\Task\CodeQualityBaseTask;
use Robo\TaskAccessor;

class Phpstan extends CodeQualityBaseTask {

  use TaskAccessor;
  use \Robo\Task\Base\loadTasks;

  protected $format = 'checkstyle';
  protected $reportFile = 'tests/reports/phpstan/phpstan.xml';
  protected $path;
  protected $preset;

  const PRESETS = [
    'drupal8' => [
      'path' => 'services/drupal/public/',
    ],
    'wordpress' => [
      'path' => 'services/wordpress/public/',
    ]
  ];

  /**
   * Get the configured execution task for adding to a collection.
   *
   * @return \Robo\Collection\CollectionBuilder|\Robo\Task\Base\Exec
   */
  public function taskPhpstan() {
    // Assemble the command with dynamic arguments using the taskExec structure.
    $execTask = $this->taskExec('phpstan analyse')
      ->option('error-format', $this->format, '=')
      ->arg($this->path);

    // Pull the assembled command into a separate execution task to enable
    // funneling the output to a file for ingestion from other tools.
    return $this->taskExec($execTask->getCommand() . ' > ' . $this->reportFile);
  }

  /**
   * {@inheritdoc}
   */
  public function run() {
    return $this->collectionBuilder()
      ->addTask($this->taskPrepare())
      ->addTask($this->taskPhpstan())
      ->run();
  }

}
