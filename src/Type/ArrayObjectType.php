<?php

namespace KunicMarko\GraphQLTest\Type;

final class ArrayObjectType implements TypeInterface
{
    /**
     * @var array
     */
    private $value;

    public function __construct(array $value = [])
    {
        $this->value = $value;
    }

    public function __invoke($identifier): string
    {
        return '';
    }

    public function getValue(): array
    {
        return $this->value;
    }
}
