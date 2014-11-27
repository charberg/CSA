import javax.swing.JButton;


public class SubmitButton extends JButton{
	
	private String id;
	private String nextPanelID;
	
	public SubmitButton(String l, String i,String np){
		super(l);
		this.id = i;
		this.nextPanelID = np;
	}

	public String getNextPanel(){
		return this.nextPanelID;
	}
	
	public String getID() {
		return id;
	}
	
	
}
