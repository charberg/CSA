import java.awt.*;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.net.*;
import java.io.*;
import javax.swing.*;


public class IntroPage extends JFrame implements ActionListener{
	
	private JPanel panel;
	private JComboBox program, yearComplete;
	private JRadioButton onsched, offsched;
	private ButtonGroup schedButtons;
	
	public IntroPage(){
		setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
		setSize(1200,800);
		setTitle("Briglio Course Selector");
		panel = new JPanel();
		
		JLabel welcome = new JLabel("Welcome to the Briglio course selector!");
		welcome.setFont(new Font("Calibri",1,40));
		JPanel mainPanel = new JPanel();
		mainPanel.setLayout(new BorderLayout());
		mainPanel.add(welcome,BorderLayout.NORTH);
		add(mainPanel);
		
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
			gc.gridy = 5;
			JButton submit = new JButton("SUBMIT");
			submit.addActionListener(this);
			panel.add(submit,gc);
		}else{
			gc.gridx = 0;
			gc.gridy = 0;
			panel.add(new JLabel("Error encountered while connecting to the server."),gc);
		}
		mainPanel.add(panel,BorderLayout.CENTER);
		setVisible(true);
	}

	@Override
	public void actionPerformed(ActionEvent arg0) {
		JButton source = (JButton)arg0.getSource();
		System.out.println(source.getText());
		
	}
	
	public boolean fillPrograms(){
		try{
			URL urlpost = new URL("http://localhost/davidweb/4504/project/CSA/php/server.php?");
			URLConnection connection = urlpost.openConnection();
			connection.setDoOutput(true);
			OutputStreamWriter out = new OutputStreamWriter(connection.getOutputStream());
			out.write("requesttype=GetPrograms");
			out.flush();
			BufferedReader in = new BufferedReader(new InputStreamReader(connection.getInputStream()));
			String text;
			
			text = in.readLine();
			System.out.println(text);
			text = in.readLine();
			System.out.println(text);
			
			
			out.close();
			in.close();
			return true;
		}catch(Exception ion){}
		return false;
	}
	
	public static void main(String [ ] args) {
		IntroPage main = new IntroPage();
	}
}
