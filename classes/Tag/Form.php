<?php
namespace Tag;

use Tag;

/**
 * @method self method($value)
 * @method self action($value)
 */
class Form extends NamedTag
{
    protected static function name(): string
    {
        return 'form';
    }

    public static function input($name, $type = 'text', $value = null) {
        $attributes = ['name' => $name, 'type' => $type];
        if($value)
            $attributes['value'] = $value;

        return Tag::input($attributes);
    }

    public static function password($name, $value = null) {
        return self::input($name, 'password', $value);
    }

    public static function file($name, $value = null) {
        return self::input($name, 'file', $value);
    }

    public static function label($for, $body) {
        $attributes = ['for' => $for];
        $label = Tag::label($attributes);

        if($body)
            $label->appendBody($body);

        return $label;
    }

    public static function textarea($name, $body, $rows, $cols) {
        $attributes = ['name' => $name, 'rows' => $rows, 'cols' => $cols];
        $textarea = Tag::textarea($attributes)->appendBody($body);

        if($body)
            $textarea->appendBody($body);

        return $textarea;
    }
}