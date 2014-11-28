import java.awt.Color;
import java.awt.Dimension;

import javax.swing.*;


public class SchedBox extends JPanel{
	
	private JLabel idLabel, timeLabel, otherLabel;
	private String ID,startTime, endTime, day;
	private boolean isLabel;
	
	public SchedBox(String i, String l, String t, String d, boolean il){
		this.ID = i;
		this.day = d;
		this.isLabel = il;
		idLabel = new JLabel(l);
		add(idLabel);
		setTime(t);
		timeLabel = new JLabel(this.startTime+"-"+this.endTime);
		if(!this.isLabel) add(timeLabel);
		
		this.setPreferredSize(new Dimension(100,100));
		this.setMinimumSize(new Dimension(100,100));
		this.setMaximumSize(new Dimension(100,100));
		setBorder(BorderFactory.createLineBorder(Color.black));
		this.setVisible(true);
		
	}
	
	public String getDay(){
		return this.day;
	}
	
	public void setTime(String time){
		//System.out.println(this.ID+"- "+time);
		String[] times = time.split("-");
		this.startTime = normTime(times[0]);
		this.endTime = normTime(times[1]);
	}
	
	public void updateLabels(){
		this.remove(this.idLabel);
		this.remove(this.timeLabel);
		this.add(new JLabel(this.ID));
		if(this.isLabel) this.add(new JLabel(this.getStart()+"-"+this.getEnd()));
	}
	
	public void setID(String i){
		this.ID = i;
	}
	
	public void showTime(){
		this.add(timeLabel);
	}
	
	public void removeTime(){
		this.remove(timeLabel);
	}
	
	public String getStart(){
		return this.startTime;
	}
	
	public String getEnd(){
		return this.endTime;
	}
	
	public String getID(){
		return this.ID;
	}
	
	public void setStart(String s){
		this.startTime = s;
	}
	
	public void setEnd(String e){
		this.endTime = e;
	}
	
	public void showTime(boolean n){
		System.out.println(!n);
		this.isLabel = !n;
	}
	
	public void hide(){
		this.ID = "";
		
		this.timeLabel.setVisible(false);
		this.idLabel.setVisible(false);
	}
	
	public String normTime(String time){
		String newTime = time.trim();
		if(newTime.length() == 3){
			newTime = '0' + newTime;
		}
		if(newTime.charAt(2) == '2'){
			newTime = newTime.substring(0,2) + "30";
		}else if(newTime.charAt(2) == '5'){
			newTime = Integer.toString(Integer.parseInt(newTime.substring(0,2)) + 1) + "00";
		}else{
			newTime = newTime.substring(0,3) + '0';
		}
		return newTime;
	}
}
