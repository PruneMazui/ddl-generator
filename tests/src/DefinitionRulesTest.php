<?php
namespace PruneMazui\DdlGenerator\Definition;

use PruneMazui\DdlGenerator\DdlGeneratorException;
use PruneMazui\DdlGeneratorTest\AbstractTestCase;
use PruneMazui\DdlGenerator\Definition\Rules\Column;
use PruneMazui\DdlGenerator\Definition\Rules\Table;
use PruneMazui\DdlGenerator\Definition\Rules\Index;
use PruneMazui\DdlGenerator\Definition\Rules\ForeignKey;
use PruneMazui\DdlGenerator\Definition\Rules\Schema;

class DefinitionRulesTest extends AbstractTestCase
{
    /**
     * @test
     */
    public function columnTest()
    {
        // colum name is required
        try {
            new Column("", "INT");
            $this->fail();
        } catch (DdlGeneratorException $ex) {
            $this->addToAssertionCount(1);
        }

        // data type is required
        try {
            new Column("name", "");
            $this->fail();
        } catch (DdlGeneratorException $ex) {
            $this->addToAssertionCount(1);
        }

        // toString
        $column = new Column("name", "INT");
        assertEquals((string) $column, "name");
    }

    /**
     * @test
     */
    public function tableTest()
    {
        // table name is required
        try {
            new Table("");
            $this->fail();
        } catch (DdlGeneratorException $ex) {
            $this->addToAssertionCount(1);
        }

        // primary key
        $table = new Table("table");
        $table->addColumn(new Column("column1", "INT"));
        $table->addColumn(new Column("column2", "INT"));

        // nocolumn
        try {
            $table->setPrimaryKey("not_exist_column");
            $this->fail();
        } catch (DdlGeneratorException $ex) {
            $this->addToAssertionCount(1);
        }

        $table->setPrimaryKey(array("column1", "column2"));
        assertCount(2, $table->getPrimaryKey());

        $table->setPrimaryKey("column1");
        assertCount(1, $table->getPrimaryKey());

        $table->addPrimaryKey("column2");
        assertCount(2, $table->getPrimaryKey());

        // already exist column add error
        try {
            $table->addColumn(new Column("column1", "INT"));
            $this->fail();
        } catch (DdlGeneratorException $ex) {
            $this->addToAssertionCount(1);
        }

        // to string
        assertEquals((string) $table, "table");

        // immutable test
        $table->lock();
        try {
            $table->addColumn(new Column("column3", "test"));
            $this->fail();
        } catch (DdlGeneratorException $ex) {
            $this->addToAssertionCount(1);
        }

        try {
            $table->addPrimaryKey("column2");
            $this->fail();
        } catch (DdlGeneratorException $ex) {
            $this->addToAssertionCount(1);
        }
    }

    /**
     * @test
     */
    public function indexTest()
    {
        // key name is required
        try {
            new Index("", true, null, "table_name");
            $this->fail();
        } catch (DdlGeneratorException $ex) {
            $this->addToAssertionCount(1);
        }

        // table name is required
        try {
            new Index("key_name", true, null, "");
            $this->fail();
        } catch (DdlGeneratorException $ex) {
            $this->addToAssertionCount(1);
        }

        $index = new Index("key_name", true, null, "table_name");
        // column name is required
        try {
            $index->addColumn("");
            $this->fail();
        } catch (DdlGeneratorException $ex) {
            $this->addToAssertionCount(1);
        }

        // to string
        assertEquals((string) $index, "key_name");

        // immutable test
        $index->lock();
        try {
            $index->addColumn("column");
            $this->fail();
        } catch (DdlGeneratorException $ex) {
            $this->addToAssertionCount(1);
        }
    }

    /**
     * @test
     */
    public function foreignKeyTest()
    {
        // key name is required
        try {
            new ForeignKey("", null, "table_name", null, "lookup_table_name", "CASCADE", "CASCADE");
            $this->fail();
        } catch (DdlGeneratorException $ex) {
            $this->addToAssertionCount(1);
        }

        // table name is required
        try {
            new ForeignKey("key_name", null, "", null, "lookup_table_name", "CASCADE", "CASCADE");
            $this->fail();
        } catch (DdlGeneratorException $ex) {
            $this->addToAssertionCount(1);
        }

        // lookup table name is required
        try {
            new ForeignKey("key_name", null, "table_name", null, "", "CASCADE", "CASCADE");
            $this->fail();
        } catch (DdlGeneratorException $ex) {
            $this->addToAssertionCount(1);
        }

        // on update is required
        try {
            new ForeignKey("key_name", null, "table_name", null, "lookup_table_name", "", "CASCADE");
            $this->fail();
        } catch (DdlGeneratorException $ex) {
            $this->addToAssertionCount(1);
        }

        // on delete is required
        try {
            new ForeignKey("key_name", null, "table_name", null, "lookup_table_name", "CASCADE", "");
            $this->fail();
        } catch (DdlGeneratorException $ex) {
            $this->addToAssertionCount(1);
        }

        $foreign_key = new ForeignKey("key_name", null, "table_name", null, "lookup_table_name", "CASCADE", "CASCADE");

        // column name is required
        try {
            $foreign_key->addColumn("", "lookup_column");
            $this->fail();
        } catch (DdlGeneratorException $ex) {
            $this->addToAssertionCount(1);
        }

        // lookup column name is required
        try {
            $foreign_key->addColumn("column", "");
            $this->fail();
        } catch (DdlGeneratorException $ex) {
            $this->addToAssertionCount(1);
        }

        // to string
        assertEquals((string) $foreign_key, "key_name");

        // immutable test
        $foreign_key->lock();
        try {
            $foreign_key->addColumn("column", "lookup_column");
            $this->fail();
        } catch (DdlGeneratorException $ex) {
            $this->addToAssertionCount(1);
        }
    }

    /**
     * @test
     */
    public function schemaTest()
    {
        // add table conflict error
        $schema = new Schema("schama_name");
        $schema->addTable(new Table("table"));

        try {
            $schema->addTable(new Table("table"));
            $this->fail();
        } catch (DdlGeneratorException $ex) {
            $this->addToAssertionCount(1);
        }

        // to string
        assertEquals((string) $schema, "schama_name");

        // immutable test
        $schema = new Schema("schama_name");
        $schema->lock();

        try {
            $schema->addTable(new Table("table"));
            $this->fail();
        } catch (DdlGeneratorException $ex) {
            $this->addToAssertionCount(1);
        }
    }
}
