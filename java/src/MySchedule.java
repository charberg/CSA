import java.awt.BorderLayout;
import java.awt.GridLayout;
import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.OutputStreamWriter;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.URL;
import java.util.ArrayList;

import javax.swing.*;
import javax.swing.table.DefaultTableModel;
import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.parsers.ParserConfigurationException;

import org.w3c.dom.Document;
import org.xml.sax.InputSource;
import org.xml.sax.SAXException;
import org.w3c.dom.NamedNodeMap;
import org.w3c.dom.NodeList;
import org.w3c.dom.Node;

public class MySchedule extends JPanel{
	
	private MainFrame main;
	private JComboBox options;
	private int schedNum;
	private SchedBox[][] table;
	
	public MySchedule(MainFrame m){
		main = m;
		setLayout(new BorderLayout());
		
		JPanel topPanel = new JPanel();
		JLabel topLabel = new JLabel("My Schedule");
		topPanel.setLayout(new BoxLayout(topPanel, BoxLayout.PAGE_AXIS));
		topPanel.add(topLabel);
		
		JPanel selectPanel = new JPanel();
		selectPanel.add(new JLabel("Your Options: "));
		options = new JComboBox();
		selectPanel.add(options);
		JButton selectButton = new JButton("SELECT");
		selectPanel.add(selectButton);
		topPanel.add(selectPanel);
		add(topPanel, BorderLayout.NORTH);
		
		JPanel timePanel = new JPanel();
		timePanel.setLayout(new GridLayout(0,8,10,10));
		
		JScrollPane scrollPanel = new JScrollPane(timePanel);
		
		//Put column names into the panel
		timePanel.add(new JLabel("Time"));
		timePanel.add(new JLabel("Sunday"));
		timePanel.add(new JLabel("Monday"));
		timePanel.add(new JLabel("Tuesday"));
		timePanel.add(new JLabel("Wednesday"));
		timePanel.add(new JLabel("Thursday"));
		timePanel.add(new JLabel("Friday"));
		timePanel.add(new JLabel("Saturday"));
		
		table = new SchedBox[7][28];
		String extra = "";
		int j=8;
		for(int i=0;i<28;i++){	//28 goes until 22:00
			if(i<4){
				extra = "0";
			}else{
				extra = "";
			}
			//System.out.println(i);
			
			//Create schedule cells to go into the grid, and put them into an array
			timePanel.add(new SchedBox(extra+Integer.toString(j)+"00", extra+Integer.toString(j)+":00", "0000-0000",true));
			table[0][i] = new SchedBox("sun"+extra+Integer.toString(j)+"00", "0,"+i, extra+Integer.toString(j)+"00-"+extra+Integer.toString(j)+"30",true);
			table[1][i] = new SchedBox("mon"+extra+Integer.toString(j)+"00", "1,"+i, extra+Integer.toString(j)+"00-"+extra+Integer.toString(j)+"30",true);
			table[2][i] = new SchedBox("tues"+extra+Integer.toString(j)+"00", "2,"+i, extra+Integer.toString(j)+"00-"+extra+Integer.toString(j)+"30",true);
			table[3][i] = new SchedBox("wed"+extra+Integer.toString(j)+"00", "3,"+i, extra+Integer.toString(j)+"00-"+extra+Integer.toString(j)+"30",true);
			table[4][i] = new SchedBox("thurs"+extra+Integer.toString(j)+"00", "4,"+i, extra+Integer.toString(j)+"00-"+extra+Integer.toString(j)+"30",true);
			table[5][i] = new SchedBox("fri"+extra+Integer.toString(j)+"00", "5,"+i, extra+Integer.toString(j)+"00-"+extra+Integer.toString(j)+"30",true);
			table[6][i] = new SchedBox("sat"+extra+Integer.toString(j)+"00", "6,"+i, extra+Integer.toString(j)+"00-"+extra+Integer.toString(j)+"30",true);
			//Add the cells to the grid
			timePanel.add(table[0][i]);
			timePanel.add(table[1][i]);
			timePanel.add(table[2][i]);
			timePanel.add(table[3][i]);
			timePanel.add(table[4][i]);
			timePanel.add(table[5][i]);
			timePanel.add(table[6][i]);
			i++;
			
			timePanel.add(new SchedBox(extra+Integer.toString(j)+"30", extra+Integer.toString(j)+":30", "0000-0000",true));
			table[0][i] = new SchedBox("sun"+extra+Integer.toString(j)+"30", "0,"+i, extra+Integer.toString(j)+"30-"+extra+Integer.toString(j)+"00",true);
			table[1][i] = new SchedBox("mon"+extra+Integer.toString(j)+"30", "1,"+i, extra+Integer.toString(j)+"30-"+extra+Integer.toString(j)+"00",true);
			table[2][i] = new SchedBox("tues"+extra+Integer.toString(j)+"30", "2,"+i, extra+Integer.toString(j)+"30-"+extra+Integer.toString(j)+"00",true);
			table[3][i] = new SchedBox("wed"+extra+Integer.toString(j)+"30", "3,"+i, extra+Integer.toString(j)+"30-"+extra+Integer.toString(j)+"00",true);
			table[4][i] = new SchedBox("thurs"+extra+Integer.toString(j)+"30", "4"+i, extra+Integer.toString(j)+"30-"+extra+Integer.toString(j)+"00",true);
			table[5][i] = new SchedBox("fri"+extra+Integer.toString(j)+"30", "5"+i, extra+Integer.toString(j)+"30-"+extra+Integer.toString(j)+"00",true);
			table[6][i] = new SchedBox("sat"+extra+Integer.toString(j)+"30", "6"+i, extra+Integer.toString(j)+"30-"+extra+Integer.toString(j)+"00",true);
			timePanel.add(table[0][i]);
			timePanel.add(table[1][i]);
			timePanel.add(table[2][i]);
			timePanel.add(table[3][i]);
			timePanel.add(table[4][i]);
			timePanel.add(table[5][i]);
			timePanel.add(table[6][i]);
			
			j++;

		}
		//timePanel.setVisible(true);
		//table[2][1].setID("TEST");
		
		add(scrollPanel, BorderLayout.CENTER);
		
		SubmitButton submitButton = new SubmitButton("PICK THIS SCHEDULE","mysched","none");
		add(submitButton, BorderLayout.SOUTH);
		
		getSchedules();
		
		setVisible(true); 
		
	}
	
