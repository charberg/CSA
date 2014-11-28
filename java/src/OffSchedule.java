import java.awt.BorderLayout;
import java.awt.Font;
import java.awt.GridLayout;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.OutputStreamWriter;
import java.net.HttpURLConnection;
import java.net.URL;
import java.util.ArrayList;

import javax.swing.*;
import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.parsers.ParserConfigurationException;

import org.w3c.dom.Document;
import org.w3c.dom.NodeList;
import org.w3c.dom.Node;
import org.xml.sax.InputSource;
import org.xml.sax.SAXException;

/** Panel containing the current program tree, allowing the user to select which courses they have taken. */
public class OffSchedule extends JPanel implements ActionListener{

	private static final long serialVersionUID = 1L;
	private MainFrame main;
	private ArrayList<CourseBox> courses;
	private ArrayList<JPanel> termPanels;
	
	public OffSchedule(MainFrame m){
		main = m;
	}
	
	public void setup(){
		setLayout(new BorderLayout());
		JLabel topText = new JLabel("Select Your Courses Taken", SwingConstants.CENTER);
		topText.setFont(new Font("Calibri",1,40));
		add(topText,BorderLayout.NORTH);
		
		JPanel buttonPanel = new JPanel();
		SubmitButton submitButton = new SubmitButton("SUBMIT","offsched","none");
		submitButton.addActionListener(this);
		
		SubmitButton backButton = new SubmitButton("BACK","back","none");
		backButton.addActionListener(this);
		buttonPanel.add(backButton);
		buttonPanel.add(submitButton);
		add(buttonPanel, BorderLayout.SOUTH);
		
		JPanel coursePanel = new JPanel();
		coursePanel.setLayout(new GridLayout(0,8,10,10));
		termPanels = new ArrayList<JPanel>();
		
		for(int i=0;i<8;i++){
			termPanels.add(new JPanel());
		}
		courses = new ArrayList<CourseBox>();
		
		for(int i=0;i<termPanels.size();i++){
			termPanels.get(i).setLayout(new BoxLayout(termPanels.get(i),BoxLayout.PAGE_AXIS));
			coursePanel.add(termPanels.get(i));
		}
		JScrollPane scrollPanel = new JScrollPane(coursePanel);
		add(scrollPanel, BorderLayout.CENTER);
		
		getCourses();
		fillTable();
		
		setVisible(true);
	}
	
