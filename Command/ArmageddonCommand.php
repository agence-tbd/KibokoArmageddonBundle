<?php
/**
 * Created by Kiboko.
 * User: @Brain_out <hello@kiboko.fr>
 * Date: 30/04/18
 *
 * Kiboko is a consulting and development agency for e-commerce and business solutions,
 * created by the reunion of 3 e-commerce seasoned developers, working on various scale of e-commerce websites.
 * Depending on your business needs, Kiboko develops and maintains e-commerce web stores using Magento
 * and OroCommerce. Kiboko also integrates Akeneo (PIM), OroCRM (CRM) and Marello (ERP) into pre-existing
 * environement or into a new one to build as your business needs.
 * Kiboko has been one of the first companies to trust OroCommerce
 * as a true B2B e-commerce solution and one of the first to release live a web store using it.
 */

namespace Kiboko\Bundle\ArmageddonBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\Process;

/**
 * Class ArmageddonCommand
 * @package Kiboko\Bundle\ArmageddonBundle\Command
 */
class ArmageddonCommand extends ContainerAwareCommand

{

    protected function configure()
    {
        $this
            ->setName('kiboko:armageddon')
            ->setDescription('Flush everything and recreates everything')
            ->addOption('bruce', '--bruce', InputOption::VALUE_NONE, 'Bruce will perform Armageddon')
            ->setHelp('This command clear cache, vendor, web directory and recreate everything');

        parent::configure();

    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $bruce = $input->getOption('bruce');
        $output->writeln([
            'Armageddon.......',
            '=================',
            '',
        ]);

        if (!$bruce) {
            $output->writeln('<comment>Armageddon can\'t be played without --bruce rerun the command to perform armageddon<comment>');
            return 0;
        }

        $output->writeln('Fire!');

        $rootDir = realpath(__DIR__ . '/../../../../../') . '/';
        $env = !$input->getOption('env') ? 'dev' : $input->getOption('env');

        $processExec = new Process('rm -rf ' . $rootDir . 'vendor ' . $rootDir . 'app/cache/*');
        $output->writeln('<comment>Running `rm -rf vendor app/cache/*`<comment>');
        $processExec->run();
        echo $processExec->getOutput();

        $processExec = new Process('composer install --no-dev --optimize-autoloader');
        $output->writeln('<comment>Running `composer install`<comment>');
        $processExec->run();

        foreach ($this->getProcesses() as $process) {
            $processExec = new Process($rootDir . 'app/console ' . $process . ' --env=' . $env);
            $output->writeln('<comment>Running app/console ' . $process . ' --env=' . $env . '</comment>');
            $processExec->setTimeout(0)->run();
            echo $processExec->getOutput();
        }
    }

    private function getProcesses()
    {
        return [
            'cache:warmup',
            'oro:asset:install',
            'oro:requirejs:build',
            'assetic:dump',
            'oro:translation:dump',
            'oro:localization:dump',
            'fos:js-routing:dump'
        ];
    }
}