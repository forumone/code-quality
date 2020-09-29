<?php

namespace ForumOne\CodeQuality\Robo\Task;

trait Tasks {

  /**
   * @return \ForumOne\CodeQuality\Robo\Task\Reviewdog|\Robo\Collection\CollectionBuilder
   */
  protected function taskReviewdog() {
    return $this->task(Reviewdog::class);
  }

}
