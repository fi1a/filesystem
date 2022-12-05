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
     *
     * @return NodeCollectionInterface|false
     */
    public function all();

    /**
     * Возвращает коллекцию из дочерних файлов
     *
     * @return NodeCollectionInterface|false
     */
    public function allFiles();

    /**
     * Возвращает коллекцию из дочерних папок
     *
     * @return NodeCollectionInterface|false
     */
    public function allFolders();
}
