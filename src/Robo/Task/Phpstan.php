<?php

namespace ForumOne\CodeQuality\Robo\Task;

use Robo\Result;
use Robo\Task\Base\Exec;

class Phpstan extends CodeQualityBaseTask {

  use \Robo\Task\Base\loadTasks;
  use \Robo\Task\File\loadTasks;

  /**
   * {@inheritdoc}
   */
  protected $format = 'checkstyle';

  /**
   * {@inheritdoc}
   */
  protected $reportFile = 'tests/reports/phpstan/phpstan.xml';

  /**
   * Specifies the path to a configuration file.
   *
   * @var string
   */
  protected $configuration = '';

  const PRESETS = [
    'drupal8' => [
      'path' => 'services/drupal/public/',
    ],
    'wordpress' => [
      'path' => 'services/wordpress/public/',
    ],
  ];

  /**
   * Get a list of configurable options for the command.
   *
   * @return string[]
   *   A list of configurable options to customize the command execution.
   */
  public function getAvailableOptions() {
    // @todo Expand this more completely.
    return [
      'path',
    ];
  }

  /**
   * Add options to the execute task based on class configuration.
   *
   * @param \Robo\Task\Base\Exec $task
   *   The Exec task being configured.
   *
   * @return \Robo\Task\Base\Exec
   *   The configured task.
   */
  protected function configureOptions(Exec $task) {
    // Add remaining options.
    foreach ($this->getAvailableOptions() as $option) {
      // Skip assignment for any options that were handles independently.
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
   * Set the provided option value on the task.
   *
   * Process the format of the value being set to structure the option
   * assignment appropriately.
   *
   * @param \Robo\Task\Base\Exec $task
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
  protected function setOption(Exec $task, string $option, $value = NULL) {
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
   * Get the configured execution task for adding to a collection.
   *
   * @return \Robo\Collection\CollectionBuilder|\Robo\Task\Base\Exec
   *   The configured task for execution.
   */
  public function taskPhpstan() {
    // Assemble the command with dynamic arguments using the taskExec structure.
    $execTask = $this->taskExec('phpstan analyse')
      ->option('error-format', $this->getWithPreset('format'), '=')
      ->arg($this->getWithPreset('path', '.'))
      // Pipe output to a file for separate processing.
      ->rawArg("> $this->reportFile");

    return $execTask;
  }

  /**
   * {@inheritdoc}
   */
  public function run() {
    $this->taskPrepare()->run();
    $result = $this->taskPhpstan()->run();

    // Triage acceptable return codes.
    switch ($result->getExitCode()) {
      case 0:
        $result->setMessage('No errors found.');
        break;

      case 1:
        $result = Result::success($this, 'Found warnings.', $result->getData());
        break;
    }

    return $result;
  }

}
