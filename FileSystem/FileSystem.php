<?php
namespace System\FileSystem;

class FileSystem implements FileSystemInterface
{

    private $currentDir = FileSystemInterface::DEFAULT_DIRECTORY;

    /**
     * @param string $path
     * @return FileSystemInterface
     */
    public function setCurrentDirectory($path)
    {
        if (is_dir($path)) {
            $this->currentDir = $path;
        } else {
            throw new \RuntimeException("Path {$path} is not directory");
        }
    }

    /**
     * @return string
     */
    public function getCurrentDirectory()
    {
        return $this->currentDir;
    }

    /**
     * @param string $pattern
     * @param int $depth
     * @return File[]
     */
    public function find($pattern, $depth = 0)
    {
        // TODO: Implement find() method.
    }

    /**
     * @param string $filename
     * @param string $content
     * @param string $mode
     * @param string $permission
     * @return null|File
     */
    public function createFile($filename, $content = '', $mode = FileSystemInterface::MODE_WRITE_READ, $permission = self::PERMISSION_READ_WRITE)
    {
        if (false == $this->currentDir) {
            throw new \RuntimeException("Default directory is not set.");
        }

        $fullFilename = $this->generateFilePath($filename);
        $file = new File($fullFilename);

        return $file;
    }

    private function generateFilePath($filename)
    {
        return trim($this->currentDir, '/') . '/' . trim($filename, '/');
    }

    /**
     * @param string $directory
     * @param string $permission
     * @return bool
     */
    public function createDir($directory, $permission = self::PERMISSION_READ_WRITE)
    {
        // TODO: Implement createDir() method.
    }
}