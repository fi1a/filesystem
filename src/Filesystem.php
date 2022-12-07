<?php

declare(strict_types=1);

namespace Fi1a\Filesystem;

use Fi1a\Filesystem\Adapters\FilesystemAdapterInterface;

/**
 * Файловая система
 */
class Filesystem implements FilesystemInterface
{
    /**
     * @var FilesystemAdapterInterface
     */
    private $adapter;

    /**
     * @inheritDoc
     */
    public function __construct(FilesystemAdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @inheritDoc
     */
    public function factory(string $path): NodeInterface
    {
        if ($this->adapter->isFolder($path)) {
            return $this->factoryFolder($path);
        }

        return $this->factoryFile($path);
    }

    /**
     * @inheritDoc
     */
    public function factoryFile(string $path): FileInterface
    {
        return new File($path, $this);
    }

    /**
     * @inheritDoc
     */
    public function factoryFolder(string $path): FolderInterface
    {
        return new Folder($path, $this);
    }

    /**
     * @inheritDoc
     */
    public function getName(string $path): string
    {
        return $this->adapter->getName($path);
    }

    /**
     * @inheritDoc
     */
    public function getExtension(string $path): ?string
    {
        return $this->adapter->getExtension($path);
    }

    /**
     * @inheritDoc
     */
    public function getBaseName(string $path): string
    {
        return $this->adapter->getBaseName($path);
    }

    /**
     * @inheritDoc
     */
    public function peekParentPath(string $path)
    {
        return $this->adapter->peekParentPath($path);
    }

    /**
     * @inheritDoc
     */
    public function canRead(string $path): bool
    {
        return $this->adapter->canRead($path);
    }

    /**
     * @inheritDoc
     */
    public function canWrite(string $path): bool
    {
        return $this->adapter->canWrite($path);
    }

    /**
     * @inheritDoc
     */
    public function canExecute(string $path): bool
    {
        return $this->adapter->canExecute($path);
    }

    /**
     * @inheritDoc
     */
    public function isFolderExist(string $path): bool
    {
        return $this->adapter->isFolderExist($path);
    }

    /**
     * @inheritDoc
     */
    public function isFileExist(string $path): bool
    {
        return $this->adapter->isFileExist($path);
    }

    /**
     * @inheritDoc
     */
    public function makeFolder(string $path): bool
    {
        return $this->adapter->makeFolder($path);
    }

    /**
     * @inheritDoc
     */
    public function all(string $path)
    {
        return $this->adapter->all($path);
    }

    /**
     * @inheritDoc
     */
    public function allFiles(string $path)
    {
        return $this->adapter->allFiles($path);
    }

    /**
     * @inheritDoc
     */
    public function allFolders(string $path)
    {
        return $this->adapter->allFolders($path);
    }

    /**
     * @inheritDoc
     */
    public function filesize(string $path)
    {
        return $this->adapter->filesize($path);
    }

    /**
     * @inheritDoc
     */
    public function deleteFile(string $path): bool
    {
        return $this->adapter->deleteFile($path);
    }

    /**
     * @inheritDoc
     */
    public function deleteFolder(string $path): bool
    {
        return $this->adapter->deleteFolder($path);
    }

    /**
     * @inheritDoc
     */
    public function write(string $path, $content)
    {
        return $this->adapter->write($path, $content);
    }

    /**
     * @inheritDoc
     */
    public function copyFile(string $path, string $destination): bool
    {
        return $this->adapter->copyFile($path, $destination);
    }

    /**
     * @inheritDoc
     */
    public function readFile(string $path)
    {
        return $this->adapter->readFile($path);
    }

    /**
     * @inheritDoc
     */
    public function getMTime(string $path)
    {
        return $this->adapter->getMTime($path);
    }

    /**
     * @inheritDoc
     */
    public function rename(string $from, string $to): bool
    {
        return $this->adapter->rename($from, $to);
    }
}
