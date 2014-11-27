import java.awt.*;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.net.*;
import java.io.*;

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

public class IntroPage extends JPanel implements ActionListener{
	
	private JComboBox program, yearComplete;
	private JRadioButton onsched, offsched, fallTerm, winterTerm;
	private ButtonGroup schedButtons, termButtons;
	private MainFrame main;
	
	public IntroPage(MainFrame m){
		main = m;
		JLabel welcome = new JLabel("Welcome to the Briglio course selector!");
		welcome.setFont(new Font("Calibri",1,40));
		JPanel panel = new JPanel();
		setLayout(new BorderLayout());
		add(welcome,BorderLayout.NORTH);
		
		panel.setLayout(new GridBagLayout());
		GridBagConstraints gc = new GridBagConstraints();
		gc.fill = GridBagConstraints.HORIZONTAL;
		gc.insets = new Insets(2,2,2,2);
		program = new JComboBox();
		
		if(fillPrograms()){
			gc.gridx = 0;
			gc.gridy = 1;
			JLabel infoTxt = new JLabel("Please enter your information:");
			infoTxt.setFont(new Font("Calibri",1,22));
			panel.add(infoTxt,gc);
			
			gc.gridx = 0;
			gc.gridy = 2;
			gc.gridwidth = 0;
			panel.add(new JLabel("Stream:"),gc);
			gc.gridx = 1;
			panel.add(program,gc);
			
			gc.gridx = 0;
			gc.gridy = 3;
			panel.add(new JLabel("Total number of years completed:"),gc);
			gc.gridx = 1;
			String[] years = {"1","2","3"};
			yearComplete = new JComboBox(years);
			panel.add(yearComplete,gc);
			
			gc.gridx = 0;
			gc.gridy = 4;
			panel.add(new JLabel("What term are you planning?"),gc);
			gc.gridx = 1;
			fallTerm = new JRadioButton("Fall",true);
			winterTerm = new JRadioButton("Winter",false);
			termButtons = new ButtonGroup();
			termButtons.add(fallTerm);
			termButtons.add(winterTerm);
			gc.gridwidth = 1;
			panel.add(fallTerm,gc);
			gc.gridx = 2;
			panel.add(winterTerm,gc);
			
			gc.gridx = 0;
			gc.gridy = 5;
			panel.add(new JLabel("Are you on schedule?"),gc);
			gc.gridx = 1;
			onsched = new JRadioButton("ON",true);
			offsched = new JRadioButton("OFF",false);
			schedButtons = new ButtonGroup();
			schedButtons.add(onsched);
			schedButtons.add(offsched);
			gc.gridwidth = 1;
			panel.add(onsched,gc);
			gc.gridx = 2;
			panel.add(offsched,gc);
			
			gc.gridx = 0;
			gc.gridy = 6;
			SubmitButton submit = new SubmitButton("SUBMIT","introInfo","none");
			submit.addActionListener(this);
			panel.add(submit,gc);
		}else{
			gc.gridx = 0;
			gc.gridy = 0;
			panel.add(new JLabel("Error encountered while connecting to the server."),gc);
		}
		add(panel,BorderLayout.CENTER);
		setVisible(true);
	}

	@Override
	public void actionPerformed(ActionEvent arg0) {
		SubmitButton source = (SubmitButton)arg0.getSource();
		if(source.getId() == "introInfo"){
			//get all info and send to server
			String prog;
			if((String)this.program.getSelectedItem() == "Computer Systems Engineering"){
				prog = "CSE";
			}else{
				prog = "SE";
			}
			
			String year = (String)this.yearComplete.getSelectedItem();
			
			String onoff;
			if(onsched.isSelected()){
				onoff = "on";
			}else{
				onoff = "off";
			}
			
			String term;
			if(fallTerm.isSelected()){
				term = "fall";
			}else{
				term = "winter";
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
				out.write("requesttype=SubmitInfo&requesttype=SubmitInfo&sched="+onoff+"&term="+term+"&yearCompleted="+year+"&programName="+prog);
				out.flush(); //sends to server
				
				BufferedReader in = new BufferedReader(new InputStreamReader(connection.getInputStream()));
				
				System.out.println("Response: "+in.readLine());
				System.out.println("Response: "+in.readLine());
				//System.out.println("&requesttype=SubmitInfo&sched="+onoff+"&term="+term+"&yearCompleted="+year+"&programName="+prog);
				
			} catch (MalformedURLException e) {
				e.printStackTrace();
			} catch (IOException e) {
				e.printStackTrace();
			}
		}
	}
	
	public boolean fillPrograms(){
		try{
			URL urlpost = new URL("http://localhost/davidweb/4504/project/CSA/php/server.php?");
			HttpURLConnection connection = (HttpURLConnection)urlpost.openConnection();
			connection.setDoOutput(true);
			
			OutputStreamWriter out = new OutputStreamWriter(connection.getOutputStream());
			out.write("requesttype=GetPrograms");
			out.flush(); //sends to server
			
			DocumentBuilderFactory dbf = DocumentBuilderFactory.newInstance();
			DocumentBuilder db = dbf.newDocumentBuilder();
			Document doc = db.parse(new InputSource(connection.getInputStream()));	//get xml response
			
			NodeList programs = doc.getElementsByTagName("programs");
			NodeList programList = programs.item(0).getChildNodes();	//program
			NodeList itemTags;
			
			for(int i=0;i<programList.getLength();i++){
				//System.out.println(programList.item(i).getNodeName());
				itemTags = programList.item(i).getChildNodes();
				System.out.println(itemTags.item(2).getTextContent());
				program.addItem(itemTags.item(2).getTextContent());	//make a subclass to go in here
				//program.addItem(new ProgramItem(itemTags.item(1).getTextContent(),itemTags.item(2).getTextContent()),itemTags.item(2).getTextContent());
			}
			
			out.close();
			
			return true;
		}catch(Exception ion){}
		return false;
	}
	
}
