<?php

namespace ForumOne\CodeQuality\Robo\Plugin\Commands;

class CodeSnifferCommands extends \Robo\Tasks {

  use \ForumOne\CodeQuality\Robo\Task\Tasks;

  /**
   * Run the code sniffer scans on custom code.
   *
   * @return \Robo\Result
   *   Results from task execution.
   */
  public function runCodeSniffer() {
    // @todo Execute multiple tools using a collection.
    return $this->runPhpcs();
  }

  /**
   * Run PHPCS on custom code.
   *
   * @return \Robo\Result
   *   Results from task execution.
   */
  protected function runPhpcs() {
    $this->say('Running phpcs...');
    // Run as an independent collection since any issues discovered cause a
    // non-zero return code which kills the execution of the rest of the
    // collection and prevents filtering of the results by reviewdog.
    $result = $this->taskPhpcs()->run();

    // Filter with Reviewdog if errors were found with an acceptable code.
    if ($result->wasSuccessful()) {
      $result->setMessage('No errors found.');
    }
    elseif (in_array($result->getExitCode(), [1, 2], TRUE)) {
      $this->say('Filtering results...');
      $result = $this->taskReviewdog()
        ->reportFile('tests/reports/phpcs/phpcs.xml')
        ->run();
    }

    return $result;
  }

  /**
   * Run the PHPStan on custom code.
   *
   * @return \Robo\Result
   *   Results from task execution.
   */
  protected function runPhpstan() {
    $this->say('Running PHPStan...');
    // Run as an independent collection since any issues discovered cause a
    // non-zero return code which kills the execution of the rest of the
    // collection and prevents filtering of the results by reviewdog.
    $result = $this->taskPhpstan()->run();

    // Filter with Reviewdog if errors were found with an acceptable code.
    if ($result->wasSuccessful()) {
      $result->setMessage('No errors found.');
    }
    elseif (in_array($result->getExitCode(), [1, 2], TRUE)) {
      $this->say('Filtering results...');
      $result = $this->taskReviewdog()
        ->reportFile('tests/reports/phpstan/phpstan.xml')
        ->run();
    }

    return $result;
  }

}
