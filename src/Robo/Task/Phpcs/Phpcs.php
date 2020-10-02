<?php

namespace ForumOne\CodeQuality\Robo\Task\Phpcs;

use Robo\Contract\BuilderAwareInterface;
use Robo\Task\BaseTask;
use Robo\TaskAccessor;

class Phpcs extends BaseTask implements BuilderAwareInterface {

  use TaskAccessor;
  use \Robo\Task\Filesystem\loadTasks;
  use \Robo\Task\Base\loadTasks;

  protected $reportFile = 'tests/reports/phpcs/phpcs.xml';
  protected $format = 'checkstyle';
  protected $extensions;
  protected $ignore_patterns;
  protected $standard;
  protected $basepath = '/code';
  protected $path;
  protected $preset;

  const PRESETS = [
    'drupal8' => [
      'reportFile' => 'tests/reports/phpcs/phpcs.xml',
      'format' => 'checkstyle',
      'extensions' => 'php,module,inc,profile,theme,install',
      'ignore_patterns' => 'contrib/,core/,vendor/',
      'standard' => 'Drupal,DrupalPractice',
      'path' => 'services/drupal/public/',
    ],
    'wordpress' => [
      'reportFile' => 'tests/reports/phpcs/phpcs.xml',
      'format' => 'checkstyle',
      'extensions' => 'php,inc',
      'standard' => 'WordPress',
      'path' => 'services/wordpress/',
    ]
  ];

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
    assert(isset(self::PRESETS[$preset]),
      sprintf('Unknown preset: "%s"', $preset));

    $this->preset = $preset;

    // Assign all preset values into object properties.
    foreach (self::PRESETS[$preset] as $key => $value) {
      assert(property_exists($this, $key),
        sprintf('Unknown preset attribute: "%s" in preset "%s"', $key, $preset));
      $this->$key = $value;
    }

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
   * Set file extensions to filter scanning.
   *
   * @param string $extensions
   *
   * @return $this
   */
  public function extensions(string $extensions) {
    $this->extensions = $extensions;

    return $this;
  }

  /**
   * Set patterns to ignore for scanning.
   *
   * @param string $ignore_patterns
   *
   * @return $this
   */
  public function ignore_patterns(string $ignore_patterns) {
    $this->ignore_patterns = $ignore_patterns;

    return $this;
  }

  /**
   * Set standards for code sniffs.
   *
   * @param string $standard
   *
   * @return $this
   */
  public function standard(string $standard) {
    $this->standard = $standard;

    return $this;
  }

  /**
   * Set the base path to be removed from result paths.
   *
   * @param string $basepath
   *
   * @return $this
   */
  public function basepath(string $basepath) {
    $this->basepath = $basepath;

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
  public function taskPhpcs() {
    return  $this->taskExec('phpcs')
      ->option('report', 'summary', '=')
      ->option('report-checkstyle', $this->reportFile, '=')
      ->option('extensions', $this->extensions, '=')
      ->option('ignore', $this->ignore_patterns, '=')
      ->option('standard', $this->standard, '=')
      ->option('basepath', $this->basepath, '=')
      ->arg($this->path);
  }

  /**
   * {@inheritdoc}
   */
  public function run() {
    return $this->collectionBuilder()
      ->addTask($this->taskPrepare())
      ->addTask($this->taskPhpcs())
      ->run();
  }

}
