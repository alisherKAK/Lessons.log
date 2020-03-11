<?php
namespace Tag;

class BaseTag
{
    protected const SELF_CLOSING = [
        'area', 'base', 'br', 'embed', 'hr', 'iframe', 'img', 'input',
        'link', 'meta', 'param', 'source', 'track'
    ];
    protected $name, $attributes = [], $body;

    public function __construct($name, array $attributes = [])
    {
        $this->setName($name);
        $this->setAttribute($attributes);
    }

    public function setAttribute($key, $value = null)
    {

        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->setAttribute($k, $v);
            }
        } else {
            $this->attributes[$key] = $value;
        }
        return $this;
    }

    public function appendBody($body)
    {
        if(is_array($body))
            foreach ($body as $item)
                $this->appendBody($item);
        else
            $this->body[] = $body;

        return $this;
    }

    protected function setBody($body)
    {
        $this->body = is_array($body) ? $body : [$body];
        return $this;
    }

    public function getBody()
    {
        if (!$this->isSelfClosing())
            return implode($this->body ?? []);
        return null;
    }

    public function isSelfClosing()
    {
        return in_array($this->getName(), self::SELF_CLOSING);
    }

    public function getName()
    {
        return strtolower($this->name);
    }

    protected function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function prependBody($body)
    {
        if(is_array($body))
            foreach (array_reverse($body) as $item)
                $this->prependBody($item);
        else
            array_unshift($this->body, $body);

        return $this;
    }

    public function __toString()
    {
        return $this->getString();
    }

    public function getString()
    {
        return $this->start() . $this->getBody() . $this->end();
    }

    public function start()
    {
        $tag = "<{$this->getName()}";
        foreach ($this->getAttributes() as $key => $attribute) {
            $tag .= " $key";
            if ($attribute != null)
                $tag .= "=\"$attribute\"";
        }
        return $tag . ($this->isSelfClosing() ? " />" : ">");
    }

    protected function getAttributes()
    {
        return $this->attributes;
    }

    public function end()
    {
        if (!$this->isSelfClosing())
            return "</{$this->getName()}>";

        return null;
    }

    public function appendAttribute($key, $value)
    {
        return $this->setAttribute($key, $this->getAttribute($key) . $value);
    }

    // -------- CLASS ATTRIBUTE -----------

    protected function getAttribute($key)
    {
        return $this->attributes[$key] ?? null;
    }

    public function prependAttribute($key, $value)
    {
        return $this->setAttribute($key, $value . $this->getAttribute($key));
    }

    public function addClass($class)
    {
        $classes = $this->classesAsArray();

        if (!$this->classExists($class))
            $classes[] = $class;

        $classes = implode(' ', $classes);
        $this->setAttribute('class', $classes);

        return $this;
    }

    public function classesAsArray()
    {
        $classAttr = $this->getAttribute('class');

        if ($classAttr == null)
            return [];

        return explode(' ', $classAttr);
    }

    public function __call($name, $arguments)
    {
        return $this->setAttribute($name, $arguments[0] ?? null);
    }

    // ------------ STATIC ------------

    public function classExists($class): bool
    {
        $classes = $this->classesAsArray();
        return in_array($class, $classes);
    }

    public function removeClass($class)
    {
        if ($this->classExists($class)) {
            $classes = $this->classesAsArray();
            $classes = array_diff($classes, [$class]);
            $classes = implode(' ', $classes);
            $this->setAttribute('class', $classes);
        }

        return $this;
    }

    public function appendTo(BaseTag $tag) {
        $tag->appendBody($this);
        return $this;
    }

    public function prependTo(BaseTag $tag) {
        $tag->prependTo($this);
        return $this;
    }
}
