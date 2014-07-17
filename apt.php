<?php



class APT
{


	function available() {
		$this->policy();
		$versions = array_splice(&$this->res, 3);
		//print_r($versions);
		array_shift($versions);
		//$pat = '\d+\.\d+.*\-.*';
		$pat = "/^([\d|\.]+)\-(.*)/";
		$pat_repo = "/\w*(\d\d\d) (http.*|\/var.*)/";

		foreach ($versions as $line) {
			//echo "X".$line."X \n";
			$line_tr = trim($line);
			//echo $line_tr."\n";

			if (preg_match($pat_repo, $line)) {
				//echo "repo!\n";
				continue;
			}
			//echo substr(trim($line),0,3)."\n"; // == '***' ) {

			if (substr($line_tr,0,3) == '***' ) {
				// installed
				//$line_tr = substr($line_tr, 3);
				//echo "DEFAULT\n";
			}
			$matches = array();
			if ( $res = preg_match( $pat, $line_tr, &$matches) ) {
				//echo "$line_tr \n";
				if ($matches ) {
					$this->result = array('a','b');
				}
				//print_r($matches);
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


//test($argv);
