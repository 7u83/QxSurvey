<?php


class QxImage extends Imagick
{

	function get_row($y)
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

		return array ('row'=>$row,'min'=>$min,'max'=>$max);	
	}

	function grey2bw($g,$thr)
	{
		if ($g>=$thr)
			return 1;
		return 0;
	}


	function get_w($row,$min,$max) 
	{
		$xdim = count($row);


		$range = $max-$min;
		$thr = $range/2.0+$min;


		$b=array();

		$col=$this->grey2bw($row[0],$thr);


		$count=1;
		for ($x = 1; $x < $xdim; $x++) {
			$g=$row[$x]-$min;
	
			$bw = $this->grey2bw($g,$thr);	

			if ($bw != $col )
			{
				$n = ($range-$g)/$range;	
				$o = 1-$n;

				if ($bw>$col) {
					$o=1-$o;
					$n=1-$n;
					$z=-1;
				}
				else{
					$z=1;
				}

				$count+=$o;
				
				$b[] = $count*$z;

				$count=$n;
				$col = $bw;
			}
			$count++;	
		}


		return $b;
	}


	function get_bc($y)
	{
		$r = $this->get_row(176);

		$r = $this->get_w($r['row'],$r['min'],$r['max']);

		for ( $i=0; $i<count($r); $i++){
			printf("%0.2f|",$r[$i]);
		}

		// Based on min and max convert to BW	
	
/*	
		$thr = ($max-$min)/2.0+$min;

		for($x=0; $x<$width; $x++){
			if ($row[$x]>$thr)
				$row[$x]=1;
			else
				$row[$x]=0;
		}
	*/
		
	}





	function test()
	{
		$rc = $this->getImagePage();

			$this->get_bc(141);


		var_dump($rc);
			
	}
	

}
