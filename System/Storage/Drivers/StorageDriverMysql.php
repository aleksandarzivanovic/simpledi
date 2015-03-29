<?php

namespace System\Storage\Drivers;

use System\Di\Di;

class StorageDriverMysql implements StorageDriverInterface {

    private static $instance;
    private $hostname;
    private $username;
    private $password;
    private $database;
    private $port;

    public function __construct() {
        if (self::$instance) {
            return self::$instance;
        }

        self::$instance = $this;

        return $this;
    }

    private function connect() {
        /* @var $config \System\Config\ConfigInterface */
        $config = Di::getInstance()->get('system.cache')->get('data/storage.json');
        $data = $config->get('mysql');
        $this->validateHostData($data);
        $this->populateParameters($data);
    }

    /**
     * 
     * @param array $data
     */
    private function validateHostData(array $data) {
        $errors = [];

        if (empty($data['hostname'])) {
            $errors[] = 'Invalid hostname.';
        }

        if (empty($data['username'])) {
            $errors[] = 'Invalid username.';
        }

        if (empty($data['database'])) {
            $errors[] = 'Invalid database.';
        }

        if (false == empty($errors)) {
            $this->printErrorMessages($errors);
        }
    }

    /**
     * 
     * @param array $data
     */
    private function populateParameters(array $data) {
        $this->database = $data['database'];
        $this->username = $data['username'];
        $this->hostname = $data['hostname'];
        $this->password = empty($data['password']) ? '' : $data['password'];
        $this->port = empty($data['port']) ? 3306 : (int) $data['port'];
    }

    /**
     * 
     * @param array $messages
     * @throws \RuntimeException
     */
    private function printErrorMessages(array $messages) {
        $error = 'Following errors occured: ' . implode(', ', $messages);
        throw new \RuntimeException($error);
    }

    public function get($table, array $criteria, array $orderBy = null, $offset = null, $limit = null) {
        
    }

    public function getAll($table, array $criteria, array $orderBy = null, $offset = null, $limit = null) {
        
    }

    public function insert($table, array $values) {
        
    }

    public function insertAll($table, array $values) {
        
    }

    public function setDriver(StorageDriverInterface $driver) {
        
    }

}
