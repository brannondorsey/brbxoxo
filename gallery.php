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
	//if(!$first_images = array_get_random($files, 4)) die("Error: Initial images could not load");

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
		<style>
			.image-container{
				width: 920px;
				margin-top: 15px;
				margin-bottom: 20px;
			}

			a.asterisk{
				position: fixed;
				right: 25px;
				font-size: 24pt;
				margin-bottom: 0px;
			}
		</style>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js" ></script>
	</head>
	<body>
		<div class="image-container">
			<div class="asterisk-container">
				<a class="asterisk" href="#">&para;</a>
			</div>
			<!--<img id="0" src=<?php echo '"images/' . $first_images[0] . '"'; ?>/>-->
			<?php 
			$i = 0;
			foreach($files as $filename){ ?>
			<img id="<?php echo $i ?>" src="<?php echo "images/" . $filename ?>"/>
			<?php 
				if($i == 15) break; //limit to 15 images for now
				$i++;
			} ?>
		</div>
	</body>
</html>
