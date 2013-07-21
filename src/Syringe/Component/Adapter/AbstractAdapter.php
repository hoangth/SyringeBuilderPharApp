<?php

namespace Syringe\Component\Adapter;

use Syringe\Component\Parser\DelegatedParser;
use Syringe\Component\Parser\IParser;
use Syringe\Component\Parser\PhpParser;
use Syringe\Component\Parser\Sf2YamlParser;

abstract class AbstractAdapter
{
    /**
     * @var IParser
     */
    private $parser;

    /**
     * @var array
     */
    protected $configurationsList = array();

    public function __construct()
    {
        $this->initParser();
    }

    /**
     * @return array
     */
    public function getConfigurationsList()
    {
        return $this->configurationsList;
    }

    protected function initParser()
    {
        $this->parser = new DelegatedParser(array(
            new PhpParser(),
            new Sf2YamlParser(),
        ));
    }

    /**
     * @param string $file
     * @return array
     */
    protected function parse($file)
    {
        return $this->parser->parse($file);
    }
}
