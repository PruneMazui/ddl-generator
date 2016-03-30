<?php
namespace PruneMazui\DdlGenerator\DdlBuilder;

use PruneMazui\DdlGenerator\TableDefinition\TableDefinition;

interface DdlBuilderInterface
{
    /**
     * @param TableDefinition $definition
     * @return string
     */
    public function build(TableDefinition $definition);
}
