<?php

namespace ForumOne\CodeQuality\Robo\Task\Phpstan;

trait Tasks {

  /**
   * Run PHPStan code scanning on code.
   *
   * @return \ForumOne\CodeQuality\Robo\Task\Phpstan\Phpstan|\Robo\Collection\CollectionBuilder
   */
  protected function taskPhpstan() {
    return $this->task(Phpstan::class);
  }

}
