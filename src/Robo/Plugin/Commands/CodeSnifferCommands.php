<?php

namespace ForumOne\CodeQuality\Robo\Plugin\Commands;

class CodeSnifferCommands extends \Robo\Tasks {

  use \ForumOne\CodeQuality\Robo\Task\Tasks;
  use \ForumOne\CodeQuality\Robo\Task\Phpcs\Tasks;
  use \ForumOne\CodeQuality\Robo\Task\Phpstan\Tasks;
  use \Robo\Task\Base\loadTasks;

  /**
   * Run the code sniffer scans on custom code.
   *
   * @return \Robo\Result
   */
  public function runCodeSniffer() {
    $collectionBuilder = $this->collectionBuilder();

    return $collectionBuilder
      ->addCode([$this, 'runPhpcs'])
      ->addCode([$this, 'runPhpstan'])
      ->run();
  }

  /**
   * Run PHPCS on custom code.
   *
   * @return \Robo\Result
   */
  public function runPhpcs() {
    $this->say('Running phpcs...');
    // Run as an independent collection since any issues discovered cause a
    // non-zero return code which kills the execution of the rest of the
    // collection and prevents filtering of the results by reviewdog.
    $this->taskPhpcs('public')
      ->preset('drupal8')
      ->run();

    $this->say('Filtering results...');
    return $this->taskReviewdog()
      ->reportFile('tests/reports/phpcs/phpcs.xml')
      ->run();
  }

  /**
   * Run the PHPStan on custom code.
   *
   * @return \Robo\Result
   */
  public function runPhpstan() {
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
