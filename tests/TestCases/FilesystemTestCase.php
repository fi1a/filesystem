<?php

declare(strict_types=1);

namespace Fi1a\Unit\Filesystem\TestCases;

use Fi1a\Filesystem\Adapters\FilesystemAdapterInterface;
use Fi1a\Filesystem\Adapters\LocalAdapter;
use Fi1a\Filesystem\Filesystem;
use Fi1a\Filesystem\FilesystemInterface;
use PHPUnit\Framework\TestCase;

/**
 * Test case
 */
class FilesystemTestCase extends TestCase
{
    /**
     * Возвращает объект файловой системы
     */
    protected function getFilesystem(): FilesystemInterface
    {
        return new Filesystem($this->getAdapter());
    }

    /**
     * Возвращает адаптер
     */
    protected function getAdapter(): FilesystemAdapterInterface
    {
        return new LocalAdapter();
    }
}
