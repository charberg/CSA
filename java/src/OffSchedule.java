import java.awt.BorderLayout;
import java.awt.GridLayout;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
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
import org.w3c.dom.Element;
import org.w3c.dom.NamedNodeMap;
import org.w3c.dom.NodeList;
import org.w3c.dom.Node;
import org.xml.sax.InputSource;
import org.xml.sax.SAXException;


public class OffSchedule extends JPanel implements ActionListener{
	
	private MainFrame main;
	private ArrayList<CourseBox> courses;
	private ArrayList<JPanel> termPanels;
	
	public OffSchedule(MainFrame m){
		main = m;
		setLayout(new BorderLayout());
		
		add(new JLabel("Select Your Courses Taken"),BorderLayout.NORTH);
		SubmitButton submitButton = new SubmitButton("SUBMIT","offsched","none");
		submitButton.addActionListener(this);
		add(submitButton,BorderLayout.SOUTH);
		
		JPanel contentPanel = new JPanel();
		contentPanel.setLayout(new BoxLayout(contentPanel, BoxLayout.PAGE_AXIS));
		JPanel coursePanel = new JPanel();
		termPanels = new ArrayList<JPanel>();
		for(int i=0;i<8;i++){
			termPanels.add(new JPanel());
		}
		courses = new ArrayList<CourseBox>();
		
		for(int i=0;i<termPanels.size();i++){
			termPanels.get(i).setLayout(new BoxLayout(termPanels.get(i),BoxLayout.PAGE_AXIS));
			coursePanel.add(termPanels.get(i));
		}
		contentPanel.add(coursePanel);
		add(contentPanel, BorderLayout.CENTER);
		
		getCourses();
		fillTable();
		
		setVisible(true);
	}
	
	public void getCourses(){
			String prog = "SE";
		try {
			URL urlpost = new URL("http://localhost/davidweb/4504/project/CSA/php/server.php?");
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
						//System.out.println("Unknown Tag: "+attributes.item(j).getTextContent());
					}
				}
				if(year != "" & term != "" && courseID != ""){	//courseNum is not considered because electives do not have course numbers
					if(courseNum == "") courseNum = "-1";
					//System.out.println("Year: "+year+", Term: "+term+", courseID: "+courseID+", courseNum: "+courseNum);
					courses.add(new CourseBox(courseID, Integer.parseInt(courseNum),term,Integer.parseInt(year)));
				}
			}
			
			out.close();
			//in.close();
			connection.disconnect();
			
		} catch (IOException e) {
			e.printStackTrace();
		} catch (ParserConfigurationException e) {
			e.printStackTrace();
		} catch (SAXException e) {
			e.printStackTrace();
		}
		
	}
	
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
			
			String prog = "SE";
			String year = "0";
			String term = "fall";
			
			//send all courses checking checkbox states
			String message = "";
			boolean emptyList = true;
			for(int i=0;i<this.courses.size();i++){
				if(this.courses.get(i).getChecked()){
					message += "&coursesTaken[]='" + courses.get(i).getID() + ":"  +courses.get(i).getCode() + "'";
					emptyList = false;
				}
			}
			if(emptyList){
				message = "&coursesTaken[]=''";
			}
			
			try {
				URL urlpost = new URL("http://localhost/davidweb/4504/project/CSA/php/server.php?");
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
				System.out.println("Response: "+in.readLine());
				System.out.println("Response: "+in.readLine());
				
			} catch (IOException e1) {
				e1.printStackTrace();
			}	
		}
		
	}
}
