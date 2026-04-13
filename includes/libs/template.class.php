<?php
if (!defined('IN_CRONLITE')) exit();
class Template
{

	static public function getList()
	{
		$dir = TEMPLATE_ROOT;
		$dirArray[] = NULL;
		if (false != ($handle = opendir($dir))) {
			$i = 0;
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != ".." && !strpos($file, ".")) {
					$dirArray[$i] = $file;
					$i++;
				}
			}
			closedir($handle);
		}
		return $dirArray;
	}

	static public function getList2()
	{
		$dir = TZ_TEMPLATE_ROOT;
		$dirArray[] = NULL;
		if (false != ($handle = opendir($dir))) {
			$i = 0;
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != ".." && !strpos($file, ".")) {
					$dirArray[$i] = $file;
					$i++;
				}
			}
			closedir($handle);
		}
		return $dirArray;
	}

	static public function load($name = 'index')
	{
		global $conf;
		$template = $conf['template'] ? $conf['template'] : 'default';
		if (!preg_match('/^[a-zA-Z0-9]+$/', $name)) exit('error');
		$filename = TEMPLATE_ROOT . $template . '/' . $name . '.php';
		$filename_default = TEMPLATE_ROOT . 'default/' . $name . '.php';
		if (file_exists($filename)) {
			return $filename;
		} elseif (file_exists($filename_default)) {
			return $filename_default;
		} else {
			exit('Template file not found');
		}
	}

	static public function load2($template='jump1',$name = 'index')
	{
		if (!preg_match('/^[a-zA-Z0-9]+$/', $name)) exit('error');
		$filename = TZ_TEMPLATE_ROOT . $template . '/' . $name . '.php';
		$filename_default = TZ_TEMPLATE_ROOT . 'jump1/' . $name . '.php';
		if (file_exists($filename)) {
			return $filename;
		} elseif (file_exists($filename_default)) {
			return $filename_default;
		} else {
			exit('Template file not found');
		}
	}

	static public function exists($template)
	{
		$filename = TEMPLATE_ROOT . $template . '/index.php';
		if (file_exists($filename)) {
			return true;
		} else {
			return false;
		}
	}

	static public function exists2($template)
	{
		$filename = TZ_TEMPLATE_ROOT . $template . '/index.php';
		if (file_exists($filename)) {
			return true;
		} else {
			return false;
		}
	}
}
