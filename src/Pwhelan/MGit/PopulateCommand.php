<?php

namespace Pwhelan\MGit;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PopulateCommand extends Command
{
	protected function configure()
	{
		$this
			->setName("populate")
			->setDescription("populate the repositories");
	}
	
	protected function execute(InputInterface $input, OutputInterface $output)
	{
	}
}
