<?php

namespace Syringe\Component\Adapter;

use Syringe\Component\Parser\IParser;

class SimpleAdapter extends AbstractAdapter implements IAdapter
{
    /**
     * @param array $fileList
     */
    public function __construct(array $fileList)
    {
        parent::__construct();

        foreach ($fileList as $file) {
            if (!is_readable($file)) {
                continue;
            }

            $this->configurationsList[] = $this->parse($file);
        }
    }
}
