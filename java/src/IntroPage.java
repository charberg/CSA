import java.awt.Dimension;
import java.awt.Font;

import javax.swing.*;


public class IntroPage extends JFrame{
	
	private JPanel panel;
	private JLabel welcome, infoTxt;
	
	public IntroPage(){
		setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
		setSize(1200,800);
		panel = new JPanel();
		
		welcome = new JLabel("Welcome to the Briglio course selector!");
		//welcome.setPreferredSize(new Dimension(5,5));
		welcome.setFont(new Font("Calibri",1,40));
		panel.add(welcome);
		
		
		add(panel);
		setVisible(true);
	}
	
	
	
	
	public static void main(String [ ] args) {
		IntroPage main = new IntroPage();
	}
}
