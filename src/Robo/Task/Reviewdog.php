<?php

namespace ForumOne\CodeQuality\Robo\Task;

use Robo\Contract\BuilderAwareInterface;
use Robo\Task\BaseTask;
use Robo\TaskAccessor;

class Reviewdog extends BaseTask implements BuilderAwareInterface {

  use TaskAccessor;
  use \Robo\Task\Base\loadTasks;

  protected $reportFile = '';
  protected $format = 'checkstyle';
  protected $diff = 'git diff';

  public function reportFile(string $reportFile) {
    $this->reportFile = $reportFile;

    return $this;
  }

  public function format(string $format) {
    $this->format = $format;

    return $this;
  }


  public function diff(string $diff) {
    $this->diff = $diff;

    return $this;
  }

  public function run() {
    $command = sprintf('cat "%s" | reviewdog -f=%s -diff="%s"',
      $this->reportFile,
      $this->format,
      $this->diff
    );

    return $this->taskExec($command)->run();
  }
}
