<?php

namespace Syringe\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Syringe\Component\Builder\Builder;
use Syringe\Component\Builder\ServiceVisitor\InvalidConfigurationException;
use Syringe\Component\Adapter\IAdapter;
use Syringe\Component\Parser\IParser;

class BuildCommand extends Command
{
    /**
     * @var Builder
     */
    protected $builder;

    /**
     * @param Builder $builder
     */
    public function __construct(Builder $builder)
    {
        parent::__construct();

        $this->builder = $builder;
    }

    protected function configure()
    {
        $this
            ->setName('syringe:build')
            ->setDescription('Build configuration')
            ->addArgument('adapter', InputArgument::REQUIRED, 'Php file which returns a list of configuration files')
            ->addArgument('outputfile', InputArgument::REQUIRED, 'Result configuration file')
            ->setHelp(<<<EOT
<info>php srbuilder.phar adapter.php ioc_configuration.php</info>
<comment>
Example a adapter.php:</comment>
<info><?php
return new SimpleAdapter(array(
    'app/config/config.php',
    'config-local.yml',
));
</info>
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $adapterPath     = $input->getArgument('adapter');
        $ouputConfigFile = $input->getArgument('outputfile');;

        if (!is_readable($adapterPath)) {
            throw new \InvalidArgumentException(sprintf("Config list file '%s' is not readable", $adapterPath));
        }

        if (!$this->isWritable($ouputConfigFile)) {
            throw new \InvalidArgumentException(sprintf("Output configuration file '%s' is not writable", $ouputConfigFile));
        }

        /** @var IAdapter $adapter */
        $adapter = require $adapterPath;

        foreach ($adapter->getConfigurationsList() as $configuration) {
            $this->builder->addConfiguration($configuration);
        }

        try {
            $this->builder->build();
        } catch (InvalidConfigurationException $e) {
            $output->writeln(sprintf("<error>%s</error>", $e->getMessage()));
            foreach ($e->getErrors() as $error) {
                $output->writeln(sprintf("<error>%s</error>", $error->getMessage()));
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
