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
			->setDescription("pull all the repositories");
	}
	
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$cfg = Main::getConfiguration();
		foreach($cfg['paths'] as $path => $pcfg) {
			
			$wrapper = new GitWrapper();
			
			$git = $wrapper->workingCopy(Main::getCfgDir().'/'.$path);
			$commit = trim($git
					->run(['rev-parse', 'HEAD'])
					->getOutput());
			
			if ($commit != $pcfg['commit']) {
				print "[{$path}]=> Pulling {$pcfg['remote']} -> {$pcfg['branch']}\n";
				$git->pull($pcfg['remote'], $pcfg['branch']);
				print "[{$path}] ".$git->getOutput();
			}
		}
	}
}
