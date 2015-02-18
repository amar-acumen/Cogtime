<?php
/*
For Youtube.....
No &fmt = FLV (verry low)
&fmt=5 = FLV (verry low)
&fmt=6 = FLV (works not always)
&fmt=13 = 3GP (mobile phone)
&fmt=18 = MP4 (normal)
&fmt=22 = MP4 (hd)
*/
class Embed_video {
	//private $video_site = 'youtube';
	private $video_site = '';
	private $video_url = '';
	private $zend_library_path = '';
	private $yt_video = null;
	private $resized_image = '';
	private $video_id = '';
	

	public function __construct($props = array()) {
		if (count($props) > 0) {
			$this->initialize($props);
		}
	}

	public function initialize($params = array()) {
		if (count($params) > 0) {
			foreach ($params as $key => $val) {
				if (isset($this->$key)) {
					$this->$key = $val;
				}
			}
		}
	}

	public function prepare() {
		if($this->video_url=='') {
			throw new Exception('Video url not set.');
		}
		

		// **************************** Calculate video_id *************************
		if( strstr($this->video_url, 'youtube.com') ||  strstr($this->video_url, 'youtu.be') ) {
			$this->video_site = 'youtube';

			if( strstr($this->video_url, 'youtube.com') ) {
				$pattern = '/watch(?:\?|#).*?v=([^&]+)/';
				$matches = array();
				preg_match($pattern, $this->video_url, $matches);
				
				$this->video_id = @$matches[1];
			}
			else if( strstr($this->video_url, 'youtu.be') ) {
				$pattern = '#youtu\.be/*([^/\?]+)#';
				$matches = array();
				preg_match($pattern, $this->video_url, $matches);
				
				$this->video_id = @$matches[1];
			}			
			
			#print_r($matches);
			if( $this->video_id == '' ) {
				throw new Exception('Invalid youtube url.');
			}
		}

		if( strstr($this->video_url, 'dailymotion.com') ) {
			$this->video_site = 'dailymotion';
			
			$pattern = '/dailymotion.com\/video\/([^_]+)/';
			$matches = array();
			preg_match($pattern, $this->video_url, $matches);
			
			
		## added new to get id form url  like http://www.dailymotion.com/in/relevance/search/cartoon/1#video=xz8nvp
			if(!preg_match($pattern, $this->video_url, $matches)){
				//$pattern = '/dailymotion.com\/in\/relevance\/search\/([\w]+)\/([\d]+)[^#]*(#video=([^_&]+))?/';

				$pattern = '/(#video=([^_&]+))?/';
				$matches_2 = array();
				preg_match($pattern,strstr($this->video_url, '#video='), $matches_2);
                
			}
			
			if( isset($matches[1]) ) {
				$this->video_id = $matches[1];
			}
			else if(isset($matches_2[2])){
				$this->video_id = $matches_2[2];
			}
			else {
				throw new Exception('Invalid dailymotion url.');
			}
		}

		if( strstr($this->video_url, 'vimeo.com') ) {
			$this->video_site = 'vimeo';

			$pattern = '/vimeo.com\/(.+)$/';
			$matches = array();
			preg_match($pattern, $this->video_url, $matches);
			//print_r($matches);
			if( isset($matches[1]) ) {
				$this->video_id = $matches[1];
			}
			else {
				throw new Exception('Invalid vimeo url.');
			}
		}
		// *************************************************************************

		if( $this->video_site!='youtube' && $this->video_site!='dailymotion' && $this->video_site!='vimeo' ) {
			throw new Exception('Only youtube, dailymotion, vimeo will work');
		}

		if( $this->video_site == 'youtube' ) {
			if( $this->zend_library_path == '' ) {
				throw new Exception('Zend library path is not set.');
			}
			else {
				set_include_path($this->zend_library_path.PATH_SEPARATOR.get_include_path());
				//set_include_path('C:/xampp/htdocs/Zend'.PATH_SEPARATOR.get_include_path());
				//echo get_include_path();
				require_once 'Zend/Loader.php';
				Zend_Loader::loadClass('Zend_Gdata_YouTube');
				//Zend_Loader::loadClass('Zend_Gdata_ClientLogin');

				$yt = new Zend_Gdata_YouTube();
				try {
				   $this->yt_video = $yt->getVideoEntry($this->video_id);
				}
				catch(Exception $e) {
					//print_r($e);
					throw new Exception('No video entry found.');
					#return false;
				}
			}
		}


            #return true;
	}

