<?php

declare(strict_types=1);

namespace Fi1a\Filesystem;

use InvalidArgumentException;

use const DIRECTORY_SEPARATOR;

/**
 * Общие методы для FileInterface и FolderInterface
 */
abstract class Node implements NodeInterface
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @var FilesystemInterface
     */
    protected $filesystem;

    /**
     * @inheritDoc
     */
    public function __construct(string $path, FilesystemInterface $filesystem)
    {
        $this->setPath($path);
        $this->filesystem = $filesystem;
    }

    /**
     * Установить путь
     */
    protected function setPath(string $path): void
    {
        if (!$path) {
            throw new InvalidArgumentException('Путь не может быть пустым');
        }
        if ($path !== DIRECTORY_SEPARATOR) {
            $path = rtrim($path, DIRECTORY_SEPARATOR);
        }
        $this->path = $path;
    }

    /**
     * @inheritDoc
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->filesystem->getName($this->getPath());
    }

    /**
     * @inheritDoc
     */
    public function getParent()
    {
        $path = $this->peekParentPath();
        if (!$path) {
            return false;
        }

        return $this->filesystem->factoryFolder($path);
    }

    /**
     * @inheritDoc
     */
    public function peekParentPath()
    {
        return $this->filesystem->peekParentPath($this->getPath());
    }

    /**
     * @inheritDoc
     */
    public function canRead(): bool
    {
        return $this->filesystem->canRead($this->getPath());
    }

    /**
     * @inheritDoc
     */
    public function canWrite(): bool
    {
        if ($this->isExist()) {
            return $this->filesystem->canWrite($this->getPath());
        }
        $parent = $this->getParent();

        return $parent && $parent->canWrite();
    }

    /**
     * @inheritDoc
     */
    public function rename(string $newName): bool
    {
        $parent = $this->getParent();
        if (!$parent || mb_strpos($newName, DIRECTORY_SEPARATOR) !== false) {
            return false;
        }
        $node = $this->filesystem->factory($parent->getPath() . DIRECTORY_SEPARATOR . $newName);
        $result = @rename($this->getPath(), $node->getPath());
        if ($result) {
            $this->setPath($node->getPath());
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function move(string $path): bool
    {
        if (!$this->isExist() || $this->peekParentPath() === false) {
            return false;
        }
        $node = $this->filesystem->factory($path);
        $parent = $node->getParent();
        if ($node->isExist() || !$parent) {
            return false;
        }
        if (!$parent->isExist()) {
            $parent->make();
        }
        $result = @rename($this->getPath(), $node->getPath());
        if ($result) {
            $this->setPath($node->getPath());
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        return $this->getPath();
    }
}