<?php

namespace Nails\Factory;

use Nails\Common\Factory\Component;

class Description
{
    public function __construct(
        private readonly string $type,
        private readonly string $name,
        private readonly Component $component
    ) {
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getComponent(): Component
    {
        return $this->component;
    }
}
