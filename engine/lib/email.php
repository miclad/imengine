<?

class Email{
	
	function sendMail($to, $subject, $message, $from = "", $file = "", $file_orig_name = "", $content_type = "text/plain") {
		
		$header = $body = "";	
		$m_bound = md5("mailbound12345");
		$file_attached = $file != "" && file_exists($file);
		
		$subject = "=?utf-8?B?".base64_encode($subject)."?=";
		
		$header .= "Content-type: ".$content_type."; charset=\"utf-8\"\r\n";
		if($from != "")		
			$header .= "From: ".$from."\r\n";
		$header .= "Subject: ".$subject."\r\n";				
		
		if($file_attached){
			
			$header .= "mime-version: 1.0\nContent-type: multipart/mixed; boundary=\"".$m_bound."\"";
			$orig_file = $file_orig_name != "" ? $file_orig_name : $file;
			$f_info = pathinfo($orig_file);
			$f_type = "application/octet-stream";//mime_content_type($orig_file);
			$f = fopen($file,"rb");
			$file_contents = base64_encode(fread($f,filesize($file)));
			
			$body = "--".$m_bound."\n";
			$body .= "Content-type: ".$content_type."; charset=\"utf-8\"\n\n";
			$body .= $message;
		
			$body .= "\n\n--".$m_bound."\n";
			$body .= "Content-type: ".$f_type."; name=\"".$f_info["basename"]."\"\ncontent-transfer-encoding:base64\ncontent-disposition:attachment\n\n";
			$body .= $file_contents;
			$body .= "\n\n".$m_bound."--\n\n";
			
		}else{
			$header .= "Content-type: ".$content_type."; charset=\"utf-8\"\r\n";
			$body = $message;
		}
		
		
		if($_SERVER['SERVER_ADDR'] == $_SERVER['REMOTE_ADDR']){
			$res = 1;	
			$write_cnt = $content_type == 'text/html' ? $body : $to."\n--\n".$subject."\n--\n".$body."\n\n\n--\n".$header;
			file_put_contents(B_DIR.'/_emails/'.md5(time().$to).($content_type == 'text/html' ? '.html' : '.txt'), $write_cnt);
		}else{
			$res = mail($to, $subject, $body, $header);
		}
		return $res;
		
	}
	
}

?>