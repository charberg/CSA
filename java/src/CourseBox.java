import java.awt.Color;
import java.awt.Dimension;

import javax.swing.*;


/* Label + Checkbox for course selection */
public class CourseBox extends JPanel{
	private String ID,term;
	private JCheckBox box;
	private int year, code;
	
	CourseBox(String name, int c, String t, int y){
		setPreferredSize(new Dimension(150,90));
		setMinimumSize(new Dimension(150,90));
		setMaximumSize(new Dimension(150,90));
		//setColour(Color.black);
		this.code = c;
		this.ID = name;
		this.year = y;
		this.term = t;
		
		setLayout(new BoxLayout(this, BoxLayout.PAGE_AXIS));
		add(new JLabel(this.getSubjectID()));
		if(this.term.equals("either")){
			add(new JLabel("Either Term"));
		}else if(this.term.equals("both")){
			add(new JLabel("Both Terms"));
		}
		this.box = new JCheckBox();
		add(this.box);
		setVisible(true);
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
