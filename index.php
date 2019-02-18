<?php
/*
* 为了防止身份证被盗用，建议上传之前加上水印
* 上传的图片不会存储在服务器，都是处理完毕后直接输出
* 如果图片不显示, 检查字体路径
*/

if(isset($_POST['content'])){
  $file = $_FILES['file'];
  $name = $file['name'];
  $type = strtolower(substr($name,strrpos($name,'.')+1));
  $allowtype = array('jpg','gif','png');
  if(!in_array($type, $allowtype)){
    die("不被支持的文件格式!");
  }
  $file['tmp_name'] = str_replace('////', '//', $file['tmp_name']);
  if(!is_uploaded_file($file['tmp_name'])){
    die("未知错误!");
  }
  $path = $file['tmp_name'];
  $im = imagecreatefromstring(file_get_contents($path));
  $font = './1.ttf';//字体
  switch ($_POST['color']){
    case 2://white
	  $color = imagecolorallocate($im, 240, 240, 240);
      break;
	case 3://black
	  $color = imagecolorallocate($im, 50, 50, 50);
      break;
	case 4://red
	  $color = imagecolorallocate($im, 252, 113, 147);
      break;
	case 5://blue
	  $color = imagecolorallocate($im, 147, 113, 255);
      break;
	default: //grey
	  $color = imagecolorallocate($im, 150, 150, 150);
      break;
  }
  switch ($_POST['space']){
    case 60:
	  $lineheight = 60;
      break;
	case 90:
	  $lineheight = 90;
      break;
	case 150:
	  $lineheight = 150;
      break;
	case 180:
	  $lineheight = 180;
      break;
	default:
	  $lineheight = 120;
      break;
  }
  $str = $_POST['content'];
  $strwidth = ceil(strlen($str) / 3 * 20);
  list($imgwidth, $imgheight, $imgtype) = getimagesize($path);
  $wtime = $imgwidth / $strwidth + 1;
  $htime = ceil($imgheight / $lineheight) * $lineheight;
  for($w = 0; $w <= $wtime; $w++){
    for($h = 0; $h - 200 <= $htime; $h += $lineheight){
      imagefttext($im, 14, -30, $w * ($strwidth - strlen($str) + 10), -200 + $h, $color, $font, $str);
    }
  }
  switch($imgtype){
    case 1://GIF
      header('Content-Type: image/gif');
      header("Content-Disposition: attachment; filename=".time().".gif");
      imagegif($im);
      break;
    case 2://JPG
      header("Content-Type: image/jpeg");
      header("Content-Disposition: attachment; filename=".time().".jpg");
      imagejpeg($im);
	  break;
    case 3://PNG
      header('Content-Type: image/png');
      header("Content-Disposition: attachment; filename=".time().".png");
      imagepng($im);
      break;
    default:
      break;
  }
  imagedestroy($im);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
 <head>
  <meta charset="utf-8">
  <title>图片加水印</title> 
  <style>
  * {font-size:16px; font-family: '微软雅黑';}
  body {background:#333;}
  #main {width:600px; margin:50px auto;}
  h1 {font-size:26px; text-align:center; color:#fff;}
  h2 {font-size:18px; text-align:center; color:#fff; font-weight:normal;}
  p {margin-bottom:20px; color:#fff;}
  button {background:#06c; color:#fff; font-weight:bold; border:none; padding:5px 10px; cursor:pointer;}
  .content {border:none; background:#eee; width:490px; padding-left:10px; height:28px;}
  .file {border:1px solid #fff; width:360px; height:28px;}
  </style> 
 </head> 
 <body> 
  <div id="main"> 
   <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" enctype="multipart/form-data" autocomplete="off"> 
    <h1>文字水印</h1>
	<h2>本站不会对用户上传的照片进行存储, 请放心使用.</h2>
	<br>
	<p>水印文字: <input type="input" name="content" value="此证件仅供办理XX业务使用，他用无效！" class="content" /></p> 
    <p>选择图片: <input type="file" name="file" multiple="multiple" accept="image/png,image/jpg,image/jpeg,image/bmp" class="file" /> 仅支持jpg, png, gif</p> 
    <p>文字颜色: 
	  <label><input type="radio" name="color" value="1" />灰</label> 
	  <label><input type="radio" name="color" value="2" />白色</label> 
	  <label><input type="radio" name="color" value="3" />黑色</label> 
	  <label><input type="radio" name="color" value="4" />红色</label> 
	  <label><input type="radio" name="color" value="5" checked="checked" />蓝色(默认)</label> 
	</p>
	<p>文字间距: 
	  <label><input type="radio" name="space" value="60" />60(密集)</label> 
	  <label><input type="radio" name="space" value="90" />90</label> 
	  <label><input type="radio" name="space" value="120" checked="checked" />120(推荐)</label> 
	  <label><input type="radio" name="space" value="150" />150</label> 
	  <label><input type="radio" name="space" value="180" />180(稀疏)</label> 
	</p> 
    <button type="submit" name="submit">生成水印</button> 
   </form> 
  </div>  
 </body>
</html>
