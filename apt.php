<?php



class APT
{


	function available() {
		$this->policy();
		$versions = array_splice(&$this->res, 3);
		//print_r($versions);
		array_shift($versions);
		//$pat = '\d+\.\d+.*\-.*';
		$pat = "/(\d+)\-(.*)/";
		$pat_repo = "/\d\d\d http.*/";

		foreach ($versions as $line) {
			print($line);

			if (preg_split($pat_repo, $line)) {
				continue;
			}
			if (substr($line,0,3) == '***' ) {
				// installed
				$line = substr($line, 3);
			}

			if ( $res = preg_split( $pat, $line) ) {
				echo "$line ";

				//print_r($res);
			}
		

		}
	}

	function policy() {
		$this->cmd = 'sudo apt-cache policy '.$this->pkg ;
		$this->execute();
		//$res = proc_open($cmd);
	}

	function execute() {
		$output = array();
		$res = exec( $this->cmd, &$output, &$return );
		$this->res = $output;
		//print_r( $this->res );
	}

}


function test($a) {

	$apt = new APT();
	
	//$apt->pkg = 'python-django';
	$apt->pkg = $a[1];
	//$apt->policy();
	$apt->available();

}


test($argv);
