import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;

import javax.swing.JFrame;


public class MainFrame extends JFrame implements ActionListener{
	
	private IntroPage introPage;
	private OffSchedule offPage;
	
	public MainFrame(){
		setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
		setSize(1200,800);
		setTitle("Briglio Course Selector");
		
		introPage = new IntroPage(this);
		add(introPage);
		
		//offPage = new OffSchedule(this);
		//add(offPage);
		
		setVisible(true);
	}
	
	public static void main(String [ ] args) {
		MainFrame main = new MainFrame();
	}

	@Override
	public void actionPerformed(ActionEvent arg0) {
		
	}
}
