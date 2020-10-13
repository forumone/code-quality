<?php

namespace ForumOne\CodeQuality\Robo\Task;

class Phpcs extends CodeQualityBaseTask {

  use \Robo\Task\Base\loadTasks;

  /**
   * {@inheritdoc}
   */
  protected $reportFile = 'tests/reports/phpcs/phpcs.xml';

  /**
   * {@inheritdoc}
   */
  protected $format = 'checkstyle';

  /**
   * An array of report outputs.
   *
   * A string value creates the parameter for `report=<value>`, but a string
   * tuple of the format `[<format>, <file>]` will result in the option output
   * of `report-<format>=<file>`.
   *
   * @var array
   */
  protected $report = [];
  protected $extensions = [];
  protected $ignore = [];
  protected $standard = [];

  /**
   * A path prefix to be filtered from all reporteded results.
   *
   * Define the current path as the basepath to simplify file output by removing
   * container-specific absolute paths.
   *
   * @var string
   */
  protected $basepath = './';

  /**
   * The format of reporting to print to stdOut during execution.
   *
   * Defaults to "summary".
   *
   * @var string|false
   */
  protected $stdOutFormat = 'summary';

  const PRESETS = [
    'drupal8' => [
      'extensions' => 'php,module,inc,profile,theme,install',
      'ignore' => 'contrib/,core/,vendor/',
      'standard' => 'Drupal,DrupalPractice',
      'path' => 'services/drupal/public/',
    ],
    'wordpress' => [
      'extensions' => 'php,inc',
      'standard' => 'WordPress',
      'path' => 'services/wordpress/',
    ],
  ];

  /**
   * Get the configured execution task for adding to a collection.
   *
   * @return \Robo\Collection\CollectionBuilder|\Robo\Task\Base\Exec
   *   The configured task for execution.
   */
  public function taskPhpcs() {
    $task = $this->taskExec('phpcs');
    $this->configureOptions($task);

    $task->arg($this->getWithPreset('path', '.'));

    return $task;
  }

  /**
   * Get a list of configurable options for the command.
   *
   * @return string[]
   *   A list of configurable options to customize the command execution.
   */
  public function getAvailableOptions() {
    // @todo Expand this more completely.
    return [
      'extensions',
      'ignore',
      'standard',
      'basepath',
      'report',
      'path',
    ];
  }

  /**
   * Format the report configuration options for use as task options.
   *
   * Translate the $report property into a usable format to assign the
   * `report=<format>` and `report-<format>=<file>` options.
   *
   * @return array
   *   A tuple of values of the format [<option>, <value>].
   */
  protected function getReportOptions() {
    $options = [];

    // Set stdOut formatting first.
    if ($this->stdOutFormat) {
      $options[] = ['report', $this->stdOutFormat];
    }

    // Add default report options first.
    $options[] = ["report", $this->format];
    $options[] = ["report-{$this->format}", $this->reportFile];

    foreach ($this->report as $item) {
      if (is_array($item)) {
        [$format, $file] = $item;
        $options[] = ["report-$format", $file];
      }
      else {
        $format = $item;
        $options[] = ['report', $format];
      }
    }

    return $options;
  }

  /**
   * Set the provided option value on the task.
   *
   * Process the format of the value being set to structure the option
   * assignment appropriately.
   *
   * @param $task
   *   The task to be configured.
   * @param string $option
   *   The name of the option being set.
   * @param string|bool|string[]|null $value
   *   (Optional) A specific value to assign to the option. If no value is
   *   provided, the property matching $option on this class will be used.
   *
   * @return bool
   *   Returns TRUE if the option was successfully set. FALSE otherwise.
   */
  protected function setOption($task, string $option, $value = NULL) {
    $success = FALSE;

    // Default the value if one is not provided.
    if (is_null($value)) {
      $value = $this->$option;
    }

    // Aggregate multi-value option values.
    if (is_array($value)) {
      // Ignore empty array values.
      if (count($value) > 0) {
        $value_aggregate = implode(',', $value);
        $task->option($option, $value_aggregate, '=');
        $this->printTaskDebug(sprintf('Set array task option "%s" = "%s"', $option, $value_aggregate));
        $success = TRUE;
      }
    }
    // Handle boolean options with no values.
    elseif (is_bool($value) && $value) {
      $task->option($option);
      $this->printTaskDebug(sprintf('Toggled boolean task option "%s" to "%s"', $option, $value));
      $success = TRUE;
    }
    else {
      assert(is_string($value), sprintf('The value "%s" for option "%s" is expected to be a string.', $value, $option));
      $task->option($option, $value, '=');
      $this->printTaskDebug(sprintf('Set string task option "%s" = "%s"', $option, $value));
      $success = TRUE;
    }

    return $success;
  }

  /**
   * Add options to the execute task based on class configuration.
   *
   * @param $task
   *   The Exec task being configured.
   *
   * @return mixed
   *   The configured task.
   */
  protected function configureOptions($task) {

    // Add report options first.
    foreach ($this->getReportOptions() as $option_tuple) {
      [$option, $value] = $option_tuple;
      assert(is_string($option), sprintf('Option name "%s" is expected to be a string.', $option));
      assert(is_string($value), sprintf('Option value "%s" is expected to be a string.', $value));
      $task->option($option, $value, '=');
    }

    // Add remaining options.
    foreach ($this->getAvailableOptions() as $option) {
      // Skip assignment for any options that were handled independently.
      if (in_array($option, ['report', 'path'])) {
        continue;
      }

      $value = $this->getWithPreset($option);
      if (!is_null($value)) {
        $this->setOption($task, $option, $value);
      }
    }

    return $task;
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
