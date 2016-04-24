<?php
/**
 * @license see LICENSE
 */

namespace Serps\Test\TDD\SearchEngine\Google\Page;

use Serps\Core\Serp\CompositeResultSet;
use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Google\Page\GoogleSerp;
use Serps\SearchEngine\Google\GoogleUrlArchive;

/**
 * @covers Serps\SearchEngine\Google\Page\GoogleSerp
 */
class GoogleSerpTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @return GoogleSerp
     */
    public function getDomJavascript()
    {
        $url = GoogleUrlArchive::fromString('https://www.google.fr/search?q=simpsons&hl=en_US');
        return new GoogleSerp(file_get_contents('test/resources/pages-evaluated/simpsons.html'), $url);
    }
    /**
     * @return GoogleSerp
     */
    public function getDomNoJavascript()
    {
        $url = GoogleUrlArchive::fromString('https://www.google.fr/search?q=simpsons&hl=en_US');
        return new GoogleSerp(file_get_contents('test/resources/pages-raw/simpsons.html'), $url);
    }

    public function testGetNumberOfResults()
    {
        $count = $this->getDomJavascript()->getNumberOfResults();
        $this->assertEquals(65200000, $count);

        $count = $this->getDomNoJavascript()->getNumberOfResults();
        $this->assertEquals(70900000, $count);
    }


    public function testGetLocation()
    {
        $this->assertEquals('Nantes', $this->getDomJavascript()->getLocation());
    }

    public function testGetNaturalResults()
    {
        $dom = $this->getDomJavascript();

        $results = $dom->getNaturalResults();

        $this->assertInstanceOf(IndexedResultSet::class, $results);
        $this->assertCount(10, $results);


        $dom = $this->getDomNoJavascript();

        $results = $dom->getNaturalResults();

        $this->assertInstanceOf(IndexedResultSet::class, $results);
        $this->assertCount(10, $results);
    }

    public function testGetAdwordsResults()
    {
        $dom = $this->getDomJavascript();
        $results = $dom->getAdwordsResults();
        $this->assertInstanceOf(CompositeResultSet::class, $results);

        $dom = $this->getDomNoJavascript();
        $results = $dom->getAdwordsResults();
        $this->assertInstanceOf(CompositeResultSet::class, $results);
    }

    public function testJavascriptEvaluated()
    {
        $this->assertTrue($this->getDomJavascript()->javascriptIsEvaluated());
        $this->assertFalse($this->getDomNoJavascript()->javascriptIsEvaluated());
    }
}