	// optional 5th param boolean $bool_smaller; if true create image within a rectangle
	// if false (default value) it has to be used as background of a div.
	public function save_thumb($dir, $thumb_marker, $width, $height, $bool_smaller = false) {
		$img_url = $this->get_thumb();
		//echo $thumb_marker; exit;
		//$img_url = '/opt/lampp/htdocs/video/images/group1_big.png';
		$arr = get_ext($img_url);

		$this->resized_image = $this->video_site.'-'.$this->video_id.'.'.$arr['ext']; 
		
		// added to check particular///
		$this->check_resized_image = $this->video_site.'-'.$this->video_id.$thumb_marker.'.'.$arr['ext']; 
		
		if(test_file($dir.'/'.$this->check_resized_image)) {
			for( $i=0; test_file($dir.'/'.$this->video_site.'-'.$this->video_id.'-'.$i.$thumb_marker.'.'.$arr['ext']); $i++ ) {
			}
		
			$this->resized_image = $this->video_site.'-'.$this->video_id.'-'.$i.'.'.$arr['ext'];
		}
		
		$image_destination = $dir.'/'.$this->resized_image;
		$image_source = $dir.'/'.'big-'.$this->resized_image; 
		

		copy($img_url, $image_source);

		$resize_val = $this->resize($image_source, $thumb_marker, $image_destination, $width, $height, $bool_smaller);
		@unlink($image_source);
		return $resize_val;
	}

	public function get_resized_imagename() {
		return $this->resized_image;
	}

	public function get_thumb() {
		if( $this->video_site == 'youtube' ) {
			
			$videoThumbnails = $this->yt_video->getVideoThumbnails();
			
			//dump($videoThumbnails);
			return $videoThumbnails[3]['url'];
		}

		else if( $this->video_site == 'dailymotion' ) {
			return get_302_Location('http://www.dailymotion.com/thumbnail/video/'.$this->video_id);
		}

		else if( $this->video_site == 'vimeo' ) {
			$api_url = 'http://vimeo.com/api/v2/video/';
			$json_url = $api_url.$this->video_id.'.json';
	
			$curl = curl_init($json_url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_TIMEOUT, 30);
			$json_output = curl_exec($curl);
			curl_close($curl);
	//pr($json_output);
			$video_details = json_decode($json_output);
			//echo $video_details[0]->thumbnail_large; exit;
			return $video_details[0]->thumbnail_large;
		}
	}

	// resize thumb image with
	// optional 5th param boolean $bool_smaller; if true create image within a rectangle
	// if false (default value) it has to be used as background of a div.
	public function resize($source, $thumb_marker, $destination, $width, $height, $bool_smaller = false) {
		//dump( $source);
		$ci = get_instance();
		$ci->load->library('image_lib');

		//die($source);

		$config['image_library'] = 'gd2';
		$config['source_image'] = $source;
		$config['create_thumb'] = true;
		$config['new_image'] = $destination;
		$config['thumb_marker'] = $thumb_marker;
		$config['maintain_ratio'] = true;
		$config['width'] = $width;
		$config['height'] = $height;
		if(!$bool_smaller) {
			$config['master_dim'] = getSmallerDimension($source, $width, $height);
		}
		$ci->image_lib->initialize($config);
		$bool_success = $ci->image_lib->resize();

		//@unlink($source);

		$ci->image_lib->clear();
		//echo 'success='.$bool_success;
		return $bool_success;
	}

