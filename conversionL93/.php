<?php
//echo "<pre>_SERVER="; print_r($_SERVER); echo "</pre>\n";
//echo "<pre>_POST="; print_r($_POST); echo "</pre>\n";
$input = (isset($_POST['input']) ? $_POST['input'] : '');
echo <<<EOT
<form method='post'><table border=1>
<tr><td><textarea name='input' rows=20 cols=130>$input</textarea></form></td></tr>
<tr><td><input type='submit'></td></tr>
</table></form>

EOT;

if ($input) {
  echo "<pre>$input</pre>";
}