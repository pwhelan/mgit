<?php

namespace Pwhelan\MGit;

use GitWrapper\GitWrapper;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class InitCommand extends Command
{
	protected function configure()
	{
		$this
			->setName("init")
			->setDescription("initialize a master repository")
			->addArgument(
				'path',
				InputArgument::OPTIONAL,
				'Path to create the master repository in'
			);
	}
	
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$path	= $input->getArgument('path');
		
		if (!$path) {
			$path = ".";
		}
		
		if (!is_dir($path)) {
			mkdir($path);
		}
		
		if (!is_dir($path."/.mgit")) {
			mkdir($path."/.mgit");
		}
		
		
		$wrapper = new GitWrapper();
		
		$git = $wrapper->workingCopy($path);
		$git->init();
	}
}
