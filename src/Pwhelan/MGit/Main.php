<?php

namespace Pwhelan\MGit;

require_once __DIR__.'/../../../vendor/autoload.php';
require_once __DIR__.'/AttachCommand.php';

use GitWrapper\GitWrapper;
use Symfony\Component\Console\Application;

use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Yaml\Yaml;

class Main
{
	private static $_instantiated = FALSE;
	private static $_configuration;
	private static $_application;
	private static $_cfgdir = FALSE;
	
	private static function _instantiate()
	{
		if (self::$_instantiated) {
			return;
		}
		
		self::$_application = new Application();
		
		self::$_application->add(new AttachCommand);
		self::$_application->add(new PopulateCommand);
		self::$_application->add(new CloneCommand);
		self::$_application->add(new InitCommand);
		self::$_application->add(new PullCommand);
		self::$_application->add(new CommitCommand);
		self::$_application->add(new GenerateCommand);
	}
	
	public static function getCfgDir()
	{
		if (self::$_cfgdir) {
			return self::$_cfgdir;
		}
		
		for ($dir = getcwd(); !is_dir($dir.'/.mgit') && $dir != '/'; $dir = $pdir) {
			$pdir = dirname($dir);
			if ($pdir == $dir) {
				die("ROOT DIR");
			}
		}
		
		self::$_cfgdir = $dir;
		return $dir;
	}
	
	public static function getConfiguration()
	{
		if (self::$_configuration == NULL) {
			$dir = self::getCfgDir();
			
			$yaml = new Parser;
			if (is_file($dir.'/.mgit/mgit.yml')) {
				self::$_configuration = $yaml->parse(file_get_contents($dir.'/.mgit/mgit.yml'));
			}
		}
		
		return self::$_configuration;
	}
	
	public static function setConfiguration($cfg)
	{
		self::$_configuration = $cfg;
		$dir = self::getCfgDir();
		
		$yaml = Yaml::dump($cfg, 4);
		file_put_contents($dir.'/.mgit/mgit.yml', $yaml);
	}
	
	public static function git($path = "./")
	{
		$wrapper = new GitWrapper();
		$git = $wrapper->workingCopy(Main::getCfgDir().'/'.$path);
		
		return $git;
	}
	
	public function __construct()
	{
		self::_instantiate();
	}
	
	public function run()
	{
		self::$_application->run();
	}
}
