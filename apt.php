<?php



class APT
{


	function work() {
		if ($this->ssh) {
			$this->execute_ssh();
		} else {
			// local execution with sudo
			$this->cmd = 'sudo '.$this->cmd;
			$this->execute_local();
		}
	}


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



	function depends() {
		$this->cmd = 'apt-cache depends '.$this->pkg ;
		$this->work();
		array_shift($this->res);
		$res = $this->res;
		$info = array();
		foreach ($res as $line) {
			if (substr(trim($line),0,7) == 'Depends') {
				$info[] = $line;
			} else {
				// needed?
			}
		}
		$this->out = $info;
	}


	function policy() {
		$this->cmd = 'apt-cache policy '.$this->pkg ;
		$this->work();

	}

	function execute_local() {
		$output = array();
		$res = exec( $this->cmd, &$output, &$return );
		$this->res = $output;
		//print_r( $this->res );
	}


	function execute_ssh() {
		$dlog = fopen('/tmp/phplog','a');
		$this->res = 'NO DATA';
		//$pass = file_get_contents('.secret');
		$pass = trim(file_get_contents('/var/www/tests/buh/sites/all/modules/apt_api/includes/php-apt-api/.secret'));
		if (!function_exists("ssh2_connect")) die("function ssh2_connect doesn't exist");
		// log in at server1.example.com on port 22
		if(!($con = ssh2_connect($this->host, 22))){
				fwrite($dlog, "fail: unable to establish connection\n");
		} else {
				// try to authenticate with username root, password secretpassword
				if(!ssh2_auth_password($con, "root", $pass)) {
						fwrite($dlog, "fail: unable to authenticate\n");
				} else {
						// allright, we're in!
						fwrite($dlog, "okay: logged in...\n" );

						// execute a command
						if (!($stream = ssh2_exec($con, $this->cmd ))) {
								fwrite($dlog, "fail: unable to execute command\n");
						} else {
								// collect returning data from command
								stream_set_blocking($stream, true);
								$data = "";
								while ($buf = fread($stream,4096)) {
										$data .= $buf;
								}
								fclose($stream);
								//fwrite($dlog, $data);
								$this->res = explode('\n',$data);
						}
				}
		}
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
