<?php

declare(strict_types=1);

namespace theodorejb\Phaster;

use DateTimeZone;

class Prop
{
    /** @readonly */
    public string $name;
    /** @readonly */
    public string $col;
    /** @readonly */
    public string $alias;

    /**
     * @var callable|null
     * @readonly
     */
    public $getValue;
    /** @readonly */
    public ?string $type = null;

    /**
     * @var DateTimeZone|null|false
     * @readonly
     */
    public $timeZone;

    /**
     * @var string[]
     * @readonly
     */
    public array $dependsOn;
    /** @readonly */
    public bool $nullGroup;
    /** @readonly */
    public bool $isDefault;

    /**
     * This is the only property which can be modified externally.
     */
    public bool $noOutput = false;

    /**
     * @var string[]
     * @readonly
     */
    public array $map;
    /** @readonly */
    public int $depth;

    /**
     * @var string[]
     * @readonly
     */
    public array $parents = [];

    /**
     * @param DateTimeZone|null|false $timeZone
     * @param string[] $dependsOn
     */
    public function __construct(
        string $name,
        string $col,
        bool $nullGroup = false,
        bool $isDefault = true,
        string $alias = '',
        ?string $type = null,
        $timeZone = false,
        ?callable $getValue = null,
        array $dependsOn = []
    ) {
        $this->map = explode('.', $name);
        $this->depth = count($this->map);
        $parent = '';

        for ($i = 0; $i < $this->depth - 1; $i++) {
            $parent .= $this->map[$i] . '.';
            $this->parents[] = $parent;
        }

        if ($nullGroup && $this->depth < 2) {
            throw new \Exception("nullGroup cannot be set on top-level {$name} property");
        }

        if ($getValue !== null) {
            if ($type !== null) {
                throw new \Exception("type cannot be set on {$name} property along with getValue");
            } elseif ($timeZone !== false) {
                throw new \Exception("timeZone cannot be set on {$name} property along with getValue");
            }
        }

        if ($type !== null) {
            $allowedTypes = ['bool', 'int', 'float', 'string'];

            if (!in_array($type, $allowedTypes, true)) {
                throw new \Exception("type for {$name} property must be bool, int, float, or string");
            }

            if ($timeZone !== false) {
                throw new \Exception("timeZone cannot be set on {$name} property along with type");
            }
        }

        if (count($dependsOn) !== 0 && $getValue === null) {
            throw new \Exception("dependsOn cannot be used on {$name} property without getValue function");
        }

        $this->name = $name;
        $this->col = $col;
        $this->alias = $alias;
        $this->getValue = $getValue;
        $this->type = $type;
        $this->timeZone = $timeZone;
        $this->dependsOn = $dependsOn;
        $this->nullGroup = $nullGroup;
        $this->isDefault = $isDefault;
    }

    public function getOutputCol(): string
    {
        if ($this->alias) {
            return $this->alias;
        }

        $col = explode('.', $this->col);
        return array_pop($col);
    }
}
