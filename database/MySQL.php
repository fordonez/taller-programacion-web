<?php

namespace Database;

use mysqli as MySQLi;

class MySQL
{
    private string $hostname;
    private int $port;
    private string $username;
    private string $password;
    private string $database;
    private ?MySQLi $connection;

    public function __construct(string $hostname, int $port, string $username, string $password, string $database)
    {
        $this->hostname = $hostname;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;
    }

    public function connect(): void
    {
        $this->connection = new MySQLi($this->hostname, $this->username, $this->password, $this->database, $this->port);

        if ($this->connection->connect_error) {
            error_log('Conexión fallida: ' . $this->connection->connect_error);
        }
    }

    public function disconnect(): void
    {
        if ($this->connection !== null) {
            $this->connection->close();
            $this->connection = null;
        }
    }

    public function insert(string $table, array $data): bool
    {
        if ($this->connection === null) {
            return false;
        }

        $columns = implode(", ", array_keys($data));
        $values = implode("', '", array_map([$this->connection, 'real_escape_string'], array_values($data)));

        /** @noinspection SqlNoDataSourceInspection */
        $query = "INSERT INTO $table ($columns) VALUES ('$values')";

        error_log($query);

        if ($this->connection->query($query) === true) {
            return true;
        } else {
            error_log('Error inserting data: ' . $this->connection->error);
            return false;
        }
    }

    public function update(string $table, array $conditions, array $data): bool
    {
        if ($this->connection === null) {
            return false;
        }

        $setClause = implode(", ", array_map(
            fn($key, $value) => "$key='" . $this->connection->real_escape_string($value) . "'",
            array_keys($data),
            $data
        ));

        $whereClause = implode(" AND ", array_map(
            fn($key, $value) => "$key='" . $this->connection->real_escape_string($value) . "'",
            array_keys($conditions),
            $conditions
        ));

        /** @noinspection SqlNoDataSourceInspection */
        $query = "UPDATE $table SET $setClause WHERE $whereClause";

        if ($this->connection->query($query) === true) {
            return true;
        } else {
            error_log('Error updating data: ' . $this->connection->error);
            return false;
        }
    }

    public function selectOne(string $table, array $conditions = []): ?array
    {
        if ($this->connection === null) {
            return null;
        }

        // Construir la consulta 'where'
        $whereClause = '';
        if (!empty($conditions)) {
            $whereClause = ' WHERE ';
            $conditionsStrings = [];
            foreach ($conditions as $column => $value) {
                $conditionsStrings[] = "$column='" . $this->connection->real_escape_string($value) . "'";
            }
            $whereClause .= implode(' AND ', $conditionsStrings);
        }

        $query = "SELECT * FROM $table $whereClause LIMIT 1";
        $result = $this->connection->query($query);

        if ($result === false) {
            error_log('Error selecting data: ' . $this->connection->error);
            return null;
        }

        $row = $result->fetch_assoc();
        $result->free();

        return $row ?: null;
    }
}
