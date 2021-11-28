<?php

namespace App\Library\Traits;

/**
 * Trait hooks
 * @package cms
 */
trait Hooks {
  private $hooks = [];

  public function addAction($hookName, $callable) {
    if (!is_string($hookName) || !is_callable($callable)) return;

    $this->hooks[$hookName] = $callable;
  }

  public function execAction($hookName, ...$args): bool {
    if ($this->exist($hookName)) {
      $func = $this->hooks[$hookName];

      if (!isset($args) || !is_array($args)) {
        $args = [];
      }

      if (isset($func)) {
        return $func(...$args);
      }
    }
    return false;
  }

  public function exist($hookName): bool {
    return isset($this->hooks[$hookName]);
  }
}
