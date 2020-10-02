<?php

namespace ForumOne\CodeQuality\Robo\Task;

use Robo\Contract\BuilderAwareInterface;
use Robo\Task\BaseTask;
use Robo\TaskAccessor;

abstract class CodeQualityBaseTask extends BaseTask implements BuilderAwareInterface {

  use TaskAccessor;
  use \Robo\Task\Filesystem\loadTasks;

  /**
   * The path to be scanned.
   *
   * @var string
   */
  protected $path;

  /**
   * The format the tool's report should be output in.
   *
   * @var string
   */
  protected $format = 'checkstyle';

  /**
   * The path to the file the report should be written to.
   *
   * @var string
   */
  protected $reportFile;

  /**
   * Track if a configuration preset has been applied.
   *
   * @var string
   */
  protected $preset;

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
    assert(isset(static::PRESETS[$preset]),
      sprintf('Unknown preset: "%s"', $preset));

    $this->preset = $preset;

    // Assign all preset values into object properties.
    foreach (static::PRESETS[$preset] as $key => $value) {
      assert(property_exists($this, $key),
        sprintf('Unknown preset attribute: "%s" in preset "%s"', $key, $preset));
      $this->$key = $value;
    }

    return $this;
  }

  /**
   * Assign the path to be scanned.
   *
   * @param string $path
   *
   * @return $this
   */
  public function path(string $path) {
    $this->path = $path;

    return $this;
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
   * Set the report format for output.
   *
   * @param string $format
   */
  public function format(string $format) {
    $this->format = $format;
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

}
