<?php

//read the names of the files in images/
if($files = scandir("images")){
	unset($files[0]);
	unset($files[1]);
	unset($files[2]);
	shuffle($files);

	//save the names as a json data obj
	$files_JSON = pack_files_as_JSON_array($files);

	//fill $first_images with four random image files
	if(!$first_images = array_get_random($files, 4)) die("Error: Initial images could not load");

}else die("Error: Images folder not found");

//encodes an array as a valid JSON array 
function pack_files_as_JSON_array($array, $wrapper_name="data"){
	$JSON_string = '{ "' . $wrapper_name . '" : [';
	foreach($array as $filename){
		$JSON_string .= '"' . $filename . '", ';
	}
	$JSON_string = rtrim($JSON_string, ", ");
	$JSON_string .= "]}";
	return $JSON_string;
}

//returns a random specified number of items from an array
//used to randomly select gifs at the beginning
//function doesn't allow duplicates to be selected
function array_get_random($array, $numb_to_return){
	$array_to_return = array();
	//get random keys
	if($rand_keys = array_rand($array, $numb_to_return)){
		//get the $array values for those keys and add them to $array_to_return
		foreach ($rand_keys as $key) {
			$array_to_return[] = $array[$key];
		}
		return $array_to_return;
	}else return false; //the numb to return is greater than the array's size
}
?>

<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="style.css">
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js" ></script>
		<script type="text/javascript">
			
			var filesNames = <?php echo $files_JSON?>;
			filesNames = filesNames.data;


			$('document').ready(function(){
				console.log(filesNames);
				
			});


			function swapImage(){
				$('p').append("test ");

				var min = 300;
				var max = 2000;
				var time = Math.floor(Math.random() * (max - min + 1)) + min;
				console.log(time + " ");
				window.setTimeout(append, time);
			}
			
		</script>
	</head>
	<body>
		<h1>brbxoxo</h1>
		<div class="image-container">
			<img src=<?php echo '"images/' . $first_images[0] . '"'; ?>/>
			<img src=<?php echo '"images/' . $first_images[1] . '"'; ?>/>
			<img src=<?php echo '"images/' . $first_images[2] . '"'; ?>/>
			<img src=<?php echo '"images/' . $first_images[3] . '"'; ?>/>
		</div>
		<span>A project by <a href="http://placesiveneverbeen.com/">Addie Wagenknecht</a> and <a href="http://pablogarcia.org/">Pablo Garcia</a>. Code by <a href="http://brannondorsey.com">Brannon Dorsey</a></span>
	</body>
</html>
