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
			var filesNames = <?php echo $files_JSON; ?>;
			filesNames = filesNames.data;

			//load the gif data as JSON array
			var gifData = <?php echo file_get_contents("data/gif_data.json"); ?>;

			//holds the image srcs at any given time
			var imageSrcs = new Array();

			$('document').ready(function(){
				$(".image-container img").each(function(){
					swapImage($(this));
				});
			});


			//use an optional parameter here to act as a switch for putting black images between each change
			//then if optional parameter'fromWebcam'is defined make display black.
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

					var time;
					//if the gif is a NOLOOP set time according to the gifs length so that it doesnt repeat
					if(filesNames[imageIndex].toLowerCase().indexOf("noloop") != -1 &&
						imageUrl.indexOf("loading.gif") == -1){
						//look up the gif in the array of gifData objects
						for(var i = 0; i < gifData.length; i++){
							var gif = gifData[i];
							//if a match is found...
							//console.log("this filename being tested is " + gif.file_name);
							//console.log("the name of the gif is " + filesNames[imageIndex]);
							if(gif.file_name == filesNames[imageIndex]){
								//console.log("a match was found");
								time = gif.duration * 10;
								//console.log("the timeout was set to occur in " + time);
								break;
							}else continue;
						}
					}else{ //otherwise set time according to a looping gif
						//set next timer
						var min, max;
						//if the image is a loading spinner
						if(imageUrl.indexOf("loading.gif") != -1){
							min = 3000;
							max = 10000;
						}else{ //if the image is a webcam
							min = 10000;
							max = 45000;
							if(Math.random() < 0.1){
								min = 2000;
								max = 5000;
							}
						}
						time = Math.floor(Math.random() * (max - min + 1)) + min;
					}
					
					window.setTimeout(function(){
						swapImage(imgObj);
					}, time);
				}else{
					//console.log("found a duplicate and chose another image");
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
