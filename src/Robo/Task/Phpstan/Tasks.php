<?php

namespace ForumOne\CodeQuality\Robo\Task\Phpstan;

trait Tasks {

  /**
   * @param string $path
   *
   * @return \ForumOne\CodeQuality\Robo\Task\Phpstan\Phpstan|\Robo\Collection\CollectionBuilder
   */
  protected function taskPhpstan(string $path) {
    return $this->task(Phpstan::class, $path);
  }

}
