<?php

namespace KunicMarko\GraphQLTest\Formatter;

use KunicMarko\GraphQLTest\Type\ArrayObjectType;
use KunicMarko\GraphQLTest\Type\TypeInterface;
use function implode;
use function is_array;
use function is_string;
use function sprintf;

/**
 * @author Marko Kunic <kunicmarko20@gmail.com>
 */
abstract class Formatter implements FormatterInterface
{
    public function __invoke(array $items): string
    {
        if (!$items) {
            return '';
        }

        return sprintf($this->getMainFormat(), implode($this->getImplodeGlue(), $this->collectChild($items)));
    }

    abstract public function getChildArrayFormat(): string;

    abstract public function getChildDefaultFormat(): string;

    abstract public function getChildStringFormat(): string;

    abstract public function getImplodeGlue(): string;

    abstract public function getMainFormat(): string;

    protected function collectChild(array $value): array
    {
        $items = [];

        foreach ($value as $key => $childValue) {
            $items[] = $this->formatChild($key, $childValue);
        }

        return $items;
    }

    private function formatChild($identifier, $value): string
    {
        if ($value instanceof ArrayObjectType) {
            $items = [];

            foreach ($value->getValue() as $item) {
                $items[] = sprintf(
                    $this->getObjectFormat(),
                    implode($this->getImplodeGlue(), $this->collectChild($item))
                );
            }

            return sprintf($this->getArrayFormat(), $identifier, implode($this->getImplodeGlue(), $items));
        }

        if (is_array($value)) {
            return sprintf(
                $this->getChildArrayFormat(),
                $identifier,
                implode($this->getImplodeGlue(), $this->collectChild($value))
            );
        }

        if ($value instanceof TypeInterface) {
            return $value($identifier);
        }

        if (is_string($value)) {
            return sprintf($this->getChildStringFormat(), $identifier, $value);
        }

        return sprintf($this->getChildDefaultFormat(), $identifier, $value);
    }

    private function getArrayFormat(): string
    {
        return '%s: [%s]';
    }

    private function getObjectFormat(): string
    {
        return '{ %s }';
    }
}
