<?php

//include_once "db_helpers.php";
//
//db_insert('lessons.books', [
//    'name' => 'asd'
//]);
//
//db_update('lessons.books', [
//    'id' => 1
//], [
//    'name' => 'QWERTY',
//    'content' => 'ZXCVB'
//]);

include "classes/Tag.php";

$link = Tag::make('a');
//$link->disabled();
//$link->href("google.com");
//$link->id("main");

/**
* @var Tag $tag
*/

echo Tag::table()->appendBody("hello")->prependBody(["asd", "123"]);