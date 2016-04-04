<?php
namespace PruneMazui\DdlGeneratorTest;

use PruneMazui\DdlGenerator\Definition\Rules\Table;
use PruneMazui\DdlGenerator\Definition\Rules\ForeignKey;

class DefinitionTest extends AbstractTestCase
{
    /**
     * @test
     */
    public function foreignKeyTest()
    {
        $table = new Table('hoge');
        $lookup_table = new Table('fuga');

        $key = new ForeignKey($table, $lookup_table, 'test', 'CASCADE', 'CASCADE');

        assertTrue($key instanceof ForeignKey);

        unset($lookup_table);
        var_dump($key);
    }
}
