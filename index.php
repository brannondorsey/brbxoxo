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
			
			//load the image names as JSON array inside data obj
			var filesNames = <?php echo $files_JSON?>;
			filesNames = filesNames.data;

			//holds the image srcs at any given time
			var imageSrcs = new Array();

			$('document').ready(function(){
				$(".image-container img").each(function(){
					swapImage($(this));
				});
			});


			function swapImage(imgObj){
				//select image
				var imageIndex = pickIndex();
				//if the image that was picked is not already displaying
				if($.inArray(filesNames[imageIndex], imageSrcs) == -1){
					var imageUrl = "images/" + filesNames[imageIndex];
					var chanceToBuffer = 1/10;
					if(Math.random() < chanceToBuffer) imageUrl = "loading.gif";
					$(imgObj).attr("src", imageUrl);
					var id = $(imgObj).attr("id");
					imageSrcs[parseInt(id)] = filesNames[imageIndex];
					
					//set next timer
					var min = 2000;
					var max = 8000;
					var time = Math.floor(Math.random() * (max - min + 1)) + min;
					window.setTimeout(function(){
						swapImage(imgObj);
					}, time);
				}else{
					console.log("found a duplicate and chose another image");
					swapImage(imgObj);
					return;
				}
			}

			//picks a random index value for an gif in images/ and returns it
			//stored in its own function so that it can easily be recalled.
			function pickIndex(){
				var numbImages = filesNames.length;
				return Math.floor(Math.random()*numbImages); //make this mutually exclusive so that no images can have the same url
			}
		</script>
	</head>
	<body>
		<h1>brbxoxo</h1>
		<div class="image-container">
			<img id="0" src=<?php echo '"images/' . $first_images[0] . '"'; ?>/>
			<img id="1" src=<?php echo '"images/' . $first_images[1] . '"'; ?>/>
			<img id="2" src=<?php echo '"images/' . $first_images[2] . '"'; ?>/>
			<img id="3" src=<?php echo '"images/' . $first_images[3] . '"'; ?>/>
		</div>
		<span>A project by <a href="http://placesiveneverbeen.com/">Addie Wagenknecht</a> and <a href="http://pablogarcia.org/">Pablo Garcia</a> | Code by <a href="http://brannondorsey.com">Brannon Dorsey</a></span>
	</body>
</html>
