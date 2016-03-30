<?php
namespace PruneMazui\DdlGeneratorTest;

use PruneMazui\DdlGenerator\DdlGenerator;

class DdlGenerateTest extends \PHPUnit_Framework_TestCase
{
    private $config;

    protected function setUp()
    {
        $this->config = array(
            'filename'    => __DIR__ . '/files/db_specifications.xlsx',
        );
    }

    /**
     * @test
     */
    public function runTest()
    {
        $generator = new DdlGenerator($this->config);

        assertTrue($generator instanceof DdlGenerator);
    }
}
