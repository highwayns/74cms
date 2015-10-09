<?php
/**
 * 利用GD库读写bmp格式图片
 */
 if(!defined('IN_QISHI'))
{
die('Access Denied!');
}
function imagebmp(&$im, $filename = '', $bit = 0, $compression = 0){
	if(empty($bit)){
		$colorcot=imagecolorstotal($im);
		$transparent=imagecolortransparent($im);
		$istransparent=$transparent!=-1;
		if($istransparent) $colorcot--;
		if(($colorcot>0) && ($colorcot<=2)) $bit=1;
		elseif(($colorcot>2) && ($colorcot<=16)) $bit=4;
		elseif(($colorcot>16) && ($colorcot<=256)) $bit=8;
	}
	if (!in_array($bit, array(1, 4, 8, 16, 24, 32))) $bit = 8;
	elseif($bit == 32) $bit = 24;

	$bits = pow(2, $bit);

	// 调整调色板
	imagetruecolortopalette($im, true, $bits);
	$width  = imagesx($im);
	$height = imagesy($im);
	$colors_num = imagecolorstotal($im);

	if ($bit <= 8){
		// 颜色索引
		$rgb_quad = '';
		for ($i = 0; $i < $colors_num; $i ++){
			$colors = imagecolorsforindex($im, $i);
			$rgb_quad .= chr($colors['blue']) . chr($colors['green']) . chr($colors['red']) . "\0";
		}

		// 位图数据
		$bmp_data = '';

		// 非压缩
		if ($compression == 0 || $bit < 8){
			if (!in_array($bit, array(1, 4, 8))){
				$bit = 8;
			}

			$compression = 0;

			// 每行字节数必须为4的倍数，补齐。
			$extra = '';
			$padding = 4 - ceil($width / (8 / $bit)) % 4;
			if ($padding % 4 != 0){
				$extra = str_repeat("\0", $padding);
			}

			for ($j = $height - 1; $j >= 0; $j --){
				$i = 0;
				while ($i < $width){
					$bin = 0;
					$limit = $width - $i < 8 / $bit ? (8 / $bit - $width + $i) * $bit : 0;

					for ($k = 8 - $bit; $k >= $limit; $k -= $bit){
						$index = imagecolorat($im, $i, $j);
						$bin |= $index << $k;
						$i ++;
					}

					$bmp_data .= chr($bin);
				}

				$bmp_data .= $extra;
			}
		}elseif ($compression == 1 && $bit == 8){ // RLE8 压缩
			for ($j = $height - 1; $j >= 0; $j --){
				$last_index = "\0";
				$same_num   = 0;
				for ($i = 0; $i <= $width; $i ++){
					$index = imagecolorat($im, $i, $j);
					if ($index !== $last_index || $same_num > 255){
						if ($same_num != 0){
							$bmp_data .= chr($same_num) . chr($last_index);
						}

						$last_index = $index;
						$same_num = 1;
					}else{
						$same_num ++;
					}
				}
				$bmp_data .= "\0\0";
			}
			$bmp_data .= "\0\1";
		}
		$size_quad = strlen($rgb_quad);
		$size_data = strlen($bmp_data);
	}else{
		// 每行字节数必须为4的倍数，补齐。
		$extra = '';
		$padding = 4 - ($width * ($bit / 8)) % 4;
		if ($padding % 4 != 0){
			$extra = str_repeat("\0", $padding);
		}

		// 位图数据
		$bmp_data = '';

		for ($j = $height - 1; $j >= 0; $j --){
			for ($i = 0; $i < $width; $i ++){
				$index  = imagecolorat($im, $i, $j);
				$colors = imagecolorsforindex($im, $index);

				if ($bit == 16){
					$bin = 0 << $bit;

					$bin |= ($colors['red'] >> 3) << 10;
					$bin |= ($colors['green'] >> 3) << 5;
					$bin |= $colors['blue'] >> 3;

					$bmp_data .= pack("v", $bin);
				}else{
					$bmp_data .= pack("c*", $colors['blue'], $colors['green'], $colors['red']);
				}
				// todo: 32bit;
			}
			$bmp_data .= $extra;
		}

		$size_quad = 0;
		$size_data = strlen($bmp_data);
		$colors_num = 0;
	}

	// 位图文件头
	$file_header = "BM" . pack("V3", 54 + $size_quad + $size_data, 0, 54 + $size_quad);

	// 位图信息头
	$info_header = pack("V3v2V*", 0x28, $width, $height, 1, $bit, $compression, $size_data, 0, 0, $colors_num, 0);

	// 写入文件
	if ($filename != ''){
		$fp = fopen($filename, "wb");
		fwrite($fp, $file_header);
		fwrite($fp, $info_header);
		fwrite($fp, $rgb_quad);
		fwrite($fp, $bmp_data);
		fclose($fp);
		return true;
	}else{
		// 浏览器输出
		header("Content-Type: image/bmp");
		echo $file_header . $info_header;
		echo $rgb_quad;
		echo $bmp_data;
		return true;
	}
}

