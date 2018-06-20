<?php

namespace Sensi\Pgdecorators;

use Monolyth\Disclosure\Injector;
use Ornament\Core\DecoratorInterface;
use JsonSerializable;

class PgArray extends ArrayObject implements JsonSerializable, DecoratorInterface
{
    use Injector;

    private $adapter;

    /**
     * @param object $state
     * @param string $prop
     * @return void
     */
    public function __construct(object $state, string $prop)
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

    /**
     * @return string
     */
    public function __toString() : string
    {
        return '{'.implode(', ', (array)$this).'}';
    }

    /**
     * @return array
     */
    public function jsonSerialize() : array
    {
        return (array)$this;
    }

    /**
     * @return array
     */
    public function getSource()
    {
        return (array)$this;
    }

    /**
     * Reset to pristine state.
     *
     * @return void
     */
    public function scrub() : void
    {
        parent::__construct([]);
    }
}

