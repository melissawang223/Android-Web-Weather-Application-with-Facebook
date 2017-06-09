<?php
error_reporting(0);
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
  $xml;
  if(!empty($_GET)){
  
  	   $xml = simplexml_load_file("https://maps.googleapis.com/maps/api/geocode/xml?address="
           .$_GET['streetInput']."&citystatezip=".$_GET['cityInput']."%2C+".$_GET['stateInput']."&key=AIzaSyBwnf1v4ExJJh8-SEasW0eW0r7YuDvfcOs");

       
    }
  if(!empty($_POST)){
   		$xml = simplexml_load_file("https://maps.googleapis.com/maps/api/geocode/xml?address="
           .$_POST['streetInput']."&citystatezip=".$_POST['cityInput']."%2C+".$_POST['stateInput']."&key=AIzaSyBwnf1v4ExJJh8-SEasW0eW0r7YuDvfcOs");
       
    }
  
 
    if($xml->message->code != 0){
        
        $arr = array("not_found" => "1");
        echo  json_encode($arr);
    } 
    
    else{
    
        $result = $xml->result->geometry->location;
        $lat = $result->lat;
        $lng = $result->lng;
    
    
	    $content = file_get_contents("https://api.forecast.io/forecast/d0064d0a892c441e4202e9d6da1cd3d0/".$lat.",".$lng."?units=".$_GET['unit']."&exclude=flags");
	    $json = json_decode($content, true);
        
        $icon = $json['currently']['icon'];
        $precipitation = $json['currently']['precipIntensity'];

        if ($precipitation == "0") {
      	    $precipitation= "None";
        }
        elseif ($precipitation == "0.002") {
            $precipitation= "Very Light";
        }
        elseif ($precipitation == "0.017") {
            $precipitation= "Light";
        }
        elseif ($precipitation == "0.1") {
            $precipitation= "Moderate";
        }
        elseif ($precipitation == "0.4") {
            $precipitation= "Heavy";
        }


        $precipProbability=$json['currently']['precipProbability'];
        $wind=$json['currently']['windSpeed'];
        $hum=$json['currently']['humidity'];
        $sunset = $json['daily']['data'][0][sunsetTime];
        $temper = $json['currently']['temperature'];
        $visibility_integer=$json['currently']['visibility'];
        $timezone = $json['timezone'];
        $sunrise = $json['daily']['data'][0][sunriseTime];
        $n24h_data = $json['hourly']['data'];
        $n7d_data = $json['daily']['data'];
  
  
   
    
        $arr = array(
            "now" => array(
                "precipitation"               =>check_data(          (string)$precipitation),
                "rain"                        =>check_data(          (string)precent($precipProbability)),
                "windspeed"                   =>check_data(          (string)wind($wind)),
                "dewpoint"                    =>check_data(          (string)(int)$json['currently']['dewPoint']),
                "humidity"                    =>check_data(          (string)precent($hum)),
                "visibility"                  =>check_data(          (string)visibility($visibility_integer)),
                "sunrise"                     =>check_data(          (string)Sun($sunrise,$timezone)),
                "sunset"                      =>check_data(          (string)Sun($sunset,$timezone)), 
                "temper"                      =>check_data(          (string)unit($temper)),  
                "icon"                        =>check_data(          (string)icon($icon)),
                "mintemp"                     =>check_data(          (string)unit2($n7d_data[0]['temperatureMin'])),
                "maxtemp"                     =>check_data(          (string)unit2($n7d_data[0]['temperatureMax'])),
                "summary"                     =>check_data(          (string)$json['currently']['summary']),
                "picture"					  =>check_data(          (string)picture($icon)),
                "lat"						  =>$lat,
                "lng"						  =>$lng
    
            )
        
        );
   
        echo json_encode($arr) ;  
         
    }//end else

    
    function cloudcover($str){
        if(empty($str)){
            return "0";
        }   
        else  
            return $str;
    }   
    function check_data($str){
        if(empty($str)){
            return "N/A";
        }   
        else  
            return $str;
    }   
    
    function precent($pre){
    	if(empty($pre)){
        	$pre=0;
    	}
    	$pre=$pre*100;
    	return $pre." %";
	}
	
	function visibility($vis){
    	if(empty($vis)){
        	$vis=0;
    	}
    
    	return (int)$vis." mi";
	}
	
	function wind($wind){
    	if(empty($wind)){
        	return "";
    	}
    	return $wind." mph";
	}

	function unit($unit){
    	if(empty($unit)){
        	return "";
    	}
    	if($_GET['unit']=="us"){
    		return (int) $unit." &#176; F";
    	}
    	else{
    		return (int) $unit." &#176; C";
    	}
	}

	function unit2($unit){
    	if(empty($unit)){
        	return "";
    	}
    	
    	return (int) $unit." &#176;";
    	
	}
	
	function Sun($unixtime,$time){
    	if(empty($unixtime)){
        	return "";
    	}
    	if(empty($time)){
        	date_default_timezone_set('America/Los_Angeles');
    	}
    
    	date_default_timezone_set($time);
    	return date('h:i A', $unixtime);
	}
	function icon($icon){
    	if ($icon == "clear-day") {
      	$icon= "<img src='clear.png' height='50' width='50'/>";
  		}
  		elseif ($icon == "clear-night") {
      		$icon= "<img src='clear_night.png' height='50' width='50'/>";
  		}
  		elseif ($icon == "rain") {
      		$icon= "<img src='rain.png' height='50' width='50' />";
  		}
  		elseif ($icon == "snow") {
      		$icon= "<img src='snow.png' height='50' width='50'/>";
  		}
  		elseif ($icon == "sleet") {
      		$icon= "<img src='sleet.png' height='50' width='50' />";
  		}
  		elseif ($icon == "wind") {
      		$icon= "<img src='wind.png' height='50' width='50'/>";
  		}
  		elseif ($icon == "fog") {
      		$icon= "<img src='fog.png' height='50' width='50'/>";
  		}
  		elseif ($icon == "cloudy") {
      		$icon= "<img src='cloudy.png' height='50' width='50'/>";
  		}
  		elseif ($icon == "partly-cloudy-day") {
      		$icon= "<img src='cloud_day.png' height='50' width='50' />";
  		}
  		elseif ($icon == "partly-cloudy-night") {
      		$icon= "<img src='cloud_night.png' height='50' width='50'/>";
  		}
  		return $icon;
	}
	function iconsmall($icon){
    	if ($icon == "clear-day") {
      		$icon= "<img src='clear.png' height='40' width='40'>";
  		}
  		elseif ($icon == "clear-night") {
      		$icon= "<img src='clear_night.png' height='40' width='40'>";
  		}
  		elseif ($icon == "rain") {
      		$icon= "<img src='rain.png' height='40' width='40'>";
  		}
  		elseif ($icon == "snow") {
      		$icon= "<img src='snow.png' height='40' width='40'>";
  		}
  		elseif ($icon == "sleet") {
      		$icon= "<img src='sleet.png' height='40' width='40'>";
  		}
  		elseif ($icon == "wind") {
      		$icon= "<img src='wind.png' height='40' width='40'>";
  		}
  		elseif ($icon == "fog") {
      		$icon= "<img src='fog.png' height='40' width='40'>";
  		}
  		elseif ($icon == "cloudy") {
      		$icon= "<img src='cloudy.png' height='40' width='40'>";
  		}
  		elseif ($icon == "partly-cloudy-day") {
      		$icon= "<img src='cloud_day.png' height='40' width='40'>";
  		}
  		elseif ($icon == "partly-cloudy-night") {
      		$icon= "<img src='cloud_night.png' height='40' width='40'>";
  		}
  		return $icon;
	}
	
	function picture($icon){
    	if ($icon == "clear-day") {
      		$icon= 'clear.png' ;
  		}
  		elseif ($icon == "clear-night") {
      		$icon= 'clear_night.png' ;
  		}
  		elseif ($icon == "rain") {
      		$icon= 'rain.png';
  		}
  		elseif ($icon == "snow") {
      		$icon= 'snow.png';
  		}
  		elseif ($icon == "sleet") {
      		$icon= 'sleet.png';
  		}
  		elseif ($icon == "wind") {
      		$icon= 'wind.png';
  		}
  		elseif ($icon == "fog") {
      		$icon= 'fog.png';
  		}
  		elseif ($icon == "cloudy") {
      		$icon= 'cloudy.png' ;
  		}
  		elseif ($icon == "partly-cloudy-day") {
      		$icon= 'cloud_day.png' ;
  		}
  		elseif ($icon == "partly-cloudy-night") {
      		$icon= 'cloud_night.png';
  		}
  		return $icon;
	}
	
	
	function day($unixtime,$time){
    	if(empty($unixtime)){
        	return "";
    	}
    	if(empty($time)){
        	date_default_timezone_set('America/Los_Angeles');
    	}
    
    	date_default_timezone_set($time);
    	if(date('D', $unixtime)=="Mon"){
    		return "Monday";
    	}
    	else if(date('D', $unixtime)=="Tue"){
    		return "Tuesday";
    	}
    	else if(date('D', $unixtime)=="Wed"){
    		return "Wednesday";
    	}
    	else if(date('D', $unixtime)=="Thu"){
    		return "Thursday";
    	}
    	else if(date('D', $unixtime)=="Fri"){
    		return "Friday";
    	}
    	else if(date('D', $unixtime)=="Sat"){
    		return "Saturday ";
    	}
    	else if(date('D', $unixtime)=="Sun"){
    		return "Sunday";
    	}
    	return date('D', $unixtime);
	}

	function mondate($unixtime,$time){
    	if(empty($unixtime)){
        	return "";
    	}
    	if(empty($time)){
        	date_default_timezone_set('America/Los_Angeles');
    	}
    
    	date_default_timezone_set($time);
    	return date('M d', $unixtime);
	}
?>
