function imagecreatefrombmp($file){
	global  $CurrentBit;

	$f=fopen($file,"r");
	$Header=fread($f,2);

	if($Header=="BM"){
		$Size=freaddword($f);
		$Reserved1=freadword($f);
		$Reserved2=freadword($f);
		$FirstByteOfImage=freaddword($f);

		$SizeBITMAPINFOHEADER=freaddword($f);
		$Width=freaddword($f);
		$Height=freaddword($f);
		$biPlanes=freadword($f);
		$biBitCount=freadword($f);
		$RLECompression=freaddword($f);
		$WidthxHeight=freaddword($f);
		$biXPelsPerMeter=freaddword($f);
		$biYPelsPerMeter=freaddword($f);
		$NumberOfPalettesUsed=freaddword($f);
		$NumberOfImportantColors=freaddword($f);
		
		if($biBitCount<16){
			$img=imagecreate($Width,$Height);
			$Colors=pow(2,$biBitCount);
			for($p=0;$p<$Colors;$p++){
				$B=freadbyte($f);
				$G=freadbyte($f);
				$R=freadbyte($f);
				$Reserved=freadbyte($f);
				$Palette[]=imagecolorallocate($img,$R,$G,$B);
			}

			if($RLECompression==0){
				$Zbytek=(4-ceil(($Width/(8/$biBitCount)))%4)%4;

				for($y=$Height-1;$y>=0;$y--){
					$CurrentBit=0;
					for($x=0;$x<$Width;$x++){
						$C=freadbits($f,$biBitCount);
						imagesetpixel($img,$x,$y,$Palette[$C]);
					}
					if($CurrentBit!=0) {freadbyte($f);}
					for($g=0;$g<$Zbytek;$g++)
					freadbyte($f);
				}

			}
		}elseif($RLECompression==1){
			$y=$Height;
			$pocetb=0;
			while(true){
				$y--;
				$prefix=freadbyte($f);
				$suffix=freadbyte($f);
				$pocetb+=2;
				$echoit=false;

				if($echoit)echo "Prefix: $prefix Suffix: $suffix<BR>";
				if(($prefix==0) && ($suffix==1)) break;
				if(feof($f)) break;

				while(!(($prefix==0) && ($suffix==0))){
					if($prefix==0){
						$pocet=$suffix;
						$Data.=fread($f,$pocet);
						$pocetb+=$pocet;
						if($pocetb%2==1) {freadbyte($f); $pocetb++;}
					}
					if($prefix>0){
						$pocet=$prefix;
						for($r=0;$r<$pocet;$r++)
						$Data.=chr($suffix);
					}
					$prefix=freadbyte($f);
					$suffix=freadbyte($f);
					$pocetb+=2;
					if($echoit) echo "Prefix: $prefix Suffix: $suffix<BR>";
				}

				for($x=0;$x<strlen($Data);$x++){
					imagesetpixel($img,$x,$y,$Palette[ord($Data[$x])]);
				}
				$Data="";

			}

		}elseif($RLECompression==2){ //$BI_RLE4
			$y=$Height;
			$pocetb=0;
			/*while(!feof($f))
			echo freadbyte($f)."_".freadbyte($f)."<BR>";*/
			while(true){
				//break;
				$y--;
				$prefix=freadbyte($f);
				$suffix=freadbyte($f);
				$pocetb+=2;

				$echoit=false;

				if($echoit)echo "Prefix: $prefix Suffix: $suffix<BR>";
				if(($prefix==0) && ($suffix==1)) break;
				if(feof($f)) break;

				while(!(($prefix==0) && ($suffix==0))){
					if($prefix==0){
						$pocet=$suffix;

						$CurrentBit=0;
						for($h=0;$h<$pocet;$h++)
						$Data.=chr(freadbits($f,4));
						if($CurrentBit!=0) freadbits($f,4);
						$pocetb+=ceil(($pocet/2));
						if($pocetb%2==1) {freadbyte($f); $pocetb++;}
					}
					if($prefix>0){
						$pocet=$prefix;
						$i=0;
						for($r=0;$r<$pocet;$r++){
							if($i%2==0){
								$Data.=chr($suffix%16);
							}else{
								$Data.=chr(floor($suffix/16));
							}
							$i++;
						}
					}
					$prefix=freadbyte($f);
					$suffix=freadbyte($f);
					$pocetb+=2;
					if($echoit) echo "Prefix: $prefix Suffix: $suffix<BR>";
				}

				for($x=0;$x<strlen($Data);$x++){
					imagesetpixel($img,$x,$y,$Palette[ord($Data[$x])]);
				}
				$Data="";

			}
		}

		if($biBitCount==16){
			$img=imagecreatetruecolor($Width,$Height);
			$Zbytek=$Width%4;

			for($y=$Height-1;$y>=0;$y--){
				for($x=0;$x<$Width;$x++){
					$BL=freadbyte($f);
					$BH=freadbyte($f);
					$B=($BL & 0x1F) * 8;
					$G=(($BH & 0x03) * 8 + (($BL & 0xE0) >> 5)) * 8;
					$R=(($BH & 0x7C) >> 2) * 8;
					$color=imagecolorexact($img,$R,$G,$B);
					if($color==-1) $color=imagecolorallocate($img,$R,$G,$B);
					imagesetpixel($img,$x,$y,$color);
				}
				for($z=0;$z<$Zbytek;$z++) freadbyte($f);
			}
		}elseif($biBitCount==24){
			$img=imagecreatetruecolor($Width,$Height);
			$Zbytek=$Width%4;

			for($y=$Height-1;$y>=0;$y--){
				for($x=0;$x<$Width;$x++){
					$B=freadbyte($f);
					$G=freadbyte($f);
					$R=freadbyte($f);
					$color=imagecolorexact($img,$R,$G,$B);
					if($color==-1) $color=imagecolorallocate($img,$R,$G,$B);
					imagesetpixel($img,$x,$y,$color);
				}
				for($z=0;$z<$Zbytek;$z++) freadbyte($f);
			}
		}elseif($biBitCount==32){
			$img=imagecreatetruecolor($Width,$Height);
			for($y=$Height-1;$y>=0;$y--){
				for($x=0;$x<$Width;$x++){
					$B=freadbyte($f);
					$G=freadbyte($f);
					$R=freadbyte($f);
					$A=freadbyte($f);
					$color=imagecolorexact($img,$R,$G,$B);
					if($color==-1) $color=imagecolorallocate($img,$R,$G,$B);
					imagesetpixel($img,$x,$y,$color);
				}
			}
		}
		return $img;

	}
	fclose($f);
}

