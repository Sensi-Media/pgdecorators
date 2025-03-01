<?php

namespace Sensi\Pgdecorators;

use Monolyth\Disclosure\Injector;
use JsonSerializable;
use ArrayObject;

class PgArray extends ArrayObject implements JsonSerializable
{
    use Injector;

    private $adapter;

    /**
     * @param mixed $value
     * @return void
     */
    public function __construct(mixed $value)
    {
        static $stmt;
        if (!isset($stmt)) {
            $this->inject(function ($adapter) {});
            $stmt = $this->adapter->prepare("SELECT array_to_json(?::varchar[])");
        }
        if (is_string($value)) {
            $stmt->execute([$value]);
            $value = json_decode($stmt->fetchColumn());
        }
        parent::__construct((array)$value);
    }

    /**
     * @return string
     */
    public function __toString() : string
    {
        $data = (array)$this;
        array_walk($data, fn (&$el) => $el = '"'.addslashes($el).'"');
        return sprintf('{%s}', implode(',', $data));
    }

    /**
     * @return array
     */
    public function jsonSerialize() : array
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

    /**
     * Get the source, needed to comply with DecoratorInterface.
     *
     * @return array
     */
    public function getSource() : array
    {
        return (array)$this;
    }
}

