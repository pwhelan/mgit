<?php

namespace Pwhelan\MGit;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


/* TODO:
 *    * support path as an optional parameter
 */
class CloneCommand extends Command
{
	protected function configure()
	{
		$this
			->setName("clone")
			->setDescription("Clone the entire repository with children")
			->addArgument(
				'uri',
				InputArgument::REQUIRED,
				'URI to remote repository'
			)
			->addArgument(
				'path',
				InputArgument::REQUIRED,
				'Path to clone into, usually the basename of the URI'
			);
	}
	
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$uri	= $input->getArgument('uri');
		$path	= $input->getArgument('path');
		
		$git = Main::git();
	}
}
