<?php
/**
 * Created by PhpStorm.
 * User: mmarum
 * Date: 12/29/15
 * Time: 6:12 PM
 */

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
            ->setDescription('Launch a Vagrant box for running your Sugar instance.  Vagrant and Virtualbox must be installed first.')
            ->addArgument('box', InputArgument::REQUIRED, 'Name of Vagrant Box?')
            ->addOption('skip-install', InputOption::VALUE_NONE, 'Skips automatic install')
        ;
    }

    protected function interact(InputInterface $input, OutputInterface $output ){
        $box = $input->getArgument('box');
        if (empty($box)) {
            $output->writeln("<info>You must provide a Vagrant Box Name for your Sugar instance.</info>");
            $output->writeln("<comment>You may pick one from below OR provide your own.</comment>");
            $table = new Table($output);
            $table->setHeaders(array('Box Name', 'PHP', 'Apache', 'MySQL', 'Elasticsearch', 'OS'));
            $table->addRow(['mmarum/sugar7-php54', '5.4.x', '2.2.x', '5.5.x', '1.4.x', 'Ubuntu 12.04']);
            $table->addRow(['mmarum/sugar7-php53', '5.3.x', '2.2.x', '5.5.x', '1.4.x', 'Ubuntu 12.04']);
            $table->render();
            $helper = $this->getHelper('question');
            $question = new Question('<question>Name of Vagrant Box:</question> ');
            $question->setValidator(function($answer){
                if (empty($answer)) {
                    throw new \RuntimeException(
                        'You must provide a Vagrant Box name to continue!'
                    );
                }
            });
            $question->setMaxAttempts(2);
            $box = $helper->ask($input, $output, $question);

            $input->setArgument('box', $box);
        }
    }

    /**
     * {inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("RUN WITH: " . $input->getArgument('box'));

    }
}
