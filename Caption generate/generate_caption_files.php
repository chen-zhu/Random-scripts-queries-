<?php 
//Chen Zhu chen_zhu@umail.ucsb.edu 
//useful regex pattern: http://regexr.com; 


if($argc < 2){
	echo "Usage:   php generate_caption_files.php <directory path>\n"; 
	exit(1);
}

$filepath = $argv[1]; 

$files = array_diff(scandir($filepath), array('..', '.', basename(__FILE__))); 

foreach($files as $file){
	if(pathinfo($file)['extension'] == "qt"){
		$disassemble = explode(".", $file); 

		//check matching pattern. if not, throw error here, saying that pattern doesn not apply 
		if(!preg_match("/^Job_/", $disassemble[0])){
			echo "'$file' does not match file name pattern. File skipped.\n"; 
			continue; 
		}

		//then explode and separate the string--> get file name. 
		$actual_name = explode("Job_", $disassemble[0]);
		if(!$actual_name[1]){
			echo "'$file' does not match file name pattern(Job_xxx..). File skipped.\n"; 
			continue; 
		}

		//generate smil file. 
		generate_smil($filepath, $actual_name[1]); 

		//generate XXXX.qt.txt file. 
		$new_name = $actual_name[1].".qt.txt"; 
		rename($filepath.DIRECTORY_SEPARATOR.$file, $filepath.DIRECTORY_SEPARATOR.$new_name);

		//process each caption inside smil file. 
		del_blank_audio($filepath, $new_name);  
	}
}


/**
* Generate smil files by using the template.smil file. 
* @param string $filepath, the path of corresponding file. 
* @param string $newfilename the new file name that will be generated (No extension). 
*/
function generate_smil($filepath, $newfilename){
	$new_smil = $newfilename.".smil"; 
	
	//construct smil file here. 
	if(!copy($filepath.DIRECTORY_SEPARATOR."template.smil", $filepath.DIRECTORY_SEPARATOR.$new_smil)){
		echo "Makes a copy of smil file in $newfile failed.\n";
	}

	//edit smil files here. 
	$file_contents = file_get_contents($filepath.DIRECTORY_SEPARATOR.$new_smil); //need to change path here. 
	$file_contents = str_replace("{file_name}",$newfilename,$file_contents);
	file_put_contents($filepath.DIRECTORY_SEPARATOR.$new_smil,$file_contents); 
}


/**
* Delete blank audio with ending timestamp. replace [] with () in each block. 
* @param string $filepath, the path of corresponding file. 
* @param string $filename, the name of file that needs to modify. 
*/
function del_blank_audio($filepath, $filename){
	//timestamp pattern 
	$timestamp = "/\[\d\d\:\d\d\:\d\d\.\d\d\d\]/"; 

	//regular expression pattern. [XX:XX:XX.XXX] <block_content> [XX:XX:XX.XXX]
	$pattern = "/\[\d\d\:\d\d\:\d\d\.\d\d\d\](\s(.*?)*\s*)*\[\d\d\:\d\d\:\d\d\.\d\d\d\]/"; 

	//open && read files, Then use regular expression (wildcard) to format matches. 
	$file_contents = file_get_contents($filepath.DIRECTORY_SEPARATOR.$filename);
	preg_match_all($pattern, $file_contents, $matches); 
	$matches = $matches[0];

	//delete blank_audio, 
	foreach($matches as $block){
		//if blank audio, detele from the file. 
		if(strpos($block, "[BLANK_AUDIO]")){
			$file_contents = str_replace($block,"",$file_contents);
		} else { 
			//1. then remove the end time. 
			preg_match_all($timestamp, $block, $time_array); 
			if(empty($time_array[0][1])){
				echo "regex error (Timestamp reading error).\n"; 
				exit(1); 
			}
			$modified_block = str_replace($time_array[0][1],"",$block);
			$file_contents = str_replace($block,$modified_block,$file_contents);

			//2. replace [] with (); 
			//Each modified_block only have one timestamp now. 
			$block_content = str_replace($time_array[0][0],"",$modified_block);
			$modified_content =  str_replace("[","(",$block_content);
			$modified_content =  str_replace("]",")",$modified_content);
			$file_contents = str_replace($block_content,$modified_content,$file_contents);
		}
	}
	//write it back, 
	file_put_contents($filepath.DIRECTORY_SEPARATOR.$filename, $file_contents); 
}






?>





