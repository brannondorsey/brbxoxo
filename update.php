<?php
require_once 'class.GifFrameExtractor.inc.php';
ini_set('memory_limit','128M');

//read the names of the files in images/
if($files = scandir("images")){
	unset($files[0]);
	unset($files[1]);
	unset($files[2]);

	$json_output = "";
	$gif_extractors = extract_gifs($files);
	$data = array();
	$i = 0;
	foreach($gif_extractors as $file_name => $extractor){
		$extractor->extract("images/" . $file_name); //exctract
		$numb_frames = $extractor->getFrameNumber(); //get number of frames
		$duration = $extractor->getTotalDuration(); //get the total duration 
		$data[] = new stdClass();
		$data[$i]->file_name = $file_name;
		$data[$i]->numb_frames = $numb_frames;
		$data[$i]->duration = $duration;
		$i++;
		// echo "Filename: $file_name <br>";
		// echo "Number of frames: $numb_frames <br>";
		// echo "Duration: $duration <br>";
		// echo "<br>";
	}
	$file = json_encode($data);
	echo (file_put_contents("data/gif_data.json", $file) !== false) ? "updated successfully" : "Error: there was a problem updting the file";

	//save the names as a json data obj
	//$files_JSON = pack_files_as_JSON_array($files);

}else die("Error: Images folder not found");

//returns an array of GifFrameExtractor Objects
function extract_gifs($files){
	$gif_frame_extractors = array();
	foreach($files as $gif){
		if(GifFrameExtractor::isAnimatedGif("images/" . $gif)){
			$gif_frame_extractors[$gif] = new GifFrameExtractor();
		}
	}
	return $gif_frame_extractors;
}

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
