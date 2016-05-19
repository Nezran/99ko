<?php
/**
 * 99ko cms
 *
 * This source file is part of the 99ko cms. More information,
 * documentation and support can be found at http://99ko.org
 *
 * @package     99ko
 *
 * @author      Jonathan Coulet (contact@99ko.org)
 * @copyright   2016 Jonathan Coulet (contact@99ko.org)
 * @copyright   2015 Jonathan Coulet (contact@99ko.org) / Frédéric Kaplon (frederic.kaplon@me.com)
 * @copyright   2013-2014 Florent Fortat (florent.fortat@maxgun.fr) / Jonathan Coulet (contact@99ko.org) / Frédéric Kaplon (frederic.kaplon@me.com)
 * @copyright   2010-2012 Florent Fortat (florent.fortat@maxgun.fr) / Jonathan Coulet (contact@99ko.org)
 * @copyright   2010 Jonathan Coulet (contact@99ko.org)  
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

defined('ROOT') OR exit('No direct script access allowed');

class util{
	
    public static function setMagicQuotesOff(){
		if(phpversion() < 5.4){
			if(get_magic_quotes_gpc()){
				function stripslashes_gpc(&$value){
					$value = stripslashes($value);
				}
				array_walk_recursive($_GET, 'stripslashes_gpc');
				array_walk_recursive($_POST, 'stripslashes_gpc');
				array_walk_recursive($_COOKIE, 'stripslashes_gpc');
				array_walk_recursive($_REQUEST, 'stripslashes_gpc');
			}
		}
    }
    
    public static function sort2DimArray($data, $key, $mode){
    	if($mode == 'desc') $mode = SORT_DESC;
    	elseif($mode == 'asc') $mode = SORT_ASC;
    	elseif($mode == 'num') $mode = SORT_NUMERIC;
    	$temp = array();
    	foreach($data as $k=>$v){
			$temp[$k] = $v[$key];
		}
    	array_multisort($temp, $mode, $data);
    	return $data;
    }
	
	public static function cutStr($str, $length, $add = '...'){
		if(mb_strlen($str) > $length) $str = mb_strcut($str, 0, $length).$add;
		return $str;
	}

    public static function strToUrl($str){
    	$str = str_replace('&', 'et', $str);
    	if($str !== mb_convert_encoding(mb_convert_encoding($str,'UTF-32','UTF-8'),'UTF-8','UTF-32')) $str = mb_convert_encoding($str,'UTF-8');
    	$str = htmlentities($str, ENT_NOQUOTES ,'UTF-8');
    	$str = preg_replace('`&([a-z]{1,2})(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig);`i','$1',$str);
    	$str = preg_replace(array('`[^a-z0-9]`i','`[-]+`'),'-',$str);
    	return strtolower(trim($str,'-'));
    }

	public static function strCheck($str) {
		return htmlspecialchars($str, ENT_QUOTES);
	}
	
    public static function isEmail($email){
    	if(preg_match("/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@([a-zA-Z0-9-]+\.)+[a-zA-Z]{2,4}$/", $email)) return true;
    	return false;
    }
    
    public static function isValidEmail($email){
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) return false;
        return true;
    }

    public static function sendEmail($from, $reply, $to, $subject, $msg){
    	$headers = "From: ".$from."\r\n";
    	$headers.= "Reply-To: ".$reply."\r\n";
    	$headers.= "X-Mailer: PHP/".phpversion()."\r\n";
    	$headers.= 'Content-Type: text/plain; charset="utf-8"'."\r\n";
    	$headers.= 'Content-Transfer-Encoding: 8bit';
    	if(@mail($to, $subject, $msg, $headers)) return true;
    	return false;
    }

    public static function hideEmail($email){               
    	$length = strlen($email);
    	$hideEmail = '';                         
    	for ($i = 0; $i < $length; $i++){                
    	$hideEmail .= "&#" . ord($email[$i]).";";
    	}
    	return $hideEmail;
    }    

    public static function getFileExtension($file){
        return substr(strtolower(strrchr(basename($file), ".")), 1);
    }

    public static function scanDir($folder, $not = array()){
    	$data['dir'] = array();
    	$data['file'] = array();
    	foreach(scandir($folder) as $file){
    		if($file[0] != '.' && !in_array($file, $not)){
    			if(is_file($folder.$file)) $data['file'][] = $file;
    			elseif(is_dir($folder.$file)) $data['dir'][] = $file;
    		}
    	}
    	return $data;
    }

    public static function phpVersion(){
    	return substr(phpversion(), 0, 5);
    }

    public static function writeJsonFile($file, $data){
        if(@file_put_contents($file, json_encode($data), LOCK_EX)) return true; 
    	return false;
    }

    public static function readJsonFile($file, $assoc = true){
    	return json_decode(@file_get_contents($file), $assoc);
    }

    public static function uploadFile($k, $dir, $name, $validations = array()){
    	if(isset($_FILES[$k]) && $_FILES[$k]['name'] != ''){
    		$extension = mb_strtolower(util::getFileExtension($_FILES[$k]['name']));
    		if(isset($validations['extensions']) && !in_array($extension, $validations['extensions'])) return 'extension error';
    		$size = filesize($_FILES[$k]['tmp_name']);
    		if(isset($validations['size']) && $size > $validations['size']) return 'size error';
    		if(move_uploaded_file($_FILES[$k]['tmp_name'], $dir.$name.'.'.$extension)) return 'success';
    		else return 'upload error';
    	}
    	return 'undefined';
    }

    public static function htmlTable($cols, $vals, $params = ''){
    	$cols = explode(',', $cols);
    	$data = '<table '.$params.'><thead><tr>';
    	foreach($cols as $v) $data.= '<th>'.$v.'</th>';
    	$data.= '</tr></thead><tbody>';
    	foreach($vals as $v){
    		$data.= '<tr>';
    		foreach($v as $v2) $data.= '<td>'.$v2.'</td>';
    		$data.= '</tr>';
    	}
    	$data.= '</tbody><tfoot><tr>';
    	foreach($cols as $v) $data.= '<th>'.$v.'</th>';
    	$data.= '</tr></tfoot></table>';
    	return $data;
    }

    public static function htmlSelect($options, $selected = '', $params = ''){
    	$data = '<select '.$params.'>';
    	foreach($options as $k=>$v) $data.= '<option value="'.$k.'"'.(($k == $selected) ? ' selected="selected"' : '').'>'.$v.'</option>';
    	$data.= '</select>';
    	return $data;
    }

    public static function formatDate($date, $langFrom = 'en', $langTo = 'en'){
    	$date = substr($date, 0, 10);
    	$temp = preg_split('#[-_;\. \/]#', $date);
    	if($langFrom == 'en'){
    		$year = $temp[0];
    		$month = $temp[1];
    		$day = $temp[2];
    	}
    	elseif($langFrom == 'fr'){
    		$year = $temp[2];
    		$month = $temp[1];
    		$day = $temp[0];
    	}
    	if($langTo == 'en') $data = $year.'-'.$month.'-'.$day;
    	elseif($langTo == 'fr') $data = $day.'/'.$month.'/'.$year;
    	return $data;
    }
    
}
?>