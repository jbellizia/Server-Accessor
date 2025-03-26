#include <fstream>
#include <iostream>
#include <string>
#include <vector>
#include <filesystem>

namespace fs = std::filesystem;
using namespace std;

vector<string> getDirectoryContents(string filePath);

int main(int argc, char* argv[]) {
    //get the instruction provided by php
    string instruction = argv[1];
    if (instruction == "showDirectoryContents"){
        //if the instruction was to show the directory, print that we are doing so
        cout << "<h3>Showing directory contents for " << argv[3] << "</h3>" << endl;
        //get the directory contents into vector form
        vector<string> contents = getDirectoryContents(argv[2]);
        //if the directory is empty, say dat
        if(contents.empty()){
            cout << "<p>The specified directory was empty.</p>";
        } else {
            //if the directory has contents, loop through the vector and add the contents to a numbered table
            cout << "<table>\n<tr><th></th><th>Contents</th></tr>" << endl;
            int counter = 1;
            for (string file : contents){
                cout << "<tr><td>" << counter << "</td><td>" << file << "</td></tr>" << endl;
                counter++;
            }
            cout << "</table>" << endl;
        }

    } else if (instruction == "downloadFile"){  
        //if the instruction was to download, print that we are doing so
        cout << "<h3>Showing download contents for " << argv[3] << "</h3>" << endl;
        //generate a download link for the file on the server
        
        fs::path path = argv[2];
        if(!fs::is_directory(path)){
            cout << "<a href = \"" << argv[3] << "\" download>Download " << argv[4] << "</a>" << endl;
        } else {
            cout << "Selected path led to a directory, which cannot be downloaded.";
        }
       
    } else {
        cout << "No instruction specified." << endl;
    }
    
    return 0;
}

//this method puts the contents of a specified filepath in a vector of strings (file names)
vector<string> getDirectoryContents(string filePath){
    vector<string> contents;
    //the following code snippet I ripped and modified slightly from a stack overflow post
    // the link to the post is one of my sources in the README.md file, but I have another
    //link to it here for clarity : https://stackoverflow.com/questions/612097/how-can-i-get-the-list-of-files-in-a-directory-using-c-or-c 

    for (const auto & entry : fs::directory_iterator(filePath)) {
        contents.push_back(entry.path().filename());
    }
    return contents;
}