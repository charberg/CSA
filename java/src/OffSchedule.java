import java.awt.BorderLayout;
import java.awt.GridLayout;
import java.util.ArrayList;

import javax.swing.*;


public class OffSchedule extends JPanel{
	
	private MainFrame main;
	private ArrayList<CourseBox> courses;
	
	public OffSchedule(MainFrame m){
		main = m;
		setLayout( new BorderLayout());
		
		add(new JLabel("Select Your Courses Taken"),BorderLayout.NORTH);
		JButton submitButton = new JButton("SUBMIT");
		add(submitButton,BorderLayout.SOUTH);
		
		JPanel coursePanel = new JPanel();
		coursePanel.setLayout(new GridLayout(8,10));
		
		courses = new ArrayList<CourseBox>();
		courses.add(new CourseBox("SYSC1005", "fall", 1));
		courses.add(new CourseBox("SYSC2004", "winter", 1));
		
		coursePanel.add(courses.get(0));
		coursePanel.add(courses.get(1));
		
		add(coursePanel, BorderLayout.CENTER);
		
		
		
		setVisible(true);
	}
	
	public void fillTable(){
		
	}
}
