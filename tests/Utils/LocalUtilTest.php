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

    /**
     * Возвращает путь до родительской папки
     */
    public function testPeekParentPath(): void
    {
        $this->assertEquals('/path/to', LocalUtil::peekParentPath('/path/to/folder'));
        $this->assertEquals('/path/to/folder', LocalUtil::peekParentPath('/path/to/folder/file.txt'));
        $this->assertFalse(LocalUtil::peekParentPath('/'));
        $this->assertFalse(LocalUtil::peekParentPath(''));
    }

    /**
     * Возвращает нормализованный путь
     */
    public function testNormalizePath(): void
    {
        $this->assertEquals('/path/to/folder', LocalUtil::normalizePath('/path/to/folder'));
        $this->assertEquals('/path/to', LocalUtil::normalizePath('/path/to/folder/..'));
        $this->assertEquals('path/to', LocalUtil::normalizePath('path/to/folder/..'));
        $this->assertEquals('path/to', LocalUtil::normalizePath('./path/to/folder/..'));
        $this->assertEquals('C:/path/to/folder', LocalUtil::normalizePath('C:\path\to\folder'));
        $this->assertEquals('C:/path', LocalUtil::normalizePath('C:\path\to\folder\..\..'));
        $this->assertEquals('C:/', LocalUtil::normalizePath('C:\\'));
        $this->assertEquals('/', LocalUtil::normalizePath('/'));
        $this->assertEquals('', LocalUtil::normalizePath('.'));
        $this->assertEquals('', LocalUtil::normalizePath('..'));
    }
}
