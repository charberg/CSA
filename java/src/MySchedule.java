import java.awt.BorderLayout;
import java.awt.Font;
import java.awt.GridLayout;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.OutputStreamWriter;
import java.io.StringWriter;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.URL;

import javax.swing.*;
import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.parsers.ParserConfigurationException;
import javax.xml.transform.Transformer;
import javax.xml.transform.TransformerConfigurationException;
import javax.xml.transform.TransformerException;
import javax.xml.transform.TransformerFactory;
import javax.xml.transform.TransformerFactoryConfigurationError;
import javax.xml.transform.dom.DOMSource;
import javax.xml.transform.stream.StreamResult;

import org.w3c.dom.Document;
import org.xml.sax.InputSource;
import org.xml.sax.SAXException;
import org.w3c.dom.NodeList;

/** Panel allowing the user to see and select a generated schedule. */
@SuppressWarnings("serial")
public class MySchedule extends JPanel implements ActionListener{
	
	private MainFrame main;
	private JComboBox options;
	private int schedNum;
	private SchedBox[][] table;
	private NodeList GlobalSchedules;
	
	public MySchedule(MainFrame m){
		main = m;
	}
	
	public void setup(){
		
		setLayout(new BorderLayout());
		
		JPanel topPanel = new JPanel();
		JLabel topLabel = new JLabel("My Schedule", SwingConstants.CENTER);
		topLabel.setFont(new Font("Calibri",1,30));
		topPanel.setLayout(new BoxLayout(topPanel, BoxLayout.PAGE_AXIS));
		topPanel.add(topLabel, SwingConstants.CENTER);
		
		JPanel selectPanel = new JPanel();
		selectPanel.add(new JLabel("Your Options: "));
		options = new JComboBox();						//Contains all of the schedule indexes
		selectPanel.add(options);
		SubmitButton selectButton = new SubmitButton("SELECT", "select",null);
		selectButton.addActionListener(this);
		selectPanel.add(selectButton);					//Chooses schedule to load from the combination box
		topPanel.add(selectPanel);
		add(topPanel, BorderLayout.NORTH);
		
		JPanel timePanel = new JPanel();
		timePanel.setLayout(new GridLayout(0,8,10,10));
		
		JScrollPane scrollPanel = new JScrollPane(timePanel);	//Make the schedule scrollable
		
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
		//Fills grid with appropriate cells/panels
		for(int i=0;i<28;i++){	//28 goes until 22:00
			if(i<4){
				extra = "0";
			}else{
				extra = "";
			}
			
			//Create schedule cells to go into the grid, and put them into an array
			timePanel.add(new SchedBox(extra+Integer.toString(j)+"00", extra+Integer.toString(j)+":00", "0000-0000", "x", true));
			table[0][i] = new SchedBox("sun"+extra+Integer.toString(j)+"00", "", extra+Integer.toString(j)+"00-"+extra+Integer.toString(j)+"30", "sun", true);
			table[1][i] = new SchedBox("mon"+extra+Integer.toString(j)+"00", "", extra+Integer.toString(j)+"00-"+extra+Integer.toString(j)+"30", "mon", true);
			table[2][i] = new SchedBox("tues"+extra+Integer.toString(j)+"00", "", extra+Integer.toString(j)+"00-"+extra+Integer.toString(j)+"30", "tues", true);
			table[3][i] = new SchedBox("wed"+extra+Integer.toString(j)+"00", "", extra+Integer.toString(j)+"00-"+extra+Integer.toString(j)+"30", "wed", true);
			table[4][i] = new SchedBox("thurs"+extra+Integer.toString(j)+"00", "", extra+Integer.toString(j)+"00-"+extra+Integer.toString(j)+"30", "thurs", true);
			table[5][i] = new SchedBox("fri"+extra+Integer.toString(j)+"00", "", extra+Integer.toString(j)+"00-"+extra+Integer.toString(j)+"30", "fri", true);
			table[6][i] = new SchedBox("sat"+extra+Integer.toString(j)+"00", "", extra+Integer.toString(j)+"00-"+extra+Integer.toString(j)+"30", "sun", true);
			//Add the cells to the grid
			timePanel.add(table[0][i]);
			timePanel.add(table[1][i]);
			timePanel.add(table[2][i]);
			timePanel.add(table[3][i]);
			timePanel.add(table[4][i]);
			timePanel.add(table[5][i]);
			timePanel.add(table[6][i]);
			
			i++;
			
			//Half hour
			timePanel.add(new SchedBox(extra+Integer.toString(j)+"30", extra+Integer.toString(j)+":30", "0000-0000","x",true));
			table[0][i] = new SchedBox("sun"+extra+Integer.toString(j)+"30", "", extra+Integer.toString(j)+"30-"+extra+Integer.toString(j)+"00", "sun",true);
			table[1][i] = new SchedBox("mon"+extra+Integer.toString(j)+"30", "", extra+Integer.toString(j)+"30-"+extra+Integer.toString(j)+"00", "mon",true);
			table[2][i] = new SchedBox("tues"+extra+Integer.toString(j)+"30", "", extra+Integer.toString(j)+"30-"+extra+Integer.toString(j)+"00", "tues",true);
			table[3][i] = new SchedBox("wed"+extra+Integer.toString(j)+"30", "", extra+Integer.toString(j)+"30-"+extra+Integer.toString(j)+"00", "wed",true);
			table[4][i] = new SchedBox("thurs"+extra+Integer.toString(j)+"30", "", extra+Integer.toString(j)+"30-"+extra+Integer.toString(j)+"00", "thurs", true);
			table[5][i] = new SchedBox("fri"+extra+Integer.toString(j)+"30", "", extra+Integer.toString(j)+"30-"+extra+Integer.toString(j)+"00", "fri", true);
			table[6][i] = new SchedBox("sat"+extra+Integer.toString(j)+"30", "", extra+Integer.toString(j)+"30-"+extra+Integer.toString(j)+"00", "sat", true);
			timePanel.add(table[0][i]);
			timePanel.add(table[1][i]);
			timePanel.add(table[2][i]);
			timePanel.add(table[3][i]);
			timePanel.add(table[4][i]);
			timePanel.add(table[5][i]);
			timePanel.add(table[6][i]);
			
			j++;

		}
		
		add(scrollPanel, BorderLayout.CENTER);
		
		JPanel buttonPanel = new JPanel();
		SubmitButton homeButton = new SubmitButton("HOME","home","none");
		homeButton.addActionListener(this);
		SubmitButton submitButton = new SubmitButton("PICK THIS SCHEDULE","mysched","none");
		submitButton.addActionListener(this);
		buttonPanel.add(homeButton);
		buttonPanel.add(submitButton);
		add(buttonPanel, BorderLayout.SOUTH);
		
		getSchedules();
		
		setVisible(true); 
		
	}
	
