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
			
			// Convert it to greyscale
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

	var $ihash = array(
		'nnWWn' => 0,
		'WnnnW' => 1,
		'nWnnW' => 2,
		'WWnnn' => 3,
		'nnWnW' => 4,
		'WnWnn' => 5,
		'nWWnn' => 6,
		'nnnWW' => 7,
		'WnnWn' => 8,
		'nWnWn' => 9
	);


	function decode_digit( & $row,$start, $cmp)
	{

		$str = "";
		for($x=$start; $x<$start+10; $x+=2){

			$w=abs($row[$x]);
			if ($w>$cmp)
				$str.='W';
			else
				$str.='n';
		}

		echo "Decoded: ($cmp)$str\n";
		
		if (isset($this->ihash[$str]))
			return $this->ihash[$str];

		return false;
	}







function try_decode(& $row ,$start,$end)
{

	$ratio=1.45;

	$len=$end-$start;

	if($len<10)
		return false;

	// we always want to start with a black sequence	
	if ($row[$start]>0)
		return $this->try_decode($row,$start+1,$end);

	// Take the black narrow witdh from first 2 black bars
	// and the white narrow width from first 2 white bars
	$bnw=($row[$start]+$row[$start+2])/-2;
	$wnw=($row[$start+1]+$row[$start+3])/2;


	$str = "";
	for ($x=$start+4; $x<$len-10; $x+=10){
		$c = $row[$x];
		$w = abs($c);

		if ($c<0) {	//black
			$cmp=$bnw;
		}
		else{
			$cmp=$wnw;
		}

		$black = $this->decode_digit($row,$x,$bnw*$ratio);

		$white = $this->decode_digit($row,$x+1,$wnw*$ratio);

		



		$str.=$black.$white;

	}
echo "STR: $str\n";
	return $str;
}




	function get_bc($y)
	{
		$r = $this->get_row($y);

		$r = $this->get_w($r['row'],$r['min'],$r['max']);

		$this->try_decode($r,0,count($r));

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

		$this->get_bc(147);


		var_dump($rc);
			
	}
	

}
