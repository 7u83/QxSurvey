<?php




class QxImage extends Imagick
{

	function togrey($y)
	{

		$width = $this->getImagePage()['width'];

		$row=array();
		$min=255;
		$max=0;
		
		// Find min, max and convert to grey
		for($x=0; $x<$width; $x++){
	

			// Get RGB value of pixel
			$r = $this->getImagePixelColor($x,$y)->getColor();
			
			// Convert it to grey
			$g =($r['r']+$r['g']+$r['b'])/3.0;
		
			$row[$x]=$g;

			$c = $row[$x];
			if($c>$max)
				$max=$c;
			if($c<$min)
				$min=$c;

		}

		// Based on min and max convert to BW	
		
		$thr = ($max-$min)/2.0+$min;

		for($x=0; $x<$width; $x++){
			if ($row[$x]>$thr)
				$row[$x]=1;
			else
				$row[$x]=0;
		}
	
		return $row;	
	}

	function get_bc($y)
	{
		$this->togrey(176);

		
	}





	function test()
	{
		$rc = $this->getImagePage();
		$this->get_bc(141);


		var_dump($rc);
			
	}
	

}
