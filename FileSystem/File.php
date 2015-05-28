<?php
/**
 * Created by PhpStorm.
 * User: coaps
 * Date: 14.5.2015.
 * Time: 10.36
 */

namespace System\FileSystem;


class File
{
    /** @var resource */
    private $handle;

    /** @var int */
    private $fileSize;

    /** @var string */
    private $content;

    /**
     * @param string $filename
     * @param bool $createIfNotExist
     */
    public function __construct($filename, $createIfNotExist = true)
    {
        if (false == $filename) {
            return;
        }

        if (file_exists($filename) && is_readable($filename)) {
            $this->handle = fopen($filename, FileSystemInterface::MODE_APPEND_READ);
        } else if (file_exists($filename) && false == is_readable($filename)) {
            throw new \RuntimeException("File {$filename} is not readable.");
        } else if (false == file_exists($filename) && false == $createIfNotExist) {
            throw new \RuntimeException("File {$filename} not found.");
        } else if (false == file_exists($filename) && $createIfNotExist) {
            $this->handle = fopen($filename, FileSystemInterface::MODE_WRITE_READ);
        }

        $this->fileSize = filesize($filename);
    }

    public function __destruct()
    {
        if ($this->handle) {
            $this->saveAndClose();
        }
    }

    /**
     * @param callable $callback
     * @return string
     */
    public function read(callable $callback = null)
    {
        if (false == $this->handle) {
            throw new \RuntimeException('File handler not found.');
        }

        $content = '';
        $line = 1;

        while (false == feof($this->handle)) {
            $lineContent = fread($this->handle, $this->fileSize);

            if ($callback) {
                $callback($line, $lineContent);
            }

            $line++;
            $content .= $lineContent;
        }

        return $content;
    }

    /**
     * @return $this
     */
    public function save()
    {
        if (false == $this->handle) {
            throw new \RuntimeException('File handler not found.');
        }

        fseek($this->handle, 0);
        fwrite($this->handle, $this->content);

        return $this;
    }

    /**
     * @return bool
     */
    public function saveAndClose()
    {
        $this->save();

        return fclose($this->handle);
    }
}