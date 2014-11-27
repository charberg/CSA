import java.awt.Color;
import java.awt.Dimension;

import javax.swing.*;


/* Label + Checkbox for course selection */
public class CourseBox extends JPanel{
	private String ID,term;
	private JLabel label;
	private JCheckBox box;
	private int year, code;
	
	CourseBox(String name, int c, String t, int y){
		//setPreferredSize(new Dimension(90,90));
		//setColour(Color.black);
		this.code = c;
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
	
	public String getSubjectID(){
		return this.ID + Integer.toString(this.code);
	}
	
	public void setColour(Color c){
		setBorder(BorderFactory.createLineBorder(c));
	}

}
