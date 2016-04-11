<?php
namespace PruneMazui\DdlGenerator\DataSource;

use PruneMazui\DdlGenerator\DdlGeneratorException;

/**
 * DataSource Readed Data Container
 * @author tanaka
 */
class RowData implements \ArrayAccess
{
    private $row;

    private $keyMap;

    /**
     * @param array $row
     * @param array $key_map
     */
    public function __construct(array $row, array $key_map)
    {
        $this->row = $row;
        $this->keyMap = $key_map;
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see ArrayAccess::offsetExists()
     */
    public function offsetExists($offset)
    {
        return $this->getFeild($offset) !== null;
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see ArrayAccess::offsetGet()
     */
    public function offsetGet($offset)
    {
        return $this->getFeild($offset);
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see ArrayAccess::offsetSet()
     */
    public function offsetSet($offset, $value)
    {
        throw new DdlGeneratorException('This class is readonly.');
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see ArrayAccess::offsetUnset()
     */
    public function offsetUnset($offset)
    {
        throw new DdlGeneratorException('This class is readonly.');
    }

    /**
     * get feild data
     * @param string $feild constant Feild::const
     * @return string | null
     */
    public function getFeild($feild)
    {
        if (! isset($this->keyMap[$feild])) {
            return null;
        }

        $key = $this->keyMap[$feild];

        if (! isset($this->row[$key])) {
            return null;
        }

        return $this->row[$key];
    }
}
