# PHP filesystem обеспечивает уровень абстракции файловой системы

[![Latest Version][badge-release]][packagist]
[![Software License][badge-license]][license]
[![PHP Version][badge-php]][php]
![Coverage Status][badge-coverage]
[![Total Downloads][badge-downloads]][downloads]
[![Support mail][badge-mail]][mail]

Уровень абстракции файловой системы позволяет разрабатывать приложение без необходимости знать,
где и как будут храниться файлы. Предоставляет один интерфейс для взаимодействия с разными типами файловых систем.
Также классы абстракции файла и папки имеют вспомогательные методы для работы с ними.

Доступные адаптеры:

- `Fi1a\Filesystem\Adapters\LocalAdapter` - адаптер файловой системы.

## Установка

Установить этот пакет можно как зависимость, используя Composer.

``` bash
composer require fi1a/filesystem
```

## Класс файловой системы

Класс файловой системы имеет три фабричных метода для получения классов абстракции файлов и папок.

- factory - опредляет до чего передан путь и создает объект абстракции файла или папки;
- factoryFolder - создает объект абстракции папки файловой системы;
- factoryFile - создает объект абстракции файла файловой системы.

```php
use Fi1a\Filesystem\Adapters\LocalAdapter;
use Fi1a\Filesystem\Filesystem;

$adapter = new LocalAdapter(__DIR__ . '/Resources');
$filesystem = new Filesystem($adapter);

$folder = $filesystem->folder('./folder'); // Fi1a\Filesystem\FolderInterface
$folder->make();

$folder = $filesystem->factory('./folder'); // Fi1a\Filesystem\FolderInterface
$folder->isExist(); // true

$file = $filesystem->file($folder->getPath() . '/file.txt'); // Fi1a\Filesystem\FileInterface
$file->make();

$file = $filesystem->factory('/folder/file.txt'); // Fi1a\Filesystem\FileInterface
$file->isExist(); // true
```

## Класс абстракции папки

Класс абстракции папки предназначен для упрощения работы с папкой, независимо от выбранной файловой системы.

Методы `Fi1a\Filesystem\FolderInterface`:

| Метод                                    | Описание                                   |
|------------------------------------------|--------------------------------------------|
| getPath()                                | Возвращает путь                            |
| isExist(): bool                          | Проверяет существование                    |
| isFile(): bool                           | Является ли файлом                         |
| isFolder(): bool                         | Является ли папкой                         |
| getSize()                                | Возвращает размер                          |
| getName()                                | Возвращает имя                             |
| getParent()                              | Возвращает класс родительской папки        |
| peekParentPath()                         | Возвращает путь до родительской папки      |
| canRead(): bool                          | Проверяет возможность чтения               |
| canWrite(): bool                         | Проверяет возможность записи               |
| make(): bool                             | Создание                                   |
| delete(): bool                           | Удаляет                                    |
| copy(string $path): bool                 | Копирует                                   |
| rename(string $newName): bool            | Переименовывает                            |
| move(string $path): bool                 | Перемещает                                 |
| all()                                    | Возвращает коллекцию из дочерних элементов |
| allFiles()                               | Возвращает коллекцию из дочерних файлов    |
| allFolders()                             | Возвращает коллекцию из дочерних папок     |
| getFilesystem(): FilesystemInterface     | Возвращает объект файловой системы         |
| getFolder(string $path): FolderInterface | Возвращает дочернюю папку                  |
| getFile(string $path): FileInterface     | Возвращает дочерний файл                   |

Пример создания папки:

```php
use Fi1a\Filesystem\Adapters\LocalAdapter;
use Fi1a\Filesystem\Filesystem;

$adapter = new LocalAdapter(__DIR__ . '/Resources');
$filesystem = new Filesystem($adapter);

$folder = $filesystem->folder('./folder'); // Fi1a\Filesystem\FolderInterface
if (!$folder->isExist()) {
    $folder->make(); // true
}
```

