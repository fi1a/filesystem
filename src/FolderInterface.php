<?php

declare(strict_types=1);

namespace Fi1a\Filesystem;

/**
 * Интерфейс папки
 */
interface FolderInterface extends NodeInterface
{
    /**
     * Возвращает массив из дочерних элементов
     *
     * @return CollectionInterface|false
     */
    public function all();

    /**
     * Возвращает массив из дочерних файлов
     *
     * @return CollectionInterface|false
     */
    public function getFiles();

    /**
     * Возвращает массив из дочерних папок
     *
     * @return CollectionInterface|false
     */
    public function getFolders();
}
