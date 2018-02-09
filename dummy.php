<?php
include 'RandomColor.php';
use \Colors\RandomColor;

// Dynamic Dummy Image Generator - DummyImage.com
$x = strtolower($_GET["x"]); //GET the query string from the URL. x would = 600x400 if the url was http://dummyimage.com/600x400
if (strlen($x) > 10) { //Limit the size of the image by limiting the length of the query string
	die("Too big of an image!"); //If it is too big we kill the script.
}
list($width, $height) = explode('x', $x); // Split X up at the 'x' character so we have our image width and height.

$angle = 0; //I don't use this but if you wanted to angle your text you would change it here.
$fontsize = $width/12; //I came up with 16 to scale the text size based on the width.
if($fontsize<= 10) { //I do set a minimum font size so it is still sort of legible at very small image sizes.
	$fontsize = 10;
}

$font = "OpenSans-Regular.ttf"; // If you want to use a different font simply upload the true type font (.ttf) file to the same directory as this PHP file and set the $font variable to the font file name. 

$im = imagecreatetruecolor($width,$height); //Create an image.
$white = imageColorAllocate($im, 255, 255, 255); //Set the color gray for the background color. Hex value = #CCCCCC
$gray = imageColorAllocate($im, 242, 242, 242); //Set the color gray for the background color. Hex value = #CCCCCC
$black = imageColorAllocate($im, 0, 0, 0); //Set the black color for the text
$randomColor = imageColorAllocate($im, mt_rand(10,255), mt_rand(10,255), mt_rand(10,255));

$text = $width." x ".$height; //This is the text string that will go right in the middle of the gray rectangle.

$textBox = imagettfbbox_t($fontsize, $angle, $font, $text); //Pass these variable to a function that calculates the position of the bounding box.
$textWidth = $textBox[4] - $textBox[1]; //Calculates the width of the text box by subtracting the Upper Right X position with the Lower Left X position.
$textHeight = abs($textBox[7])+abs($textBox[1]); //Calculates the height of the text box by adding the absolute value of the Upper Left Y position with the Lower Left Y position.

$textX = ($width - $textWidth)/2; //Determines where to set the X position of the text box so it is centered.
$textY = ($height - $textHeight)/2 + $textHeight; //Determines where to set the Y position of the text box so it is centered.
imagefill($im,0,0,0x7fff0000);
imagesavealpha($im,true);
//imageFilledRectangle($im, 0, 0, $width, $height,$white );
//imageFilledRectangle($im, 1, 1, $width-2, $height-2, $gray); //Creates the gray rectangle http://us2.php.net/manual/en/function.imagefilledrectangle.php
$count =0;
for($px = 0;$px<=$width;$px+=10){
    for($py = 0;$py<=$height;$py+=10){
        $count++;
        $Rcolor = get_randomColor();
        if($px%50==40 || $py%50==40){
            if($px%100==90 || $py%100==90){
                //$rr  =  imageColorAllocate($im, 164,231,229);//RGB(164,231,229)
                 $rr  =  imagecolorallocatealpha($im, 0, 0, 0, 95);//RGB(164,231,229)
                 imagefilledrectangle ($im, $px, $py, $px+9 ,$py+9, $rr);
            }else{
            //    $rr  =  imagecolorallocatealpha($im, 255, 255, 255, 90);;
            }
            
        }else{
        $rr = imageColorAllocate($im, $Rcolor['r'], $Rcolor['g'], $Rcolor['b']);
        imagefilledrectangle ($im, $px, $py, $px+9 ,$py+9, $rr);
        }
    
   
    //imagesetpixel($im, $px,0, $rr);
   // imageline($im, $px, 0, $px, 0, $rr);
    }
}
imagettftext($im, $fontsize, $angle, $textX, $textY, $black, $font, $text);	 //Create and positions the text http://us2.php.net/manual/en/function.imagettftext.php

//for($px = $width;$px>=0;$px--){
//    imagesetpixel($im, $px,0, get_randomColor($im));
//}


header('Access-Control-Allow-Origin: *');

header('Access-Control-Allow-Methods: GET, POST');

header("Access-Control-Allow-Headers: X-Requested-With");
if(isset($_GET['base64']) ){
ob_start (); 

imagepng ($im);
$image_data = ob_get_contents (); 

ob_end_clean (); 
$image_data_base64 = 'src="data:image/png;base64,'.base64_encode ($image_data).'"';
//echo strlen($image_data_base64)."<br>";
echo $image_data_base64;
}else{
header('Content-type: image/png'); //Set the header so the browser can interpret it as an image and not a bunch of weird text.
header('Content-disposition: inline;filename="dummy'.$_GET["x"]."_".time().'.png"'); 
imagepng($im); //Create the final  image
imageDestroy($im);//Destroy the image to free memory.
}


	
function imagettfbbox_t($size, $angle, $fontfile, $text){ //Ruquay K Calloway http://ruquay.com/sandbox/imagettf/ made a better function to find the coordinates of the text bounding box so I used it.
    // compute size with a zero angle
    $coords = imagettfbbox($size, 0, $fontfile, $text);
    // convert angle to radians
    $a = deg2rad($angle);
    // compute some usefull values
    $ca = cos($a);
    $sa = sin($a);
    $ret = array();
    // perform transformations
    for($i = 0; $i < 7; $i += 2){
        $ret[$i] = round($coords[$i] * $ca + $coords[$i+1] * $sa);
        $ret[$i+1] = round($coords[$i+1] * $ca - $coords[$i] * $sa);
    }
    return $ret;
}

function get_randomColor(){
    //return (RandomColor::one(array('format'=>'rgb','hue'=>'monochrome')));//exit;
    return RandomColor::one(array('format'=>'rgb','luminosity' => 'light'));

}
?>
