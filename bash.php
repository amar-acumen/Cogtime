<?php error_reporting(-1);
//echo '1';
 $output = shell_exec('git pull 2>&1');
echo "<pre>$output</pre>";
//var_dump(shell_exec('node nodejs/server.js 2>&1'));?>