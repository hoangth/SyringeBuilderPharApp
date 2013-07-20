<?php

namespace Syringe\Component\Parser;

interface IParser
{
    /**
     * @param string $file
     * @return array
     */
    public function parse($file);
}
