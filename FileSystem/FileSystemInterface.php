<?php

namespace System\FileSystem;

interface FileSystemInterface
{
    const PERMISSION_NONE = '0000';
    const PERMISSION_EXECUTE = '0111';
    const PERMISSION_WRITE = '0222';
    const PERMISSION_WRITE_EXECUTE = '0333';
    const PERMISSION_READ = '0444';
    const PERMISSION_READ_EXECUTE = '0555';
    const PERMISSION_READ_WRITE = '0666';
    const PERMISSION_ALL = '0777';

    const MODE_READ = 'r';
    const MODE_READ_WRITE = 'r+';
    const MODE_WRITE = 'w';
    const MODE_WRITE_READ = 'w+'; // investigate how is this different form MODE_READ_WRITE
    const MODE_APPEND = 'a';
    const MODE_APPEND_READ = 'a+'; // investigate how is this different form MODE_READ_WRITE

    const DEFAULT_DIRECTORY = '/';

    /**
     * @param string $path
     * @return FileSystemInterface
     */
    public function setCurrentDirectory($path);

    /**
     * @return string
     */
    public function getCurrentDirectory();

    /**
     * @param string $pattern
     * @param int $depth
     * @return File[]
     */
    public function find($pattern, $depth = 0);

    /**
     * @param string $filename
     * @param string $content
     * @param string $permission
     * @return bool
     */
    public function createFile($filename, $content = '', $permission = FileSystemInterface::PERMISSION_READ_WRITE);

    /**
     * @param string $directory
     * @param string $permission
     * @return bool
     */
    public function createDir($directory, $permission = FileSystemInterface::PERMISSION_READ_WRITE);


}