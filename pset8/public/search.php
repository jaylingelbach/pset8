<?php
 
    require(__DIR__ . "/../includes/config.php");
    
    // numerically indexed array of places
    $places = [];
    $geo = $_GET["geo"]; 
    
    //the following is to help organize and split off the query to assist in a more detailed search
    $state_list = array('AL'=>"Alabama",'AK'=>"Alaska",'AZ'=>"Arizona",'AR'=>"Arkansas",'CA'=>"California",'CO'=>"Colorado",'CT'=>"Connecticut",'DE'=>"Delaware",'DC'=>"District Of Columbia",'FL'=>"Florida",'GA'=>"Georgia",'HI'=>"Hawaii",'ID'=>"Idaho",'IL'=>"Illinois", 'IN'=>"Indiana", 'IA'=>"Iowa",  'KS'=>"Kansas",'KY'=>"Kentucky",'LA'=>"Louisiana",'ME'=>"Maine",'MD'=>"Maryland", 'MA'=>"Massachusetts",'MI'=>"Michigan",'MN'=>"Minnesota",'MS'=>"Mississippi",'MO'=>"Missouri",'MT'=>"Montana",'NE'=>"Nebraska",'NV'=>"Nevada",'NH'=>"New Hampshire",'NJ'=>"New Jersey",'NM'=>"New Mexico",'NY'=>"New York",'NC'=>"North Carolina",'ND'=>"North Dakota",'OH'=>"Ohio",'OK'=>"Oklahoma", 'OR'=>"Oregon",'PA'=>"Pennsylvania",'RI'=>"Rhode Island",'SC'=>"South Carolina",'SD'=>"South Dakota",'TN'=>"Tennessee",'TX'=>"Texas",'UT'=>"Utah",'VT'=>"Vermont",'VA'=>"Virginia",'WA'=>"Washington",'WV'=>"West Virginia",'WI'=>"Wisconsin",'WY'=>"Wyoming");
    // check if it's a 5 digit zip code
    
    //check and see if user input exists in state_list
    
        if(strstr($geo, ',')){
        $parts = explode(",", $geo);
        $state = trim($parts[1], ", ");
        $city = trim($parts[0], ", ");
        }
        
    if(!array_key_exists(strtoupper($state),$state_list) && !in_array(ucwords(strtolower($state)),$state_list)){
		$parts = preg_split("/[\s,;]+/",$geo);
		$state = array_pop($parts);
		// first see if the last array element is a state abbreviation
		if(strlen($state) == 2 && array_key_exists(strtoupper($state),$state_list)){
			$state = strtoupper($state);
			print_r($state);
			foreach($state_list as $abb => $full){
			    for($i = 0; $i< count($state_list); $i++){
			        if($state === $abb[$i]){
			            $state = $full[$i];
			            print_r($state);
			        }
			    }
			}
			$city = implode(' ',$parts);
		} else {
			// since it's not an abbreviation let's see if the last element is the full name of a state
			if(in_array(ucwords(strtolower($state)),$state_list)){
				$state = ucwords(strtolower($state));
				$city = implode(' ',$parts);
				//check if this could be the wrong state (i.e. virginia could be west virginia)
				if(in_array(ucwords(strtolower($parts[count($parts)-1] . ' ' . $state)),$state_list)){
					$state = ucwords(strtolower(array_pop($parts) . ' ' . $state));
					$city = implode(' ',$parts);
				}
			} else {
				// we need at least 2 words left to continue
				if(count($parts) < 2) return false;
				$state = array_pop($parts) . ' ' . $state;
				if(in_array(ucwords(strtolower($state)),$state_list)){
					$state = ucwords(strtolower($state));
					$city = implode(' ',$parts);
				} else {
					// we need at least 2 words left to continue
					if(count($parts) < 2) return false;
					// check if the 3rd word from the end forms a valid state name
					$state = array_pop($parts) . ' ' . $state;
					if(in_array(ucwords(strtolower($state)),$state_list)){
						$state = ucwords(strtolower($state));
						
						$city = implode(' ',$parts);
					} else {
						return false;
					}
				}
			}
			$state = array_search($state,$state_list);
		}
	}
  
$query = query("SELECT * FROM places WHERE MATCH (place_name, admin_name1) AGAINST ('$city, $state')");

$places = $query;

    
    header("Content-type: application/json");
    print(json_encode($places, JSON_PRETTY_PRINT));
    

?>
