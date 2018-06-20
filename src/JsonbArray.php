<?php

namespace Sensi\Pgdecorators;

use Monolyth\Disclosure\Injector;
use Ornament\Core\DecoratorInterface;
use ArrayObject;
use JsonSerializable;

class JsonbArray extends ArrayObject implements JsonSerializable, DecoratorInterface
{
    use Injector;

    private $adapter;

    /**
     * @param mixed $data
     * @return void
     */
    public function __construct($state, $prop)
    {
        $data = $state->$prop;
        static $stmt;
        if (!isset($stmt)) {
            $this->inject(function ($adapter) {});
            $stmt = $this->adapter->prepare("SELECT array_to_json(?::varchar[])");
        }
        if (is_string($data)) {
            $stmt->execute([$data]);
            $data = json_decode($stmt->fetchColumn());
            foreach ($data as &$el) {
                $el = json_decode($el);
            }
        }
        parent::__construct((array)$data);
    }

    public function __toString() : string
    {
        $items = [];
        foreach ((array)$this as $item) {
            $items[] = '"'.str_replace('"', '\"', json_encode($item)).'"';
        }
        return '{'.implode(', ', $items).'}';
    }

    public function jsonSerialize() : array
    {
        return (array)$this;
    }

    public function getSource()
    {
        return (array)$this;
    }

    public function scrub() : void
    {
        parent::__construct([]);
    }
}

