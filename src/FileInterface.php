<?php

declare(strict_types=1);

namespace Fi1a\Filesystem;

/**
 * Интерфейс файла
 */
interface FileInterface extends NodeInterface
{
    /**
     * Возвращает расширение
     *
     * @return string|null
     */
    public function getExtension();

    /**
     * Возвращает содержимое файла
     *
     * @return string|false
     */
    public function read();

    /**
     * Возвращает имя без расширения
     *
     * @return string
     */
    public function getBaseName();

    /**
     * Запись в файл
     *
     * @return int|false
     */
    public function write(string $content);

    /**
     * Проверяет возможность выполнения
     */
    public function canExecute(): bool;

    /**
     * Является ли файл символической ссылкой
     */
    public function isLink(): bool;

    /**
     * Возвращает время изменения файла
     *
     * @return int|false
     */
    public function getMTime();

    /**
     * Возвращает mime тип файла
     *
     * @return string|false
     */
    public function getMimeType();
}
