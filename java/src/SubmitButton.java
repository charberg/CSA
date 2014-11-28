import javax.swing.JButton;

/** Button that is used to store ID and leading panel. */
public class SubmitButton extends JButton{
	private static final long serialVersionUID = 1L;
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
