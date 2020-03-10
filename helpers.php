<?php
function render($template, array $variables = []) {
    $template = str_replace("/", "\\", $template);
    $template = str_replace("\\", DIRECTORY_SEPARATOR, $template);
    $template = ltrim($template, DIRECTORY_SEPARATOR);
    $template = rtrim($template, DIRECTORY_SEPARATOR);
    $template = getcwd() . DIRECTORY_SEPARATOR . $template;

    if(!file_exists($template) && !is_file($template))
        die("Template {$template} is not found");

    if($variables)
        foreach ($variables as $variable => $value)
            $$variable = $value;

    include $template;
}

function attr($attr, $value = null){
    $result = "";
    if(is_string($attr))
        $result = $attr;

    if(is_string($attr) && $value)
        $result .= '=';

    if($value)
        $result .= "\"{$value}\"";

    return $result;
}
function tag($name, array $attributes = [], $body = null, bool $selfClosing = false){
    $selfColosed = ["area", "base", "br", "col", "hr", "embed", "img", "input", "link", "meta", "param", "source", "track", "wbr"];

    $selfClosing = in_array($name, $selfColosed) || $selfClosing;

    $tag = "<{$name}";

    foreach ($attributes as $attribute => $value)
        $tag .= " " . attr($attribute, $value);

    return $tag . ($selfClosing ? " />" : ">{$body}</{$name}>");
}

function csvToArray(string $file, string $separator = ";") : array {
    $result = [];
    if(!file_exists($file) || !is_file($file))
        return $result;

    $lines = file($file);

    $headers = explode($separator, trim($lines[0], "\n"));

    $result["headers"] = $headers;
    $result["data"] = [];

    for($i = 1; $i < count($lines); $i++)
    {
        $linesData = explode($separator, trim($lines[$i], "\n"));
        $data = [];

        for($j = 0; $j < count($result["headers"]); $j++)
        {
            $data[$result["headers"][$j]] = trim($linesData[$j], "\n");
        }

        $result["data"][] = $data;
    }

    return $result;
}
function arrayToCSV(strign $file, array $arr, string $separator = ";") {
    $result = "";

    $headers = $arr["headers"];
    $data = $arr["data"];

    for($i = 0; $i < count($headers); $i++)
    {
        $result .= $headers[$i] . $separator;
    }
    $result = trim($result, $separator);

    //TODO
}
function addToCSV(string $file, array $arr){

}