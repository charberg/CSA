import javax.swing.*;


public class SchedBox extends JPanel{
	
	private JLabel label;
	
	public SchedBox(String l){
		this.label = new JLabel(l);
		this.add(this.label);
		setSize(100,100);
		setVisible(true);
	}
}
