<?php

namespace Lib\Logging;

abstract class BaseLogger
{
    abstract public function log(string $level, string $message): void;
}