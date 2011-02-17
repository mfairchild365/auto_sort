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
//Directories
$musicDir   = dirname(__file__).'/music/';
$videoDir   = dirname(__file__).'/video/';
$appDir     = dirname(__file__).'/app/';
$defualtDir = dirname(__file__).'/';

//Create an array of the alphabet.
$alpha = range('a', 'z');
$music = array('.aac', '.aif', '.iff', '.m3u', '.mid', '.mp3', '.mpa', '.ra', '.wav', '.wma');
$video = array('.3g2', '.3gp', '.asf', '.asx', '.avi', '.flv', '.mov', '.mp4', '.mpg', '.rm', '.swf', '.vob', '.wmv' );
$apps  = array('.exe', '.iso');
$log   = date("F j, Y, g:i a") . "\n";

//Make sure directory structure is in place.
echo "ensuring directory structure...\n";
if (!is_dir($musicDir)) {
    mkdir($musicDir);
}
if (!is_dir($videoDir)) {
    mkdir($videoDir);
}
if (!is_dir($appDir)) {
    mkdir($appDir);
}

foreach ($alpha as $lowercase) {
    //create a lowercase folder if it does not already exist.
    if (!is_dir($defualtDir.$lowercase)) {
        mkdir($defualtDir.$lowercase);
    }
    if (!is_dir($videoDir.$lowercase)) {
        mkdir($videoDir.$lowercase);
    }
    if (!is_dir($appDir.$lowercase)) {
        mkdir($appDir.$lowercase);
    }
    if (!is_dir($musicDir.$lowercase)) {
        mkdir($musicDir.$lowercase);
    }
}

//Take a look at all the files in the current folder.
echo "Scanning and moving files...\n";
$dir = new DirectoryIterator(dirname(__FILE__));
foreach ($dir as $file) {
    $file = $file->getFilename();
    
    //Get the first letter of the file and convert it to lowercase.
    $letter = strtolower($file[0]);
    
    if (!in_array($letter, $alpha)) {
        continue;
    }
    
    //if the letter is a file, don't me it.  Its an alpha folder.
    if ($letter == $file) {
        continue;
    }
    
    //Don't move these file/folders
    if ($file == 'autosort.php' |
        $file == 'autosort.log' |
        dirname(__file__)."/".$file."/" == $musicDir |
        dirname(__file__)."/".$file."/" == $videoDir |
        dirname(__file__)."/".$file."/" == $appDir) {
        continue;
    }
    
    //Take a look at subfolders to see where to send the item.
    if (is_dir($file)) {
        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($file));
        $musicCount = 0;
        $videoCount = 0;
        $appCount   = 0;
        while($it->valid()) {
        
            if (!$it->isDot()) {
                echo 'SubPathName: ' . $it->getSubPathName() . "\n";
                echo 'SubPath:     ' . $it->getSubPath() . "\n";
                echo 'Key:         ' . $it->key() . "\n";
                $type = substr($it->key(), -4, 4);
                echo 'type:        ' . $type . "\n\n";
                if (in_array($type, $music)) {
                    $musicCount++;
                }
                if (in_array($type, $video)) {
                    $videoCount++;
                }
                if (in_array($type, $apps)) {
                    $appCount++;
                }
            }
            $it->next();
        }
    } else {
        $type = substr($file, -4, 4);
        echo 'type:        ' . $type . "\n\n";
        if (in_array($type, $music)) {
            $musicCount++;
        }
        if (in_array($type, $video)) {
            $videoCount++;
        }
        if (in_array($type, $apps)) {
            $appCount++;
        }
    }
    
    $dir = $defualtDir;
    
    if (($musicCount > $appCount) && ($musicCount > $videoCount)) {
        $to = $musicDir;
    }
    
    if (($videoCount > $appCount) && ($videoCount > $musicCount)) {
        $to = $videoDir;
    }
    
    if (($appCount > $videoCount) && ($appCount > $musicCount)) {
        $to = $appDir;
    }
    
    echo "to:   $to \n";

    /*Don't move while testing script*/
    //Move it
    $log .= " Moving $file TO $to...\n";
    
    if (!rename($defualtDir.$file, $to.$letter.'/'.$file)) {
       $log .= " failed to copy $file...\n";
    }
}

//Log
$log .= "\n";
touch(getcwd()."/autosort.log");
$file = new SplFileObject("autosort.log", "a");
$written = $file->fwrite($log);

echo "done!\n";