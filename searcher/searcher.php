
<head>
	<meta charset="utf-8">
	<title>File Browser</title>
</head>
<body>
	<form action="searcher.php" method="post">
		search:<input type="text" name="word" required placeholder="search....."><br>
		<input type="checkbox" name="check_list[]" value="META_TAG_SEARCH"><label>META_TAG_SEARCH</label><br/>
		<input type="checkbox" name="check_list[]" value="TEXT SEARCH"><label>TEXT SEARCH</label><br/>
		<input type="reset">
		<input type="submit" name="Submit" value="Submit">  
	</form>
<?php
$text_search=false;
$tag_search=false;
$both=false;

$word="";

if(isset($_POST['Submit'])){
	//check the check box is clicked or not
	if(!empty($_POST['check_list'])) {
		//for slection for check_boxes
		foreach($_POST['check_list'] as $selected){
			if($selected==='META_TAG_SEARCH'){
				$tag_search=true;
			}
			else if($selected==='TEXT SEARCH'){
				$text_search=true;
			}
		}
	}
	$word=$_POST["word"];
}

if($text_search && $tag_search){
	$both=true;
}
//for database conncetion
$con=mysqli_connect("localhost","root","","indexer");
// Check connection
if (mysqli_connect_errno())
{
 	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

if($both){
	//run the quary for search a word  in both (text  and meta tags) 
	$sql_query="select distinct f.file_url,f.file_name, NULL as word_count 
			    from files f 
			    join file_word fw on fw.file_id=f.file_id 
			    join meta_info m on f.file_id=m.file_id 
			    where m.content LIKE '%".$word."%' 
			    UNION ALL
			    select distinct f.file_url,f.file_name,fw.word_count 
				from words w 
				join file_word fw on w.word_id=fw.word_id 
				join files f on fw.file_id=f.file_id 
				where w.word='".$word."' 
				order by word_count desc";
	$res=mysqli_query($con,$sql_query);
	if(mysqli_num_rows($res) > 0)
	{
		while($row=mysqli_fetch_array($res,MYSQLI_NUM)){
			echo "<A href='display.php?file=$row[0]'>$row[0]</A>"."\t\t\t\t\t"."\t\t\t\t\t word_count=>".$row[2]."</BR>";
		}
	}
	else{
		echo "Did not match any documents.";
	}
}

else if($tag_search)
{	 //run the quary for search a word only in  tag(meta tags)
	$sql_meta="select distinct f.file_url,f.file_name 
			   from files f 
			   join file_word fw on fw.file_id=f.file_id 
			   join meta_info m on f.file_id=m.file_id 
			   where m.content LIKE '%".$word."%' 
			   order by fw.word_count desc";
	$res_meta=mysqli_query($con,$sql_meta);
	if(mysqli_num_rows($res_meta) > 0){
		while($row=mysqli_fetch_array($res_meta,MYSQLI_NUM)){
			echo "<A href='display.php?file=$row[0]'>$row[0]</A>"."\t\t\t\t\t"."</BR>";
		}
	}
	else{
		echo "Did not match any documents.";
	}

}
else if($text_search){
	//run the quary for search a word only in  text 
	$sql_text="select distinct f.file_url,f.file_name,fw.word_count 
			   from words w 
			   join file_word fw on w.word_id=fw.word_id 
			   join files f on fw.file_id=f.file_id 
			   where w.word='".$word."' 
			   order by fw.word_count desc";
	$res_text=mysqli_query($con,$sql_text);
	if(mysqli_num_rows($res_text) > 0){
		while($row=mysqli_fetch_array($res_text,MYSQLI_NUM)){
			echo "<A href='display.php?file=$row[0]'>$row[0]</A>"."\t\t\t\t\t"."\t\t\t\t\t word_count=>".$row[2]."</BR>";
		}
	}
	else{
		echo "Did not match any documents.";
	}

}
mysqli_close($con);
?>
</body>
</html>