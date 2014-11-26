SYSC 4504 Project
Course Selection Assistant
November 28 2014

1.
Team Members:
100890068	Christopher Briglio		chrisbriglio@cmail.carleton.ca
100890858	David Briglio			davidbriglio@cmail.carleton.ca

2.
Program used for testing: Software Engineering

3.
TA: Mr.Abaza.

4.
Member Contributions:

The program was divided into 2 sections which were completed by the 2 members.
Christopher was in charge of designing and implement all the back end of the program which would include the database, 
the files needed to create the database, and all php scripts. (All files in php folder & data folder)
David was in charge of all the front end which included all html, javascript, and java code. (all files in java folder, css folder, and pages folder)


5.
Project Folders:
Main folder:
Hub of project. Should hold all sub folders required by program and the index.php
file to re-direct to the main page.

Data Folder:
Holds all data (held in files) needed to create the database. Within are 2 sub-folders. 
These are the Electives sub-folder, and the Patterns sub-folder. The Electives Sub-folder contains txt files outlining the courses involved
in aparticular elective groups. The Patterns folder contains text files that represent the individual patterns of academic programs.


Documentation Folder:
Holds documentation on the project included database UML diagrams, program specs ect.

CSS Folder:
Folder holding all CSS Styles used in the client side HTML pages.

Java Folder:
Folder containing all the java code. This folder holds the java version of the client side interface.

PHP Folder:
This folder holds all php scripts and all server-side code. Within this folder is a sub-folder called 'classes'. This folder contains php classes
that are used throughout the server-side code.

Useful folder:
This folder contains reference documents used in the project. These include the project outline, and the 'Tree' of a
Software Engineer at Carleton. These documents where what the implementation of the program was based off of.

Pages Folder:
This folder contains all client-side pages writen in HTMl and Javascript.

The tasks of the project and mapping of the files used within the project between html-php are as follows:
Upon entering the program (localhost/CSA/), the index.php script will run which will re-direct the user to intro_page.html contained
within the pages folder. At this point the user will be asked to specify his/her program, the year they've completed, whether or not they are
on or off pattern, and what term they which to generate a schedule for. From here the page sends all this information to the server.php
file located within the php folder. The server will take this information and do one of 2 things:
1. If the User specified on-patter, then it will generate a list of schedules for the specified term using a OnScheduleCourseCalculator
object, and send this list of schedules along with the other information specified to the my_schedule.php page located in the pages folder.

2. If the User specified off-pattern, the server will re-direct the user to the Off_Schedule_Courses.php page inside th pages folder.
From here the user will specify which courses were previously completed and send this information back to the server.php. 
Then server.php will generate a list of schedules that best corresponds to the courses previously taken by the user
using a OffScheduleCourseCalculator object and send this list along with all other information to the my_schedule.php page located in the pages folder.

At this point the my_schedule.php will take that list of schedules and allow the user to choose which one to display. Once the user has choosen
a schedule, he/she hits the submit button, sending the choosen schedule to courseServer.php. The server then takes this list of courses and increases
the number of students for each, returning to my_schedule.php wheter or not all the courses were successfully registered in.


6.
Before running the program for the first time, the install.php file must be executed in the browser in order that the database be created. 
From there, the user may just navigate through the local host to the Main program folder. 
From here the index.php file will redirect the user to the first view page of the program.

In order to add more information to the database, the files within the data folder must be updated.
To add more academic program, simply add them to the AcademicPrograms.txt file while ensure that they follow the proper format.


7.


