import javax.swing.JFrame;

/** Main Frame class that contains all frames/panels that will be used. */
public class MainFrame extends JFrame{

	private static final long serialVersionUID = 1L;
	private IntroPage introPage;
	private OffSchedule offPage; 
	private MySchedule mySchedule;
	private String FileLocation, ProgramName, Year, Term;
	
	public MainFrame(){
		setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
		setSize(1200,800);
		setTitle("Course Selection Assistant");
		
		introPage = new IntroPage(this);
		
		introPage.setup();
		setContentPane(introPage);
		setVisible(true);
	}
	
	/**
	 * Switch current content pane based off of argument string.
	 * @param panel - String that indicates which panel to switch to.
	 */
	public void panelSwitch(String panel){
		if(panel.equals("intro")){
			introPage = new IntroPage(this);		//instances are re-initialized to prevent information not refreshing
			introPage.setup();
			setContentPane(introPage);
			
		}else if(panel.equals("mysched")){
			mySchedule = new MySchedule(this);
			mySchedule.setup();
			setContentPane(mySchedule);
			
		}else if(panel.equals("offsched")){
			offPage = new OffSchedule(this);
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

	public String getProgramName() {
		return ProgramName;
	}

	public void setProgramName(String programName) {
		ProgramName = programName;
	}

	public String getYear() {
		return Year;
	}

	public void setYear(String year) {
		Year = year;
	}

	public String getTerm() {
		return Term;
	}

	public void setTerm(String term) {
		Term = term;
	}
	
	public static void main(String [ ] args) {
		@SuppressWarnings("unused")
		MainFrame main = new MainFrame();
	}
}
