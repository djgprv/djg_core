<?php
/*
 * Wolf CMS - Content Management Simplified. <http://www.wolfcms.org>
 * Copyright (C) 2008-2010 Martijn van der Kleijn <martijn.niji@gmail.com>
 *
 * This file is part of Wolf CMS. Wolf CMS is licensed under the GNU GPLv3 license.
 * Please see license.txt for the full license text.
 */

/**
 * @package Plugins
 * @subpackage djg_core
 *
 * @author Michał Uchnast <djgprv@gmail.com>
 * @copyright kreacjawww.pl, 2012
 * @license http://www.gnu.org/licenses/gpl.html GPLv3 license
 */

class Djgcore {
	function Djgcore()
	{
		// constructor;
	}
	/*
	ver. 0.0.1
	*/
	public static function executeSql($sql)
	{
		$PDO = Record::getConnection();
		$stmt = $PDO->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	/*
	ver. 0.0.2
	*/
	public static function translateTitle($parent)
	{
		if($_SESSION['lang']) $lang=$_SESSION['lang']; else $lang="pl";
		if ($parent->hasContent('titles')):
			$titles2 = $parent->content('titles');
			$res = preg_match('/'.$_SESSION['lang'].':\s*(.*)/i', $titles2, $results2);
			if($res == 1):
				$title2 = trim($results2[1]);
			else: 
				$title2 = $parent->title();
			endif;
		else:
			$title2 = $parent->title();
		endif;
		return $title2;
	}
	public static function translateContent($content)
	{
		return $content;
	}
	/**
	example: <?php $ids=array(4,9,15); echo Djgcore::menuTree(Page::find('/'),2,8,$ids); ?>
	*/
	public static function menuTree($parent,$level=2,$count=8,$ids=array(),$noContentLink=false)
	{
	$i=1;
	$translate = false;
    $out = '';
    $childs = $parent->children();
    if (count($childs) > 0):
		if(($childs[0]->level()) <= $level):
			if( ($childs[0]->level()!=1) ) $out = '<ul>';
			foreach ($childs as $child)
					if(!in_array($child->parent()->id(), $ids)):
						if($childs[0]->level()==1):
							$span = '';
							$i++;
						else:
							$span = '';
						endif;
						$out .= '<li>'.$child->link($child->title(),(url_start_with($child->slug) ? ' class="current"': null)).self::menuTree($child,$level,$count,$ids,$noContentLink).'</li>';
					endif;
			if(($childs[0]->level()) !=1 )$out .= '</ul>';
		endif;
    endif;
	$out = str_replace("<ul></ul>", "", $out);
    return $out;
	}
	/*
	ver. 0.0.3
	*/
	/**
	 * Translates a number to a short alhanumeric version
	 *
	 * Translated any number up to 9007199254740992
	 * to a shorter version in letters e.g.:
	 * 9007199254740989 --> PpQXn7COf
	 *
	 * specifiying the second argument true, it will
	 * translate back e.g.:
	 * PpQXn7COf --> 9007199254740989
	 *
	 * this function is based on any2dec && dec2any by
	 * fragmer[at]mail[dot]ru
	 * see: http://nl3.php.net/manual/en/function.base-convert.php#52450
	 *
	 * If you want the alphaID to be at least 3 letter long, use the
	 * $pad_up = 3 argument
	 *
	 * In most cases this is better than totally random ID generators
	 * because this can easily avoid duplicate ID's.
	 * For example if you correlate the alpha ID to an auto incrementing ID
	 * in your database, you're done.
	 *
	 * The reverse is done because it makes it slightly more cryptic,
	 * but it also makes it easier to spread lots of IDs in different
	 * directories on your filesystem. Example:
	 * $part1 = substr($alpha_id,0,1);
	 * $part2 = substr($alpha_id,1,1);
	 * $part3 = substr($alpha_id,2,strlen($alpha_id));
	 * $destindir = "/".$part1."/".$part2."/".$part3;
	 * // by reversing, directories are more evenly spread out. The
	 * // first 26 directories already occupy 26 main levels
	 *
	 * more info on limitation:
	 * - http://blade.nagaokaut.ac.jp/cgi-bin/scat.rb/ruby/ruby-talk/165372
	 *
	 * if you really need this for bigger numbers you probably have to look
	 * at things like: http://theserverpages.com/php/manual/en/ref.bc.php
	 * or: http://theserverpages.com/php/manual/en/ref.gmp.php
	 * but I haven't really dugg into this. If you have more info on those
	 * matters feel free to leave a comment.
	 *
	 * @author  Kevin van Zonneveld <kevin@vanzonneveld.net>
	 * @author  Simon Franz
	 * @author  Deadfish
	 * @copyright 2008 Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD Licence
	 * @version   SVN: Release: $Id: alphaID.inc.php 344 2009-06-10 17:43:59Z kevin $
	 * @link    http://kevin.vanzonneveld.net/
	 *
	 * @param mixed   $in    String or long input to translate
	 * @param boolean $to_num  Reverses translation when true
	 * @param mixed   $pad_up  Number or boolean padds the result up to a specified length
	 * @param string  $passKey Supplying a password makes it harder to calculate the original ID
	 *
	 * @return mixed string or long
	 * !	require PHP: BC Math
	 */
	public static function alphaID($in, $to_num = false, $pad_up = false, $passKey = null)
	{
	  $index = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	  if ($passKey !== null) {
		// Although this function's purpose is to just make the
		// ID short - and not so much secure,
		// with this patch by Simon Franz (http://blog.snaky.org/)
		// you can optionally supply a password to make it harder
		// to calculate the corresponding numeric ID
		for ($n = 0; $n<strlen($index); $n++) {
		  $i[] = substr( $index,$n ,1);
		}
		$passhash = hash('sha256',$passKey);
		$passhash = (strlen($passhash) < strlen($index))
		  ? hash('sha512',$passKey)
		  : $passhash;
		for ($n=0; $n < strlen($index); $n++) {
		  $p[] =  substr($passhash, $n ,1);
		}
		array_multisort($p,  SORT_DESC, $i);
		$index = implode($i);
	  }
	  $base  = strlen($index);
	 
	  if ($to_num) {
		// Digital number  <<--  alphabet letter code
		$in  = strrev($in);
		$out = 0;
		$len = strlen($in) - 1;
		for ($t = 0; $t <= $len; $t++) {
		  $bcpow = bcpow($base, $len - $t);
		  $out   = $out + strpos($index, substr($in, $t, 1)) * $bcpow;
		} 
		if (is_numeric($pad_up)) {
		  $pad_up--;
		  if ($pad_up > 0) {
			$out -= pow($base, $pad_up);
		  }
		}
		$out = sprintf('%F', $out);
		$out = substr($out, 0, strpos($out, '.'));
	  } else {
		// Digital number  -->>  alphabet letter code
		if (is_numeric($pad_up)) {
		  $pad_up--;
		  if ($pad_up > 0) {
			$in += pow($base, $pad_up);
		  }
		}
		$out = "";	
		for ($t = floor(log($in, $base)); $t >= 0; $t--) {	
		  $bcp = bcpow($base, $t);
		  $a   = floor($in / $bcp) % $base;
		  $out = $out . substr($index, $a, 1);
		  $in  = $in - ($a * $bcp);
		  
		}
		$out = strrev($out); // reverse
	  }
	 
	  return $out;
	} // end base_encode()
	/*
	* uniqeId
	* method return uniqe string (6 chars)
	* parametrs: none;
	*/
	public static function uniqeId()
	{
	$u='';$i=0;
	do{
		usleep(5);
		$t = microtime(true);
		$t = (int)substr($t,0,strpos($t,'.')).substr($t,strpos($t,'.')+1,strlen($t));
		$u = (string)self::alphaID($t);
		$i++;
		//if ($i>10): return false; exit(); endif;
	} while (strlen($u) == 7);
	return $u;
	} 		
	/*
	ver. 0.0.4
	*/
	public static function mimeByExt($ext=NULL) { 
    $mime_types = array(
	"323" => "text/h323",
    "acx" => "application/internet-property-stream",
    "ai" => "application/postscript",
    "aif" => "audio/x-aiff",
    "aifc" => "audio/x-aiff",
    "aiff" => "audio/x-aiff",
    "asf" => "video/x-ms-asf",
    "asr" => "video/x-ms-asf",
    "asx" => "video/x-ms-asf",
    "au" => "audio/basic",
    "avi" => "video/x-msvideo",
    "axs" => "application/olescript",
    "bas" => "text/plain",
    "bcpio" => "application/x-bcpio",
    "bin" => "application/octet-stream",
    "bmp" => "image/bmp",
    "c" => "text/plain",
    "cat" => "application/vnd.ms-pkiseccat",
    "cdf" => "application/x-cdf",
    "cer" => "application/x-x509-ca-cert",
    "class" => "application/octet-stream",
    "clp" => "application/x-msclip",
    "cmx" => "image/x-cmx",
    "cod" => "image/cis-cod",
    "cpio" => "application/x-cpio",
    "crd" => "application/x-mscardfile",
    "crl" => "application/pkix-crl",
    "crt" => "application/x-x509-ca-cert",
    "csh" => "application/x-csh",
    "css" => "text/css",
    "dcr" => "application/x-director",
    "der" => "application/x-x509-ca-cert",
    "dir" => "application/x-director",
    "dll" => "application/x-msdownload",
    "dms" => "application/octet-stream",
    "doc" => "application/msword",
    "dot" => "application/msword",
    "dvi" => "application/x-dvi",
    "dxr" => "application/x-director",
    "eps" => "application/postscript",
    "etx" => "text/x-setext",
    "evy" => "application/envoy",
    "exe" => "application/octet-stream",
    "fif" => "application/fractals",
    "flr" => "x-world/x-vrml",
    "gif" => "image/gif",
    "gtar" => "application/x-gtar",
    "gz" => "application/x-gzip",
    "h" => "text/plain",
    "hdf" => "application/x-hdf",
    "hlp" => "application/winhlp",
    "hqx" => "application/mac-binhex40",
    "hta" => "application/hta",
    "htc" => "text/x-component",
    "htm" => "text/html",
    "html" => "text/html",
    "htt" => "text/webviewhtml",
    "ico" => "image/x-icon",
    "ief" => "image/ief",
    "iii" => "application/x-iphone",
    "ins" => "application/x-internet-signup",
    "isp" => "application/x-internet-signup",
    "jfif" => "image/pipeg",
    "jpe" => "image/jpeg",
    "jpeg" => "image/jpeg",
    "jpg" => "image/jpeg",
    "js" => "application/x-javascript",
    "latex" => "application/x-latex",
    "lha" => "application/octet-stream",
    "lsf" => "video/x-la-asf",
    "lsx" => "video/x-la-asf",
    "lzh" => "application/octet-stream",
    "m13" => "application/x-msmediaview",
    "m14" => "application/x-msmediaview",
    "m3u" => "audio/x-mpegurl",
    "man" => "application/x-troff-man",
    "mdb" => "application/x-msaccess",
    "me" => "application/x-troff-me",
    "mht" => "message/rfc822",
    "mhtml" => "message/rfc822",
    "mid" => "audio/mid",
    "mny" => "application/x-msmoney",
    "mov" => "video/quicktime",
    "movie" => "video/x-sgi-movie",
    "mp2" => "video/mpeg",
    "mp3" => "audio/mpeg",
    "mpa" => "video/mpeg",
    "mpe" => "video/mpeg",
    "mpeg" => "video/mpeg",
    "mpg" => "video/mpeg",
    "mpp" => "application/vnd.ms-project",
    "mpv2" => "video/mpeg",
    "ms" => "application/x-troff-ms",
    "mvb" => "application/x-msmediaview",
    "nws" => "message/rfc822",
    "oda" => "application/oda",
    "p10" => "application/pkcs10",
    "p12" => "application/x-pkcs12",
    "p7b" => "application/x-pkcs7-certificates",
    "p7c" => "application/x-pkcs7-mime",
    "p7m" => "application/x-pkcs7-mime",
    "p7r" => "application/x-pkcs7-certreqresp",
    "p7s" => "application/x-pkcs7-signature",
    "pbm" => "image/x-portable-bitmap",
    "pdf" => "application/pdf",
    "pfx" => "application/x-pkcs12",
    "pgm" => "image/x-portable-graymap",
    "pko" => "application/ynd.ms-pkipko",
    "pma" => "application/x-perfmon",
    "pmc" => "application/x-perfmon",
    "pml" => "application/x-perfmon",
    "pmr" => "application/x-perfmon",
    "pmw" => "application/x-perfmon",
    "pnm" => "image/x-portable-anymap",
    "pot" => "application/vnd.ms-powerpoint",
    "ppm" => "image/x-portable-pixmap",
    "pps" => "application/vnd.ms-powerpoint",
    "ppt" => "application/vnd.ms-powerpoint",
    "prf" => "application/pics-rules",
    "ps" => "application/postscript",
    "pub" => "application/x-mspublisher",
    "qt" => "video/quicktime",
    "ra" => "audio/x-pn-realaudio",
    "ram" => "audio/x-pn-realaudio",
    "ras" => "image/x-cmu-raster",
    "rgb" => "image/x-rgb",
    "rmi" => "audio/mid",
    "roff" => "application/x-troff",
    "rtf" => "application/rtf",
    "rtx" => "text/richtext",
    "scd" => "application/x-msschedule",
    "sct" => "text/scriptlet",
    "setpay" => "application/set-payment-initiation",
    "setreg" => "application/set-registration-initiation",
    "sh" => "application/x-sh",
    "shar" => "application/x-shar",
    "sit" => "application/x-stuffit",
    "snd" => "audio/basic",
    "spc" => "application/x-pkcs7-certificates",
    "spl" => "application/futuresplash",
    "src" => "application/x-wais-source",
    "sst" => "application/vnd.ms-pkicertstore",
    "stl" => "application/vnd.ms-pkistl",
    "stm" => "text/html",
    "svg" => "image/svg+xml",
    "sv4cpio" => "application/x-sv4cpio",
    "sv4crc" => "application/x-sv4crc",
    "t" => "application/x-troff",
    "tar" => "application/x-tar",
    "tcl" => "application/x-tcl",
    "tex" => "application/x-tex",
    "texi" => "application/x-texinfo",
    "texinfo" => "application/x-texinfo",
    "tgz" => "application/x-compressed",
    "tif" => "image/tiff",
    "tiff" => "image/tiff",
    "tr" => "application/x-troff",
    "trm" => "application/x-msterminal",
    "tsv" => "text/tab-separated-values",
    "txt" => "text/plain",
    "uls" => "text/iuls",
    "ustar" => "application/x-ustar",
    "vcf" => "text/x-vcard",
    "vrml" => "x-world/x-vrml",
    "wav" => "audio/x-wav",
    "wcm" => "application/vnd.ms-works",
    "wdb" => "application/vnd.ms-works",
    "wks" => "application/vnd.ms-works",
    "wmf" => "application/x-msmetafile",
    "wps" => "application/vnd.ms-works",
    "wri" => "application/x-mswrite",
    "wrl" => "x-world/x-vrml",
    "wrz" => "x-world/x-vrml",
    "xaf" => "x-world/x-vrml",
    "xbm" => "image/x-xbitmap",
    "xla" => "application/vnd.ms-excel",
    "xlc" => "application/vnd.ms-excel",
    "xlm" => "application/vnd.ms-excel",
    "xls" => "application/vnd.ms-excel",
    "xlt" => "application/vnd.ms-excel",
    "xlw" => "application/vnd.ms-excel",
    "xof" => "x-world/x-vrml",
    "xpm" => "image/x-xpixmap",
    "xwd" => "image/x-xwindowdump",
    "z" => "application/x-compress",
    "zip" => "application/zip"
	);
	return $mime[$ext];
   }
	/*
	ver. 0.0.5
	*/
	public static function copyDirectory( $source, $target ) {
		if ( is_dir( $source ) ):
			@mkdir( $target );
			$d = dir( $source );
			while ( FALSE !== ( $entry = $d->read() ) ):
				if ( $entry == '.' || $entry == '..' ): continue; endif;
				$Entry = $source . '/' . $entry; 
				if ( is_dir( $Entry ) ): self::copyDirectory( $Entry, $target . '/' . $entry ); continue; endif;
				copy( $Entry, $target . '/' . $entry );
			endwhile;
			$d->close();
		else: copy( $source, $target );
		endif;
	}
	/*
	
	*/
	public static function directoryToArray($directory, $recursive) {
		$array_items = array();
		if ($handle = opendir($directory)) {
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != "..") {
					if (is_dir($directory. "/" . $file)) {
						if($recursive) {
							$array_items = array_merge($array_items, self::directoryToArray($directory. "/" . $file, $recursive));
						}
						$file = $directory . "/" . $file;
						$array_items[] = preg_replace("/\/\//si", "/", $file);
					} else {
						$file = $directory . "/" . $file;
					//	$array_items[] = preg_replace("/\/\//si", "/", $file);
					}
				}
			}
			closedir($handle);
		}
		return $array_items;
	}
	/*
	ver. 0.0.6
	*/
	public static function removeDir($path) {
		$dir = new DirectoryIterator($path);
		foreach ($dir as $fileinfo):
			if ($fileinfo->isFile() || $fileinfo->isLink()):
				unlink($fileinfo->getPathName());
			elseif (!$fileinfo->isDot() && $fileinfo->isDir()):
				self::removeDir($fileinfo->getPathName());
			endif;
		endforeach;
	rmdir($path);
	return true;
	}
	/**
	* ver. 0.0.7
	* mail spam protect
	* return hashed adress
	*/
		/** how to use
		theme:
		<script type="text/javascript">// <![CDATA[	$(document).ready(function(){$('span.djg_core_mail').css("cursor","pointer");$('span.djg_core_mail').click(function() {var value = $(this).attr('id').replace('#','@'); value = value.replace(/[^a-zA-Z0-9@.]+/g,''); $(this).replaceWith('<a href="mailto:'+value+'">'+value+'</a>'); }); }); 	// ]]></script>
		content:
		 <?php echo Djgcore::protectEmail('youremaill@domain.com'); ?></p>
	*/
	public static function protectEmail($adress) {
		$chars="!$%^&*()_+";
		$new = "";
		for($i = 0; $i < strlen($adress); $i++) $new = $new.$adress[$i].$chars[rand(0,strlen($chars)-1)];
		$new = str_replace("@", "#", $new);
		return '<span class="djg_core_mail" id="'.$new.'">'.__('click to show adress').'</span>';
	}
	/**
	* ver. 0.1
	* phone number link for mobile
	*/
		/** how to use
		theme:
		<script type="text/javascript">// <![CDATA[	$(document).ready(function(){$('span.djg_core_phone').css("cursor","pointer");$('span.djg_core_phone').click(function() {var value = $(this).attr('id').replace('#','+'); value = value.replace(/[^0-9+.]+/g,''); $(this).replaceWith('<a href="tel:'+value+'">'+value+'</a>'); }); }); // ]]></script>
		content:
		 <?php echo Djgcore::protectPhone('+48 501-502-503'); ?></p>
	*/
	public static function protectPhone($phonenumber) {
		$chars="!$%^&*()_abcdefghijklmnoprstuwzABCDEFGHIJKLMNOPRSTUWZ";
		$new = "";
		for($i = 0; $i < strlen($phonenumber); $i++) $new = $new.$phonenumber[$i].$chars[rand(0,strlen($chars)-1)];
		$new = str_replace("+", "#", $new);
		return '<span class="djg_core_phone" id="'.$new.'">'.__('click to show phone number').'</span>';
	}
}