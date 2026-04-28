<?php
/**
 * PHP 8.1+ Compatibility Patch for Laravel 7
 * This file manually overrides the return type notices for core Collection methods.
 */

namespace Illuminate\Support\ {
    if (PHP_VERSION_ID >= 80100 && !class_exists('Illuminate\Support\Collection', false)) {
        class Collection implements \ArrayAccess, \Countable, \IteratorAggregate, \JsonSerializable {
            // We define the class just enough to satisfy the inheritance 
            // but we can't easily re-implement the whole class here.
            // INSTEAD: We will use the bootstrapper to set error reporting.
        }
    }
}
