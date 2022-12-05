<?php

declare(strict_types=1);

namespace Fi1a\Filesystem;

use Fi1a\Collection\AbstractInstanceCollection;

/**
 * Коллекция файлов и папок
 */
class NodeCollection extends AbstractInstanceCollection implements NodeCollectionInterface
{
    /**
     * @var FilesystemInterface
     */
    private $filesystem;

    /**
     * Конструктор
     *
     * @param FileInterface[]|FolderInterface[]|string[]|null $input
     */
    public function __construct(FilesystemInterface $filesystem, ?array $input = null)
    {
        $this->filesystem = $filesystem;
        parent::__construct($input);
    }

    /**
     * @inheritDoc
     */
    protected function factory($key, $value)
    {
        return $this->filesystem->factory((string) $value);
    }

    /**
     * @inheritDoc
     */
    protected function isInstance($value): bool
    {
        return $value instanceof NodeInterface;
    }
}
