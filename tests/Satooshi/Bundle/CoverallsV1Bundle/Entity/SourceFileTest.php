<?php
namespace Satooshi\Bundle\CoverallsV1Bundle\Entity;

use Satooshi\ProjectTestCase;

/**
 * @covers Satooshi\Bundle\CoverallsV1Bundle\Entity\SourceFile
 * @covers Satooshi\Bundle\CoverallsV1Bundle\Entity\Coveralls
 *
 * @author Kitamura Satoshi <with.no.parachute@gmail.com>
 */
class SourceFileTest extends ProjectTestCase
{
    protected function setUp()
    {
        $this->projectDir = realpath(__DIR__ . '/../../../..');

        $this->setUpDir($this->projectDir);

        $this->filename = 'test.php';
        $this->path     = $this->srcDir . DIRECTORY_SEPARATOR . $this->filename;

        $this->object = new SourceFile($this->path, $this->filename);
    }

    // getName()

    /**
     * @test
     */
    public function shouldHaveNameOnConstruction()
    {
        $this->assertEquals($this->filename, $this->object->getName());
    }

    // getSource()

    /**
     * @test
     */
    public function shouldHaveSourceOnConstruction()
    {
        $expected = trim(file_get_contents($this->path));

        $this->assertEquals($expected, $this->object->getSource());
    }

    // getCoverage()

    /**
     * @test
     */
    public function shouldHaveNullCoverageOnConstruction()
    {
        $expected = array_fill(0, 9, null);

        $this->assertEquals($expected, $this->object->getCoverage());
    }

    // getPath()

    /**
     * @test
     */
    public function shouldHavePathOnConstruction()
    {
        $this->assertEquals($this->path, $this->object->getPath());
    }

    // getFileLines()

    /**
     * @test
     */
    public function shouldHaveFileLinesOnConstruction()
    {
        $this->assertEquals(9, $this->object->getFileLines());
    }

    // toArray()

    /**
     * @test
     */
    public function shouldConvertToArray()
    {
        $expected = array(
            'name'     => substr($this->path, strlen(getcwd())+1),
            'source'   => trim(file_get_contents($this->path)),
            'coverage' => array_fill(0, 9, null),
        );

        $this->assertEquals($expected, $this->object->toArray());
        $this->assertEquals(json_encode($expected), (string)$this->object);
    }

    // addCoverage()

    /**
     * @test
     */
    public function shouldAddCoverage()
    {
        $this->object->addCoverage(5, 1);

        $expected = array_fill(0, 9, null);
        $expected[5] = 1;

        $this->assertEquals($expected, $this->object->getCoverage());
    }

    // getMetrics()
    // reportLineCoverage()

    /**
     * @test
     */
    public function shouldReportLineCoverage0PercentWithoutAddingCoverage()
    {
        $metrics = $this->object->getMetrics();

        $this->assertEquals(0, $metrics->getStatements());
        $this->assertEquals(0, $metrics->getCoveredStatements());
        $this->assertEquals(0, $metrics->getLineCoverage());
        $this->assertEquals(0, $this->object->reportLineCoverage());
    }

    /**
     * @test
     */
    public function shouldReportLineCoverage100PercentAfterAddingCoverage()
    {
        $this->object->addCoverage(6, 1);

        $metrics = $this->object->getMetrics();

        $this->assertEquals(1, $metrics->getStatements());
        $this->assertEquals(1, $metrics->getCoveredStatements());
        $this->assertEquals(100, $metrics->getLineCoverage());
        $this->assertEquals(100, $this->object->reportLineCoverage());
    }
}
