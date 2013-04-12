<?php
# Copyright 2013 Mike Thorn (github: WasabiVengeance). All rights reserved.
# Use of this source code is governed by a BSD-style
# license that can be found in the LICENSE file.

global $__lgr;
$__lgr = array(
	'default'=>'',
	'handles'=>array()
);

class lgr
{
	function init($config)
	{
		global $__lgr;
		foreach($config as $type=>$filename)
		{
			if($__lgr['default'] == '')
				$__lgr['default'] = $type;
			if(file_exists($filename))
			{
				$__lgr['handles'][$type] = fopen($filename,'a');
			}
			else
			{
				throw new Exception('LGR: Could not open log file: '.$filename);
			}
		}
	}
	
	function write($to_write,$type = null)
	{
		global $__lgr;
		
		# write to the default log if one is not specified.
		$type = (is_null($type))?$__lgr['default']:$type;
		
		# if an object||array is passed, print_r it
		if(is_object($to_write) || is_array($to_write))
			$to_write = print_r($to_write,true);
			
		# throw an exception if this log isn't openeed
		if(!isset($__lgr['handles'][$type]))
		{
			throw new Exception('LGR: invalid log type: '.$type);
		}
		
		fwrite($__lgr['handles'][$type],$to_write."\n");
	}
	
	function request()
	{
		lgr::write($_REQUEST);
	}
	
	function server()
	{
		lgr::write($_SERVER);
	}
	
	function deinit()
	{
		global $__lgr;
		foreach($__lgr['handles'] as $type=>$handle)
		{
			if(!isset($__lgr['handles'][$type]))
			{
				throw new Exception('LGR: Could not close type: '.$type);
			}
			fclose($__lgr['handles'][$type]);
		}
	}
}

?>