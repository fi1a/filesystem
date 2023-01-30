<?php

declare(strict_types=1);

namespace Fi1a\Unit\Filesystem;

use Fi1a\Filesystem\FileInterface;
use Fi1a\Filesystem\FolderInterface;
use Fi1a\Unit\Filesystem\TestCases\FilesystemTestCase;
use InvalidArgumentException;

/**
 * Папка
 */
class FolderTest extends FilesystemTestCase
{
    /**
     * Возвращает путь
     */
    public function testGetPath(): void
    {
        $folderPath = realpath(__DIR__ . '/Resources/folder');
        $filesystem = $this->getFilesystem();
        $folder = $filesystem->factoryFolder($folderPath);
        $this->assertEquals($folderPath, $folder->getPath());
        $this->assertEquals($folderPath, (string) $folder);
    }

    /**
     * Возвращает путь
     */
    public function testGetRelativePath(): void
    {
        $folderPath = realpath(__DIR__ . '/Resources/folder');
        $filesystem = $this->getFilesystem();
        $folder = $filesystem->factoryFolder('./folder');
        $this->assertEquals($folderPath, $folder->getPath());
    }

    /**
     * Исключение при пустом пути
     */
    public function testPathException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $filesystem = $this->getFilesystem();
        $filesystem->factoryFolder('');
    }

    /**
     * Возвращает путь
     */
    public function testGetPathRoot(): void
    {
        $folderPath = '.';
        $filesystem = $this->getFilesystem();
        $folder = $filesystem->factoryFolder($folderPath);
        $this->assertEquals(realpath(__DIR__ . '/Resources'), $folder->getPath());
    }

    /**
     * Возвращает путь
     */
    public function testGetPathNotExists(): void
    {
        $filesystem = $this->getFilesystem('/not/exists');
        $folder = $filesystem->factoryFolder('./folder/subfolder/..');
        $this->assertEquals('/not/exists/folder', $folder->getPath());
    }

    /**
     * Возвращает имя
     */
    public function testGetName(): void
    {
        $filesystem = $this->getFilesystem();
        $folder = $filesystem->factoryFolder('./not-exists');
        $this->assertEquals('not-exists', $folder->getName());
    }

    /**
     * Возвращает путь до родительской папки
     */
    public function testPeekParentPath(): void
    {
        $filesystem = $this->getFilesystem();
        $folder = $filesystem->factoryFolder(__DIR__ . '/Resources/not-exists');
        $this->assertEquals(__DIR__ . '/Resources', $folder->peekParentPath());
    }

    /**
     * Возвращает путь до родительской папки
     */
    public function testPeekParentPathRoot(): void
    {
        $filesystem = $this->getFilesystem('/');
        $folder = $filesystem->factoryFolder('/');
        $this->assertFalse($folder->peekParentPath());
    }

    /**
     * Возвращает класс родительской папки
     */
    public function testParent(): void
    {
        $filesystem = $this->getFilesystem();
        $folder = $filesystem->factoryFolder(__DIR__ . '/Resources/not-exists');
        $this->assertEquals(__DIR__ . '/Resources', $folder->getParent()->getPath());
        $this->assertFalse($folder->getParent()->getParent());
    }

    /**
     * Возвращает класс родительской папки
     */
    public function testParentRelative(): void
    {
        $filesystem = $this->getFilesystem();
        $folder = $filesystem->factoryFolder('./folder');
        $this->assertEquals(__DIR__ . '/Resources', $folder->getParent()->getPath());
        $folder = $filesystem->factoryFolder('./not-exists');
        $this->assertEquals(__DIR__ . '/Resources', $folder->getParent()->getPath());
    }

    /**
     * Возвращает класс родительской папки (ограничение в адаптере)
     */
    public function testParentDirectoryRestriction(): void
    {
        $filesystem = $this->getFilesystem(__DIR__ . '/Resources');
        $folder = $filesystem->factoryFolder('./folder');
        $this->assertInstanceOf(FolderInterface::class, $folder->getParent());
        $this->assertFalse($folder->getParent()->getParent());
    }

    /**
     * Возвращает класс родительской папки
     */
    public function testParentRoot(): void
    {
        $filesystem = $this->getFilesystem('/');
        $folder = $filesystem->factoryFolder('/');
        $this->assertFalse($folder->getParent());
    }

    /**
     * Проверяет возможность чтения
     */
    public function testCanRead(): void
    {
        $filesystem = $this->getFilesystem();
        $folder = $filesystem->factoryFolder(__DIR__ . '/Resources/folder');
        $this->assertTrue($folder->canRead());
    }

    /**
     * Проверяет возможность записи
     */
    public function testCanWrite(): void
    {
        $filesystem = $this->getFilesystem('/');
        $folder = $filesystem->factoryFolder(__DIR__ . '/Resources/folder');
        $this->assertTrue($folder->canWrite());
        $folder = $filesystem->factoryFolder(__DIR__ . '/Resources/folder/not-exists');
        $this->assertTrue($folder->canWrite());
        $folder = $filesystem->factoryFolder('/not-exists');
        $this->assertFalse($folder->canWrite());
    }

    /**
     * Проверяет существование
     */
    public function testIsExists(): void
    {
        $filesystem = $this->getFilesystem();
        $folder = $filesystem->factoryFolder(__DIR__ . '/Resources/folder');
        $this->assertTrue($folder->isExist());
        $folder = $filesystem->factoryFolder(__DIR__ . '/Resources/folder/not-exists');
        $this->assertFalse($folder->isExist());
    }

    /**
     * Переименование
     */
    public function testRename(): void
    {
        $filesystem = $this->getFilesystem();
        $folder = $filesystem->factoryFolder(__DIR__ . '/Resources/folder');
        $this->assertTrue($folder->rename('new-name'));
        $this->assertEquals('new-name', $folder->getName());
        $this->assertTrue($folder->rename('folder'));
    }

    /**
     * Переименование
     */
    public function testRenameRoot(): void
    {
        $filesystem = $this->getFilesystem('/');
        $folder = $filesystem->factoryFolder('/');
        $this->assertFalse($folder->rename('new-name'));
    }

    /**
     * Создание
     */
    public function testMake(): void
    {
        $pathFolder = __DIR__ . '/Resources/folder/new-folder';
        $filesystem = $this->getFilesystem();
        $folder = $filesystem->factoryFolder($pathFolder);
        $this->assertTrue($folder->make());
        $this->assertFalse($folder->make());
        chmod($pathFolder, 0000);
        $folder = $filesystem->factoryFolder($pathFolder . '/fail');
        $this->assertFalse($folder->make());
        chmod($pathFolder, 0775);
        rmdir($pathFolder);
    }

    /**
     * Перемещает
     *
     * @depends testMake
     */
    public function testMove(): void
    {
        $filesystem = $this->getFilesystem();
        $folder = $filesystem->factoryFolder(__DIR__ . '/Resources/folder/new-folder');
        $this->assertTrue($folder->make());
        $pathFolder = __DIR__ . '/Resources/folder/move-folder';
        $this->assertTrue($folder->move($pathFolder));
        $parentPathFolder = __DIR__ . '/Resources/folder';
        chmod($parentPathFolder, 0000);
        $this->assertFalse($folder->move(__DIR__ . '/Resources/folder/move-folder-fail'));
        chmod($parentPathFolder, 0775);
        rmdir($pathFolder);
    }

    /**
     * Перемещает
     */
    public function testMoveRoot(): void
    {
        $filesystem = $this->getFilesystem('/');
        $folder = $filesystem->factoryFolder('/');
        $this->assertFalse($folder->move('/move-folder-fail'));
    }

    /**
     * Перемещает
     */
    public function testMoveNotExists(): void
    {
        $filesystem = $this->getFilesystem();
        $folder = $filesystem->factoryFolder(__DIR__ . '/Resources/folder/not-exists');
        $this->assertFalse($folder->move(__DIR__ . '/Resources/folder/move-folder-fail'));
    }

    /**
     * Перемещает
     */
    public function testMoveParentNotExists(): void
    {
        $filesystem = $this->getFilesystem();
        $folder = $filesystem->factoryFolder(__DIR__ . '/Resources/folder');
        $this->assertTrue($folder->move(__DIR__ . '/Resources/new/move-folder'));
        $folder = $filesystem->factoryFolder(__DIR__ . '/Resources/new/move-folder');
        $this->assertTrue($folder->move(__DIR__ . '/Resources/folder'));
        rmdir(__DIR__ . '/Resources/new');
    }

    /**
     * Перемещает
     */
    public function testMoveExists(): void
    {
        $filesystem = $this->getFilesystem();
        $folder = $filesystem->factoryFolder(__DIR__ . '/Resources/folder');
        $this->assertFalse($folder->move(__DIR__ . '/Resources/folder/.gitkeep'));
    }

    /**
     * Является ли файлом
     */
    public function testIsFile(): void
    {
        $filesystem = $this->getFilesystem();
        $folder = $filesystem->factoryFolder(__DIR__ . '/Resources/folder');
        $this->assertFalse($folder->isFile());
    }

    /**
     * Является ли папкой
     */
    public function testIsFolder(): void
    {
        $filesystem = $this->getFilesystem();
        $folder = $filesystem->factoryFolder(__DIR__ . '/Resources/folder');
        $this->assertTrue($folder->isFolder());
    }

    /**
     * Является ли ссылкой
     */
    public function testIsLink(): void
    {
        $filesystem = $this->getFilesystem();
        $folder = $filesystem->factoryFolder(__DIR__ . '/Resources/folder');
        $this->assertFalse($folder->isLink());
    }

    /**
     * Возвращает коллекцию из дочерних элементов
     */
    public function testAll(): void
    {
        $filesystem = $this->getFilesystem();
        $folder = $filesystem->factoryFolder(__DIR__ . '/Resources/folder');
        $this->assertCount(3, $folder->all());
    }

    /**
     * Возвращает коллекцию из дочерних элементов
     */
    public function testAllNotExists(): void
    {
        $filesystem = $this->getFilesystem();
        $folder = $filesystem->factoryFolder(__DIR__ . '/Resources/not-exists');
        $this->assertCount(0, $folder->all());
    }

    /**
     * Возвращает коллекцию из дочерних файлов
     */
    public function testAllFiles(): void
    {
        $filesystem = $this->getFilesystem();
        $folder = $filesystem->factoryFolder(__DIR__ . '/Resources/folder');
        $files = $folder->allFiles();
        $this->assertCount(2, $files);
        foreach ($files as $item) {
            $this->assertInstanceOf(FileInterface::class, $item);
        }
    }

    /**
     * Возвращает коллекцию из дочерних файлов
     */
    public function testAllFilesNotExists(): void
    {
        $filesystem = $this->getFilesystem();
        $folder = $filesystem->factoryFolder(__DIR__ . '/Resources/not-exists');
        $this->assertCount(0, $folder->allFiles());
    }

    /**
     * Возвращает коллекцию из дочерних файлов
     */
    public function testAllFolders(): void
    {
        $filesystem = $this->getFilesystem();
        $folder = $filesystem->factoryFolder(__DIR__ . '/Resources/folder');
        $folders = $folder->allFolders();
        $this->assertCount(1, $folders);
        foreach ($folders as $item) {
            $this->assertInstanceOf(FolderInterface::class, $item);
        }
    }

    /**
     * Возвращает коллекцию из дочерних файлов
     */
    public function testAllFoldersNotExists(): void
    {
        $filesystem = $this->getFilesystem();
        $folder = $filesystem->factoryFolder(__DIR__ . '/Resources/not-exists');
        $this->assertCount(0, $folder->allFolders());
    }

    /**
     * Возвращает размер
     */
    public function testGetSize(): void
    {
        $filesystem = $this->getFilesystem();
        $folder = $filesystem->factoryFolder(__DIR__ . '/Resources/folder');
        $this->assertEquals(4, $folder->getSize());
    }

    /**
     * Возвращает размер
     */
    public function testGetSizeNotExists(): void
    {
        $filesystem = $this->getFilesystem();
        $folder = $filesystem->factoryFolder(__DIR__ . '/Resources/not-exists');
        $this->assertFalse($folder->getSize());
    }

    /**
     * Удаляет
     *
     * @depends testMake
     */
    public function testDelete(): void
    {
        $pathFolder = __DIR__ . '/Resources/folder/new-folder';
        $filesystem = $this->getFilesystem();
        $folder = $filesystem->factoryFolder($pathFolder);
        $this->assertTrue($folder->make());
        $file = $filesystem->factoryFile($pathFolder . '/file.txt');
        $this->assertTrue($file->make());
        $this->assertTrue($folder->delete());
    }

    /**
     * Удаляет
     *
     * @depends testDelete
     */
    public function testDeleteNotExists(): void
    {
        $pathFolder = __DIR__ . '/Resources/folder/new-folder';
        $filesystem = $this->getFilesystem();
        $folder = $filesystem->factoryFolder($pathFolder);
        $this->assertFalse($folder->delete());
    }

    /**
     * Копирование
     *
     * @depends testMake
     */
    public function testCopy(): void
    {
        $pathFolder = __DIR__ . '/Resources/folder/new-folder';
        $filesystem = $this->getFilesystem();
        $folder = $filesystem->factoryFolder($pathFolder);
        $this->assertTrue($folder->make());
        $file = $filesystem->factoryFile($pathFolder . '/file.txt');
        $this->assertTrue($file->make());
        $pathCopyFolder = __DIR__ . '/Resources/folder/copy-folder';
        $this->assertTrue($folder->copy($pathCopyFolder));
        $copyFolder = $filesystem->factoryFolder($pathCopyFolder);
        $this->assertTrue($copyFolder->isExist());
        $this->assertTrue($copyFolder->delete());
        $this->assertTrue($folder->delete());
    }

    /**
     * Копирование
     *
     * @depends testMake
     */
    public function testCopyFail(): void
    {
        $filesystem = $this->getFilesystem();
        $folder = $filesystem->factoryFolder(__DIR__ . '/Resources/folder/not-exists');
        $this->assertFalse($folder->copy(__DIR__ . '/Resources/folder/move-folder-fail'));
    }

    /**
     * Копирование
     *
     * @depends testMake
     */
    public function testCopyAccessFail(): void
    {
        $pathFolder = __DIR__ . '/Resources/folder/new-folder';
        $filesystem = $this->getFilesystem();
        $folder = $filesystem->factoryFolder($pathFolder);
        $this->assertTrue($folder->make());
        $file = $filesystem->factoryFile($pathFolder . '/file.txt');
        $this->assertTrue($file->make());
        chmod($file->getPath(), 0000);
        $this->assertFalse($folder->copy(__DIR__ . '/Resources/folder/move-folder-fail'));
        chmod($file->getPath(), 0775);
        $this->assertTrue(
            $filesystem->factoryFolder(__DIR__ . '/Resources/folder/move-folder-fail')->delete()
        );
        $this->assertTrue($folder->delete());
    }

    /**
     * Возвращает дочернюю папку
     */
    public function testGetFolder(): void
    {
        $pathFolder = __DIR__ . '/Resources/folder';
        $filesystem = $this->getFilesystem();
        $folder = $filesystem->factoryFolder($pathFolder);
        $this->assertEquals('subfolder', $folder->getFolder('subfolder')->getName());
        $this->assertEquals('subfolder', $folder->getFolder('/subfolder/')->getName());
        $this->assertEquals('not-exists', $folder->getFolder('/not-exists/')->getName());
    }

    /**
     * Возвращает дочерний файл
     */
    public function testGetFile(): void
    {
        $pathFolder = __DIR__ . '/Resources/folder';
        $filesystem = $this->getFilesystem();
        $folder = $filesystem->factoryFolder($pathFolder);
        $this->assertEquals('file.txt', $folder->getFile('file.txt')->getName());
        $this->assertEquals('file.txt', $folder->getFile('/file.txt')->getName());
        $this->assertEquals('not-exists.txt', $folder->getFile('/not-exists.txt')->getName());
    }
}