	public void getSchedules(){
		try {
			URL urlpost = new URL("http://localhost/davidweb/4504/project/CSA/php/server.php?");
			HttpURLConnection connection = (HttpURLConnection)urlpost.openConnection();
			connection.setDoOutput(true);
			connection.setDoInput(true);
			connection.setRequestMethod("POST");
			connection.setRequestProperty("Content-Type", "application/x-www-form-urlencoded");
			connection.setRequestProperty("charset", "utf-8");
			connection.connect();
			
			String fileName = "../tempSchedules/testfile.txt";
			//System.out.println("sending");
			OutputStreamWriter out = new OutputStreamWriter(connection.getOutputStream());
			out.write("requesttype=GetCourseFile&fileName="+fileName+"&source=java");
			out.flush(); //sends to server
			
			DocumentBuilderFactory dbf = DocumentBuilderFactory.newInstance();
			DocumentBuilder db = dbf.newDocumentBuilder();
			Document doc = db.parse(new InputSource(connection.getInputStream()));	//get xml response
			
			NodeList schedules = doc.getElementsByTagName("schedules").item(0).getChildNodes();
			if(schedules.getLength() == 0){
				System.out.println("No schedules found.");
			}else{
				for(int i=0;i<schedules.getLength();i++){
					options.addItem(i+1);
				}
				schedNum = 0;
				setSchedule(schedNum, schedules);
			}
			
			out.close();
			connection.disconnect();
			
		} catch (MalformedURLException e) {
			e.printStackTrace();
		} catch (IOException e) {
			e.printStackTrace();
		} catch (ParserConfigurationException e) {
			e.printStackTrace();
		} catch (SAXException e) {
			e.printStackTrace();
		}
		
		
	}
	
	public void setSchedule(int index, NodeList scheduleList){
		if(scheduleList.item(index) == null){
			System.out.println("ERROR: Invalid schedule.");
		}
		NodeList courses = scheduleList.item(index).getChildNodes();
		NodeList attributes;
		
		String time, subjectID, code, section, number, days;
		SchedBox temp;
		//Go through each course
		for(int i=0;i<courses.getLength();i++){
			time = "";
			subjectID = "";
			code = "";
			section = "";
			number = "";
			days = "";
			
			attributes = courses.item(i).getChildNodes();
			
			//System.out.println(attributes.item(0).getTextContent());
			//Get all course information
			//System.out.println(courses.item(i).getNodeName());
			for(int j=0;j<attributes.getLength();j++){
				//System.out.println(attributes.item(j).getNodeName());
				if(attributes.item(j).getNodeName().equals("subjectID")){
					subjectID = attributes.item(j).getTextContent();
					//System.out.println(subjectID);
				}else if(attributes.item(j).getNodeName().equals("time")){
					time = attributes.item(j).getTextContent();
				}else if(attributes.item(j).getNodeName().equals("courseNum")){
					number = attributes.item(j).getTextContent();
				}else if(attributes.item(j).getNodeName().equals("scheduleCode")){
					code = attributes.item(j).getTextContent();
				}else if(attributes.item(j).getNodeName().equals("sectionCode")){
					section = attributes.item(j).getTextContent();
				}else if(attributes.item(j).getNodeName().equals("days")){
					days = attributes.item(j).getTextContent();
				}
			}
			
			if(subjectID != "" && time != ""){
				temp = new SchedBox(subjectID+number+section,subjectID+number+section,time,false);
				String []day = days.split("");
				for(int n=0;n<7;n++){
					for(int m=0;m<28;m++){
						System.out.println(table[n][m].getStart()+" - time: "+temp.getStart());
						if(table[n][m].getStart().equals(temp.getStart())){
							table[n][m].setEnd(temp.getEnd());
							table[n][m].setID(temp.getID());
							table[n][m].updateLabels();
						}
					}
				}
			}
			
		}
		
	}
	
}
