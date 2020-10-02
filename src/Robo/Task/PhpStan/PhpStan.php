<?php

namespace ForumOne\CodeQuality\Robo\Task\PhpStan;

use Robo\Contract\BuilderAwareInterface;
use Robo\Task\BaseTask;
use Robo\TaskAccessor;

class PhpStan extends BaseTask implements BuilderAwareInterface {

  use TaskAccessor;
  use \Robo\Task\Filesystem\loadTasks;
  use \Robo\Task\Base\loadTasks;

  protected $reportFile = 'tests/reports/phpstan/phpstan.xml';
  protected $path;

  const PRESET = [
    'drupal8' => [
      'reportFile' => 'tests/reports/phpstan/phpstan.xml',
      'format' => 'checkstyle',
      'path' => 'services/drupal/public/',
    ],
    'wordpress' => [
      'reportFile' => 'tests/reports/phpstan/phpstan.xml',
      'format' => 'checkstyle',
      'path' => 'services/wordpress/public/',
    ]
  ];

  /**
   * Set the path to be scanned.
   *
   * @param string $path
   */
  public function path(string $path) {
    $this->path = $path;
  }

  /**
   * Set the file to write result output to.
   *
   * @param string $reportFile
   *
   * @return $this
   */
  public function reportFile(string $reportFile) {
    $this->reportFile = $reportFile;

    return $this;
  }

  /**
   * Assign known preset values for default configuration.
   *
   * These defaults may be further customized with additional property-
   * specific assignments afterward.
   *
   * @param string $preset
   *
   * @return $this
   */
  public function preset(string $preset) {
    assert(isset(self::PRESET[$preset]),
      sprintf('Unknown preset: "%s"', $preset));

    // Assign all preset values into object properties.
    foreach (self::PRESET[$preset] as $key => $value) {
      assert(property_exists($this, $key),
        sprintf('Unknown preset attribute: "%s" in preset "%s"', $key, $preset));
      $this->$key = $value;
    }

    return $this;
  }

  /**
   * Prepare the task report directory for generating a new report file.
   *
   * Creates the report directory if it doesn't exist, or cleans it if it does.
   *
   * @return \Robo\Collection\CollectionBuilder|\Robo\Task\Filesystem\CleanDir|\Robo\Task\Filesystem\FilesystemStack
   */
  public function taskPrepare() {
    // Create or clean the reports directory as needed.
    $reportsDirectory = dirname($this->reportFile);
    if (!file_exists($reportsDirectory)) {
      $task = $this->taskFilesystemStack()
        ->mkdir($reportsDirectory);
    }
    else {
      $task = $this->taskCleanDir($reportsDirectory);
    }

    return $task;
  }

  /**
   * Get the configured execution task for adding to a collection.
   *
   * @return \Robo\Collection\CollectionBuilder|\Robo\Task\Base\Exec
   */
  public function taskPhpstan() {
    // Assemble the command with dynamic arguments using the taskExec structure.
    $execTask = $this->taskExec('phpstan analyse')
      ->option('error-format', 'checkstyle', '=')
      ->arg($this->path);

    // Pull the assembled command into a separate execution task to enable
    // funneling the output to a file for ingestion from other tools.
    return $this->taskExec($execTask->getCommand() . ' > ' . $$this->reportFile);
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
