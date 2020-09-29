<?php

namespace ForumOne\CodeQuality\Robo\Task\Phpcs;

trait Tasks {

  /**
   * @param string $path
   *
   * @return \ForumOne\CodeQuality\Robo\Task\Phpcs\Phpcs|\Robo\Collection\CollectionBuilder
   */
  protected function taskPhpcs(string $path) {
    return $this->task(Phpcs::class, $path);
  }

  /**
   * @param string $path
   *
   * @return \ForumOne\CodeQuality\Robo\Task\Phpcs\PhpcsDrupal|\Robo\Collection\CollectionBuilder
   */
  protected function taskPhpcsDrupal(string $path) {
    return $this->task(PhpcsDrupal::class, $path);
  }

  /**
   * @return \ForumOne\CodeQuality\Robo\Task\Phpcs\Reviewdog|\Robo\Collection\CollectionBuilder
   */
  protected function taskPhpcsReviewdog() {
    return $this->task(Reviewdog::class);
  }
}
