<?php

namespace ForumOne\CodeQuality\Robo\Plugin\Commands;

class CodeSnifferCommands extends \Robo\Tasks {

  use \ForumOne\CodeQuality\Robo\Task\Tasks;
  use \Robo\Task\Base\loadTasks;

  protected $codePath = 'public/';
  protected $preset = 'drupal8';

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
    $this->taskPhpcs()
      ->preset('drupal8')
      ->path('public')
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
    $this->taskPhpstan()
      ->path('public/modules/custom')
      ->run();

    $this->say('Filtering results...');
    return $this->taskReviewdog()
      ->reportFile('tests/reports/phpstan/phpstan.xml')
      ->run();
  }

}
