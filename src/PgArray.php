<?php

namespace Sensi\Pgdecorators;

use Monolyth\Disclosure\Injector;
use Ornament\Core\DecoratorInterface;
use JsonSerializable;
use ArrayObject;

class PgArray extends ArrayObject implements JsonSerializable, DecoratorInterface
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
        }
        parent::__construct((array)$data);
    }

    public function __toString() : string
    {
        return '{'.implode(', ', (array)$this).'}';
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

