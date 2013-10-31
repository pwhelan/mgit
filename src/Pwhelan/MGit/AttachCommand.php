<?php

namespace Pwhelan\MGit;

use GitWrapper\GitWrapper;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AttachCommand extends Command
{
	protected function configure()
	{
		$this
			->setName("attach")
			->setDescription("attach a remote repository")
			->addArgument(
				'path',
				InputArgument::REQUIRED,
				'Path to attach the repository to'
			)
			->addArgument(
				'uri',
				InputArgument::REQUIRED,
				'URI to remote repository'
			)
			->addArgument(
				'branch',
				InputArgument::OPTIONAL,
				'Branch to attach to'
			);
	}
	
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$path	= $input->getArgument('path');
		$uri	= $input->getArgument('uri');
		$branch	= $input->getArgument('branch');
		
		if (!$branch) {
			$branch = "master";
		}
		
		
		$wrapper = new GitWrapper();
		
		$git = $wrapper->workingCopy($path);
		$git->clone($uri);
		
		$cfg = Main::getConfiguration();
		
		
		if (!isset($cfg['paths'])) {
			$cfg['paths'] = [];
		}
		
		$commit = trim($git->clearOutput()
			->run(['rev-parse', 'HEAD'])
			->getOutput());
		
		$cfg['paths'][$path] = [
			'branch'	=> $branch,
			'commit'	=> $commit,
			'remote'	=> 'origin',
			'uri'		=> $uri
		];
		
		Main::setConfiguration($cfg);
	}
}
