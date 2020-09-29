<?php

namespace ForumOne\CodeQuality\Robo\Task\Phpcs;

use Robo\Contract\BuilderAwareInterface;
use Robo\Task\BaseTask;
use Robo\TaskAccessor;

class Phpcs extends BaseTask implements BuilderAwareInterface {

  use TaskAccessor;
  use \Robo\Task\Filesystem\loadTasks;
  use \Robo\Task\Base\loadTasks;

  protected $reportsPath = 'tests/reports/phpcs/';
  protected $reportName = 'phpcs.xml';
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

  protected function getExecTask() {
    return  $this->taskExec('phpcs')
      ->option('report', 'checkstyle', '=')
      ->option('report-checkstyle', $this->reportsPath . $this->reportName, '=')
      ->option('report', 'summary', '=')
      ->option('extensions', $this->extensions, '=')
      ->option('ignore', $this->ignore_patterns, '=')
      ->option('standard', $this->standard, '=')
      ->option('basepath', $this->basepath, '=')
      ->arg($this->path);
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
