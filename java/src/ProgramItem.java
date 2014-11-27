
public class ProgramItem {
	
	private String id;
	private String text;
	
	public ProgramItem(String i, String t){
		this.id = i;
		this.text = t;
	}
	
	private String getID(){
		return this.id;
	}
	
	private String getText(){
		return this.text;
	}
	
}
