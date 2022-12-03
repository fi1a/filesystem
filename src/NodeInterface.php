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
    public function __construct(string $key, FilesystemInterface $filesystem);

    /**
     * Устанавливает путь
     *
     * @return $this
     */
    public function setPath(string $path);

    /**
     * Возвращает путь
     *
     * @return string|false
     */
    public function getPath();

    /**
     * Возвращает абсолютный путь
     *
     * @return string|false
     */
    public function getRealPath();

    /**
     * Проверяет существование
     */
    public function isExist(): bool;

    /**
     * Проверяет существование файла или папки
     */
    public function isNodeExist(): bool;

    /**
     * Является ли файлом
     */
    public function isFile(): bool;

    /**
     * Является ли папкой
     */
    public function isFolder(): bool;

    /**
     * Является ссылкой
     */
    public function isLink(): bool;

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
     *
     * @param string $newName новое имя
     *
     * @return $this|false
     */
    public function rename(string $newName);

    /**
     * Перемещает
     *
     * @param string $path путь куда будет перемещен
     *
     * @return bool|static
     */
    public function move(string $path);

    /**
     * Меняет права на файл или папку
     */
    public function chmod(int $rights): bool;

    /**
     * Возвращает дефолтные права на папки и файлы
     */
    public function getDefaultRights(): int;

    /**
     * Установить дефолтные праван на папки и файлы
     *
     * @return static
     */
    public function setDefaultRights(int $rights);

    /**
     * @return string
     */
    public function __toString();
}
