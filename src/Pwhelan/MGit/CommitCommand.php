<?php

namespace Pwhelan\MGit;

use GitWrapper\GitWrapper;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CommitCommand extends Command
{
	protected function configure()
	{
		$this
			->setName("commit")
			->setDescription("wrap commit to save latest revision")
			->addOption(
				'interactive',
				NULL,
				InputOption::VALUE_NONE,
				'Interactive Mode'
			)
			->addOption(
				'all',
				'a',
				InputOption::VALUE_NONE,
				'Tell the command to automatically stage files that have been '.
				'modified and deleted, but new files you have not told Git about are '.
				'not affected.'
			)
			->addOption(
				'patch',
				'p',
				InputOption::VALUE_NONE,
				'Use the interactive patch selection interface to chose which changes to commit. See git-add(1) for details.'
			)
			->addOption(
				'signoff',
				's',
				InputOption::VALUE_NONE,
				'Add Signed-off-by line by the committer at the end of the commit log message'
			)
			->addOption(
				'quiet',
				'q',
				InputOption::VALUE_NONE,
				'Add Signed-off-by line by the committer at the end of the commit log message'
			)
			->addOption(
				'amend',
				NULL,
				InputOption::VALUE_NONE,
				'Add Signed-off-by line by the committer at the end of the commit log message'
			)
			->addOption(
				'dry-run',
				NULL,
				InputOption::VALUE_NONE,
				'Dry Run....'
			)
			->addOption(
				'reedit-message',
				'c',
				InputOption::VALUE_REQUIRED,
				''
			)
			->addOption(
				'reuse-message',
				'C',
				InputOption::VALUE_REQUIRED,
				'Like -C, but with -c the editor is invoked, so that the user can '.
				'further edit the commit message.'
			)
			->addOption(
				'file',
				'F',
				InputOption::VALUE_NONE,
				''
			)
			->addOption(
				'message',
				'm',
				InputOption::VALUE_REQUIRED,
				''
			)
			->addOption(
				'reset-author',
				NULL,
				InputOption::VALUE_REQUIRED,
				'Rest author'
			)
			->addOption(
				'allow-empty',
				NULL,
				InputOption::VALUE_NONE,
				'Allow empty'
			)
			->addOption(
				'allow-empty-message',
				NULL,
				InputOption::VALUE_NONE,
				'Allow empty'
			)
			->addOption(
				'no-verify',
				NULL,
				InputOption::VALUE_NONE,
				'Add Signed-off-by line by the committer at the end of the commit log message'
			)
			->addOption(
				'author',
				NULL,
				InputOption::VALUE_REQUIRED,
				'Author'
			)
			->addOption(
				'date',
				NULL,
				InputOption::VALUE_REQUIRED,
				'Date'
			)
			->addOption(
				'cleanup',
				NULL,
				InputOption::VALUE_REQUIRED,
				'Cleanup mode'
			)
			->addOption(
				'status',
				NULL,
				InputOption::VALUE_NONE,
				'Status'
			)
			->addOption(
				'no-status',
				NULL,
				InputOption::VALUE_NONE,
				'No Status'
			)
			->addOption(
				'include',
				'i',
				InputOption::VALUE_NONE,
				'No Status'
			)
			->addOption(
				'only',
				'o',
				InputOption::VALUE_NONE,
				'No Status'
			)
			->addOption(
				'gpg-sign',
				'S',
				InputOption::VALUE_REQUIRED,
				'Set Sigkey?'
			)
			
			/*
			->addOption(
				'verbose',
				'v',
				InputOption::VALUE_NONE,
				'Show unified diff between the HEAD commit and what would be '.
				'committed at the bottom of the commit message template. Note that '.
				'this diff output doesn’t have its lines prefixed with #'
			)
			*/
			->addArgument(
				'files',
				InputArgument::IS_ARRAY,
				'Who do you want to greet (separate multiple names with a space)?'
			);
	}
	
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$cfg = Main::getConfiguration();
		$rootDir = Main::getCfgDir();
		$curDir = getcwd();
		if ($rootDir == $curDir) {
			$curDir = './';
		}
		$path = $curDir;
		
		
		$argv = [];
		
		foreach($input->getOptions() as $name => $value) {
			$option = $input->getOption($name);
			if ($option) {
				$argv[] = "--{$name}";
				if ($option != 1)
					$argv[] = $option;
			}
		}
		
		foreach($input->getArguments()['files'] as $file) {
			$argv[] = $file;
		}
		
		$wrapper = new GitWrapper();
		$git = $wrapper->workingCopy($path);
		
		
		call_user_func_array([$git, 'commit'], $argv);
		$result = $git->getOutput();
		$git->clearOutput();
		
		$git->run(['rev-parse', 'HEAD']);
		$commit = trim($git->getOutput());
		
		$cfg['paths'][$path]['commit'] = $commit;
		Main::setConfiguration($cfg);
	}
}
