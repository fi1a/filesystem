<?php

declare(strict_types=1);

namespace Fi1a\Filesystem;

use Fi1a\Filesystem\Adapters\FilesystemAdapterInterface;

/**
 * Файловая система
 */
interface FilesystemInterface
{
    /**
     * Конструктор
     */
    public function __construct(FilesystemAdapterInterface $adapter);

    /**
     * Фабричный метод
     */
    public function factory(string $path): NodeInterface;

    /**
     * Фабричный метод файла
     */
    public function factoryFile(string $path): FileInterface;

    /**
     * Фабричный метод папки
     */
    public function factoryFolder(string $path): FolderInterface;

    /**
     * Возвращает название
     */
    public function getName(string $path): string;

    /**
     * Возвращает расширение
     */
    public function getExtension(string $path): ?string;

    /**
     * Возвращает имя без расширения
     */
    public function getBaseName(string $path): string;

    /**
     * Возвращает путь до родительской папки
     *
     * @return string|false
     */
    public function peekParentPath(string $path);

    /**
     * Проверяет возможность чтения
     */
    public function canRead(string $path): bool;

    /**
     * Проверяет возможность записи
     */
    public function canWrite(string $path): bool;

    /**
     * Проверяет возможность выполнения
     */
    public function canExecute(string $path): bool;

    /**
     * Проверяет существование папки
     */
    public function isFolderExist(string $path): bool;

    /**
     * Проверяет существование файла
     */
    public function isFileExist(string $path): bool;

    /**
     * Создание папки
     */
    public function makeFolder(string $path): bool;

    /**
     * Возвращает массив из дочерних элементов
     *
     * @return string[]|false
     */
    public function all(string $path);

    /**
     * Возвращает массив из дочерних файлов
     *
     * @return string[]|false
     */
    public function allFiles(string $path);

    /**
     * Возвращает массив из дочерних папок
     *
     * @return string[]|false
     */
    public function allFolders(string $path);

    /**
     * Размер файла
     *
     * @return int|false
     */
    public function filesize(string $path);

    /**
     * Удаление файла
     */
    public function deleteFile(string $path): bool;

    /**
     * Удаление папки
     */
    public function deleteFolder(string $path): bool;

    /**
     * Запись файла
     *
     * @param int|string|null $content
     *
     * @return int|false
     */
    public function write(string $path, $content);

    /**
     * Копирование файла
     */
    public function copyFile(string $path, string $destination): bool;

    /**
     * Чтение файла
     *
     * @return string|false
     */
    public function readFile(string $path);

    /**
     * Возвращает время изменения файла
     *
     * @return int|false
     */
    public function getMTime(string $path);

    /**
     * Переименование
     */
    public function rename(string $from, string $to): bool;
}
