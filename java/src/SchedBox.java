import java.awt.Dimension;

import javax.swing.*;


public class SchedBox extends JPanel{
	
	private JLabel label;
	
	public SchedBox(String l){
		this.label = new JLabel(l);
		this.add(this.label);
		this.setSize(100,100);
		this.setVisible(true);
		this.setPreferredSize(new Dimension(100,100));
		this.setMinimumSize(new Dimension(100,100));
		this.setMaximumSize(new Dimension(100,100));
	}
}
