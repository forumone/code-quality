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

}
