<?php

namespace ForumOne\CodeQuality\Robo\Task\Phpcs;

use ForumOne\CodeQuality\Robo\Task\CodeQualityBaseTask;

class Phpcs extends CodeQualityBaseTask {

  use \Robo\Task\Base\loadTasks;

  protected $reportFile = 'tests/reports/phpcs/phpcs.xml';
  protected $format = 'checkstyle';
  protected $extensions;
  protected $ignore_patterns;
  protected $standard;
  protected $basepath = '/code';

  const PRESETS = [
    'drupal8' => [
      'extensions' => 'php,module,inc,profile,theme,install',
      'ignore_patterns' => 'contrib/,core/,vendor/',
      'standard' => 'Drupal,DrupalPractice',
      'path' => 'services/drupal/public/',
    ],
    'wordpress' => [
      'extensions' => 'php,inc',
      'standard' => 'WordPress',
      'path' => 'services/wordpress/',
    ]
  ];

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
