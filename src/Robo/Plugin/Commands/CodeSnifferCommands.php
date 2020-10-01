<?php

namespace ForumOne\CodeQuality\Robo\Plugin\Commands;

use Robo\Contract\VerbosityThresholdInterface;

class CodeSnifferCommands extends \Robo\Tasks {

  use \ForumOne\CodeQuality\Robo\Task\Tasks;
  use \ForumOne\CodeQuality\Robo\Task\Phpcs\Tasks;

  /**
   * Run the code sniffer on custom code.
   *
   * @return \Robo\Result
   */
  public function runCodeSniffer() {

    $this->say('Running phpcs...');
    // Run as an independent collection since any issues discovered cause a
    // non-zero return code which kills the execution of the rest of the
    // collection and prevents filtering of the results by reviewdog.
    $this->taskPhpcs('public')
      ->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_VERY_VERBOSE)
      ->preset('drupal8')
      ->run();

    $this->say('Filtering results...');
    return $this->taskReviewdog()
      ->reportFile('tests/reports/phpcs/phpcs.xml')
      ->run();
  }

}