## Класс абстракции файла

Класс абстракции файла предназначен для упрощения работы с файлом, независимо от выбранной файловой системы.

Методы `Fi1a\Filesystem\FileInterface`:

| Метод                                | Описание                              |
|--------------------------------------|---------------------------------------|
| getPath()                            | Возвращает путь                       |
| isExist(): bool                      | Проверяет существование               |
| isFile(): bool                       | Является ли файлом                    |
| isFolder(): bool                     | Является ли папкой                    |
| getSize()                            | Возвращает размер                     |
| getName()                            | Возвращает имя                        |
| getBaseName(): string                | Возвращает имя без расширения         |
| getExtension()                       | Возвращает расширение                 |
| getParent()                          | Возвращает класс родительской папки   |
| peekParentPath()                     | Возвращает путь до родительской папки |
| canRead(): bool                      | Проверяет возможность чтения          |
| canWrite(): bool                     | Проверяет возможность записи          |
| canExecute(): bool                   | Проверяет возможность выполнения      |
| make(): bool                         | Создание                              |
| delete(): bool                       | Удаляет                               |
| copy(string $path): bool             | Копирует                              |
| rename(string $newName): bool        | Переименовывает                       |
| move(string $path): bool             | Перемещает                            |
| read()                               | Возвращает содержимое файла           |
| write(string $content)               | Запись в файл                         |
| getMTime()                           | Возвращает время изменения файла      |
| getFilesystem(): FilesystemInterface | Возвращает объект файловой системы    |

Пример записи в файл:

```php
use Fi1a\Filesystem\Adapters\LocalAdapter;
use Fi1a\Filesystem\Filesystem;

$adapter = new LocalAdapter(__DIR__ . '/Resources');
$filesystem = new Filesystem($adapter);

$file = $filesystem->file('./folder/file.txt'); // Fi1a\Filesystem\FileInterface
$file->write('file content'); // 12
```

## Адаптеры

### Адаптер файловой системы

Адаптер файловой системы `Fi1a\Filesystem\Adapters\LocalAdapter` предназначен для использования локальной файловой системы
в классах абстракции.

```php
use Fi1a\Filesystem\Adapters\LocalAdapter;
use Fi1a\Filesystem\Filesystem;

$adapter = new LocalAdapter(__DIR__ . '/Resources');
$filesystem = new Filesystem($adapter);
```

Аргументы конструктора `Fi1a\Filesystem\Adapters\LocalAdapter`:

| Аргумент           | Описание                                                    |
|--------------------|-------------------------------------------------------------|
| string $directory  | Путь ограничивающий доступ                                  |
| int $rights = 0775 | Права устанавливаемые по умолчанию для новых папок и файлов |

Вспомогательные методы для файловой системы содержатся в классе `Fi1a\Filesystem\Utils\LocalUtil`:

| Метод                              | Описание                               |
|------------------------------------|----------------------------------------|
| isAbsolutePath(string $path): bool | Определяет является ли путь абсолютным |

[badge-release]: https://img.shields.io/packagist/v/fi1a/filesystem?label=release
[badge-license]: https://img.shields.io/github/license/fi1a/filesystem?style=flat-square
[badge-php]: https://img.shields.io/packagist/php-v/fi1a/filesystem?style=flat-square
[badge-coverage]: https://img.shields.io/badge/coverage-100%25-green
[badge-downloads]: https://img.shields.io/packagist/dt/fi1a/filesystem.svg?style=flat-square&colorB=mediumvioletred
[badge-mail]: https://img.shields.io/badge/mail-support%40fi1a.ru-brightgreen

[packagist]: https://packagist.org/packages/fi1a/filesystem
[license]: https://github.com/fi1a/filesystem/blob/master/LICENSE
[php]: https://php.net
[downloads]: https://packagist.org/packages/fi1a/filesystem
[mail]: mailto:support@fi1a.ru