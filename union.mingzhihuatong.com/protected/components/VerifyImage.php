<?php

class VerifyImage  extends CAction{

    public function run() {
        $this->verify();
    }

    function  getRandomVerify($type=1,$length=4){
        if($type==1){
            $arr=range(0,9);
            $str=join("",$arr);
            $string=substr(str_shuffle($str),0,$length);
        }elseif($type==2){
            $arr1=range("a","z");
            $arr2=range("A","Z");
            $arr=array_merge($arr1,$arr2);
            $str=join("",$arr);
            $string=substr(str_shuffle($str),0,$length);
        }elseif($type==3){
            $str="23456789QWERTYUPASDFGHJKZXCVBNM";
            $string=substr(str_shuffle($str),0,$length);
        }elseif($type==4){
            mb_internal_encoding("utf-8");
            $str="尼和猜花哈炫香糖迈啦辣家军事理论吗没卡死暴走";
            $string=mb_substr($str,mt_rand(0,mb_strlen($str)-$length),$length);
        }
        return $string; 
    }


    function verify($width=90,$height=22,$size=18,$numPixel=60,$numLine=6){
        $image=imagecreatetruecolor($width,$height);
        $white=imagecolorallocate($image,255,255,255);
        imagefilledrectangle($image,0,0,$width-1,$height-1,$white);
        for($i = 1; $i < $numPixel; $i ++) {
            $color = imagecolorallocate ( $image, mt_rand ( 0, 255 ), mt_rand ( 0, 255 ), mt_rand ( 0, 255 ) );
            imagesetpixel ( $image, mt_rand ( 0, $width ), mt_rand ( 0, $height ), $color );
        }
        for($i = 1; $i < $numLine; $i ++) {
            $color = imagecolorallocate ( $image,0,0,0);
            imageline($image,mt_rand(10,$width-10),mt_rand(0,15),mt_rand(10,$width-10),mt_rand($height-8,$height-5),$color);
        }
        $str="";
        session_start();
        for($i=0;$i<4;$i++){
            $angle=0;
            $x=$i *18+10;
            $y=$height/2+$size/2;
            $fontfile = "MSYH.TTF";
            $fontfile= dirname(__FILE__).'/'.$fontfile;
            // var_dump($fontfile);exit;
            $text=$this->getRandomVerify(3,1);

            $str.=$text;
            $str=trim($str);
            $color=imagecolorallocate($image,mt_rand(0,200),mt_rand(0,200),mt_rand(0,200));
            imagettftext($image,$size,$angle,$x,$y,$color,$fontfile,$text);
            //imagestring($image,3,20,20,"rr",$color);
        }       
        $_SESSION['trueCode']=$str;
        header("content-type:image/jpeg");
        imagejpeg($image);
        imagedestroy($image);
    }

}
