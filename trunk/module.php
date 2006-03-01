<?php
class Module {
	var $path;
	var $name;

	function Module($path) {
		$this->path = $path;
		$this->name = preg_replace('/([.a-zA-Z0-9]+\/)*/', '', $path); // creates name from path (FIXME check the regex)
	}

	/**
	 * Searches a given folder for module packages. TODO: sort after priority
	 * @param string path to search
	 */
	function search($dir = 'modules') {
		echo "Looking in $dir...\n";
		foreach (glob("$dir/*", GLOB_ONLYDIR) as $fn) { // glob depends upon PHP >= 4.3.0 (4.3.3 for GLOB_ONLYDIR in Win32)
			if (file_exists($fn.'/module.xml')) {
				// good to go (TODO: in the future I may want to parse this file and follow instructions in it: load priority, pipe output through function, etc.)
				echo "Found $fn.\n";
				// FIXME: THIS SHOULDN'T BE IN THE SEARCH FUNCTION! It should be one function for searching (building collection) and one for activating all.
				$m = new Module($fn);
				echo "Activating $fn...\n";
				$m->activate(); // probably a bulldozer sized security hole =)
				echo "Done.\n\n";
			} // end if (else: ignore the folder)
		} // end foreach
	} // end search()

	/**
	 * Activates a module
	 */
	function activate() {
		foreach (glob($this->path.'/includes/*.php') as $inc) {
			echo " - $inc\n";
			require_once ($inc); // probably a bulldozer sized security hole =)
		}
		if (function_exists($this->name.'_activate')) {
			echo "Running activation script:\n";
			call_user_func($this->name.'_activate'); // one of the files contains a function named "ModuleName"_activate. this runs that function (ensuring that it's run last)
		}
	} // end activate()

} // end Module
?>