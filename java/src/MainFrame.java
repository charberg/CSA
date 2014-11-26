import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;

import javax.swing.JFrame;
import javax.swing.JPanel;


public class MainFrame extends JFrame implements ActionListener{
	
	private JPanel introPage, offPage, mySchedule; 
	
	public MainFrame(){
		setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
		setSize(1200,800);
		setTitle("Briglio Course Selector");
		
		//introPage = new IntroPage(this);
		//add(introPage);
		
		//offPage = new OffSchedule(this);
		//add(offPage);
		
		mySchedule = new MySchedule(this);
		add(mySchedule);
		
		setVisible(true);
	}
	
	public static void main(String [ ] args) {
		MainFrame main = new MainFrame();
	}

	@Override
	public void actionPerformed(ActionEvent arg0) {
		
	}
}
