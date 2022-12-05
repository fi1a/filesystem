<?php

declare(strict_types=1);

namespace Fi1a\Unit\Filesystem\Utils;

use Fi1a\Filesystem\Utils\LocalUtil;
use PHPUnit\Framework\TestCase;

/**
 * Вспомогательные методы для файловой системы
 */
class LocalUtilTest extends TestCase
{
    /**
     * Является ли путь абсолютным
     */
    public function testIsAbsolutePath(): void
    {
        $this->assertTrue(LocalUtil::isAbsolutePath('/path/to/file.txt'));
        $this->assertTrue(LocalUtil::isAbsolutePath('C:\path\to\file.txt'));
        $this->assertFalse(LocalUtil::isAbsolutePath('./../path/to/file.txt'));
        $this->assertFalse(LocalUtil::isAbsolutePath('.\..\path\to\file.txt'));
    }
}
