<?php

namespace ForumOne\CodeQuality\Robo\Task\Phpcs;

use Robo\Contract\BuilderAwareInterface;
use Robo\Task\BaseTask;
use Robo\TaskAccessor;

class Reviewdog extends BaseTask implements BuilderAwareInterface {

  use TaskAccessor;
  use \Robo\Task\Filesystem\loadTasks;
  use \Robo\Task\Base\loadTasks;

  protected $reportsPath = 'tests/reports/phpcs/';
  protected $reportName = 'phpcs.xml';
  protected $basepath = '/code';
  protected $gitDiff = 'git diff';

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
   * @param string $basepath
   *
   * @return $this
   */
  public function basepath(string $basepath) {
    $this->basepath = $basepath;

    return $this;
  }

  public function gitDiff(string $gitDiff) {
    $this->gitDiff = $gitDiff;

    return $this;
  }

  protected function runReviewdog() {
    $tasks = [];
//
    $command = sprintf('cat "%s" | reviewdog -f=checkstyle -diff="%s"',
      $this->reportsPath . $this->reportName,
      $this->gitDiff
    );

//    $command = 'cat "tests/reports/phpcs/phpcs.xml"';
    $tasks[] = $this->taskExec($command);

    return $tasks;
  }

  public function run() {
    $collection = $this->collectionBuilder();
    $collection->addTaskList($this->runReviewdog());

    return $collection->run();
  }
}
