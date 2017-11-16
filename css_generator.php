<?php
$folder = "";
$recursive = "right";
$imagePath = "sprite";
$styleCss = "style";
$override = 0;//override
$padding = 0;//padding
$col = 0;//column
$rec = false;
$css = false;
$img = false;
$over = false;//override
$pdg = false;//padding
$cl = false;//column
function man()
{
	echo"    -r, –recursive\n\t\tLook for images into the assets_folder passed as arguement and all of its subdirectories.
    -i, –output-image=IMAGE\n\t\tName of the generated image. If blank, the default name is « sprite.png ».
    -s, –output-style=STYLE\n\t\tName of the generated stylesheet. If blank, the default name is « style.css »
    -p, –padding=NUMBER\n\t\tAdd padding between images of NUMBER pixels
    -o, –override-size=SIZE\n\t\tForce each images of the sprite to fit a size of SIZExSIZE pixels
    -c, –columns_number=NUMBER\n\t\tThe maximum number of elements to be generated horizontally.\n";
}/**/
function ft_override($tab,$dim)
{
	global $imagePath,$folder,$pdg,$padding;
	$array = array();
	$namePicture = array();
	$widthPicture = array();
	$heightPicture = array();
	$path = getcwd();
	$path = str_replace('\\', '/', $path);			
	$height = 0;
	$width = 0;
	$decalage = 0;
	$decalage2 = 0 ;
	foreach($tab as  $value)
	{
		$size = getimagesize($value);
		$height = $dim;
		$value = basename($value);
		$value = str_replace(".png", "", $value);
		$value = str_replace("-", "", $value);
		$value = "sprite-".$value;
		array_push($namePicture, $value);
		array_push($widthPicture, $dim);
		array_push($heightPicture, $dim);
		$width +=($width == 0) ? $dim : $dim + $padding;	
	}		  	
	array_push($array,$namePicture);
	array_push($array,$widthPicture);
	array_push($array,$heightPicture);
	$img = @imagecreatetruecolor($width, $height) or die ("Cannot Initialize GD extension");
	foreach($tab as  $value)
	{
		$picture = imagecreatefrompng($value);
		$size = getimagesize($value);
		imagecopyresampled($img , $picture, $decalage , $decalage2 , 0 , 0 , $dim , $dim , $size[0] , $size[1]);
		//imagecopyresampled(dst_image, src_image, dst_x, dst_y, src_x, src_y, dst_w, dst_h, src_w, src_h)
		$decalage += $dim + $padding;
	}
	imagepng($img,$path.'/cssSpriteGenerator/'.$imagePath.".png");
	return $array;
}/**/
function my_generate_css($tab)
{
	global $imagePath,$styleCss,$folder,$padding;
	if($tab == false)
		return false;
	$path = getcwd();
	$path = str_replace('\\', '/', $path);
	$image = "";
	for($i=0;$i<count($tab[0]);$i++){
		$image.="\t".'<li class = "sprite '.$tab[0][$i].'"></li>'."\n\t\t\t";
	}
	
	$html = '<!DOCTYPE html>
	<html>
		<head>
			<title>Accueil</title>
			<meta charset="utf-8" />
			<link rel="stylesheet" href="'.$styleCss.'.css"/>		
		</head>
		<body>
			<ul class="menu">
			'.$image.'
			</ul>
		</body>
	</html>';
	$css = ".sprite {
	display: inline-block; 
	background: url('".$imagePath.".png') no-repeat;
}";	
	$bckg = 0;
	$column = 0;
	$cmpt = 0;	
	for($i=0;$i<count($tab[0]);$i++)
	{
		$css .= ".".$tab[0][$i]."{\n\t background-position: -".$bckg."px ".$column."px;\n\twidth: ".$tab[1][$i]."px;\n\theight: ".$tab[2][$i]."px;\n}";		
		$bckg += $tab[1][$i] + $padding;		
	}			
	$myhtml = fopen($path.'/cssSpriteGenerator/index.html', 'w');
	fwrite($myhtml, $html);
	fclose($myhtml);
	$mycss = fopen($path.'/cssSpriteGenerator/'.$styleCss.'.css', 'w');
	fwrite($mycss, $css);
	fclose($mycss);
}
function display($tab)
{
	global $imagePath,$folder,$override,$padding,$col;
	$array = array();
	$namePicture = array();
	$widthPicture = array();
	$heightPicture = array();
	$path = getcwd();
	$path = str_replace('\\', '/', $path);
	if($tab != false  && $override == 0)
	{		
		$height = 0;
		$width = 0;
		$decalage = 0 ;
		$decalage2 = 0;
		foreach($tab as  $value)
		{
			$size = getimagesize($value);
			if($height < $size[1])
				$height = $size[1];
			$value = basename($value);
			$value = str_replace(".png", "", $value);
			$value = str_replace("-", "", $value);
			$value = "sprite-".$value;
			array_push($namePicture, $value);
			array_push($widthPicture, $size[0]);
			array_push($heightPicture, $size[1]);
		    $width +=($width == 0) ? $size[0] : $size[0] + $padding;	   	   	
		} 	  
		array_push($array,$namePicture);
		array_push($array,$widthPicture);
		array_push($array,$heightPicture);
		$img = @imagecreatetruecolor($width, $height) or die ("Cannot Initialize GD extension");
		foreach($tab as  $value)
		{
			$picture = imagecreatefrompng($value);
			$size = getimagesize($value);
			imagecopyresampled($img , $picture, $decalage , $decalage2 , 0 , 0 , $size[0] , $size[1] , $size[0] , $size[1]);
			//imagecopyresampled(dst_image, src_image, dst_x, dst_y, src_x, src_y, dst_w, dst_h, src_w, src_h)
			$decalage += $size[0] + $padding;
		}
		imagepng($img,$path.'/cssSpriteGenerator/'.$imagePath.".png");
	}
	else if($tab != false)
	{
		$array = ft_override($tab,$override);
	}
	else
		return false;
	return $array;
}
function ft_picture($dir)
{
	static $results_array = array();
	global  $css,$img,$rec;
	if (is_dir($dir) && (($css || $img || $rec) && $rec))
	{
        if ($handle = opendir($dir))
        {
            while(($ff = readdir($handle)) !== FALSE)
            {
            	if($ff != "." && $ff != "..")
            	{
            		if(is_dir($dir.'/'.$ff))
					{
						ft_picture($dir.'/'.$ff);			
					}
					else
					{	
						if(is_dir($dir))
						{
							if(exif_imagetype($dir."/".$ff) == IMAGETYPE_PNG){
							    $results_array[] = $dir."/".$ff;
							}														
						}
					}	                        
            	}
            }
            closedir($handle);
        }
	}
	else
	{
		if ($handle = opendir($dir))
        {
            while(($ff = readdir($handle)) !== FALSE)
            {
            	if($ff != "." && $ff != "..")
            	{    		
            		if(@exif_imagetype($dir."/".$ff) == IMAGETYPE_PNG){
					    $results_array[] = $dir."/".$ff;
					}		              
            	}
            }
            closedir($handle);
        }	
	}
	return $results_array;
}
function args_picture($tab)
{
	$newTab = array();	
	$path = getcwd();
	$path = str_replace('\\', '/', $path);	
	if($tab == false)
		return false;
	if(file_exists($tab[0]))
		$newTab = ft_picture($tab[0]);
	else
	{
		echo "No folder provided, please Provide a folder.\n";
		return false;
	}				
	if(count($newTab) <= 1)
	{
		echo "One picture provided, please provide at least two pictures to make the sprite.\n";
		return false;
	}
	if (!file_exists($path.'/cssSpriteGenerator')) {
			mkdir($path.'/cssSpriteGenerator', 0777, true);	
	}
	return $newTab;
}
function ft_args($tab)
{
	$array = array();
	$args = array();
	$flag = true;
	array_splice($tab, 0 , 1);	
	$count = 0;	
	global $folder,	$recursive,	$imagePath,$styleCss,$override,$padding,$col, $rec ,$css ,$img,$over,$pdg,$cl;
	$i = 0;
	foreach($tab as $arg)	
	{
		array_push($array, $arg);
		$count++;
	}	
	if($count == 1)
	{
		if(is_dir($array[0]))
		{
			$folder = $array[0];
			array_push($args, $folder);
			return $args;
		}
	}
	else
	{		
		while($i < count($array)) {			
			if(is_dir($array[$i]))
			{				
				$flag = false;
				$folder = $array[$i];		
				$i++;	
			}
			elseif($array[$i] == "-r" || preg_match('/--recursive/',$array[$i]) )
			{
				if($rec)
				{
					echo "Error , use -r or --recursive.\n";
					$recursive = null;
				}
				else if($array[$i] == "-r")
				{
					$rec = true;
				}
				else if(preg_match('/--recursive/',$array[$i]))
				{
					$rec = true;
				}
				$i++;				
			}
			elseif($array[$i] == "-i" ||  preg_match('/--output-image/',$array[$i]))
			{		
				if($img)
				{
					echo "Error , use -i or --output-image.\n";
					$imagePath = null;
					$i+=2;
				}		
				else if($array[$i] == "-i")	
				{
					$i++;	
					if($i >= count($array))
					{
						echo "Error, need argument for the sprite name.\n";
						$imagePath = null;
						$i++;
					}
					else if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $array[$i]))
					{
						echo "Error, invalid name.\n";
						$imagePath = null;
						$i++;
					}
					else
					{					
						$imagePath = $array[$i];
						$img = true;
						$i++;
					}
				}	
				elseif(preg_match('/--output-image/',$array[$i]))
				{
					if(preg_match('/=/',$array[$i]))
					{
						$imagePath = substr($array[$i],strpos($array[$i], "=") + 1);
						if($imagePath == "")
						{
							echo "Error, need argument for the sprite name.\n";
							$imagePath = null;
						}
						else if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $imagePath))
						{
							echo "Error, invalid name.\n";
							$imagePath = null;
							$i++;
						}
						$img = true;
					}					
					else
					{
						echo "Error, need argument for the sprite name.\n";
						$imagePath = null;
					}
					$i++;
				}	
				else
				{
					$i++;
				}				
			}
			elseif($array[$i] == "-s" || preg_match('/--output-style/',$array[$i]))
			{
				if($css)
				{
					echo "Error , use -s or --output-style.\n";
					$styleCss = null;
					$i+=2;
				}	
				else if($array[$i] == "-s")	
				{
					$i++;					
					if($i >= count($array))
					{
						echo "Error, need argument for the css name.\n";
						$styleCss = null;
						$i++;
					}
					else if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $array[$i]))
					{
						echo "Error, need argument for the css name.\n";
						$styleCss = null;
						$i++;
					}
					else
					{					
						$styleCss = $array[$i];		
						$css = true;
						$i++;
					}
				}	
				elseif(preg_match('/--output-style/',$array[$i]))
				{

					if(preg_match('/=/',$array[$i]))
					{
						$styleCss = substr($array[$i],strpos($array[$i], "=") + 1);
						if($styleCss== "")
						{
							echo "Error, need argument for the css name.\n";
							$styleCss = null;
						}
						else if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $styleCss))
						{
							echo "Error, invalid name.\n";
							$styleCss = null;
						}
						$css = true;
					}
					else
					{
						echo "Error, need argument for the css name.\n";
						$styleCss = null;
					}
					$i++;					
				}	
				else
				{
					$i++;
				}			
			}/**/
			elseif($array[$i] == "-o" || preg_match('/-–override-size/',$array[$i]))
			{
				if($over)
				{
					echo "Error , use -o or --override-size.\n";
					$override = null;
					$i+=2;
				}	
				else if($array[$i] == "-o")	
				{
					$i++;					
					if($i >= count($array))
					{
						echo "Error, need argument for the override.\n";
						$override = null;
						$i++;
					}
					else
					{					
						$override = intval($array[$i]);		
						$over = true;
						$i++;
					}
				}	
				elseif(preg_match('/-–override-size/',$array[$i]))
				{
					if(preg_match('/=/',$array[$i]))
					{
						$override = intval(substr($array[$i],strpos($array[$i], "=") + 1));
						if($override== "")
						{
							echo "Error, need argument for the override.\n";
							$override = null;
						}
						$over = true;
					}
					else
					{
						echo "Error, need argument for the override.\n";
						$override = null;
					}
					$i++;					
				}	
				else
				{
					$i++;
				}			
			}
			elseif($array[$i] == "-p" || preg_match('/-–padding/',$array[$i]))
			{
				if($pdg)
				{
					echo "Error , use -p or --padding.\n";
					$padding = null;
					$i+=2;
				}	
				if($array[$i] == "-p")	
				{
					$i++;					
					if($i >= count($array))
					{
						echo "Error, need argument for the padding.\n";
						$padding = null;
						$i++;
					}
					else
					{					
						$padding = intval($array[$i]);		
						$pdg = true;
						$i++;
					}
				}	
				elseif(preg_match('/-–override-size/',$array[$i]))
				{
					if(preg_match('/=/',$array[$i]))
					{
						$padding = intval(substr($array[$i],strpos($array[$i], "=") + 1));
						if($padding== "")
						{
							echo "Error, need argument for the padding.\n";
							$padding = null;
						}
						$pdg = true;
					}
					else
					{
						echo "Error, need argument for the padding.\n";
						$padding = null;
					}
					$i++;					
				}	
				else
				{
					$i++;
				}			
			}
			elseif($array[$i] == "-c" || preg_match('/-–columns_number/',$array[$i]))
			{
				if($cl)
				{
					echo "Error , use -c or --columns_number.\n";
					$col = null;
					$i+=2;
				}
				if($array[$i] == "-c")	
				{
					$i++;					
					if($i >= count($array))
					{
						echo "Error, need argument for the columns.\n";
						$col = null;
						$i++;
					}
					else
					{					
						$col = intval($array[$i]);		
						$cl = true;
						$i++;
					}
				}	
				elseif(preg_match('/-–columns_number/',$array[$i]))
				{
					if(preg_match('/=/',$array[$i]))
					{
						$override = intval(substr($array[$i],strpos($array[$i], "=") + 1));
						if($override== "")
						{
							echo "Error, need argument for the columns.\n";
							$col = null;
						}
						$cl = true;
					}
					else
					{
						echo "Error, need argument for the columns.\n";
						$col = null;
					}
					$i++;					
				}	
				else
				{
					$i++;
				}			
			}/**/
			/**/
			else
			{
				echo "$array[$i] argument doesn't exist.\n";
				man();
				return false;
			}
		}
	}	
	if($padding < 0 || $override <=0 && $over)
	{
		echo "Invalid value.\n";
		return false;
	}
	array_push($args, $folder);		
	array_push($args, $imagePath);
	array_push($args, $styleCss);	
	array_push($args, $override);	//override
	if($flag)
	{
		echo "Please provide a directory.\n";
		return false;
	}	
	if($styleCss == null || $imagePath == null || $override === null || $padding === null || $col === null || $recursive == null)/**/
		return false;
	return $args;
}
my_generate_css(display(args_picture(ft_args($argv))));
?>
