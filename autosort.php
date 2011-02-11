<?php
/*
Auto_Sort
(to be ran from command line)

purpose: Script to be used to sort files and folders in the same directory as the script.
The files and folders will be sorted into respective alphabetical sub-folders (a,b,c,d,e...).

goal: to automate (could be combined with a cron-job) file system order.  Example: placed in a 
media server to sort massive amounts of videos.

@author Michael Fairchild <mfairchild365@gmail.com>
*/

//Create an array of the alphabet.
$alpha = range('a', 'z');

echo "--Starting Auto Sort---" . PHP_EOL;

foreach ($alpha as $lowercase) {
    //create a lowercase folder if it does not already exist.
    if (!is_dir($lowercase)) {
        mkdir($lowercase);
    }
    echo $lowercase.PHP_EOL;
}


if ($handle = opendir('.')) {
    echo "Directory handle: $handle\n";
    echo "Files:\n";

    while (false !== ($file = readdir($handle))) {
        
        //Get the first letter of the file and convert it to lowercase.
        $letter = strtolower($file[0]);
        
        if (!in_array($letter, $alpha)) {
            continue;
        }
        
        //if the letter is a file, don't me it.  Its an alpha folder.
        if ($letter == $file) {
            continue;
        }
        
        //Don't move this file
        if ($file == 'autosort.php') {
            continue;
        }
        
        //Move it
        echo "Moving $file ...";
        
        if (!rename($file, $letter.'/'.$file)) {
            echo "failed to copy $file...\n";
        }
        
    }
    closedir($handle);
}

echo "--Auto Sort End--------" . PHP_EOL;
echo PHP_EOL;