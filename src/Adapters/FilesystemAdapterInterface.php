<?php

declare(strict_types=1);

namespace Fi1a\Filesystem\Adapters;

/**
 * Адаптер файловой системы
 */
interface FilesystemAdapterInterface
{
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
     * Проверяет существование
     */
    public function isFolderExist(string $path): bool;

    /**
     * Проверяет существование файла
     */
    public function isFileExist(string $path): bool;

    /**
     * Является папкой, или нет
     */
    public function isFolder(string $path): bool;

    /**
     * Является ссылкой, или нет
     */
    public function isLink(string $path): bool;

    /**
     * Является файлом, или нет
     */
    public function isFile(string $path): bool;

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
}
