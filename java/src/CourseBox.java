import java.awt.Color;
import java.awt.Dimension;

import javax.swing.*;


/* Label + Checkbox for course selection */
public class CourseBox extends JPanel{
	private String ID,term;
	private JLabel label;
	private JCheckBox box;
	private int year;
	
	CourseBox(String name, String t, int y){
		//setPreferredSize(new Dimension(90,90));
		//setColour(Color.black);
		this.ID = name+t+Integer.toString(y);
		this.label = new JLabel(name);
		this.box = new JCheckBox();
		this.year = y;
		this.term = t;
		setLayout(new BoxLayout(this, BoxLayout.PAGE_AXIS));
		add(this.label);
		add(this.box);
		setVisible(true);
	}

	public String getID() {
		return ID;
	}
	
	public void setColour(Color c){
		setBorder(BorderFactory.createLineBorder(c));
	}

}
