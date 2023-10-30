<?php

use PDO;
use PDOException;

class Database
{
    function __construct()
    {
        $config = [
            'host' => '127.0.0.1',
            'driver' => 'mysql',
            'database' => 'brigada',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
            'collation' => 'utf8_general_ci',
        ];

        $config['driver'] = $config['driver'] ?? 'mysql';
        $config['host'] = $config['host'] ?? 'localhost';
        $config['charset'] = $config['charset'] ?? 'utf8mb4';
        $config['collation'] = $config['collation'] ?? 'utf8mb4_general_ci';
        $config['port'] = $config['port'] ?? (strstr($config['host'], ':') ? explode(':', $config['host'])[1] : '');

        $dsn = '';

        if (in_array($config['driver'], ['', 'mysql', 'pgsql'])) {
            $dsn = $config['driver'] . ':host=' . str_replace(':' . $config['port'], '', $config['host']) . ';'
                . ($config['port'] !== '' ? 'port=' . $config['port'] . ';' : '')
                . 'dbname=' . $config['database'];
        } elseif ($config['driver'] === 'sqlite') {
            $dsn = 'sqlite:' . $config['database'];
        } elseif ($config['driver'] === 'oracle') {
            $dsn = 'oci:dbname=' . $config['host'] . '/' . $config['database'];
        }

        try {
            $this->pdo = new PDO($dsn, $config['username'], $config['password']);
            $this->pdo->exec("SET NAMES '" . $config['charset'] . "' COLLATE '" . $config['collation'] . "'");
            $this->pdo->exec("SET CHARACTER SET '" . $config['charset'] . "'");
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die('Cannot the connect to Database with PDO. ' . $e->getMessage());
        }
        return $this->pdo;
    }

    public function query($sql, array $params = [])
    {
        $sth = $this->pdo->prepare($sql);
        $sth->execute($params);
        return $sth;
    }

    public function get($sql, array $params = [])
    {
        $sth = $this->pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetch();
    }

    public function getAll($sql, array $params = [])
    {
        $sth = $this->pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetchAll();
    }

    public function getCount($sql, array $params = [])
    {
        $sth = $this->pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetchColumn();
    }

    public function lastInsertId()
    {
        return $this->pdo->lastInsertId();
    }
}