<?php

namespace ForumOne\CodeQuality\Robo\Task\PhpStan;

trait Tasks {

  /**
   * @param string $path
   *
   * @return \ForumOne\CodeQuality\Robo\Task\PhpStan\PhpStan|\Robo\Collection\CollectionBuilder
   */
  protected function taskPhpstan(string $path) {
    return $this->task(PhpStan::class, $path);
  }

}
