import java.awt.BorderLayout;
import java.awt.GridLayout;
import java.io.IOException;
import java.io.OutputStreamWriter;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.URL;
import java.util.ArrayList;

import javax.swing.*;
import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.parsers.ParserConfigurationException;

import org.w3c.dom.Document;
import org.w3c.dom.NamedNodeMap;
import org.w3c.dom.NodeList;
import org.w3c.dom.Node;
import org.xml.sax.InputSource;
import org.xml.sax.SAXException;


public class OffSchedule extends JPanel{
	
	private MainFrame main;
	private ArrayList<CourseBox> courses;
	private ArrayList<JPanel> termPanels;
	
	public OffSchedule(MainFrame m){
		main = m;
		setLayout( new BorderLayout());
		
		add(new JLabel("Select Your Courses Taken"),BorderLayout.NORTH);
		SubmitButton submitButton = new SubmitButton("SUBMIT","offsched","none");
		add(submitButton,BorderLayout.SOUTH);
		
		JPanel coursePanel = new JPanel();
		termPanels = new ArrayList<JPanel>();
		courses = new ArrayList<CourseBox>();
		
		for(int i=0;i<termPanels.size();i++){
			termPanels.get(i).setLayout(new BoxLayout(coursePanel,BoxLayout.PAGE_AXIS));
			coursePanel.add(termPanels.get(i));
		}
		
		courses.add(new CourseBox("SYSC", 1005, "fall", 1));
		courses.add(new CourseBox("SYSC", 2004, "winter", 1));
		
		
		add(coursePanel, BorderLayout.CENTER);
		
		fillTable();
		
		setVisible(true);
	}
	
	public void fillTable(){
			String prog = "SE";
		try {
			URL urlpost = new URL("http://localhost/davidweb/4504/project/CSA/php/server.php?");
			HttpURLConnection connection = (HttpURLConnection)urlpost.openConnection();
			connection.setDoOutput(true);
			
			OutputStreamWriter out = new OutputStreamWriter(connection.getOutputStream());
			out.write("requesttype=GetPattern&"+prog);
			out.flush(); //sends to server
			
			DocumentBuilderFactory dbf = DocumentBuilderFactory.newInstance();
			DocumentBuilder db = dbf.newDocumentBuilder();
			Document doc = db.parse(new InputSource(connection.getInputStream()));	//get xml response
			
			//for(int i=0;i<)
			
			
		} catch (IOException e) {
			e.printStackTrace();
		} catch (ParserConfigurationException e) {
			e.printStackTrace();
		} catch (SAXException e) {
			e.printStackTrace();
		}
		
	}
}
