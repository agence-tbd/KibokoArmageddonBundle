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
use Symfony\Component\Console\Style\SymfonyStyle;
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
            ->addOption('progress', '--p', InputOption::VALUE_NONE, 'show a progress bar')
            ->addOption('dry-run', '--D', InputOption::VALUE_NONE, 'Nothing will be deleted')
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
        $dryRun = $input->getOption('dry-run');
        $progressBar = $input->getOption('progress');


        $io = new SymfonyStyle($input, $output);
        $io->title('Armageddon utility to clean up assets and dependencies');
        if ($dryRun) {
            $io->note("Armageddon has been lauched in dry-run mode");
        }

        if (!$bruce) {
            $io->error("Armageddon can't be performed without bruce");
            $io->note("You need to add the '--bruce' option in order to perform Armageddon");
            return 0;
        }

        $validation = $io->confirm('Do you really want to run Armageddon ?');
        if($validation === false) {
            $io->note("Houston we have a problem, Armageddon is cancelled");
            return;
        } else {
            $io->note("We have visual of the target, Houston.");
            if($progressBar) {
                $io->progressStart(8);
            }

        }

        $rootDir = realpath(__DIR__ . '/../../../../../') . '/';
        $env = !$input->getOption('env') ? 'dev' : $input->getOption('env');

        $processExec = new Process('rm -rf ' . $rootDir . 'vendor ' . $rootDir . 'app/cache/*');
        $io->section('Running `rm -rf vendor app/cache/*`');
        if (!$dryRun) {
            $processExec->run();
            $io->text($processExec->getOutput());
            if($progressBar) {
                $io->progressAdvance(1);
            }
        }


        $processExec = new Process('composer install --optimize-autoloader');
        $io->section('Running `composer install`');
        if (!$dryRun) {
            $processExec->run();
            if($progressBar) {
                $io->progressAdvance(1);
            }
        }

        foreach ($this->getProcesses() as $process) {
            $processExec = new Process($rootDir . 'app/console ' . $process . ' --env=' . $env);
            $io->section('Running app/console ' . $process . ' --env=' . $env . '');
            if (!$dryRun) {
                $processExec->setTimeout(0)->run();
                $io->text($processExec->getOutput());
                if($progressBar) {
                    $io->progressAdvance(1);
                }
            }

        }

        if($progressBar) {
            $io->progressFinish();
        }
        $io->success('Look Like Armageddon has cleaned up all the mess....');
    }

    private function getProcesses()
    {
        return [
            'cache:warmup',
            'oro:asset:install',
            'assetic:dump',
            'oro:translation:dump',
            'oro:localization:dump',
            'fos:js-routing:dump'
        ];
    }
}