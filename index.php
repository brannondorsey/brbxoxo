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
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js" ></script>
		<script type="text/javascript">
			
			//load the image names as JSON array inside data obj
			var filesNames = <?php echo $files_JSON; ?>;
			filesNames = filesNames.data;
			var origFilesNames = filesNames.slice(); //copy the array. Stupid fucking sintax (http://stackoverflow.com/questions/7486085/copying-array-by-value-in-javascript)
		    var numbFiles = filesNames.length;
		    var usingOrigFilesNames = false;

			// console.log("There are " + filesNames.length + " files to choose from");

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

				//if the images have all been viewed before...
				if(!usingOrigFilesNames &&
					filesNames.length <= 0){
					console.log("All have been seen");
					//reset filesNames to contain all of the original files in the 'images' folder
					filesNames = origFilesNames;
					usingOrigFilesNames = true;
				}

				//read the current image src
				var currentImageURL = imgObj.attr("src");
				
				//get a random image index
				var imageIndex = pickIndex();
				
				//if the image that was picked is not already displaying
				if($.inArray(filesNames[imageIndex], imageSrcs) == -1){	
					var imageUrl = "images/" + filesNames[imageIndex];
					var chanceToBuffer = 1/10;
					var time;
					var isBlack = false;
					var isLoading = false;
					//if the current image was not black or loading (because it was a webcam)
					if(typeof currentImageURL !== 'undefined' &&
					   currentImageURL.indexOf("black.jpg") == -1 &&
					   currentImageURL.indexOf("loading.gif") == -1){
					   imageUrl = "black.jpg";
					   isBlack = true;
					}
					else if(Math.random() < chanceToBuffer){
						imageUrl = "loading.gif";
						isLoading = true;
					} 

					//swap the image
					$(imgObj).attr("src", imageUrl);
					var id = $(imgObj).attr("id");
					imageSrcs[parseInt(id)] = filesNames[imageIndex];

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
						}else if(isBlack){ //if the image was just set to black
							min = 1000;
							max = 2000;
						}else{ //if the image is black
							min = 10000;
							max = 45000;
							if(Math.random() < 0.1){
								min = 2000;
								max = 5000;
							}
						}
						time = Math.floor(Math.random() * (max - min + 1)) + min;
					}

					//if the image chosen is a webcam... don't let it get chosen again until all images have been seen
					if(!usingOrigFilesNames && !isBlack ||
					   !usingOrigFilesNames && !isLoading){
					   	//remove the image from filesNames
					   	filesNames = removeFromArray(filesNames[imageIndex], filesNames);
					}

					// console.log("The number of files to choose from is " + filesNames.length);

					//set the timeout
					window.setTimeout(function(){
							swapImage(imgObj);
						}, time);
				}
				else{ //if the image picked is already displaying... try again
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

			//returns a modified array with needle removed from haystack.
			//returns false on failure
			function removeFromArray(needle, haystack){
				var index = haystack.indexOf(needle);
				if(index != -1){
					// Note: slice is a void function that returns the removed elements
					// look it up (http://www.w3schools.com/jsref/jsref_splice.asp)
					haystack.splice(index, 1);
					return haystack;
				}else return false;
			}
		</script>
	</head>
	<body>
		<div class="image-container">
			<!--<img id="0" src=<?php //echo '"images/' . $first_images[0] . '"'; ?>/>-->
			<img id="0"/>
			<img id="1"/>
			<img id="2"/>
			<img id="3"/>
		</div>
		<a class="asterisk" href="about.php">*</a>
	</body>
</html>