	/** Gets the course list from the server. */
	public void getCourses(){
			String prog = main.getProgramName();
		try {
			URL urlpost = new URL("http://localhost/CSA/php/server.php?");
			HttpURLConnection connection = (HttpURLConnection)urlpost.openConnection();
			connection.setDoOutput(true);
			connection.setDoInput(true);
			connection.setRequestMethod("POST");
			connection.setRequestProperty("Content-Type", "application/x-www-form-urlencoded");
			connection.setRequestProperty("charset", "utf-8");
			connection.connect();
			
			OutputStreamWriter out = new OutputStreamWriter(connection.getOutputStream());
			out.write("requesttype=GetPattern&program="+prog+"&source=java");
			out.flush(); //sends to server
			
			DocumentBuilderFactory dbf = DocumentBuilderFactory.newInstance();
			DocumentBuilder db = dbf.newDocumentBuilder();
			Document doc = db.parse(new InputSource(connection.getInputStream()));	//get xml response
			
			//This parsing does not work.
			Node pattern = doc.getElementsByTagName("pattern").item(0);
			NodeList items = pattern.getChildNodes();
			
			NodeList attributes;
			String year,term,courseNum,courseID;
			for(int i=0;i<items.getLength();i++){
				attributes = items.item(i).getChildNodes();
				year = "";
				term = "";
				courseNum = "";
				courseID = "";
				for(int j=0;j<attributes.getLength();j++){
					if(attributes.item(j).getNodeName().equals("subjectID")){
						courseID = attributes.item(j).getTextContent();
					}else if(attributes.item(j).getNodeName().equals("yearRequired")){
						year = attributes.item(j).getTextContent();
					}else if(attributes.item(j).getNodeName().equals("termRequired")){
						term = attributes.item(j).getTextContent();
					}else if(attributes.item(j).getNodeName().equals("courseNumber")){
						courseNum = attributes.item(j).getTextContent();
					}else{
						//unknown tag
					}
				}
				if(year != "" & term != "" && courseID != ""){	//courseNum is not considered because electives do not have course numbers
					if(courseNum == "") courseNum = "-1";
					courses.add(new CourseBox(courseID, Integer.parseInt(courseNum),term,Integer.parseInt(year)));
				}
			}
			
			out.close();
			connection.disconnect();
			
		} catch (IOException e) {
			e.printStackTrace();
		} catch (ParserConfigurationException e) {
			e.printStackTrace();
		} catch (SAXException e) {
			e.printStackTrace();
		}
		
	}
	/** Fills the panel table with the courses in the course list. */
	public void fillTable(){
		int year = 1;
		for(int i=0;i<termPanels.size();i++){
			for(int j=0;j<courses.size();j++){
				
				if(i%2 == 0 && courses.get(j).getYear() == year && (courses.get(j).getTerm().equals("fall") || courses.get(j).getTerm().equals("either") || courses.get(j).getTerm().equals("both"))){
					termPanels.get(i).add(courses.get(j));
				}else if(i%2 != 0 && courses.get(j).getYear() == year && courses.get(j).getTerm().equals("winter")){
					termPanels.get(i).add(courses.get(j));
				}
			}
			if(i%2 != 0) year++;
		}
	}

	@Override
	public void actionPerformed(ActionEvent e) {
		SubmitButton button = (SubmitButton)e.getSource();
		if(button.getID().equals("offsched")){
			
			String prog = main.getProgramName();
			String year = main.getYear();
			String term = main.getTerm();
			
			//send all courses checking checkbox states
			String message = "";
			boolean emptyList = true;
			for(int i=0;i<this.courses.size();i++){		//put all courses taken into the coursesTaken array in the message
				if(this.courses.get(i).getChecked()){
					message += "&coursesTaken[]='" + courses.get(i).getID() + ":"  +courses.get(i).getCode() + "'";
					emptyList = false;
				}
			}
			
			if(emptyList){	//check to see if coursesTaken is empty
				message = "&coursesTaken[]=''";
			}
			
			try {
				URL urlpost = new URL("http://localhost/CSA/php/server.php?");
				HttpURLConnection connection = (HttpURLConnection)urlpost.openConnection();
				connection.setDoOutput(true);
				connection.setDoInput(true);
				connection.setRequestMethod("POST");
				connection.setRequestProperty("Content-Type", "application/x-www-form-urlencoded");
				connection.setRequestProperty("charset", "utf-8");
				connection.connect();
				
				OutputStreamWriter out = new OutputStreamWriter(connection.getOutputStream());
				out.write("requesttype=OffPatternSchedule"+message+"&source=java&program="+prog+"&year="+year+"&term="+term);
				out.flush(); //sends to server
				
				BufferedReader in = new BufferedReader(new InputStreamReader(connection.getInputStream()));
				String result = in.readLine();
				
				if(result.contains("success-myschedule")){
					//change panel to myschedule
					main.setFileLocation(result.substring(result.indexOf('=')+1));	//file locaiton of schedules output
					main.panelSwitch("mysched");
				}else{
					//prompt error
					JOptionPane.showMessageDialog(this,"Could not submit courses.");
				}
				
				out.close();
				in.close();
				connection.disconnect();
				
			} catch (IOException e1) {
				e1.printStackTrace();
			}	
		}else if(button.getID().equals("back")){
			main.panelSwitch("intro");
		}
		
	}
}
