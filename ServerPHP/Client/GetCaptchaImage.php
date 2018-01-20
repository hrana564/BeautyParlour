<?Php

//header ("Content-type: image/png");
require_once '../Utils/DBConfig.php';
require_once '../Utils/PHPFunctions.php';

class ImageCaptcha  implements JsonSerializable {
    function ImageCaptcha() {
        $this->ImgSrc = "";
        $this->ImgToken = "";
    }
    public function jsonSerialize() {
        return [
            'ImgSrc' => $this->ImgSrc,
            'ImgToken' => $this->ImgToken
        ];
    }
}

$im = @ImageCreate (130, 30) // Width and hieght of the image. 
or die ("Cannot Initialize new GD image stream");
$background_color = ImageColorAllocate ($im, 204, 204, 204); // Assign background color
//////Part 1 Random string generation ////////
$string1="abcdefghijklmnpqrstuvwxyz";
$string2="123456789";
$string=$string1.$string2;
$text_color = ImageColorAllocate ($im, 51, 51, 255);      // text color is given 

$random_text='';

for($i=1;$i<=5;$i++){
$src = @ImageCreate(20, 20);
$background_color = ImageColorAllocate ($src, 204, 204, 204); // Assign background color

$string= str_shuffle($string);
$text = substr($string,0,1); // One char of the random chars
ImageString($src,5,5,0,$text,$text_color); // 
/*if($i<3){$angle=rand(10,60);}
else{$angle=0;}
*/
$angle=rand(10,20);
$src = imagerotate($src, $angle, 0);
$x=$i*20;
imagecopy($im, $src, $x, 5, 0, 0, 20, 20);
//ImagePng ($src);

$random_text .=$text;
imagedestroy($src);
}
/////End of Part 1 ///////////

// Enable output buffering
ob_start();
///// Create the image ////////
ImagePng ($im); // image displayed
// Capture the output
$imagedata = ob_get_contents();
// Clear the output buffer
ob_end_clean();

$myObj = new ImageCaptcha();
$myObj->ImgSrc = base64_encode($imagedata);
$myObj->ImgToken = generateRandomString();

$myJSON = json_encode($myObj);

$sql = "INSERT INTO `trn_captcha`(`ImgToken`, `ImgActualString`) VALUES ('$myObj->ImgToken','$random_text')";

$result = mysqli_query($conn,$sql);

if($result === True){
	echo $myJSON;
}

//print '<p><img src="data:image/png;base64,'.base64_encode($imagedata).'" alt="image 1" width="130" height="30"/></p>';

imagedestroy($im); // Memory allocation for the image is removed. 
?>