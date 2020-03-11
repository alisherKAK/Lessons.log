<?php
spl_autoload_register(function ($name)
{
    //PSR-4
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $name);

    $file = "classes" . DIRECTORY_SEPARATOR . "{$path}.php";

    if(!file_exists($file) || !is_file($file))
        die("File {$file} is not found");

    include_once $file;

    if(!class_exists($name))
        die("Class {$name} is not found");
});