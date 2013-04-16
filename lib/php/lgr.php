<?php
# Copyright 2013 Mike Thorn (github: WasabiVengeance). All rights reserved.
# Use of this source code is governed by a BSD-style
# license that can be found in the LICENSE file.

global $__lgr;
$__lgr = array(
	'default'=>'',
	'handles'=>array(),
	'has_writes'=>array(),
	'header'=>'--------------------------------------',
	'footer'=>'--------------------------------------',
);

class lgr
{
	function init($config)
	{
		global $__lgr;
		
			
		foreach($config as $key=>$value)
		{
			if($key == 'logs')
			{
				foreach($config['logs'] as $type=>$filename)
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
			else
				$__dfm[$key] = $value;		
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
		
		# write a line to the log file if it hasn't been written to before
		if(!isset($__lgr['has_writes'][$type]))
		{
			$__lgr['has_writes'][$type] = true;
			fwrite($__lgr['handles'][$type],$__lgr['header']."\n");
		}
		
		fwrite($__lgr['handles'][$type],date('H:i:s',time()).': '.$to_write."\n");
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
			
			# write a line to the log file if it hasn't been written to before
			if(isset($__lgr['has_writes'][$type]))
			{
				fwrite($__lgr['handles'][$type],$__lgr['footer']."\n");
			}
			
			fclose($__lgr['handles'][$type]);
		}
	}
}

?>