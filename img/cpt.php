<?
session_start();
$x = array(
	array('bcdfgjkmnprstvwxz'),
	array('aeiou23456789')
);
for ($string = '', $i = 0; $i < 5; $i++)
	$string .= substr($x[$i%2][0], round(rand(0, strlen($x[$i%2][0])-1)), 1);
$_SESSION["control_word"] = md5($string);
$width=80;
$height=30;
$img=imagecreatetruecolor($width,$height);
$img2=imagecreatetruecolor($width,$height);
$bg=imagecolorallocate($img,255,255,255);
$textcolor = imagecolorallocate($img, 100, 100, 100);
$text=imagecolorallocate($img,0,0,0);
imagefill($img,0,0,$bg);
imagefill($img2,0,0,$bg);
imagestring($img,5,20,10,$string,$textcolor);
//imagettftext($img,14,0,16,20,$text,"verdana.ttf","qwert");


// ��������� ��������� (����� �������������������� � ��������������):
// �������
$rand1 = mt_rand(700000, 1000000) / 15000000;
$rand2 = mt_rand(700000, 1000000) / 15000000;
$rand3 = mt_rand(700000, 1000000) / 15000000;
$rand4 = mt_rand(700000, 1000000) / 15000000;
// ����
$rand5 = mt_rand(0, 3141592) / 1000000;
$rand6 = mt_rand(0, 3141592) / 1000000;
$rand7 = mt_rand(0, 3141592) / 1000000;
$rand8 = mt_rand(0, 3141592) / 1000000;
// ���������
$rand9 = mt_rand(200,450) / 100;
$rand10 = mt_rand(200,450) / 100;
 
for($x = 0; $x < $width; $x++){
  for($y = 0; $y < $height; $y++){
    // ���������� �������-�����������.
    $sx = $x + ( sin($x * $rand1 + $rand5) + sin($y * $rand3 + $rand6) ) * $rand9;
    $sy = $y + ( sin($x * $rand2 + $rand7) + sin($y * $rand4 + $rand8) ) * $rand10;
 
    // ���������� �� ��������� �����������
    if($sx < 0 || $sy < 0 || $sx >= $width - 1 || $sy >= $height - 1){ 
      $color = 255;
      $color_x = 255;
      $color_y = 255;
      $color_xy = 255;
    }else{ // ����� ��������� ������� � ��� 3-� ������� ��� ������� �������������
      $color = (imagecolorat($img, $sx, $sy) >> 16) & 0xFF;
      $color_x = (imagecolorat($img, $sx + 1, $sy) >> 16) & 0xFF;
      $color_y = (imagecolorat($img, $sx, $sy + 1) >> 16) & 0xFF;
      $color_xy = (imagecolorat($img, $sx + 1, $sy + 1) >> 16) & 0xFF;
    }



    // ���������� ������ �����, ����� ������� ������� ����������
    if($color == $color_x && $color == $color_y && $color == $color_xy){
      $newcolor=$color;
    }else{
      $frsx = $sx - floor($sx); //���������� ��������� ����������� �� ������
      $frsy = $sy - floor($sy);
      $frsx1 = 1 - $frsx;
      $frsy1 = 1 - $frsy;

      // ���������� ����� ������ ������� ��� ��������� �� ����� ��������� ������� � ��� �������
      $newcolor = floor( $color    * $frsx1 * $frsy1 +
                         $color_x  * $frsx  * $frsy1 +
                         $color_y  * $frsx1 * $frsy  +
                         $color_xy * $frsx  * $frsy );
    }
    imagesetpixel($img2, $x, $y, imagecolorallocate($img, $newcolor, $newcolor, $newcolor));
  }
}


Header("Content-type: image/jpg");
imagejpeg($img2, null, 100);
imagedestroy($img);
imagedestroy($img2);

?>
