<?php

namespace Syringe\Component\Parser;

interface IFileSupportedParser extends IParser
{
    /**
     * @param string $filePath
     * @return bool
     */
    public function isSupport($filePath);
}