	/** Sends request to the server and receives all schedules compatible with previous data. */ 
	public void getSchedules(){
		try {
			//Send request to server
			URL urlpost = new URL("http://localhost/CSA/php/server.php?");
			HttpURLConnection connection = (HttpURLConnection)urlpost.openConnection();
			connection.setDoOutput(true);
			connection.setDoInput(true);
			connection.setRequestMethod("POST");
			connection.setRequestProperty("Content-Type", "application/x-www-form-urlencoded");
			connection.setRequestProperty("charset", "utf-8");
			connection.connect();
			
			
			String fileName = main.getFileLocation().trim();
			
			OutputStreamWriter out = new OutputStreamWriter(connection.getOutputStream());
			out.write("requesttype=GetCourseFile&fileName="+fileName+"&source=java");
			out.flush(); //sends to server
			
			DocumentBuilderFactory dbf = DocumentBuilderFactory.newInstance();
			DocumentBuilder db = dbf.newDocumentBuilder();
			Document doc = db.parse(new InputSource(connection.getInputStream()));	//get xml response
			
			GlobalSchedules = doc.getElementsByTagName("schedules").item(0).getChildNodes();
			if(GlobalSchedules.getLength() == 0){
				//prompt error
				JOptionPane.showMessageDialog(this,"There are no matching schedules.");
				//main.panelSwitch("intro");
			}else{
				for(int i=0;i<GlobalSchedules.getLength();i++){	//Loads indecies of schedules into combination box based off of xml
					options.addItem(i+1);
				}
				schedNum = 0;
				setSchedule(schedNum);			//fills the table with the first schedule
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
	
	/** Sets the panel grid with the current schedule items*/
	public void setSchedule(int index){
		
		NodeList scheduleList = GlobalSchedules;
		if(scheduleList.item(index) == null){
			//prompt error
			JOptionPane.showMessageDialog(this,"This schedule is invalid.");
		}
		NodeList courses = scheduleList.item(index).getChildNodes();
		NodeList attributes;
		
		String time, subjectID, section, number, days, compDay, currentTime;	//temporary variables used in the loop
		SchedBox temp;
		String []day;
		//Go through each course
		for(int i=0;i<courses.getLength();i++){
			time = "";
			subjectID = "";
			section = "";
			number = "";
			days = "";
			
			attributes = courses.item(i).getChildNodes();
			
			//Get all course information
			for(int j=0;j<attributes.getLength();j++){
				//Save all attributes
				if(attributes.item(j).getNodeName().equals("subjectID")){
					subjectID = attributes.item(j).getTextContent();
					
				}else if(attributes.item(j).getNodeName().equals("time")){
					time = attributes.item(j).getTextContent();
					
				}else if(attributes.item(j).getNodeName().equals("courseNum")){
					number = attributes.item(j).getTextContent();
					
				}else if(attributes.item(j).getNodeName().equals("sectionCode")){
					section = attributes.item(j).getTextContent();
					
				}else if(attributes.item(j).getNodeName().equals("days")){
					days = attributes.item(j).getTextContent();
					
				}
			}
			//If a complete course was received, put it on the grid
			if(subjectID != "" && time != ""){
				temp = new SchedBox(" ",subjectID+number+section,time,"X",false);
				day = days.split("");	//split all characters in the days attribute
				compDay = "";
				currentTime = "";
				int mod = 0;	//add to table for multiple cells (more than half hour)
				for(int ds = 0;ds < days.length();ds++){
					//interpret days course is on
					switch(day[ds]){
						case "M":
							compDay = "mon";
							break;
						case "T":
							compDay = "tues";
							break;
						case "W":
							compDay = "wed";
							break;
						case "R":
							compDay = "thurs";
							break;
						case "F":
							compDay = "fri";
							break;
						case "S":
							compDay = "sat";
							break;
						case "U":
							compDay = "sun";
							break;
						default:
							//none
					}
					//loop over each day and add the course to the grid in the appropriate time slots
					for(int n=0;n<7;n++){
						for(int m=0;m<28;m++){
							currentTime = temp.getStart();
							if(table[n][m].getDay().equals(compDay) && table[n][m].getID().equals(compDay+currentTime)){
								//Continue through the schedule until the end time of the course has been reached
								while(!currentTime.equals(temp.getEnd())){
									table[n][m+mod].removeAll();
									table[n][m+mod].setStart(temp.getStart());
									table[n][m+mod].setEnd(temp.getEnd());
									table[n][m+mod].setName(temp.getName());
									table[n][m+mod].updateLabels();
									currentTime = incTime(currentTime);
									mod++;
								}
								mod = 0;
								break;
							}
						}
					}
				}
			}
			
		}
		
	}
	
	/** Increments 'time' string by half hours. */
	public String incTime(String time){
		
		String thisTime = time;
		if(thisTime.substring(2).equals("00")){			//if on the hour
			thisTime = thisTime.substring(0,2) + "30";
			
		}else{											//if on the half hour
			thisTime = Integer.toString(Integer.parseInt(thisTime.substring(0,2)) + 1) + "00";
			if(thisTime.length() == 3) thisTime = '0' + thisTime;
			
		}
		return thisTime;
	}

	/** Clears the grid on the main panel. */
	public void clearGrid(){
		for(int i=0;i<7;i++){
			for(int j=0;j<28;j++){
				table[i][j].removeAll();
			}
		}
	}
	
	
	@Override
	public void actionPerformed(ActionEvent arg0) {
		SubmitButton button = (SubmitButton)arg0.getSource();
		if(button.getID().equals("select")){
			clearGrid();
			schedNum = options.getSelectedIndex();
			setSchedule(schedNum);
			updateUI();
		}else if(button.getID().equals("home")){
			main.panelSwitch("intro");
		}else if(button.getID().equals("mysched")){
			//Send request to server
			try {
				URL urlpost = new URL("http://localhost/CSA/php/courseServer.php?");
				HttpURLConnection connection = (HttpURLConnection)urlpost.openConnection();
				connection.setDoOutput(true);
				connection.setDoInput(true);
				connection.setRequestMethod("POST");
				connection.setRequestProperty("Content-Type", "application/x-www-form-urlencoded");
				connection.setRequestProperty("charset", "utf-8");
				connection.connect();
				
				//Convert schedule back into xml to send back to the server
				StringWriter strw = new StringWriter();
				Transformer trans = TransformerFactory.newInstance().newTransformer();
				trans.transform(new DOMSource(GlobalSchedules.item(schedNum)), new StreamResult(strw));
				
				OutputStreamWriter out = new OutputStreamWriter(connection.getOutputStream());
				out.write("source=java&xml="+strw.toString());
				out.flush(); //sends to server
				
				BufferedReader in = new BufferedReader(new InputStreamReader(connection.getInputStream()));
				if(in.readLine().equals("PASS")){
					//redirect to intro and prompt
					JOptionPane.showMessageDialog(this,"Your schedule has been registered.");
					main.panelSwitch("intro");
				}else{
					//prompt error
					JOptionPane.showMessageDialog(this,"Could not properly register schedule.");
				}
				
				in.close();
				out.close();
				connection.disconnect();
				
				
			} catch (IOException e) {
				e.printStackTrace();
			} catch (TransformerConfigurationException e) {
				e.printStackTrace();
			} catch (TransformerFactoryConfigurationError e) {
				e.printStackTrace();
			} catch (TransformerException e) {
				e.printStackTrace();
			}
			
		}
	}
	
}
