<!document html>
<html lang="en_us">
<head>
	<meta charset="utf-8">
	<title>File Browser</title>
</head>
<body>
<?php
$url=$_POST["path"];

function indexer($url){
	$rawdir=rawurldecode($url);
	#check for file or dir exist or not
	if (file_exists($rawdir) or is_file($rawdir))
	{ 	
	
		if(is_dir($rawdir))
		{
			
			$fd=opendir($rawdir);
			$file=scandir($rawdir);
			natcasesort($file);
			foreach($file as $f)
			{
				
				if(is_dir($rawdir."\\".$f))
				{     
					if(($f != '.') && ($f != '..'))
					{
						#call the function 
						indexer($rawdir."\\".$f);
					}	
				
				}
			}
             
			chdir($rawdir);
			#check for html and htm files and print word count in alphabetically order
			$filetypes='{*.html,*.htm}';
			$filearray=glob($filetypes , GLOB_BRACE);
			natcasesort($filearray);
			foreach($filearray as $filename){
				#echo "$filename <br>";
				wordcount($filename,$rawdir."//".$filename);
			}
			closedir($fd);
		}

	}
	else 
	{	#check url is exist or not
		$headers = @get_headers($url);
		if(strpos($headers[0],'200')===false){
			echo "url doesnt exist <br>";
		}
		else{
			#if url exist than its extension must be html or htm
			$arr=superExplode($url,"\./");
			if(end($arr)=== "html" ||end($arr)=== "htm" )
			{
				#echo"wordcount";
				wordcount($url,"");
			}
			else{
				echo "valid html or htm page not provided <br>";
			}
		
		}
	}  
}
function wordcount($url, $rawdir)
{
	$dirarray=array();
	#get the meta tags
	$tags=(get_meta_tags($url));
	$i=1; $x="";
	if($fp=fopen($url,"r"))
	{
		while($line=fgets($fp,1000))
		{
			$x.=$line;
			$i++;
		}
		fclose($fp);
	}
		
	else
	{
		echo "file could not be open";
	}
	#strip the meta tags & convert all the text in lower case
	$str=strip_tags($x);
	$Newstr=strtolower($str);
	$dirarray=array_count_values(superExplode($Newstr," \" \| \== \= \',!\n\t().:;"));
	ksort($dirarray);
	#echo "$url<br>"; 	
	foreach($dirarray as $key =>$value)
	{
		if(strlen($key)>1){

			#echo "$key =>$value <br>";
		}
	}   
	echo "file is save in the Database";
	 #if there is no meta tags in the script
	if(sizeof($tags)==0){
		#echo "<b>No META Information Found</b> <br>";
	}
	else{
		#echo "<b>META Information </b><br>";
		#foreach($tags as$x=>$x_value)
		{
			#echo "$x => $x_value<br>";
		}
	}
	if($rawdir!=""){
		$url=$rawdir;
		echo "$url<br>";
		db($url,$dirarray, $tags);
	}else{
		db($url,$dirarray, $tags);
	}	#echo "................................................................................<br>";
}

function superExplode($Newstr,$sep)
{
	$i=0;
	$arr[$i++]=strtok($Newstr,$sep);
	while($token=strtok($sep)){
	$arr[$i]= $token;
	$i++;
	}
	$ar = array_replace($arr,array_fill_keys(array_keys($arr, null),''));	
	return $ar;
}

// function for database conctivity
function db($filename, $word, $meta)
{	
	$con=mysqli_connect("localhost","root","","indexer");
	// Check connection
	if (mysqli_connect_errno())
	{
	 	echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}

	//select quary for select the file name or url
	$sql_query="select* from files where file_url='".$filename."'";
	$res=mysqli_query($con,$sql_query);
	if(mysqli_num_rows($res) > 0){

		echo "file already exist in the database<br>";
	}
	else{
		$file_id;
		//insert into query for set the values in the files table
		$sql_insert= "INSERT INTO files(file_name, file_url) VALUES ('".$filename."','".$filename."')";
		$retVal=mysqli_query($con,$sql_insert);
		if(! $retVal )
		{
	  		echo "problem with insert <br>";
		}

		//select query for to selcet file_id on the bases of file name
		$sql_id="select file_id from files where file_url='".$filename."'";
		$result_id=mysqli_query($con,$sql_id);
		if(mysqli_num_rows($result_id) > 0){
			$row=mysqli_fetch_array($result_id,MYSQLI_ASSOC);
			#printf ("%s\n",$row["file_id"]);
			$file_id=$row["file_id"];
		}
		//for each loop foor select all matching  words 
		foreach($word as $key=>$value){
			$sql_select="select * from words where word='".$key."'";
			$result=mysqli_query($con,$sql_select);
			if(mysqli_num_rows($result) > 0){
				$row=mysqli_fetch_array($result,MYSQLI_ASSOC);
				#printf ("%s\n",$row["word_id"]);
				$word_id=$row["word_id"];

				//check in file _word table whther this file_id and word _id exist  
				$sql_fw_select="select * from file_word where word_id='".$word_id."' and file_id='".$file_id."'";
				$result_fw=mysqli_query($con,$sql_fw_select);
				if(mysqli_num_rows($result_fw) > 0){
					continue;
				}	
				//if file_id,word_id  combination dosnt exist then insert into file_word table
				$sql_filewords= "INSERT INTO file_word(file_id, word_id,word_count) VALUES ('".$file_id."','".$word_id."','".$value."')";
				$fileword_result=mysqli_query($con,$sql_filewords);
				if(! $fileword_result)
				{
		  			echo "problem with fileword <br>";
				}
				mysqli_free_result($result_fw);
				mysqli_free_result($result);
				continue;
			}
			else{
				//insert that matching word into the words table
				$sql_ins= "INSERT INTO words(word) VALUES ('".$key."')";
				$ret=mysqli_query($con,$sql_ins);
				if(! $ret )
				{
	  				echo "problem with word <br>";
				}
				mysqli_free_result($result);

				// select all the word_id from the word table
				$sql_wordid="select word_id from words where word='".$key."'";
				$result_wordid=mysqli_query($con,$sql_wordid);
				$row=mysqli_fetch_array($result_wordid,MYSQLI_ASSOC);
				#printf ("%s\n",$row["word_id"]);
				$word_id=$row["word_id"];
				
				//insert file_id,word_id & word_count into the file_word table
				$sql_filewords= "INSERT INTO file_word(file_id, word_id,word_count) VALUES ('".$file_id."','".$word_id."','".$value."')";
				$fileword_result=mysqli_query($con,$sql_filewords);
				if(! $fileword_result)
				{
		  			echo "problem with fileword <br>";
				}
				
			}
		}

		foreach($meta as $key=>$value){
			//insert file_id type and contents into the meta_info table
			$sql_meta= "INSERT INTO meta_info(file_id, type,content) VALUES ('".$file_id."','".$key."','".$value."')";
			$meta_result=mysqli_query($con,$sql_meta);
			if(! $meta_result)
			{
		  		echo "problem with meta <br>";
			}
			
		}

	}
	// Free result set
	mysqli_free_result($res);
	mysqli_close($con);
}
indexer($url);
		
?>
</body>
</html>
 
