import static org.junit.Assert.*;

import org.junit.Before;
import org.junit.Test;


public class SchedBoxTest {

	private SchedBox box;
	
	@Before
	public void setUp() throws Exception {
		box = new SchedBox("0800","test","1005-1125",false);
	}

	@Test
	public void testTime() {
		assertEquals("start time","1000",box.getStart());
		assertEquals("end time","1130",box.getEnd());
	}

}
