<?php
namespace Tag;

abstract class NamedTag extends BaseTag
{
    public function __construct(array $attributes = []) {
        parent::__construct(static::name(), $attributes);
    }

    public static function make(array $attributes = []) {
        return new static($attributes);
    }

    protected abstract static function name() : string;
}