<?php


namespace Sugarcrm\Sugarcrm\Console\Quickstart;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Question\Question;

class VagrantQuickstart extends Command
{
    /**
     * {inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('quickstart:vagrant')
            ->setDescription('Launch a Vagrant box for running your Sugar instance.')
            ->addArgument('box', InputArgument::REQUIRED, 'Name of Vagrant Box?')
            ->addOption('skip-install', null, InputOption::VALUE_NONE, 'Skips automatic silent install of Sugar')
        ;
    }

    /**
     * Check that pre-reqs for this Quick Start command are met.
     * Specifically, see if Vagrant is installed already.
     * @param OutputInterface $output
     */
    protected function checkQuickstartPrereqs(OutputInterface $output){
        $version = $this->callVagrant('--version', $output);
        if(empty($version) || !preg_match('/^Vagrant/', $version[0])){
            throw new \RuntimeException(
                "Cannot continue Quick Start until Vagrant is installed!\nDownload at http://www.vagrantup.com/"
            );
        }
    }

    /**
     * {inheritDoc}
     */
    protected function interact(InputInterface $input, OutputInterface $output){
        $box = $input->getArgument('box');
        if (empty($box)) {
            $output->writeln('You MUST provide a <info>Vagrant Box Name</info> for your Sugar instance.');
            $output->writeln('<comment>You may pick one from below OR provide your own.</comment>');
            $table = new Table($output);
            $table->setHeaders(array('Box Name', 'PHP', 'Apache', 'MySQL', 'Elasticsearch', 'OS'));
            $table->addRow(['mmarum/sugar7-php54', '5.4.x', '2.2.x', '5.5.x', '1.4.x', 'Ubuntu 12.04']);
            $table->addRow(['mmarum/sugar7-php53', '5.3.x', '2.2.x', '5.5.x', '1.4.x', 'Ubuntu 12.04']);
            $table->render();
            $helper = $this->getHelper('question');
            $question = new Question('<info>Name of Vagrant Box?</info> <comment>[mmarum/sugar7-php54]</comment> ', 'mmarum/sugar7-php54');
            $question->setValidator(function($answer){
                if (empty($answer)) {
                    throw new \RuntimeException(
                        'You must provide a Box Name to continue!'
                    );
                }
                return $answer;
            });
            $question->setMaxAttempts(2);
            $box = $helper->ask($input, $output, $question);
            $output->writeln("<info>Continuing with $box ...</info>");
            $input->setArgument('box', $box);
        }
    }

    /**
     * {inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->checkQuickstartPrereqs($output);

        $box = $input->getArgument('box');
        $skipSugarInstall = ($input->getOption('skip-install') == 1) ? true : false;
        $box = escapeshellarg($box);
        $this->callVagrant("init $box", $output);
//        $this->callVagrant("up", $output);
        $pwdOut = array();
        exec("pwd", $pwdOut);
        $output->writeln($pwdOut);
        $output->writeln('<fg=green;options=bold>FINISHED!  Sugar is ready at http://localhost:8080/sugar/</>');
    }

    /**
     * Utility to help run Vagrant commands and output to user's console
     *
     * @param string $command Command to pass Vagrant
     * @param OutputInterface $output Command output interface
     * @return array Raw Vagrant command output
     */
    protected function callVagrant($command, OutputInterface $output){
        $vagrantOutput = array();
        $output->writeln("<info>vagrant $command</info>");
        exec("vagrant $command", $vagrantOutput);
        //Write output from Vagrant command to console with some formatting added
        $output->writeln(array_map(
            function($str) {
                return "<info>[vagrant]</info> $str";
            },
            $vagrantOutput
        ));

        return $vagrantOutput;
    }
}
