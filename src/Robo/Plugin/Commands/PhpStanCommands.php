<?php

namespace ForumOne\CodeQuality\Robo\Plugin\Commands;

class PhpStanCommands extends \Robo\Tasks {

  use \ForumOne\CodeQuality\Robo\Task\PhpStan\Tasks;
  use \ForumOne\CodeQuality\Robo\Task\Tasks;

  /**
   * Run the code sniffer on custom code.
   *
   * @return \Robo\Result
   */
  public function runPhpStan() {

    $this->say('Running PHPStan...');
    // Run as an independent collection since any issues discovered cause a
    // non-zero return code which kills the execution of the rest of the
    // collection and prevents filtering of the results by reviewdog.
    $this->taskPhpStan('public/modules/custom')->run();

    $this->say('Filtering results...');
    return $this->taskReviewdog()
      ->reportFile('tests/reports/phpstan/phpstan.xml')
      ->run();
  }

}
