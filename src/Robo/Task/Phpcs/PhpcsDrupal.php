<?php

namespace ForumOne\CodeQuality\Robo\Task\Phpcs;

class PhpcsDrupal extends Phpcs {

  protected $reportsPath = 'tests/reports/phpcs/';
  protected $reportName = 'phpcs.xml';
  protected $extensions = 'php,module,inc,profile,theme,install';
  protected $ignore_patterns = 'contrib/,core/,vendor/';
  protected $standard = 'Drupal,DrupalPractice';

}
