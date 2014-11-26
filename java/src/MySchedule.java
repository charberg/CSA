import java.awt.BorderLayout;
import java.awt.GridLayout;

import javax.swing.*;


public class MySchedule extends JPanel{
	
	private MainFrame main;
	private JComboBox options;
	
	public MySchedule(MainFrame m){
		main = m;
		
		JLabel topLabel = new JLabel("My Schedule");
		setLayout(new BoxLayout(this, BoxLayout.PAGE_AXIS));
		add(topLabel);
		
		add(new JLabel("Your Options: "));
		options = new JComboBox();
		add(options);
		JButton selectButton = new JButton("SELECT");
		add(selectButton);
		
		JPanel timePanel = new JPanel();
		timePanel.setLayout(new GridLayout(48,8));
		
		for(int i=8;i<24;i++){
			timePanel.add(new SchedBox(Integer.toString(i)+":00"));
			timePanel.add(new SchedBox(Integer.toString(i)+":30"));
		}
		
		add(timePanel);
		
		JButton submitButton = new JButton("PICK THIS SCHEDULE");
		add(submitButton);
		
		setVisible(true);
		
	}
	
}