	public function get_player($width = '', $height = '', $autoplay=false) {

		$AUTOSTART_VAR = '';

		if( $autoplay ) {
			$AUTOSTART_VAR = '&autoplay=1';
		}


        $ci = get_instance();
        
		if( $this->video_site == 'youtube' ) {
			if( $width == '' || $height == '' ) {
				/*$width = '640';
				$height = '385';*/
                $width = $ci->config->item('hp_video_width');
                $height = $ci->config->item('hp_video_height');
			}

			$AUTOSTART_VAR = my_url($AUTOSTART_VAR);
			$html = '<object  width="'.$width.'" height="'.$height.'" ><param name="wmode" value="opaque" /><param name="movie" value="http://www.youtube.com/v/'.$this->video_id.'&hl=en_US&fs=1&showsearch=0&showinfo=0"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/'.$this->video_id.'&hl=en_US&fs=1&showsearch=0&showinfo=0'. $AUTOSTART_VAR .'" type="application/x-shockwave-flash" wmode="transparent" allowscriptaccess="always" allowfullscreen="true" width="'.$width.'" height="'.$height.'"></embed></object>';

			$javascript = '';
		}

		else if( $this->video_site == 'dailymotion' ) {
			if( $width == '' || $height == '' ) {
				/*$width = '480';
				$height = '270';*/
                $width = $ci->config->item('hp_video_width');
                $height = $ci->config->item('hp_video_height');
			}

			$html = '<object width="'.$width.'" height="'.$height.'"><param name="wmode" value="opaque" /><param name="movie" value="http://www.dailymotion.com/swf/video/'.$this->video_id.'"></param><param name="allowFullScreen" value="true"></param><param name="allowScriptAccess" value="always"></param><embed type="application/x-shockwave-flash" src="http://www.dailymotion.com/swf/video/'.$this->video_id.''. $AUTOSTART_VAR .'" width="'.$width.'" wmode="transparent" height="'.$height.'" allowfullscreen="true" allowscriptaccess="always"></embed></object>';
		
			$javascript = '';
		}

		else if( $this->video_site == 'vimeo' ) {
			if( $width == '' || $height == '' ) {
				/*$width = '400';
				$height = '225';*/
                $width = $ci->config->item('hp_video_width');
                $height = $ci->config->item('hp_video_height');
			}

			$html = '<object width="'.$width.'" height="'.$height.'"><param name="wmode" value="opaque" /><param name="allowfullscreen" value="true" /><param name="allowscriptaccess" value="always" /><param name="movie" value="http://vimeo.com/moogaloop.swf?clip_id='.$this->video_id.'&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=&amp;fullscreen=1" /><embed src="http://vimeo.com/moogaloop.swf?clip_id='.$this->video_id.'&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=&amp;fullscreen=1'. $AUTOSTART_VAR .'" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" wmode="transparent" width="'.$width.'" height="'.$height.'"></embed></object>';

			$javascript = '';
		}

		return array('html'=>$html, 'javascript'=>$javascript);
	}

	public function get_youtube_video($video_url = '') {
		//$conn = curl_init();
		//curl_setopt($ch, CURLOPT_URL, $url);
		if( $video_url == '' ) {
			$video_url = $this->video_url;
		}
		else {
			$pattern = '/watch(?:\?|#).*?v=([^&]+)/';
			$matches = array();
			preg_match($pattern, $this->video_url, $matches);
			//print_r($matches);
			if( isset($matches[1]) ) {
				$this->video_id = $matches[1];
			}
			else {
				throw new Exception('Invalid youtube url.');
			}
		}

		$curl = curl_init($video_url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);
		$html = curl_exec($curl);
		curl_close($curl);

		//$html = $this->conn->get($url);
		//dump($html);
		$matches = array();
		preg_match('/"t": "(.*?)"/', $html, $matches);

		if( isset($matches[1]) ) {
			$t  = $matches[1];
		}
		else {
			throw new Exception('Cannot fetch data from youtube.');
		}

		$url = "http://www.youtube.com/get_video?video_id=".$this->video_id."&t=".$t;

		return $url;


// *************************** Slower method ***********************
// 		$content = file_get_contents("http://youtube.com/get_video_info?video_id=".$this->video_id);
// 		parse_str($content);
// 		if( isset($token) ) {
// 			$url = "http://www.youtube.com/get_video?video_id=".$this->video_id."&t=".$token;
// 		}
// 		else {
// 			throw new Exception('Cannot fetch data from youtube.');
// 		}
// 		return $url;
// *****************************************************************

		//return get_302_Location($url);
	}


