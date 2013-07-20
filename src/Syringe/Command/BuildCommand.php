<?php

namespace Syringe\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Syringe\Component\Builder\Builder;
use Syringe\Component\Builder\ServiceVisitor\InvalidConfigurationException;
use Syringe\Component\Parser\IParser;

class BuildCommand extends Command
{
    /**
     * @var Builder
     */
    protected $builder;

    /**
     * @var IParser
     */
    protected $parser;

    /**
     * @param Builder $builder
     * @param IParser $parser
     */
    public function __construct(Builder $builder, IParser $parser)
    {
        parent::__construct();

        $this->builder = $builder;
        $this->parser  = $parser;
    }

    protected function configure()
    {
        $this
            ->setName('syringe:build')
            ->setDescription('Build configuration')
            ->addArgument('configlist', InputArgument::REQUIRED, 'Php file which returns a list of configuration files')
            ->addArgument('outputfile', InputArgument::REQUIRED, 'Result configuration file')
            ->setHelp(<<<EOT
<info>php srbuilder.phar configlist.php ioc_configuration.php</info>
<comment>
Example a configlist.php:</comment>
<info><?php
return array(
    'app/config/config.php',
    'config-local.yaml',
);
</info>
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configListFile  = $input->getArgument('configlist');
        $ouputConfigFile = $input->getArgument('outputfile');;

        if (!is_readable($configListFile)) {
            throw new \InvalidArgumentException(sprintf("Config list file '%s' is not readable", $configListFile));
        }

        if (!$this->isWritable($ouputConfigFile)) {
            throw new \InvalidArgumentException(sprintf("Output configuration file '%s' is not writable", $ouputConfigFile));
        }

        $configList = require $configListFile;

        if (empty($configList)) {
            throw new \InvalidArgumentException("Config list file is empty");
        }

        foreach ($configList as $configFile) {
            if (!is_readable($configFile)) {
                continue;
            }

            $this->builder->addConfiguration($this->parser->parse($configFile));
        }

        try {
            $this->builder->build();
        } catch (InvalidConfigurationException $e) {
            $output->writeln(sprintf("<error>%s</error>"), $e->getMessage());
            foreach ($e->getErrors() as $error) {
                $output->writeln(sprintf("<error>%s</error>"), $error->getMessage());
            }
            exit(1);
        }

        file_put_contents($ouputConfigFile, sprintf("<?php return %s;", var_export($this->builder->build(), true)));
        $output->writeln("<comment>Done.</comment>");
    }

    /**
     * @param string $filePath
     * @return bool
     */
    protected function isWritable($filePath)
    {
        $checkingObject = file_exists($filePath) ? $filePath : dirname($filePath);

        return is_writable($checkingObject);
    }
}
