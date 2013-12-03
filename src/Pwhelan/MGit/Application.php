<?php

namespace Pwhelan\MGit;

use Symfony\Component\Console;
use Symfony\Component\Console\Input; 
use Symfony\Component\Console\Output;

class UnknownCommandException extends \Exception
{
}

class Application extends Console\Application
{
	public function find($name)
	{
		$this->setCatchExceptions(false);
		try {
			return parent::find($name);
		}
		catch (\InvalidArgumentException $ie) {
			throw new UnknownCommandException;
		}
		$this->setCatchExceptions(true);
	}
	
	public function run(Input\InputInterface $input = null, Output\OutputInterface $output = null)
	{
		if (null === $input) {
			$input = new Input\ArgvInput();
		}
		
		if (null === $output) {
			$output = new Output\ConsoleOutput();
		}
		
		$this->configureIO($input, $output);
		
		
		try {
			parent::run($input, $output);
		}
		catch (UnknownCommandException $uce) {
			throw $uce;
		}
		catch (\Exception $e) {
			if ($output instanceof Output\ConsoleOuputInterface) {
				$this->renderException($e, $output->getErrorOutput());
			}
			else {
				$this->renderException($e, $output);
			}
			
			$exitCode = $e->getCode();
			if (is_numeric($exitCode)) {
				$exitCode = (int)$exitCode;
				if (0 === $exitCode) {
					$exitCode = 1;
				}
			}
			else {
				$exitCode = 1;
			}
		}
		
		return $exitCode;
	}
	
}
