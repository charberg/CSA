import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;

import javax.swing.JFrame;
import javax.swing.JPanel;


public class MainFrame extends JFrame{
	
	private IntroPage introPage;
	private OffSchedule offPage; 
	private MySchedule mySchedule;
	private String FileLocation, ProgramName, Year, Term;
	
	public MainFrame(){
		setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
		setSize(1200,800);
		setTitle("Briglio Course Selector");
		
		introPage = new IntroPage(this);
		offPage = new OffSchedule(this);
		mySchedule = new MySchedule(this);
		
		introPage.setup();
		setContentPane(introPage);
		setVisible(true);
	}
	
	public static void main(String [ ] args) {
		MainFrame main = new MainFrame();
	}

	public void panelSwitch(String panel){
		if(panel.equals("intro")){
			//introPage = new IntroPage(this);
			introPage.setup();
			setContentPane(introPage);
			
		}else if(panel.equals("mysched")){
			//mySchedule = new MySchedule(this);
			mySchedule.setup();
			setContentPane(mySchedule);
			
		}else if(panel.equals("offsched")){
			//offPage = new OffSchedule(this);
			offPage.setup();
			setContentPane(offPage);
			
		}
		setVisible(true);
	}
	
	public void setFileLocation(String fl){
		this.FileLocation = fl;
	}
	
	public String getFileLocation(){
		return this.FileLocation;
	}
}
