<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="author" content="James Bellizia">
        <title>Server Accessor</title>
        <meta name="description" content="This page is to use C++ to print result of user action">
        <link href="style.css" rel="stylesheet" type="text/css" media="screen" />
    </head>
    <body>
    	<header>
    		<h1>James Bellizia</h1>
            <h2>Server Accessor</h2>
    	</header>
        
        <?php
        //make sure the user knows the password (not sure how secure this is tbh)
        if ($_POST["password"]  == "jbellizi"){

            //make folder in which processing will happen
            $randNumber = rand();
            while (file_exists($randNumber)) {
                $randNumber = rand();
            }
            $commandMkdir = escapeshellcmd("mkdir " . $randNumber);
            $outputMkdir = shell_exec($commandMkdir);
            // Copy the c++ file into the folder.
            $command_cp = escapeshellcmd("cp fileStorage.cpp " . $randNumber);
            $output_cp = shell_exec($command_cp);
            
            //figure out user action and make arguments for cpp file based on that
            $userAction = $_POST["userAction"];
            $cppArguments = "";
            //get the relative and absolute path of the storage folder
            $rootFileRelative = "storage-root";
            $rootFilePath = realpath("storage-root");
            
            if ($userAction == "uploadFile"){
                //get the file name from html form
                $fileName = basename($_FILES["file"]["name"]);
                //get the target directory of the new file (if empty assumes root)
                $targetDirectory = $_POST["filePathUpload"];

                //make sure input does not contain whitespace
                if(preg_match('/\s/',$fileName) || preg_match('/\s/', $targetDirectory)){
                    echo "<p class='error'>Input may not include whitespace.</p>";
                }else {
                    //if the user specified directory is empty, assume storage-root
                    if(empty($targetDirectory)){
                        $targetDirectory = $rootFilePath;
                    } else {
                        //if its not empty, get the full path of the desired directory
                        $targetDirectory = realpath($rootFilePath . "/" . $targetDirectory);
                    }
                    if ($targetDirectory == false){
                        //if realpath fails, error
                        echo "<p class = 'error'>Specified directory not a real path.</p>";
                    } else if (!str_starts_with($targetDirectory, $rootFilePath)){
                        //if user tries to go anywhere but starting with rootpath, deny
                        echo "<p class = 'error'>Invalid filepath entered, upward traversal restricted.</p>";
                    }else {
                        //if the uploaded file can be moved to the desired directory, do so and add the cpp argument to print that directory
                        // echo "file name is: " . $fileName;
                        // echo "<br>target directory name is: " . $targetDirectory . "/". $fileName;
                        // echo "<br>contents of target directory are: " . shell_exec("ls "  . $targetDirectory);
                        if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetDirectory . "/". $fileName)) {
                            echo "<p>File uploaded to target directory: " . basename($targetDirectory) ."</p>";
                            $cppArguments = "showDirectoryContents " . $targetDirectory . " " . $fileName;
                        } else {
                            //otherwise show error
                            echo "<p class = 'error'>Unable to upload file.</p>";
                        }
                    }
                }
                
            } else if ($userAction == "createDirectory"){
                //get user input for parent directory
                $filePathParent = $_POST["filePathCreateDirectory"];
                //check for whitespace in input
                if(preg_match('/\s/', $filePathParent) || preg_match('/\s/', $_POST["fileNameCreateDirectory"])){
                    echo "<p class='error'>Input may not include whitespace.</p>";
                } else {

                    echo "filepathparent: " . $filePathParent;
                    echo "file name: " . $_POST["fileNameCreateDirectory"];

                    //make the parent path the realpath returns false if isnt a true path, or the absolute path if it is
                    $realParentPath = realpath($rootFilePath . "/" . $filePathParent);
                    //echo "<br>real file path: ".$realParentPath;
                    //as long as the real parent path exists, continue
                    if(!($realParentPath == false)){
                        if (!str_starts_with($realParentPath . "/" . $rootFileRelative, $rootFilePath)){
                            //if the user tries to go anywhere but the accepted root folder, deny (must start with absolute root path)
                            echo "<p class = 'error'>Invalid filepath entered, upward traversal restricted.</p>";
                        } else {
                            //this makes the filepath of the folder to be created by joining the parent filepath to the new jawn
                            $folderName = $realParentPath . "/" . basename($_POST["fileNameCreateDirectory"]);
                            //for testing
                            //echo "<p>Folder name: ". $folderName . "</p>";
                            
                            //check that the user didn't enter an empty directory name
                            if (empty($_POST["fileNameCreateDirectory"])){
                                echo "<p class = 'error'>Must provide directory name</p>";
                            } else {
                                //verify new folder doesn't already exist!
                                if(file_exists($folderName)){
                                    echo "<p class = 'error'>Unable to make directory: folder already exists.</p>";
                                } else {
                                    //if it doesn't...
                                    //make the directory
                                    $commandMkdir = escapeshellcmd("mkdir " . $folderName);
                                    $outputMkdir = shell_exec($commandMkdir);
    
                                    //if there was an error, output it
                                    if (!empty($outputMkdir)){
                                        echo "<p class = 'error'>Unable to make directory: ". $outputMkdir . "</p>";
                                    } else if (file_exists($folderName)){
                                        //if no error, output success message and update cpp arguments to show the directory 
                                        echo "<p>Directory created: " . basename($_POST["fileNameCreateDirectory"]) . "</p>";
                                        $printPath = "";
                                        if (basename($realParentPath) == $rootFileRelative){
                                            $printPath = "root";
                                        } else {
                                            $printPath = basename($realParentPath);
                                        }
                                        $cppArguments = "showDirectoryContents "  . $realParentPath . " " . $printPath;
                                    }
                                    
                                }
                            }
                            //for testing
                            //echo "<p> mkdir output: " . $outputMkdir . "</p>";
                            // echo "working directory:\n";
                            // echo shell_exec("pwd");
                            // echo "<p> ls: " . shell_exec("cd " . $rootFilePath . ";ls") .  "</p>";
                        }
                    } else {
                        echo "<p class = 'error'>Invalid filepath entered.</p>";
                    }  
                }




            } else if ($userAction == "downloadFile"){
                //check the download path is not empty
                if(empty($_POST["filePathDownload"])){
                    echo "<p class = 'error'>Target directory not provided.</p>";
                } else if (preg_match('/\s/', $_POST["filePathDownload"])){
                    echo "<p class = 'error'>Target directory may not include whitespace.</p>";
                }else {
                    //get filepath of downloading file
                    $filePath = realpath($rootFilePath . "/" . $_POST["filePathDownload"]);
                    //get name of file to download
                    $fileName = basename($_POST["filePathDownload"]);
                    //check it exists, if so add cpp arguments for generating a download link
                    if ($filePath == false){
                        //if not real path, notify user
                        echo "<p class = 'error'>File path does not exist.</p>";
                    } else {
                        //otherwise, add cpp arguments for generating and printing download link (instruction absolutepath relativepath filename)
                        if (file_exists($filePath)){
                            $cppArguments = "downloadFile " . $rootFilePath . "/" . $_POST["filePathDownload"] . " " . $rootFileRelative . "/" . $_POST["filePathDownload"] . " " . $fileName;
                        } else {
                            //otherwise error
                            echo "<p class = 'error'>File not found.</p>";
                        }
                    }
                    
                }
            } else if ($userAction == "showDirectoryContents"){

                if (preg_match('/\s/', $_POST["filePathShow"])){
                    echo "<p class = 'error'>Target file may not include whitespace.</p>";
                } else {
                    
                    //get user input for parent directory
                    $filePath = $_POST["filePathShow"];

                    //make the parent path the realpath returns false if isnt a true path, or the absolute path if it is
                    $realFilePath = realpath($rootFilePath . "/" . $filePath);

                    //as long as the real path exists, continue
                    if(!($realFilePath == false)){
                        if (!str_starts_with($realFilePath . "/" . $rootFileRelative, $rootFilePath)){
                            //if the user tries to go anywhere but the accepted root folder, deny (must start with absolute root path)
                            echo "<p class = 'error'>Invalid filepath entered, upward traversal restricted.</p>";
                        } else {
                            //if empty, show root directory
                            if(empty($_POST["filePathShow"])){
                                $cppArguments = "showDirectoryContents "  . $rootFilePath . " root";
                            } else if(!file_exists($realFilePath)){
                                //if it doesnt exist, show error
                                echo "<p class = 'error'>Target directory does not exist.</p>";
                            } else {
                                //otherwise add filepath to cpp arguments
                                $cppArguments = "showDirectoryContents "  . $realFilePath . " " . $filePath;
                                //for testing
                                //echo "running C++ with arguments: " . $cppArguments;
                            }
                        }
                    } else {
                        echo "<p class = 'error'>Invalid filepath entered.</p>";
                    }  
                }
            }
            //for testing
            //echo "running C++ with arguments: " . $cppArguments;

            //call the c++ filestorage with the added arguments
            $output = shell_exec("cd " . $randNumber . ";g++ -std=c++17 -o fileStorage.exe fileStorage.cpp;./fileStorage.exe " . $cppArguments . ";cd .." );

            //output result to html
            echo $output;
            //delete the process directory
            array_map("unlink", glob($randNumber . "/*"));
            rmdir($randNumber);
        } else {
            echo "<p class = 'error'>INCORRECT PASSWORD</p>";
        }
        ?>
        <a href = "https://jbellizi.w3.uvm.edu/personal/Server-Accessor/fileStorage.html">back</a>
    </body>
</html>
