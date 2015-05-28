<?php

namespace System\FileSystem;

interface FileInterface
{
    /**
     * @param callable $callback
     * @return string
     */
    public function read(callable $callback = null);

    /**
     * @return FileInterface
     */
    public function save();

    /**
     * @return bool
     */
    public function saveAndClose();

    /**
     * @return bool
     */
    public function remove();

    /**
     * @param string $permission
     * @return bool
     */
    public function changeMode($permission = FileSystemInterface::PERMISSION_READ_WRITE);

    /**
     * @param string $username
     * @return bool
     */
    public function changeOwner($username);

    /**
     * @param string $groupName
     * @return bool
     */
    public function changeGroup($groupName);
}