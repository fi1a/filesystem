<?php

declare(strict_types=1);

namespace Fi1a\Filesystem;

/**
 * Общие методы для FileInterface и FolderInterface
 */
interface NodeInterface
{
    /**
     * Конструктор
     */
    public function __construct(string $path, FilesystemInterface $filesystem);

    /**
     * Возвращает путь
     *
     * @return string
     */
    public function getPath();

    /**
     * Проверяет существование
     */
    public function isExist(): bool;

    /**
     * Является ли файлом
     */
    public function isFile(): bool;

    /**
     * Является ли папкой
     */
    public function isFolder(): bool;

    /**
     * Возвращает размер
     *
     * @return int|false
     */
    public function getSize();

    /**
     * Возвращает имя
     *
     * @return string
     */
    public function getName();

    /**
     * Возвращает класс родительской папки
     *
     * @return FolderInterface|false
     */
    public function getParent();

    /**
     * Возвращает путь до родительской папки
     *
     * @return string|false
     */
    public function peekParentPath();

    /**
     * Проверяет возможность чтения
     */
    public function canRead(): bool;

    /**
     * Проверяет возможность записи
     */
    public function canWrite(): bool;

    /**
     * Создание
     */
    public function make(): bool;

    /**
     * Удаляет
     */
    public function delete(): bool;

    /**
     * Копирует
     *
     * @param string $path путь куда будет скопирован
     */
    public function copy(string $path): bool;

    /**
     * Переименовывает
     */
    public function rename(string $newName): bool;

    /**
     * Перемещает
     */
    public function move(string $path): bool;

    /**
     * @return string
     */
    public function __toString();
}
