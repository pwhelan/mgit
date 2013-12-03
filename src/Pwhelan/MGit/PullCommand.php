<?php

namespace Pwhelan\MGit;

use GitWrapper\GitWrapper;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PullCommand extends Command
{
	protected function configure()
	{
		$this
			->setName("pull")
			->setDescription("pull repositories")
			->addOption(
				'force',
				'F',
				InputOption::VALUE_NONE,
				'Force all subdirectories to pull'
			);
	}
	
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$force	= $input->getOption('force');
		$cfg	= Main::getConfiguration();
		$cwd	= getcwd();
		
		foreach(array_keys($cfg['paths']) as $path) {
			if (Main::getCfgDir().'/'.$path == $cwd) {
				$cwd = $path;
				$dcfg = $cfg['paths'][$path];
				
				$git = Main::git($path);
				$git->pull($dcfg['remote'], $dcfg['branch']);
			}
		}
		
		
		$output->writeLn("Pulling master repository");
		
		if ($cwd != ".") {
			$git = Main::git();
			$git->pull($cfg['paths']['.']['remote'], $cfg['paths']['.']['branch']);
		}
		
		
		$output->writeLn("Checking child directories");
		
		foreach($cfg['paths'] as $path => $pcfg) {
			
			if ($path == '.' || $path == $cwd) {
				continue;
			}
			
			
			$git = Main::git($path);
			if (!is_dir($path)) {
				mkdir($path);
				$commit = null;
				$git->pull($pcfg['remote'], $pcfg['branch']);
			}
			else {
				$commit = trim($git
					->run(['rev-parse', 'HEAD'])
					->getOutput());
			}
			
			$git->clearOutput();
			
			if ($commit != $pcfg['commit'] || $force) {
				print "[{$path}]=> Pulling {$pcfg['remote']} -> {$pcfg['branch']}\n";
				$git->pull($pcfg['remote'], $pcfg['branch']);
				print "[{$path}] ".$git->getOutput();
			}
		}
	}
}
