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
    public function factory(string $key): NodeInterface;

    /**
     * Фабричный метод файла
     */
    public function factoryFile(string $key): FileInterface;

    /**
     * Фабричный метод папки
     */
    public function factoryFolder(string $key): FolderInterface;
}
