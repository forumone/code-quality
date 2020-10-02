<?php

namespace ForumOne\CodeQuality\Robo\Task\Phpcs;

trait Tasks {

  /**
   * Run PHPCS code sniffing on code.
   *
   * @return \ForumOne\CodeQuality\Robo\Task\Phpcs\Phpcs|\Robo\Collection\CollectionBuilder
   */
  protected function taskPhpcs() {
    return $this->task(Phpcs::class);
  }

}
