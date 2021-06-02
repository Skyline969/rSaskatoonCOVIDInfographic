<?php
	/**
	 *	/r/saskatoon COVID-19 Infographic
	 *	Copyright (C) 2021 Logan Baron
	 *	
	 *	This program is free software: you can redistribute it and/or modify
     *	it under the terms of the GNU General Public License as published by
     *	the Free Software Foundation, either version 3 of the License, or
     *	(at your option) any later version.
	 *	
     *	This program is distributed in the hope that it will be useful,
     *	but WITHOUT ANY WARRANTY; without even the implied warranty of
     *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     *	GNU General Public License for more details.
	 *	
     *	You should have received a copy of the GNU General Public License
     *	along with this program.  If not, see <https://www.gnu.org/licenses/>.
	 */
	
	// Get data from the government dashboard API
	$page_indicators = file_get_contents("https://dashboard.saskatchewan.ca/api/health/dashboard/indicators");
	$vax_data = file_get_contents("https://dashboard.saskatchewan.ca/api/indicator/detail/health-wellness%3Acovid-19-vaccines%3Avaccines");
	
	// The file containing the template
	$image_filename = "covid_template_2.png";
	
	// Instantiate our variables
	$cases_new = $cases_total = $active_total = $recoveries_new = $recoveries_total =
	$hospitalizations_new = $hospitalizations_total = $deaths_new = $deaths_total =
	$vaccinations_new = $vaccinations_total = $tests_new = $tests_total =
	$positivity = $mortality = $saskatoon_new = $saskatoon_total = $phase1_vaccinated =
	$phase2_vaccinated = $phase3_vaccinated = $phase1_needed = $phase2_needed = 
	$phase3_needed = "N/A";
	
	/*// Data obtained from 2020 census:	//https://www150.statcan.gc.ca/t1/tbl1/en/tv.action?pid=1710000501&pickMembers%5B0%5D=1.9&pickMembers%5B1%5D=2.1&cubeTimeFrame.startYear=2016&cubeTimeFrame.endYear=2020&referencePeriods=20160101%2C20200101
	$population = array(
		"18" => 948055,
		"30" => 723861,
		"40" => 551446,
		"50" => 407982,
		"60" => 265242,
		"70" => 128949,
		"80" => 50511,
	);*/
	
	// New data obtained from Government of Saskatchewan:	//https://www.saskatchewan.ca/government/news-and-media/2021/may/18/covid19-update-for-may-18-608524-vaccines-administered-129-new-cases-269-recoveries-three-new-deaths
	$population = array(
		"18" => 942113,
		"30" => 752204,
		"40" => 568958,
		"50" => 417062,
		"60" => 269596,
		"70" => 131125,
		"80" => 51307,
	);
	
	$vax_range_totals = array();
	$vax_range_data = array();
	
	// Determine the final size of the image based on the user's request
	if (isset($_REQUEST["size"]))
	{
		switch($_REQUEST["size"])
		{
			case "medium":
				$shrink_val = 2;
			break;
			case "small":
				$shrink_val = 5;
			break;
			case "tiny":
				$shrink_val = 10;
			break;
			default:
				$shrink_val = 1;
			break;
		}
	}
	else
		$shrink_val = 1;
	
	// Just a debug flag. Set to true if you don't want the image to generate.
	// Make sure to check for this flag before printing anything
	$dump_data = false;
	
	// We'll need to reference today and yesterday a lot, so let's just calculate them once
	//$today = date("F j, Y");
	$today = date("F d, Y");
	$yesterday = date("F d, Y", strtotime("yesterday"));
	
	// If we are dumping data, specify we are gonna be spitting out plaintext
	if ($dump_data)
		header("Content-type: text/plain");
	
	// Parse the indicators page
	if ($json_data = json_decode($page_indicators, true))
	{
		//if ($dump_data)
			//print_r($json_data);
		
		// Get each indicator
		foreach($json_data as $indicator)
		{
			// We will need the indicators for Saskatchewan (marked as "sk") and Saskatoon (marked as "saskatoon")
			if ($indicator["id"] == "sk")
			{
				// Get each indicator in each category, we don't care about anything else
				foreach($indicator["categories"] as $category)
				{
					foreach($category["indicators"] as $indicator)
					{
						//if ($dump_data)
							//print_r($indicator);
						// Now we can just assign the values if they are for today
						switch($indicator["name"])
						{
							case "Total Cases":
								// Only assign if the data is for today
								if ($indicator["date"] == $today)
									$cases_total = $indicator["value"];
							break;
							case "Deaths":
								// Only assign if the data is for today
								if ($indicator["date"] == $today)
								{
									$deaths_new = $indicator["changeValue"];
									$deaths_total = $indicator["value"];
								}
							break;
							case "Active Cases":
								// Only assign if the data is for today
								if ($indicator["date"] == $today)
									$active_total = $indicator["value"];
							break;
							case "Daily New Cases":
								// Only assign if the data is for today
								if ($indicator["date"] == $today)
									$cases_new = $indicator["value"];
							break;
							case "Hospitalized Cases":
								// Only assign if the data is for today
								if ($indicator["date"] == $today)
								{
									$hospitalizations_new = $indicator["changeValue"];
									$hospitalizations_total = $indicator["value"];
								}
							break;
							case "Recovered Cases":
								// Only assign if the data is for today
								if ($indicator["date"] == $today)
								{
									$recoveries_new = $indicator["changeValue"];
									$recoveries_total = $indicator["value"];
								}
							break;
							case "Daily New Tests":
								// Only assign if the data is for today
								if ($indicator["date"] == $today)
									$tests_new = $indicator["value"];
							break;
							case "Total Tests":
								// Only assign if the data is for today
								if ($indicator["date"] == $today)
									$tests_total = $indicator["value"];
							break;
							case "New Reported Doses":
								// Only assign if the data is for today
								if ($indicator["date"] == $today)
									$vaccinations_new = $indicator["value"];
							break;
							case "Total Doses":
								// Only assign if the data is for today
								if ($indicator["date"] == $today)
									$vaccinations_total = $indicator["value"];
							break;
						}
					}
				}
			}
			else if ($indicator["id"] == "saskatoon")
			{
				// Get each indicator in each category, we don't care about anything else
				foreach($indicator["categories"] as $category)
				{
					foreach($category["indicators"] as $indicator)
					{
						//if ($dump_data)
							//print_r($indicator);
						// Now we can just assign the Saskatoon-specific values if they are for today
						switch($indicator["name"])
						{
							case "Active Cases":
								// Only assign if the data is for today
								if ($indicator["date"] == $today)
									$saskatoon_total = $indicator["value"];
							break;
							case "Daily New Cases":
								// Only assign if the data is for today
								if ($indicator["date"] == $today)
									$saskatoon_new = $indicator["value"];
							break;
						}
					}
				}
			}
		}
		
		// Now we can do math based on our findings
		// Calculate positivity using the data we have collected
		if ($cases_new != "N/A" && $tests_new != "N/A")
			$positivity = ($cases_new / $tests_new) * 100;
		
		// Calculate mortality using the data we have collected
		if ($cases_total != "N/A" && $deaths_total != "N/A")
			$mortality = ($deaths_total / $cases_total) * 100;
	}
	
	// Now process the vaccination data
	if ($vax_data = json_decode($vax_data, true))
	{
		$vax_tab_index = -1;
		// Find the index in the data that contains Doses by Age
		// Don't assume it... we know how the government can be with moving their data around randomly
		foreach($vax_data["tabTitles"] as $index => $title)
			if ($title == "Doses by Age")
				$vax_tab_index = $index;
		
		if ($vax_tab_index != -1)
		{
			$fresh_vax_data = true;
			
			// Go through the chart data and get the first doses only, they're all we care about
			foreach($vax_data["tabs"][$vax_tab_index]["chart"]["data"] as $range)
			{
				if ($range["group"] == "firstDoses")
				{
					// Just in case the data is out of order, go through all available chart data and pick
					// the one with the highest time value to get the most recent
					$last_entry = array();
					foreach($range["data"] as $data)
						if ($data["time"] > @$last_entry["time"])
							$last_entry = $data;
					
					// The last entry will be the most recent, it has the value we want since this is a total
					//$last_entry = end($range["data"]);
					
					// Get only the first number from the range
					if (preg_match("/(\d{1,})(\s{0,}-\s{0,})(\d{1,})/", $range["seriesTitle"], $matches))
						$range_index = $matches[1];
					else // The 80+ range would not have two numbers to the range, but we also don't want the +
						$range_index = rtrim($range["seriesTitle"], "+");
					
					$entry_date = date("F d, Y", $last_entry["time"]);
					
					// We assume the vaccine numbers by age range will be a day behind
					if ($entry_date != $yesterday)
					//if ($entry_date != $today)
						$fresh_vax_data = false;
					
					$vax_range_data[$range_index] = $last_entry["value"];
				}
			}
			
			// First, add all the values to the total array and set them to 0
			foreach($vax_range_data as $age => $value)
				$vax_range_totals[$age] = 0;
			
			// Now we add values
			// Basically, we want information arranged so any age group has its value
			// as well as the sum of all groups above it. To do this, just go through
			// the data and add the value to each age range if the age is less than
			// or equal to the given range.
			foreach($vax_range_data as $age => $value)
				foreach($vax_range_totals as $age_total => $value_total)
					if ($age_total <= $age)
						$vax_range_totals[$age_total] += $value;
			
			// Calculate the vaccination rates for each phase, but only if this is fresh data
			if ($fresh_vax_data)
			{
				$phase1_vaccinated = number_format(($vax_range_totals["40"] / ($population["40"] * 0.7)) * 100, 1);
				$phase2_vaccinated = number_format(($vax_range_totals["30"] / ($population["30"] * 0.7)) * 100, 1);
				$phase3_vaccinated = number_format(($vax_range_totals["18"] / ($population["18"] * 0.7)) * 100, 1);
				
				if ($phase1_vaccinated > 100)
					$phase1_vaccinated = 100;
				if ($phase2_vaccinated > 100)
					$phase2_vaccinated = 100;
				if ($phase3_vaccinated > 100)
					$phase3_vaccinated = 100;
				
				// Calculate how many vaccines are needed to reach each goal
				// If this is 100%, show how much over the goal we are
				if ($phase1_vaccinated != 100)
					$phase1_needed = number_format(intval($population["40"] * 0.7) - $vax_range_totals["40"], 0, ".", ",");
				else
					$phase1_needed = number_format($vax_range_totals["40"], 0, ".", ",") . " / " . number_format(intval($population["40"] * 0.7), 0, ".", ",");
				
				if ($phase2_vaccinated != 100)
					$phase2_needed = number_format(intval($population["30"] * 0.7) - $vax_range_totals["30"], 0, ".", ",");
				else
					$phase2_needed = number_format($vax_range_totals["30"], 0, ".", ",") . " / " . number_format(intval($population["30"] * 0.7), 0, ".", ",");
				
				if ($phase3_vaccinated != 100)
					$phase3_needed = number_format(intval($population["18"] * 0.7) - $vax_range_totals["18"], 0, ".", ",");
				else
					$phase3_needed = number_format($vax_range_totals["18"], 0, ".", ",") . " / " . number_format(intval($population["18"] * 0.7), 0, ".", ",");
				
				// Add a percent sign to the percentages for readability, and then show
				// how much of the entire age range is vaccinated
				$phase1_vaccinated .= "% (" . number_format(($vax_range_totals["40"] / ($population["40"])) * 100, 1) . "% of 40+)";
				$phase2_vaccinated .= "% (" . number_format(($vax_range_totals["30"] / ($population["30"])) * 100, 1) . "% of 30+)";
				$phase3_vaccinated .= "% (" . number_format(($vax_range_totals["18"] / ($population["18"])) * 100, 1) . "% of 18+)";
			}
		}
	}
	
	if (!$dump_data)
	{
		// Set the header now that we're going to be displaying an image
		header("Content-type: image/png");
		
		// Make sure the image does not get cached
		$ts = gmdate("D, d M Y H:i:s") . " GMT";
		header("Expires: " . $ts);
		header("Last-Modified: " . $ts);
		header("Pragma: no-cache");
		header("Cache-Control: no-cache, must-revalidate");
		
		// Define image attributes
		$image = imagecreatefrompng($image_filename);
		$font = "./arial.ttf";
		$c_white = imagecolorallocate($image, 255, 255, 255);
		$c_black = imagecolorallocate($image, 0, 0, 0);
		$c_green = imagecolorallocate($image, 0, 156, 49);
		
		// Date string
		imagettftext($image, 200, 0, 325, 600, $c_white, $font, date("M j"));
		
		// Active cases - New
		imagettftext($image, 125, 0, 325, 1850, $c_black, $font, 
			((is_numeric($cases_new)) ? number_format($cases_new, 0, ".", ",") : $cases_new));
		
		// Active cases - Total
		imagettftext($image, 125, 0, 2300, 1850, $c_black, $font, 
			((is_numeric($active_total)) ? number_format($active_total, 0, ".", ",") : $active_total));
		
		// Recoveries - New
		imagettftext($image, 125, 0, 325, 2490, $c_black, $font, 
			((is_numeric($recoveries_new)) ? number_format($recoveries_new, 0, ".", ",") : $recoveries_new));
		
		// Recoveries - Total
		imagettftext($image, 125, 0, 2300, 2490, $c_black, $font, 
			((is_numeric($recoveries_total)) ? number_format($recoveries_total, 0, ".", ",") : $recoveries_total));
		
		// Hospitalizations - New
		imagettftext($image, 125, 0, 325, 3125, $c_black, $font, 
			((is_numeric($hospitalizations_new)) ? number_format($hospitalizations_new, 0, ".", ",") : $hospitalizations_new));
		
		// Hospitalizations - Total
		imagettftext($image, 125, 0, 2300, 3125, $c_black, $font, 
			((is_numeric($hospitalizations_total)) ? number_format($hospitalizations_total, 0, ".", ",") : $hospitalizations_total));
		
		// Deaths - New
		imagettftext($image, 125, 0, 325, 3775, $c_black, $font, 
			((is_numeric($deaths_new)) ? number_format($deaths_new, 0, ".", ",") : $deaths_new));
		
		// Deaths - Total
		imagettftext($image, 125, 0, 2300, 3775, $c_black, $font, 
			((is_numeric($deaths_total)) ? number_format($deaths_total, 0, ".", ",") : $deaths_total));
		
		// Vaccinations - New
		imagettftext($image, 125, 0, 325, 4410, $c_black, $font, 
			((is_numeric($vaccinations_new)) ? number_format($vaccinations_new, 0, ".", ",") : $vaccinations_new));
		
		// Vaccinations - Total
		imagettftext($image, 125, 0, 2300, 4410, $c_black, $font, 
			((is_numeric($vaccinations_total)) ? number_format($vaccinations_total, 0, ".", ",") : $vaccinations_total));
		
		// Tests - New
		imagettftext($image, 125, 0, 325, 5055, $c_black, $font, 
			((is_numeric($tests_new)) ? number_format($tests_new, 0, ".", ",") : $tests_new));
		
		// Tests - Total
		imagettftext($image, 125, 0, 2300, 5055, $c_black, $font, 
			((is_numeric($tests_total)) ? number_format($tests_total, 0, ".", ",") : $tests_total));
		
		// Rates - New
		imagettftext($image, 125, 0, 325, 5700, $c_black, $font, 
			((is_numeric($positivity)) ? number_format($positivity, 1, ".", ",") . "%" : $positivity));
		
		// Rates - Total
		imagettftext($image, 125, 0, 2300, 5700, $c_black, $font, 
			((is_numeric($mortality)) ? number_format($mortality, 1, ".", ",") . "%" : $mortality));
		
		// Saskatoon - New
		imagettftext($image, 125, 0, 325, 6850, $c_black, $font, 
			((is_numeric($saskatoon_new)) ? number_format($saskatoon_new, 0, ".", ",") : $saskatoon_new));
		
		// Saskatoon - Total
		imagettftext($image, 125, 0, 2300, 6850, $c_black, $font, 
			((is_numeric($saskatoon_total)) ? number_format($saskatoon_total, 0, ".", ",") : $saskatoon_total));
		
		// Phase 1 Vaccinated
		imagettftext($image, 110, 0, 325, 7775, ((preg_match("/^100\%/", $phase1_vaccinated)) ? $c_green : $c_black), $font, $phase1_vaccinated);
		imagettftext($image, 110, 0, 2300, 7775, ((preg_match("/^100\%/", $phase1_vaccinated)) ? $c_green : $c_black), $font, $phase1_needed);
		
		// Phase 2 Vaccinated
		imagettftext($image, 110, 0, 325, 8310, ((preg_match("/^100\%/", $phase2_vaccinated)) ? $c_green : $c_black), $font, $phase2_vaccinated);
		imagettftext($image, 110, 0, 2300, 8310, ((preg_match("/^100\%/", $phase2_vaccinated)) ? $c_green : $c_black), $font, $phase2_needed);
		
		// Phase 3 Vaccinated
		imagettftext($image, 110, 0, 325, 8850, ((preg_match("/^100\%/", $phase3_vaccinated)) ? $c_green : $c_black), $font, $phase3_vaccinated);
		imagettftext($image, 110, 0, 2300, 8850, ((preg_match("/^100\%/", $phase3_vaccinated)) ? $c_green : $c_black), $font, $phase3_needed);
		
		// Shrink the image
		list($width, $height) = getimagesize($image_filename);
		$shrunk = imagecreatetruecolor($width / $shrink_val, $height / $shrink_val);
		imagecopyresampled($shrunk, $image, 0, 0, 0, 0, $width / $shrink_val, $height / $shrink_val, $width, $height);
		
		// Display the image
		imagepng($shrunk);
		imagedestroy($shrunk);
		imagedestroy($image);
	}
?>
