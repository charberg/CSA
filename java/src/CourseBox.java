import java.awt.Color;
import java.awt.Dimension;

import javax.swing.*;

/** Panel containing Label and Checkbox for course selection item. Used in the OffSchedule Panel. */
public class CourseBox extends JPanel{
	/**
	 * 
	 */
	private static final long serialVersionUID = 1L;
	private String ID,term;
	private JCheckBox box;
	private int year, code;
	
	CourseBox(String name, int c, String t, int y){
		setPreferredSize(new Dimension(150,150));
		setMinimumSize(new Dimension(150,150));
		setMaximumSize(new Dimension(150,150));
		
		this.code = c;
		this.ID = name;
		this.year = y;
		this.term = t;
		
		add(new JLabel(this.getSubjectID()));
		if(this.term.equals("either")){
			add(new JLabel("Either Term"));
		}else if(this.term.equals("both")){
			add(new JLabel("Both Terms"));
		}
		this.box = new JCheckBox();
		this.add(this.box);
		this.setVisible(true);
	}

	public String getID() {
		return this.ID;
	}
	
	public int getYear(){
		return this.year;
	}
	
	public int getCode(){
		return this.code;
	}
	
	public String getTerm(){
		return this.term;
	}
	
	public String getSubjectID(){
		if(this.code == -1){
			return this.ID;
		}else{
			return this.ID + Integer.toString(this.code);
		}
	}
	
	public void setColour(Color c){
		setBorder(BorderFactory.createLineBorder(c));
	}
	
	public boolean getChecked(){
		return this.box.isSelected();
	}

}
