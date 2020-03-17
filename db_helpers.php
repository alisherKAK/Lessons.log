<?php

function db_connect($database, $port = 3306) {

    $connection = mysqli_connect('localhost',
        'root',
        '',
        $database, $port);

    if ( mysqli_connect_errno() )
        die( mysqli_connect_error() );

    return $connection;
}

function db_checkOrDie($connection) {
    if (!$connection || mysqli_errno($connection))
        die( mysqli_error($connection) );
}

function db_getConnectionFromTable($table) {
    $table = explode('.', $table);
    return db_connect($table[0] ?? '');
}

function db_getTable($table) {
    return explode('.', $table)[1] ?? '';
}

function db_escapeData($data, $connection) {

    if (!is_bool($data) && !is_null($data))
        $data = mysqli_real_escape_string($connection, $data);

    if (is_numeric($data))
        return $data;
    if (is_string($data))
        return "'$data'";
    if (is_bool($data))
        return $data ? 1 : 0;
    if (is_null($data))
        return 'NULL';

    die('Incorrect $data -> ' . $data);
}

function db_whereBuilder(array $where, $connection) {
    $query = " WHERE";
    foreach ($where as $col => $value) {
        $query .= " $col=" . db_escapeData($value, $connection);
    }
    return $query;
}

function db_dataBuilder(array $data, $connection) {
    $cols = array_keys($data);

    $values = array_map(function ($item) use ($connection) {
        return db_escapeData($item, $connection);
    }, $data);

    return [
        'cols' => implode(',', $cols),
        'values' => implode(',', $values)
    ];
}

function db_columnsQueryBuilder(array $columns) {
   $result = "\n";
   foreach ($columns as $key => $constraints)
   {
       $result .= $key . ' ';
       if(!is_array($constraints))
           $result .= $constraints;
       else
           foreach ($constraints as $constraint)
               $result .= $constraint . ' ';

       $result .= ",\n";
   }

   return trim($result, ",\t\n\r\0\x0B");
}

function db_alterQueryBuilder(array $args) {
    $result = '';
    foreach ($args as $alter => $columns) {
        foreach ($columns as $key => $constraints)
        {
            switch (strtolower($alter)) {
                case "add":
                    $result .= "ADD COLUMN ";
                    break;
                case "drop":
                    $result .= "DROP COLUMN ";
                    break;
                case "modify":
                    $result .= "MODIFY COLUMN ";
                    break;
            }
            $result .= "{$key} ";

            if(!is_array($constraints))
                $constraints = [$constraints];

            foreach ($constraints as $constraint)
                $result .= "{$constraint} ";

            $result .= ",\n";
        }
    }

    return trim($result, ",\t\n\r\0\x0B");
}

function db_select($table, $cols = "*", array $where = []) {
    // Если $cols массив, то делаемм строкой через запятую
    if (is_array($cols))
        $cols = implode(',', $cols);
    // подключение к базе
    $connection = db_getConnectionFromTable($table);
    // Берем имя таблицы
    $table = db_getTable($table);
    // Базовый запрос
    $query = "SELECT $cols from `$table`";
    // Если есть WHERE
    if (count($where) > 0) {
        $query .= db_whereBuilder($where, $connection);
    }
    // Пытаемся взять результат
    $result = mysqli_query($connection, $query);
    db_checkOrDie($connection);
    // Превращаем в асоциативный массив
    $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
    // Закрываем соедиение
    mysqli_close($connection);
    // Отдаем результат
    return $rows;
}

function db_insert($table, array $data) {

    $connection = db_getConnectionFromTable($table);
    $table = db_getTable($table);

    $data = db_dataBuilder($data, $connection);
    $cols = $data['cols'];
    $values = $data['values'];

    $query = "INSERT INTO $table ($cols) VALUES ($values)";
    $result = mysqli_query($connection, $query);

    if ($result == false)
        db_checkOrDie($connection);

    mysqli_close($connection);
    return $result;
}

function db_update($table, array $where, array $data) {

    $connection = db_getConnectionFromTable($table);
    $table = db_getTable($table);

    $cols  = [];
    foreach (array_keys($data) as $col) {
        $cols[] = "$col=" . db_escapeData($data[$col], $connection);
    }
    $set = implode(',', $cols);
    $where = db_whereBuilder($where, $connection);

    $query = "UPDATE $table SET $set $where";
    $result = mysqli_query($connection, $query);

    if ($result == false)
        db_checkOrDie($connection);

    mysqli_close($connection);
    return $result;
}

function db_delete($table, array $where) {

    $connection = db_getConnectionFromTable($table);
    $table = db_getTable($table);

    $where = db_whereBuilder($where, $connection);

    $query = "DELETE FROM $table $where";

    $result = mysqli_query($connection, $query);

    if ($result == false)
        db_checkOrDie($connection);

    mysqli_close($connection);
    return $result;

}

function db_create(string $table, array $columns) {
    $tableName = db_getTable($table);
    $connection = db_getConnectionFromTable($table);
    $columnsQuery = db_columnsQueryBuilder($columns);

    $query = "CREATE TABLE {$tableName} ({$columnsQuery})";
    $result = mysqli_query($connection, $query);

    if($result == false)
        db_checkOrDie($connection);

    mysqli_close($connection);
    return $result;
}

function db_drop(string $table) {
    $connection = db_getConnectionFromTable($table);
    $table = db_getTable($table);

    $query = "DROP TABLE {$table}";
    $result = mysqli_query($connection, $query);

    if($result == false)
        db_checkOrDie($connection);

    mysqli_close($connection);
    return $result;
}

function db_alter(string $table, array $args) {
    $connection = db_getConnectionFromTable($table);
    $table = db_getTable($table);

    $query = "ALTER TABLE {$table} " . db_alterQueryBuilder($args);
    $result = mysqli_query($connection, $query);

    if($result == false)
        db_checkOrDie($connection);

    mysqli_close($connection);
    return $result;
}