     // ========================================================================
     //       PREMIUM-CONTENTS VIDEO SCREENSHOT RESIZE [BEGIN]
     // ========================================================================

	public function save_thumb_premium_video($dir, $thumb_marker, $width, $height) {

            $img_url = $this->get_thumb();
            //$img_url = '/opt/lampp/htdocs/video/images/group1_big.png';
            $arr = get_ext($img_url);

            $this->resized_image = $this->video_site.'-'.$this->video_id.'.'.$arr['ext'];

            if(test_file($dir.'/'.$this->resized_image)) {
                    for( $i=0; test_file($dir.'/'.$this->video_site.'-'.$this->video_id.'-'.$i.'.'.$arr['ext']); $i++ ) {
                    }

                    $this->resized_image = $this->video_site.'-'.$this->video_id.'-'.$i.'.'.$arr['ext'];
            }

            $image_destination = $dir.'/'.$this->resized_image;
            $image_source = $dir.'/'.'big-'.$this->resized_image;


            copy($img_url, $image_source);

            return $this->resize_premium_video($image_source, $thumb_marker, $image_destination, $width, $height, $bool_smaller);
	}


	public function resize_premium_video($source, $thumb_marker, $destination, $width, $height, $bool_smaller = false) {
		//dump( $source);
		$ci = get_instance();
		$ci->load->library('image_lib');

		//die($source);

		$config['image_library'] = 'gd2';
		$config['source_image'] = $source;
		$config['create_thumb'] = true;
		$config['new_image'] = $destination;
		$config['thumb_marker'] = $thumb_marker;
		$config['maintain_ratio'] = true;
		$config['width'] = $width;
		$config['height'] = $height;
		if(!$bool_smaller) {
			$config['master_dim'] = getSmallerDimension($source, $width, $height);
		}
		$ci->image_lib->initialize($config);
		$bool_success = $ci->image_lib->resize();

		//@unlink($source);

		$ci->image_lib->clear();
		//echo 'success='.$bool_success;
		return $bool_success;
	}

     // ========================================================================
     //       PREMIUM-CONTENTS VIDEO SCREENSHOT RESIZE [END]
     // ========================================================================
     
     
     
     
     // ========================================================================
     //       GET VIDEO DURATION [START]
     // ========================================================================
     
     function get_video_duration()
     {
         if($this->video_site == 'youtube')
         {
             //require_once 'Zend/Loader.php';
             //Zend_Loader::loadClass('Zend_Gdata_YouTube');
             $yt = new Zend_Gdata_YouTube();
             $videoEntry = $yt->getVideoEntry($this->video_id);
             $duration = $videoEntry->getVideoDuration();
             return $duration;
         }
         if($this->video_site == 'dailymotion')
         {
             $url = "https://api.".$this->video_site.".com/video/".$this->video_id."?fields=duration";
             
             
             $result = file_get_contents($url);
             $content = json_decode($result) ;
             $duration = $content ->duration;
             
             return $duration;
              
         }
         if($this->video_site == 'vimeo')
         {
             $url = "https://vimeo.com/api/v2/video/".$this->video_id.".json";
             
             $result = file_get_contents($url);
             $content = json_decode($result);
             $duration = $content->duration;
             return $duration;
         }
     }
     
     // ========================================================================
     //       GET VIDEO DURATION [END]
     // ========================================================================
     

} 


