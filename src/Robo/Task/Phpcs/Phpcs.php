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

  const PRESET = [
    'drupal8' => [
      'reportFile' => 'tests/reports/phpcs/phpcs.xml',
      'format' => 'checkstyle',
      'extensions' => 'php,module,inc,profile,theme,install',
      'ignore_patterns' => 'contrib/,core/,vendor/',
      'standard' => 'Drupal,DrupalPractice',
      'path' => 'public/',
    ],
    'wordpress' => [

    ]
  ];

  public function __construct($path) {
    $this->path = $path;
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
   * @param string $reportFile
   *
   * @return $this
   */
  public function reportFile(string $reportFile) {
    $this->reportFile = $reportFile;

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

  public function run() {
    return $this->collectionBuilder()
      ->addTask($this->taskPrepare())
      ->addTask($this->taskPhpcs())
      ->run();
  }
}
