<?php

namespace ForumOne\CodeQuality\Robo\Task\PhpStan;

use Robo\Contract\BuilderAwareInterface;
use Robo\Task\BaseTask;
use Robo\TaskAccessor;

class PhpStan extends BaseTask implements BuilderAwareInterface {

  use TaskAccessor;
  use \Robo\Task\Filesystem\loadTasks;
  use \Robo\Task\Base\loadTasks;

  protected $reportsPath = 'tests/reports/phpstan/';
  protected $reportName = 'phpstan.xml';
  protected $extensions;
  protected $ignore_patterns;
  protected $standard;
  protected $basepath = '/code';
  protected $path;

  public function __construct($path) {
    $this->path = $path;
  }

  /**
   * @param string $reportsPath
   *
   * @return $this
   */
  public function reportsPath(string $reportsPath) {
    $this->reportsPath = $reportsPath;

    return $this;
  }

  /**
   * @param string $reportName
   *
   * @return $this
   */
  public function reportName(string $reportName) {
    $this->reportName = $reportName;

    return $this;
  }

  /**
   * @param string $extensions
   *
   * @return $this
   */
  public function extensions(string $extensions) {
    $this->extensions = $extensions;

    return $this;
  }

  /**
   * @param string $ignore_patterns
   *
   * @return $this
   */
  public function ignore_patterns(string $ignore_patterns) {
    $this->ignore_patterns = $ignore_patterns;

    return $this;
  }

  /**
   * @param string $standard
   *
   * @return $this
   */
  public function standard(string $standard) {
    $this->standard = $standard;

    return $this;
  }

  /**
   * @param string $basepath
   *
   * @return $this
   */
  public function basepath(string $basepath) {
    $this->basepath = $basepath;

    return $this;
  }

  /**
   * Get the configured execution task for adding to a collection.
   *
   * @return \Robo\Collection\CollectionBuilder|\Robo\Task\Base\Exec
   */
  protected function getExecTask() {
    // Assemble the command with dynamic arguments using the taskExec structure.
    $execTask = $this->taskExec('phpstan analyse')
      ->option('error-format', 'checkstyle', '=')
      ->arg($this->path);

    // Pull the assembled command into a separate execution task to enable
    // funneling the output to a file for ingestion from other tools.
    $reportFile = $this->reportsPath . $this->reportName;
    return $this->taskExec($execTask->getCommand() . ' > ' . $reportFile);
  }

  public function run() {
    $collection = $this->collectionBuilder();

    if (!file_exists($this->reportsPath)) {
      $collection->addTask($this->taskFilesystemStack()
        ->mkdir($this->reportsPath));
    }
    else {
      $collection->addTask($this->taskCleanDir($this->reportsPath));
    }

    $collection->addTask($this->getExecTask());

    return $collection->run();
  }
}