/*
* Helping functions:
*-------------------------
*
* freadbyte($file) - reads 1 byte from $file
* freadword($file) - reads 2 bytes (1 word) from $file
* freaddword($file) - reads 4 bytes (1 dword) from $file
* freadlngint($file) - same as freaddword($file)
* decbin8($d) - returns binary string of d zero filled to 8
* RetBits($byte,$start,$len) - returns bits $start->$start+$len from $byte
* freadbits($file,$count) - reads next $count bits from $file
* RGBToHex($R,$G,$B) - convert $R, $G, $B to hex
* int_to_dword($n) - returns 4 byte representation of $n
* int_to_word($n) - returns 2 byte representation of $n
*/

function freadbyte($f){
	return ord(fread($f,1));
}

function freadword($f){
	$b1=freadbyte($f);
	$b2=freadbyte($f);
	return $b2*256+$b1;
}

function freadlngint($f){
	return freaddword($f);
}

function freaddword($f){
	$b1=freadword($f);
	$b2=freadword($f);
	return $b2*65536+$b1;
}

function RetBits($byte,$start,$len){
	$bin=decbin8($byte);
	$r=bindec(substr($bin,$start,$len));
	return $r;

}

$CurrentBit=0;
function freadbits($f,$count){
	global $CurrentBit,$SMode;
	$Byte=freadbyte($f);
	$LastCBit=$CurrentBit;
	$CurrentBit+=$count;
	if($CurrentBit==8){
		$CurrentBit=0;
	}else{
		fseek($f,ftell($f)-1);
	}
	return RetBits($Byte,$LastCBit,$count);
}

function RGBToHex($Red,$Green,$Blue){
	$hRed=dechex($Red);if(strlen($hRed)==1) $hRed="0$hRed";
	$hGreen=dechex($Green);if(strlen($hGreen)==1) $hGreen="0$hGreen";
	$hBlue=dechex($Blue);if(strlen($hBlue)==1) $hBlue="0$hBlue";
	return($hRed.$hGreen.$hBlue);
}

function int_to_dword($n){
	return chr($n & 255).chr(($n >> 8) & 255).chr(($n >> 16) & 255).chr(($n >> 24) & 255);
}

function int_to_word($n){
	return chr($n & 255).chr(($n >> 8) & 255);
}


function decbin8($d){
	return decbinx($d,8);
}

function decbinx($d,$n){
	$bin=decbin($d);
	$sbin=strlen($bin);
	for($j=0;$j<$n-$sbin;$j++)
	$bin="0$bin";
	return $bin;
}

function inttobyte($n){
	return chr($n);
}

?>