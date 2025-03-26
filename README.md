# M3OEP-jbellizi

James Bellizia

No installations are required to run this program.

This program acts as a website hosted on the UVM silk server that can create directories, upload files, generate download links of specified files, and view directories on the server. There is an html file called fileStorage.html that pulls heavily from the in class Hamilton activity- a form that prompts the user for a selection that then calls a php process file. The php process file is the most complex file in the program, as it takes in POST data from the html form and does the action based on what the user input was. The php file serves to prepare a list of arguments to be passed via shell_exec to fileStorage.cpp, which either shows the directory it is prompted with or generates a download link for the file requested. The program also requires a password for making any changes on the server, which is definitely not the most secure but adds a layer of security. For testing purposes, the password is my netid, 'jbellizi'. 

This program starts in html, then uses php to get the form data and call C++. It also uses css to style the html page. 

I use html as the building block of the form website. It's what all websites are made of, and it allows for a form to be used to get user choices. I use php to do much of the heavy lifting, which is to say validate the user's input, understand what the user wants to do, and prepare a string of arguments to pass to C++. PHP is great for making html code that does something other than be text. I use C++ to generate more html code based on user input, and I chose C++ because it is convenient to pass/accept arguments to via shell calls and because the computation needed is quick and easily understandable in C++. 

I call the php from the action part of the form section of my html code (specified in line 15). I call the C++ code from php shell_exec on line 210 of process.php. 

I am sure there are bugs in this program. If I had more time, I would do much more extensive testing on the user input validation, especially on special characters and characters that would mess up the arguments passed to C++. I also am suspicious of how secure the password for the site is, I don't think it is nearly as safe as I think but I also know little to nothing about cybersecurity protocols. 

Given more time, it would be interesting to make the website more visual and have opportunities to traverse through the storage folder instead of always being in the root. It could get frustrating to keep having to type the same folder names in to upload/download the same files. Also, I would like to have a way to zip folders so that you could download multiple files at once. I would also like to make it so that when the directory is printed, it prints the links to the files it is showing. 

Much of the code I used in this project to start was written by Professor Dion in the Hamilton assignment, or myself in the Testing-Website guided project. However, I don't know much php, so I used a significant amount of sources to learn methods or ways of doing things in php I otherwise wouldn't have known. Particularly, I used one segment of code from Stack Overflow pretty explicitly, as stated in the C++ file (line 55). Regardless here are the links to the sites I used: 

https://www.w3schools.com/cssref/sel_has.php
https://developer.mozilla.org/en-US/docs/Web/CSS/:checked 
https://www.browserstack.com/guide/sibling-selectors-in-css#:~:text=4.-,General%20sibling%20selector%20(~),being%20adjacent%20to%20each%20other. 
https://www.php.net/manual/en/function.empty.php 
https://www.w3schools.com/php/func_filesystem_file_exists.asp 
https://www.w3schools.com/howto/howto_html_download_link.asp 
https://www.w3schools.com/php/func_filesystem_realpath.asp 
https://www.codecademy.com/resources/docs/php/string-functions/str-starts-with 
https://stackoverflow.com/questions/612097/how-can-i-get-the-list-of-files-in-a-directory-using-c-or-c 
https://www.php.net/manual/en/function.preg-match.php 


I think I earned an 80 on this assignment: my main program (if you could call any of the files that) was about as complex or slightly more complex than a guided project, I used multiple languages and used them correctly, and there was significant data transfer between languages (POST, shell). The lifespan of my project was poor, and there are some bugs, so I say around 80 makes sense. 