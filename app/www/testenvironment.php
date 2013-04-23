<?
	$sapi_type = php_sapi_name();
	if (substr($sapi_type, 0, 3) == 'cli')
		$CLI = true;

	// Check POSIX
	if (!function_exists('posix_getpid')) 
		$err[] = "Cannot find posix_getpid function. Make sure that POSIX Functions enabled.";

	// Check PCNTL
	if ($CLI && !function_exists('pcntl_fork')) 
		$err[] = "Cannot find pcntl_fork function. Make sure that PCNTL Functions enabled.";
	
	// Check PECL_HTTP
	if (!class_exists('HTTPRequest')) 
		$err[] = "Cannot find PECL_HTTP functions. Make sure that PECL_HTTP Functions enabled.";
		
	// Check DOM
	if (!class_exists('DOMDocument')) 
		$err[] = "Cannot find DOM functions. Make sure that DOM Functions enabled.";
		
	// Check SimpleXML
	if (!function_exists('simplexml_load_string')) 
		$err[] = "Cannot find simplexml_load_string function. Make sure that SimpleXML Functions enabled.";
		
	// Check MySQLi
	if (!function_exists('mysqli_connect')) 
		$err[] = "Cannot find mysqli_connect function. Make sure that MYSQLi Functions enabled.";
	
	if (!function_exists('curl_init')) 
		$err[] = "Cannot find curl_init function. Make sure that CURL Functions enabled.";
	
	// Check GetText
	if (!function_exists('gettext')) 
		$err[] = "Cannot find gettext function. Make sure that GetText Functions enabled.";
		
	// Check MCrypt
	if (!function_exists('mcrypt_encrypt')) 
		$err[] = "Cannot find mcrypt_encrypt function. Make sure that mCrypt Functions enabled.";

	// Check MHash
	if (!function_exists('hash')) 
		$err[] = "Cannot find mhash function. Make sure that HASH Functions enabled.";
		
	if (!function_exists('rrd_create')) 
		$err[] = "Cannot find RRD functions. Make sure that RRDTool Extension (Verison 1.2.X) installed.";
		
	if (!function_exists('json_encode')) 
		$err[] = "Cannot find JSON functions. Make sure that JSON Functions enabled.";
		
	// Check OpenSSL
	if (!function_exists('openssl_verify')) 
		$err[] = "Cannot find OpenSSL functions. Make sure that OpenSSL Functions enabled.";	
		
	// Check SOAP
	if (!class_exists('SoapClient')) 
		$err[] = "Cannot find SoapClient class. Make sure that SoapClient Extension enabled.";

	// Check SNMP
	if (!function_exists('snmpget')) 
		$err[] = "Cannot find SNMP functions. Make sure that SNMP Functions enabled.";
		
	// Check SYSVMSG
	if (!function_exists('msg_get_queue')) 
		$err[] = "Cannot find SYSVMSG functions. Make sure that sysvmsg support enabled.";
	
		
	//
	// Check php sessings
	//
	if (ini_get('safe_mode') == 1)
		$err[] = "PHP safe mode enabled. Please disable it.";
		
	if (ini_get('register_gloabls') == 1)
		$err[] = "PHP register globals enabled. Please disable it.";
		
	if (str_replace(".", "", PHP_VERSION) < 5316)
		$err[] = "PHP version must be 5.3.16 or greater.";
	
	// If all extensions installed
	if (count($err) == 0)
	{
		if (!file_exists(dirname(__FILE__)."/../etc/.cryptokey") || filesize(dirname(__FILE__)."/../etc/.cryptokey") == 0) {
		    $key = file_get_contents('/dev/urandom', null, null, 0, 512);
            if (! $key)
                throw new Exception("Null key generated");

            $key = substr(base64_encode($key), 0, 512);
            $res = file_put_contents(dirname(__FILE__)."/../etc/.cryptokey", $key);
            if ($res == 0) {
                $err[] = "Unable to create etc/.cryptokey file. Please create empty etc/.cryptokey and chmod it to 0777.";
            }
		}
		
		// Parse config.ini and test database connection
		$cfg = @parse_ini_file(dirname(__FILE__)."/../etc/config.ini", true);
		if ($cfg)
		{
			$c = @mysqli_connect($cfg['db']['host'], $cfg['db']['user'], $cfg['db']['pass'], $cfg['db']['name']);
			if (!$c)
				$err[] = "Cannot connect to database using settings from etc/config.ini file";
		}
		else
			$err[] = "Cannot parse etc/config.ini file.";
			
		if (count($err) == 0)
			require_once (dirname(__FILE__).'/../src/prepend.inc.php');
	}
	
	if (!$CLI)
	{
		if (count($err) == 0)
			print "<span style='color:green'>Congratulations, your environment settings match Scalr requirements!</span>";
		else 
		{
			print "<span style='color:red'>Errors:</span><br>";
			foreach ($err as $e)
				print "<span style='color:red'>&bull; {$e}</span><br>";
		}
	}
	else
	{
		if (count($err) == 0)
			print "\033[32mCongratulations, your environment settings match Scalr requirements!\033[0m\n";
		else 
		{
			print "\033[31mErrors:\033[0m\n";
			foreach ($err as $e)
				print "\033[31m- {$e}\033[0m\n";
		}
	}
	
	//phpinfo();
?>