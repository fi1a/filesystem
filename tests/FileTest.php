<?php

declare(strict_types=1);

namespace Fi1a\Unit\Filesystem;

use Fi1a\Unit\Filesystem\TestCases\FilesystemTestCase;

/**
 * Файл
 */
class FileTest extends FilesystemTestCase
{
    /**
     * Возвращает расширение
     */
    public function testGetExtension(): void
    {
        $filesystem = $this->getFilesystem();

        $filePath = __DIR__ . '/Resources/folder/file.txt';
        $file = $filesystem->factoryFile($filePath);
        $this->assertEquals('txt', $file->getExtension());

        $filePath = __DIR__ . '/Resources/folder/file';
        $file = $filesystem->factoryFile($filePath);
        $this->assertNull($file->getExtension());
    }

    /**
     * Возвращает имя без расширения
     */
    public function testGetBaseName(): void
    {
        $filesystem = $this->getFilesystem();

        $filePath = __DIR__ . '/Resources/folder/file.txt';
        $file = $filesystem->factoryFile($filePath);
        $this->assertEquals('file', $file->getBaseName());

        $filePath = __DIR__ . '/Resources/folder/.txt';
        $file = $filesystem->factoryFile($filePath);
        $this->assertEquals('', $file->getBaseName());
    }

    /**
     * Чтение
     */
    public function testRead(): void
    {
        $filesystem = $this->getFilesystem();

        $filePath = __DIR__ . '/Resources/folder/file.txt';
        $file = $filesystem->factoryFile($filePath);
        $this->assertEquals('1234', $file->read());
    }

    /**
     * Чтение
     */
    public function testReadFail(): void
    {
        $filesystem = $this->getFilesystem();

        $filePath = __DIR__ . '/Resources/folder/not-exists.txt';
        $file = $filesystem->factoryFile($filePath);
        $this->assertFalse($file->read());
    }

    /**
     * Запись
     *
     * @depends testRead
     */
    public function testWrite(): void
    {
        $filesystem = $this->getFilesystem();

        $filePath = __DIR__ . '/Resources/folder/write.txt';
        $file = $filesystem->factoryFile($filePath);
        $this->assertIsInt($file->write('1234'));
        $this->assertEquals('1234', $file->read());
    }

    /**
     * Проверяет возможность выполнения
     */
    public function testCanExecute(): void
    {
        $filesystem = $this->getFilesystem();

        $filePath = __DIR__ . '/Resources/folder/file.txt';
        $file = $filesystem->factoryFile($filePath);
        chmod($filePath, 0777);
        $this->assertTrue($file->canExecute());
        chmod($filePath, 0644);
        $this->assertFalse($file->canExecute());
    }

    /**
     * Возвращает время изменения файла
     */
    public function testGetMTime(): void
    {
        $filesystem = $this->getFilesystem();

        $filePath = __DIR__ . '/Resources/folder/file.txt';
        $file = $filesystem->factoryFile($filePath);
        $this->assertIsNumeric($file->getMTime());
    }

    /**
     * Возвращает время изменения файла
     */
    public function testGetMTimeNotExists(): void
    {
        $filesystem = $this->getFilesystem();

        $filePath = __DIR__ . '/Resources/folder/not-exists.txt';
        $file = $filesystem->factoryFile($filePath);
        $this->assertFalse($file->getMTime());
    }

    /**
     * Является ли файлом
     */
    public function testIsFile(): void
    {
        $filesystem = $this->getFilesystem();

        $filePath = __DIR__ . '/Resources/folder/file.txt';
        $file = $filesystem->factoryFile($filePath);
        $this->assertTrue($file->isFile());
    }

    /**
     * Является ли папкой
     */
    public function testIsFolder(): void
    {
        $filesystem = $this->getFilesystem();

        $filePath = __DIR__ . '/Resources/folder/file.txt';
        $file = $filesystem->factoryFile($filePath);
        $this->assertFalse($file->isFolder());
    }

    /**
     * Возвращает размер
     */
    public function testGetSize(): void
    {
        $filesystem = $this->getFilesystem();

        $filePath = __DIR__ . '/Resources/folder/file.txt';
        $file = $filesystem->factoryFile($filePath);
        $this->assertEquals(4, $file->getSize());
    }

    /**
     * Возвращает размер
     */
    public function testGetSizeNotExists(): void
    {
        $filesystem = $this->getFilesystem();

        $filePath = __DIR__ . '/Resources/folder/not-exists.txt';
        $file = $filesystem->factoryFile($filePath);
        $this->assertFalse($file->getSize());
    }

    /**
     * Создание
     */
    public function testMake(): void
    {
        $filesystem = $this->getFilesystem();

        $filePath = __DIR__ . '/Resources/folder/new-file.txt';
        $file = $filesystem->factoryFile($filePath);
        $this->assertFalse($file->isExist());
        $this->assertTrue($file->make());
        $this->assertTrue($file->isExist());
        $this->assertFalse($file->make());
    }

    /**
     * Удаление
     *
     * @depends testWrite
     * @depends testMake
     */
    public function testDelete(): void
    {
        $filesystem = $this->getFilesystem();

        $filePath = __DIR__ . '/Resources/folder/write.txt';
        $file = $filesystem->factoryFile($filePath);
        $this->assertTrue($file->isExist());
        $this->assertTrue($file->delete());
        $this->assertFalse($file->isExist());
        $this->assertFalse($file->delete());

        $filePath = __DIR__ . '/Resources/folder/new-file.txt';
        $file = $filesystem->factoryFile($filePath);
        $this->assertTrue($file->isExist());
        $this->assertTrue($file->delete());
        $this->assertFalse($file->isExist());
        $this->assertFalse($file->delete());
    }

    /**
     * Копирование
     *
     * @depends testDelete
     */
    public function testCopy(): void
    {
        $filesystem = $this->getFilesystem();

        $filePath = __DIR__ . '/Resources/folder/file.txt';
        $file = $filesystem->factoryFile($filePath);
        $copyFilePath = __DIR__ . '/Resources/folder/copy-file.txt';
        $copyFile = $filesystem->factoryFile($copyFilePath);
        $this->assertTrue($file->isExist());
        $this->assertFalse($copyFile->isExist());
        $this->assertTrue($file->copy($copyFile->getPath()));
        $this->assertTrue($copyFile->isExist());
        $this->assertTrue($file->isExist());
        $this->assertTrue($copyFile->delete());
    }
}
