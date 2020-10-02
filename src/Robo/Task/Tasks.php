<?php

namespace ForumOne\CodeQuality\Robo\Task;

trait Tasks {

  /**
   * Run PHPCS code sniffing on code.
   *
   * @return \ForumOne\CodeQuality\Robo\Task\Phpcs\Phpcs|\Robo\Collection\CollectionBuilder
   */
  protected function taskPhpcs() {
    return $this->task(Phpcs::class);
  }

  /**
   * Run PHPStan code scanning on code.
   *
   * @return \ForumOne\CodeQuality\Robo\Task\Phpstan\Phpstan|\Robo\Collection\CollectionBuilder
   */
  protected function taskPhpstan() {
    return $this->task(Phpstan::class);
  }

  /**
   * Run Reviewdog to filter sniffing results.
   *
   * @return \ForumOne\CodeQuality\Robo\Task\Reviewdog|\Robo\Collection\CollectionBuilder
   */
  protected function taskReviewdog() {
    return $this->task(Reviewdog::class);
  }

}
