<?php

declare(strict_types=1);

namespace Fi1a\Unit\Filesystem\Adapters;

use Fi1a\Filesystem\Adapters\LocalAdapter;
use Fi1a\Unit\Filesystem\TestCases\FilesystemTestCase;
use InvalidArgumentException;
use OutOfBoundsException;

/**
 * Адаптер файловой системы
 */
class LocalAdapterTest extends FilesystemTestCase
{
    /**
     * Тестирование конструктора
     */
    public function testConstruct(): void
    {
        $adapter = $this->getAdapter('/');
        $this->assertInstanceOf(LocalAdapter::class, $adapter);
    }

    /**
     * Тестирование конструктора
     */
    public function testConstructEmptyException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->getAdapter('');
    }

    /**
     * Тестирование конструктора
     */
    public function testConstructException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->getAdapter('..');
    }

    /**
     * Тестирование конструктора
     */
    public function testConstructDir(): void
    {
        $adapter = $this->getAdapter(__DIR__ . '/../Resources/');
        $this->assertInstanceOf(LocalAdapter::class, $adapter);
    }

    /**
     * Возвращает название
     */
    public function testGetName(): void
    {
        $adapter = $this->getAdapter(__DIR__ . '/../Resources/');
        $this->assertEquals('Resources', $adapter->getName('./'));
        $this->assertEquals('file.txt', $adapter->getName('./folder/file.txt'));
    }

    /**
     * Исключение при выходе за рамки определенного в адаптере
     */
    public function testGetNameException(): void
    {
        $this->expectException(OutOfBoundsException::class);
        $adapter = $this->getAdapter(__DIR__ . '/../Resources/');
        $adapter->getName('/out/path');
    }

    /**
     * Возвращает расширение
     */
    public function testGetExtension(): void
    {
        $adapter = $this->getAdapter(__DIR__ . '/../Resources/');
        $this->assertNull($adapter->getExtension('./'));
        $this->assertEquals('txt', $adapter->getExtension('./folder/file.txt'));
    }

    /**
     * Возвращает имя без расширения
     */
    public function testGetBaseName(): void
    {
        $adapter = $this->getAdapter(__DIR__ . '/../Resources/');
        $this->assertEquals('Resources', $adapter->getBaseName('./'));
        $this->assertEquals('file', $adapter->getBaseName('./folder/file.txt'));
    }

    /**
     * Возвращает путь до родительской папки
     */
    public function testPeekParentPath(): void
    {
        $adapter = $this->getAdapter(__DIR__ . '/../Resources/');
        $this->assertEquals(
            realpath(__DIR__ . '/../Resources'),
            $adapter->peekParentPath(__DIR__ . '/../Resources/folder')
        );
        $this->assertEquals(
            realpath(__DIR__ . '/../Resources/folder'),
            $adapter->peekParentPath(__DIR__ . '/../Resources/folder/file.txt')
        );
    }

    /**
     * Проверяет возможность чтения
     */
    public function testCanRead(): void
    {
        $adapter = $this->getAdapter(__DIR__ . '/../Resources');
        $this->assertTrue($adapter->canRead('./'));
        chmod(__DIR__ . '/../Resources', 0000);
        $this->assertFalse($adapter->canRead('./'));
        chmod(__DIR__ . '/../Resources', 0777);
    }

    /**
     * Проверяет возможность записи
     */
    public function testCanWrite(): void
    {
        $adapter = $this->getAdapter(__DIR__ . '/../Resources');
        $this->assertTrue($adapter->canWrite('./'));
        chmod(__DIR__ . '/../Resources', 0000);
        $this->assertFalse($adapter->canWrite('./'));
        chmod(__DIR__ . '/../Resources', 0777);
    }

    /**
     * Проверяет возможность выполнения
     */
    public function testCanExecute(): void
    {
        $adapter = $this->getAdapter(__DIR__ . '/../Resources');
        chmod(__DIR__ . '/../Resources/folder/file.txt', 0777);
        $this->assertTrue($adapter->canExecute('./folder/file.txt'));
        chmod(__DIR__ . '/../Resources/folder/file.txt', 0000);
        $this->assertFalse($adapter->canExecute('./folder/file.txt'));
        chmod(__DIR__ . '/../Resources/folder/file.txt', 0777);
    }

    /**
     * Проверяет существование
     */
    public function testIsFolderExist(): void
    {
        $adapter = $this->getAdapter(__DIR__ . '/../Resources');
        $this->assertTrue($adapter->isFolderExist('./'));
        $this->assertFalse($adapter->isFolderExist('./not-exists'));
        $this->assertFalse($adapter->isFolderExist('./folder/file.txt'));
    }

    /**
     * Проверяет существование файла
     */
    public function testIsFileExist(): void
    {
        $adapter = $this->getAdapter(__DIR__ . '/../Resources');
        $this->assertFalse($adapter->isFileExist('./'));
        $this->assertFalse($adapter->isFileExist('./not-exists.txt'));
        $this->assertTrue($adapter->isFileExist('./folder/file.txt'));
    }

    /**
     * Является папкой, или нет
     */
    public function testIsFolder(): void
    {
        $adapter = $this->getAdapter(__DIR__ . '/../Resources');
        $this->assertTrue($adapter->isFolder('./'));
        $this->assertFalse($adapter->isFolder('./not-exists'));
        $this->assertFalse($adapter->isFolder('./folder/file.txt'));
    }

    /**
     * Является файлом, или нет
     */
    public function testIsFile(): void
    {
        $adapter = $this->getAdapter(__DIR__ . '/../Resources');
        $this->assertFalse($adapter->isFile('./'));
        $this->assertFalse($adapter->isFile('./not-exists.txt'));
        $this->assertTrue($adapter->isFile('./folder/file.txt'));
    }

    /**
     * Создание папки
     */
    public function testMakeFolder(): void
    {
        $adapter = $this->getAdapter(__DIR__ . '/../Resources');
        $this->assertFalse($adapter->makeFolder('./folder'));
        $this->assertTrue($adapter->makeFolder('./new-folder'));
    }

    /**
     * Удаление папки
     *
     * @depends testMakeFolder
     */
    public function testDeleteFolder(): void
    {
        $adapter = $this->getAdapter(__DIR__ . '/../Resources');
        $this->assertTrue($adapter->deleteFolder('./new-folder'));
        $this->assertFalse($adapter->deleteFolder('./new-folder'));
    }

    /**
     * Возвращает массив из дочерних элементов
     */
    public function testAll(): void
    {
        $adapter = $this->getAdapter(__DIR__ . '/../Resources');
        $this->assertCount(3, $adapter->all('./folder'));
        $this->assertFalse($adapter->all('./not-exists-folder'));
    }

    /**
     * Возвращает массив из дочерних файлов
     */
    public function testAllFiles(): void
    {
        $adapter = $this->getAdapter(__DIR__ . '/../Resources');
        $this->assertCount(2, $adapter->allFiles('./folder'));
        $this->assertFalse($adapter->allFiles('./not-exists-folder'));
    }

    /**
     * Возвращает массив из дочерних папок
     */
    public function testAllFolders(): void
    {
        $adapter = $this->getAdapter(__DIR__ . '/../Resources');
        $this->assertCount(1, $adapter->allFolders('./folder'));
        $this->assertFalse($adapter->allFolders('./not-exists-folder'));
    }

    /**
     * Размер файла
     */
    public function testFilesize(): void
    {
        $adapter = $this->getAdapter(__DIR__ . '/../Resources');
        $this->assertEquals(4, $adapter->filesize('./folder/file.txt'));
        $this->assertFalse($adapter->filesize('./folder'));
        $this->assertFalse($adapter->filesize('./not-exists'));
    }

    /**
     * Запись файла
     */
    public function testWrite(): void
    {
        $adapter = $this->getAdapter(__DIR__ . '/../Resources');
        $this->assertIsNumeric($adapter->write('./folder/write.txt', 1234));
        $this->assertEquals(4, $adapter->filesize('./folder/write.txt'));
        chmod(__DIR__ . '/../Resources/folder/write.txt', 0000);
        $this->assertFalse($adapter->write('./folder/write.txt', 4321));
        chmod(__DIR__ . '/../Resources/folder/write.txt', 0777);
    }

    /**
     * Удаление файла
     *
     * @depends testWrite
     */
    public function testDeleteFile(): void
    {
        $adapter = $this->getAdapter(__DIR__ . '/../Resources');
        $this->assertTrue($adapter->deleteFile('./folder/write.txt'));
        $this->assertFalse($adapter->deleteFile('./folder/write.txt'));
    }

    /**
     * Копирование файла
     *
     * @depends testDeleteFile
     */
    public function testCopyFile(): void
    {
        $adapter = $this->getAdapter(__DIR__ . '/../Resources');
        $this->assertTrue($adapter->copyFile('./folder/file.txt', './folder/copy.txt'));
        $this->assertEquals(4, $adapter->filesize('./folder/copy.txt'));
        $this->assertTrue($adapter->deleteFile('./folder/copy.txt'));
    }

    /**
     * Чтение файла
     */
    public function testReadFile(): void
    {
        $adapter = $this->getAdapter(__DIR__ . '/../Resources');
        $this->assertEquals('1234', $adapter->readFile('./folder/file.txt'));
        $this->assertFalse($adapter->readFile('./folder/not-exists.txt'));
    }

    /**
     * Возвращает время изменения файла
     */
    public function testGetMTime(): void
    {
        $adapter = $this->getAdapter(__DIR__ . '/../Resources');
        $this->assertIsNumeric($adapter->getMTime('./folder/file.txt'));
        $this->assertFalse($adapter->getMTime('./folder/not-exists.txt'));
    }
}
