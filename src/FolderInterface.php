<?php

declare(strict_types=1);

namespace Fi1a\Filesystem;

/**
 * Интерфейс папки
 */
interface FolderInterface extends NodeInterface
{
    /**
     * Возвращает коллекцию из дочерних элементов
     */
    public function all(): NodeCollectionInterface;

    /**
     * Возвращает коллекцию из дочерних файлов
     */
    public function allFiles(): NodeCollectionInterface;

    /**
     * Возвращает коллекцию из дочерних папок
     */
    public function allFolders(): NodeCollectionInterface;

    /**
     * Возвращает дочернюю папку
     */
    public function getFolder(string $path): FolderInterface;

    /**
     * Возвращает дочерний файл
     */
    public function getFile(string $path): FileInterface;
}
