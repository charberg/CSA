Java program structure:
  The main function is contained within the 'MainFrame.java' class.
  The MainFrame holds the different views, that are contained in different Panels.
  The views are split into three Panels: IntroPage, OffSchedule and MySchedule.
	
The user starts at the IntroPage panel.
From this screen the user selects:
 - stream/program
 - years completed
 - term being scheduled
 - on or off pattern
These options are selected through combination boxes and radio buttons.
By clicking the "SUBMIT" button, all of the information is sent to the server.

If the user is off schedule, the frame is changed to the OffSchedule panel.
If not, then the MySchedule panel is loaded.

In the OffSchedule panel the server sends all courses in the program tree. These courses are
put onto a table like interface, made up of custom panel classes. The user clicks on each 
checkbox associated with the courses they have taken in the past.

After clicking the "SUBMIT" button, the course list is sent to the server.
The server uses this information to calculate 10 schedules that do not have
conflicting courses.

The frame is then changed to the MySchedule page. Once on this page has loaded,
the schedule view (made up of custom panel classes) is filled with the course data. 

The user can select from a dropdown menu with schedule they want to see.
Once the "PICK THIS SCHEDULE" button is pressed, the server updates the database.