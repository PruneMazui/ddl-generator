<?php
namespace PruneMazui\DdlGeneratorTest;

use PruneMazui\DdlGenerator\Definition\Definition;
use PruneMazui\DdlGenerator\DdlBuilder\MySqlDdlBuilder;
use PruneMazui\DdlGenerator\Definition\Rules\Table;
use PruneMazui\DdlGenerator\Definition\Rules\Schema;
use PruneMazui\DdlGenerator\DdlGeneratorException;

class DdlBuilderTest extends AbstractTestCase
{
    private $sampleDefinition;

    protected function setUp()
    {
        $definition = new Definition();

        $schema = new Schema('');
        $definition->addSchema($schema);

        $table = new Table('t_test');
        $schema->addTable($table);

        $this->sampleDefinition = $definition;
    }

    /**
     * @test
     */
    public function emptyTest()
    {
        $definition = new Definition();

        $builder = new MySqlDdlBuilder();
        $sql = $builder->buildAll($definition);

        assertEmpty($sql);
    }

    /**
     * @test
     */
    public function configTest()
    {
        $builder = new MySqlDdlBuilder();
        $config = $builder->getConfig();

        assertTrue(is_array($config));
        assertArrayHasKey('end_of_line', $config);
        assertEquals($builder->getConfig('end_of_line'), "\n");

        $content = $builder->buildAll($this->sampleDefinition);
        assertNotContains("\r\n", $content);

        $builder = new MySqlDdlBuilder(array(
            'end_of_line' => "\r\n"
        ));
        $config = $builder->getConfig();
        assertEquals($builder->getConfig('end_of_line'), "\r\n");

        $content = $builder->buildAll($this->sampleDefinition);
        assertContains("\r\n", $content);

        assertNull($builder->getConfig('hoge'));
    }

    /**
     * @test
     */
    public function mysqlErrorTest()
    {
        $definition = clone $this->sampleDefinition;

        $builder = new MySqlDdlBuilder();

        $content = $builder->buildAll($definition);
        $this->addToAssertionCount(1);

        $schema = new Schema("fuga");
        $definition->addSchema($schema);

        try {
            $content = $builder->buildAll($definition);
            $this->fail();
        } catch (DdlGeneratorException $ex) {
            $this->addToAssertionCount(1);
        }

        try {
            $content = $builder->buildAll($definition);
            $this->fail();
        } catch (DdlGeneratorException $ex) {
            $this->addToAssertionCount(1);
        }

        try {
            $content = $builder->buildAllCreateForeignKey($definition);
            $this->fail();
        } catch (DdlGeneratorException $ex) {
            $this->addToAssertionCount(1);
        }

        try {
            $content = $builder->buildAllCreateIndex($definition);
            $this->fail();
        } catch (DdlGeneratorException $ex) {
            $this->addToAssertionCount(1);
        }

        try {
            $content = $builder->buildAllCreateTable($definition);
            $this->fail();
        } catch (DdlGeneratorException $ex) {
            $this->addToAssertionCount(1);
        }

        try {
            $content = $builder->buildAllDropTable($definition);
            $this->fail();
        } catch (DdlGeneratorException $ex) {
            $this->addToAssertionCount(1);
        }
    }
}
