<?php
namespace PruneMazui\DdlGeneratorTest;

use PruneMazui\DdlGenerator\Definition\Definition;
use PruneMazui\DdlGenerator\Definition\Rules\Table;
use PruneMazui\DdlGenerator\Definition\Rules\Schema;
use PruneMazui\DdlGenerator\Definition\Rules\Index;
use PruneMazui\DdlGenerator\Definition\Rules\ForeignKey;
use PruneMazui\DdlGenerator\DdlGeneratorException;
use PruneMazui\DdlGenerator\Definition\Rules\Column;

class DefinitionTest extends AbstractTestCase
{
    /**
     * @test
     */
    public function finalizeUnsetTest()
    {
        $definition = new Definition();

        // non column Table unset
        $table = new Table("hoge");
        $schema = new Schema(null);
        $schema->addTable($table);
        $definition->addSchema($schema);

        assertEquals(1, $definition->countSchemas());
        assertEquals(1, $definition->countAllTables());
        $definition->finalize();
        assertEquals(0, $definition->countSchemas());
        assertEquals(0, $definition->countAllTables());

        // non column Index unset
        $index = new Index("fuga", false, "", "hoge");
        $definition->addIndex($index);

        assertEquals(1, $definition->countIndexes());
        $definition->finalize();
        assertEquals(0, $definition->countIndexes());

        // non column Foreign Key unset
        $foreign_key = new ForeignKey("piyo", "", "hoge", "", "hoge2", "CASCADE", "CASACADE");
        $definition->addForgienKey($foreign_key);

        assertEquals(1, $definition->countForeignKeys());
        $definition->finalize();
        assertEquals(0, $definition->countForeignKeys());
    }

    /**
     * @test
     */
    public function addSchemaTest()
    {
        $definition = new Definition();

        $definition->addSchema(new Schema(null));
        $definition->addSchema(new Schema("fuga"));

        assertTrue($definition->hasSchema(""));   // same
        assertTrue($definition->hasSchema(null)); // same

        assertEquals(2, $definition->countSchemas());

        try {
            // conflict schema error
            $definition->addSchema(new Schema(""));
            $this->fail();
        } catch (DdlGeneratorException $ex) {
            $this->addToAssertionCount(1);
        }

        assertTrue($definition->getSchema(null)->getSchemaName() === "");
        assertTrue($definition->getSchema("fuga")->getSchemaName() === "fuga");
        assertNull($definition->getSchema("piyo"));

        assertTrue($definition->getSchema("") === $definition->getSchema(null));
        assertFalse($definition->getSchema("") === $definition->getSchema("piyo"));
    }

    /**
     * @test
     */
    public function addIndexTest()
    {
        $definition = new Definition();

        $definition->addIndex(new Index("hoge", true, "", "table"));
        $definition->addIndex(new Index("hoge", true, "", "table2")); // other table

        assertEquals(2, $definition->countIndexes());

        try {
            // conflict index error
            $definition->addIndex(new Index("hoge", true, "", "table"));
            $this->fail();
        } catch (DdlGeneratorException $ex) {
            $this->addToAssertionCount(1);
        }

        try {
            // conflict Foreign key error
            $definition->addForgienKey(new ForeignKey("hoge", "", "table", "", "lockup", "piyo", "piyo"));
            $this->fail();
        } catch (DdlGeneratorException $ex) {
            $this->addToAssertionCount(1);
        }
    }

    /**
     * @test
     */
    public function addForgienKeyTest()
    {
        $definition = new Definition();

        $definition->addForgienKey(new ForeignKey("hoge", "", "table", "", "lockup", "piyo", "piyo"));
        $definition->addForgienKey(new ForeignKey("hoge", "", "table2", "", "lockup", "piyo", "piyo")); // other table

        assertEquals(2, $definition->countForeignKeys());

        try {
            // conflict index error
            $definition->addIndex(new Index("hoge", true, "", "table"));
            $this->fail();
        } catch (DdlGeneratorException $ex) {
            $this->addToAssertionCount(1);
        }

        try {
            // conflict Foreign key error
            $definition->addForgienKey(new ForeignKey("hoge", "", "table", "", "lockup", "piyo", "piyo"));
            $this->fail();
        } catch (DdlGeneratorException $ex) {
            $this->addToAssertionCount(1);
        }
    }

    /**
     * @test
     */
    public function getterTest()
    {
        $definition = new Definition();

        $column = new Column("fuga", "INT");

        $table = new Table("hoge");
        $table->addColumn($column);

        $schema = new Schema(null);
        $schema->addTable($table);
        $definition->addSchema($schema);

        assertTrue($definition->getSchema("") instanceof Schema);
        assertTrue($definition->getSchema(null) instanceof Schema);
        assertNull($definition->getSchema("aaaa"));

        assertTrue($definition->getTable("", "hoge") instanceof Table);
        assertNull($definition->getTable("", "piyo"));
        assertNull($definition->getTable("piyo", "hoge"));

        assertTrue($definition->getColumn("", "hoge", "fuga") instanceof Column);
        assertNull($definition->getColumn("", "hoge", "piyo"));
        assertNull($definition->getColumn("", "piyo", "fuga"));
    }

}
