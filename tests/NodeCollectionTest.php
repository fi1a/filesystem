<?php

declare(strict_types=1);

namespace Fi1a\Unit\Filesystem;

use Fi1a\Filesystem\FileInterface;
use Fi1a\Filesystem\FolderInterface;
use Fi1a\Filesystem\NodeCollection;
use Fi1a\Unit\Filesystem\TestCases\FilesystemTestCase;

/**
 * Коллекция файлов и папок
 */
class NodeCollectionTest extends FilesystemTestCase
{
    /**
     * Коллекция файлов и папок
     */
    public function testCollection(): void
    {
        $filesystem = $this->getFilesystem();
        $collection = new NodeCollection($filesystem);
        $collection[] = './folder';
        $this->assertCount(1, $collection);
        $this->assertInstanceOf(FolderInterface::class, $collection[0]);
        $collection[] = './folder/file.txt';
        $this->assertCount(2, $collection);
        $this->assertInstanceOf(FileInterface::class, $collection[1]);
        $collection[] = $filesystem->factoryFile('./folder/file.txt');
        $this->assertCount(3, $collection);
        $this->assertInstanceOf(FileInterface::class, $collection[2]);
    }
}
