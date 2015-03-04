<?PHP

$file= $_GET['file'];
$fullpage=file_get_contents($file);
echo $fullpage;

?>