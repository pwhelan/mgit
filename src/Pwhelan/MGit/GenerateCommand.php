<?php

namespace Pwhelan\MGit;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

		
class GenerateCommand extends Command
{
	protected function configure()
	{
		$this
			->setName("generate")
			->setDescription("Generate the initial control files for mgit")
			->addArgument(
				'path',
				InputArgument::OPTIONAL,
				'Path to clone into, usually the basename of the URI'
			);
	}
	
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$path	= $input->getArgument('path');
		if (!$path) {
			$path = "./";
		}
		
		$gits = [];
		$iterator = new \RecursiveIteratorIterator(
					new \RecursiveDirectoryIterator(Main::getCfgDir()), 
					\RecursiveIteratorIterator::SELF_FIRST 
				);
		
		foreach ( $iterator as $path ) {
			if ($path->isDir() && $path->getFilename() == '.git') {
				$gits[] = dirname($iterator->getSubPathname());
			}
		}
		
		$config = ['paths' => []];
		foreach($gits as $path) {
			
			$git = Main::git($path);
			$commit = trim($git
					->run(['rev-parse', 'HEAD'])
					->getOutput());
			
			$branch = trim($git
					->clearOutput()
					->run(['rev-parse', '--abbrev-ref', 'HEAD'])
					->getOutput());
			
			$remotes = explode("\n", trim($git
					->clearOutput()
					->run(['remote'])
					->getOutput()
				));
			
			$uri = trim($git
					->clearOutput()
					->run(['config', '--get', "remote.{$remotes[0]}.url"])
					->getOutput()
				);
			
			if (in_array('composer', $remotes)) {
				continue;
			}
			
			$config['paths'][$path] = [
				'commit'=> $commit,
				'branch'=> $branch,
				'remote'=> $remotes[0],
				'uri'	=> $uri
			];
		}
		
		Main::setConfiguration($config);
	}
}
