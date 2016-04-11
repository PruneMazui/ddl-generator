<?php
namespace PruneMazui\DdlGenerator\Definition\Rules;

abstract class AbstractRules implements RulesInterface
{
    protected $isLocked = false;

    /**
     * @return \PruneMazui\DdlGenerator\Definition\Rules\AbstractRules
     */
    public function lock()
    {
        $this->isLocked = true;
        return $this;
    }
}
