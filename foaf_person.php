<?php

/////////////////////////////////////////////////////////// Credits
//
//
//	FOAF AGENT CREATION
//	Digital Humanities Research Group
//  School of Humanities and Communication Arts
//  University of Western Sydney
//
//	Procedural Scripting: PHP | MySQL | JQuery
//
//	FOR ALL ENQUIRIES ABOUT CODE
//
//	Who:	Dr Katie McDonough & Dr Jason Ensor
//	Email: 	k.mcdonough@westernsydney.edu.au j.ensor@westernsydney.edu.au
//
//  VERSION 0.1
//	4 April 2017
//	24 August 2017
//	28-31 August 2017
//
//
/////////////////////////////////////////////////////////// Prevent Direct Access of Included Files

	define('MyConstInclude', TRUE);
	error_reporting(-1);
	ini_set("display_errors","off");

/////////////////////////////////////////////////////////// Collect session data

	$MerdUser = session_id();
	if(empty($MerdUser)) { session_start(); }
	$SIDmerd = session_id(); 
	
/////////////////////////////////////////////////////////// Clean post and get	
	
	include("./foaf.config.php");
	include("./foaf.dbconnect.php");
	include("./index_functions.php");
	header("Cache-Control: no-cache");
	header("Pragma: no-cache");
	mb_internal_encoding("UTF-8");
	if (!mysqli_set_charset($mysqli_link, "utf8")) {
		echo "PROBLEM WITH CHARSET!";
		die;		
	}
	
/////////////////////////////////////////////////////////// Get and Set Vars

	$_GET = array();
	$_POST = array();
	$query = "TRUNCATE foaf_fbtee_person;";
	$mysqli_result = mysqli_query($mysqli_link, $query);
	$query = "TRUNCATE foaf_fbtee_agent;";
	$mysqli_result = mysqli_query($mysqli_link, $query);
	$doCreatePerson = "y";
	$doAuthorsMatch = "n";
	$doProfessionsMatch = "y";
	
/////////////////////////////////////////////////////////// Run Create Person Table
	
	if(($doCreatePerson == "y")) {
		$query = "SELECT ";
		$query .= "people.person_code, ";
		$query .= "people.person_name ";
		$query .= "FROM people ";
		$query .= "ORDER BY ";
		$query .= "people.person_name ASC ";
		$mysqli_result = mysqli_query($mysqli_link, $query);
		while($row = mysqli_fetch_row($mysqli_result)) {
			$personUUID = guidv4();
			$queryB = "INSERT INTO foaf_fbtee_person VALUES (";
			$queryB .= "\"0\", ";
			$queryB .= "\"$personUUID\", ";
			$queryB .= "\"$row[0]\", ";
			$queryB .= "\"$row[1]\");";
			$mysqli_resultB = mysqli_query($mysqli_link, $queryB);							
		}			
		echo "Create Person Table - Done ".time()." !<br />";
	}
	
/////////////////////////////////////////////////////////// Run Authors Match

	if(($doAuthorsMatch == "y")) {
		$query = "SELECT * FROM foaf_fbtee_person ORDER BY ID ASC";
		$mysqli_result = mysqli_query($mysqli_link, $query);
		while($row = mysqli_fetch_row($mysqli_result)) {
			$anymatch = "";
			$personCounter = $row[0];
			$personUUID = $row[1];
			$personCode = $row[2];
			$personName = $row[3];
			$recordUUID = guidv4();
			$queryA = "SELECT ";
			$queryA .= "authors.author_code, ";
			$queryA .= "authors.author_name ";
			$queryA .= "FROM authors ";
			$queryA .= "WHERE authors.author_name = \"$row[3]\" ";
			$queryA .= "OR authors.author_name LIKE \"$row[3] %\" ";
			$queryA .= "OR authors.author_name LIKE \"$row[3],%\" ";
			$queryA .= "ORDER BY ";
			$queryA .= "authors.author_name ASC ";
			$mysqli_resultA = mysqli_query($mysqli_link, $queryA);
			while($rowA = mysqli_fetch_row($mysqli_resultA)) {
				if((trim($personName) == trim("$rowA[1]"))) {
					$exactMatch = "Y";
				}
				$queryInsert = "INSERT INTO foaf_fbtee_agent VALUES (";
				$queryInsert .= "\"0\", ";
				$queryInsert .= "\"$recordUUID\", ";
				$queryInsert .= "\"$personUUID\", ";
				$queryInsert .= "\"$personCode\", ";
				$queryInsert .= "\"$personName\", ";
				$queryInsert .= "\"$personCounter\", ";
				$queryInsert .= "\"$rowA[1]\", ";
				$queryInsert .= "\"$rowA[0]\", ";
				$queryInsert .= "\"A\", ";
				$queryInsert .= "\"\", ";
				$queryInsert .= "\"$exactMatch\", ";
				$queryInsert .= "\"\", ";
				$queryInsert .= "\"\", ";
				$queryInsert .= "\"\", ";
				$queryInsert .= "\"\", ";
				$queryInsert .= "\"\", ";
				$queryInsert .= "\"\"";
				$queryInsert .= ")";	
				$mysqli_resultZ = mysqli_query($mysqli_link, $queryInsert);	
				$exactMatch = "";
				$anymatch = "y";						
			}
			if(($anymatch == "")) {
				$queryInsert = "INSERT INTO foaf_fbtee_agent VALUES (";
				$queryInsert .= "\"0\", ";
				$queryInsert .= "\"$recordUUID\", ";
				$queryInsert .= "\"$personUUID\", ";
				$queryInsert .= "\"$personCode\", ";
				$queryInsert .= "\"$personName\", ";
				$queryInsert .= "\"$personCounter\", ";
				$queryInsert .= "\"\", ";
				$queryInsert .= "\"\", ";
				$queryInsert .= "\"\", ";
				$queryInsert .= "\"\", ";
				$queryInsert .= "\"\", ";
				$queryInsert .= "\"\", ";
				$queryInsert .= "\"\", ";
				$queryInsert .= "\"\", ";
				$queryInsert .= "\"\", ";
				$queryInsert .= "\"\", ";
				$queryInsert .= "\"\"";
				$queryInsert .= ")";	
				$mysqli_resultZ = mysqli_query($mysqli_link, $queryInsert);	
			}
		}
		echo "Create Agents Record where Person matches Author, or Where there is no match - Done ".time()." !<br />";		
	} else {
		$query = "SELECT * FROM foaf_fbtee_person ORDER BY ID ASC";
		$mysqli_result = mysqli_query($mysqli_link, $query);
		while($row = mysqli_fetch_row($mysqli_result)) {
			$anymatch = "";
			$personCounter = $row[0];
			$personUUID = $row[1];
			$personCode = $row[2];
			$personName = $row[3];
			$recordUUID = guidv4();
			$queryInsert = "INSERT INTO foaf_fbtee_agent VALUES (";
			$queryInsert .= "\"0\", ";
			$queryInsert .= "\"$recordUUID\", ";
			$queryInsert .= "\"$personUUID\", ";
			$queryInsert .= "\"$personCode\", ";
			$queryInsert .= "\"$personName\", ";
			$queryInsert .= "\"$personCounter\", ";
			$queryInsert .= "\"\", ";
			$queryInsert .= "\"\", ";
			$queryInsert .= "\"\", ";
			$queryInsert .= "\"\", ";
			$queryInsert .= "\"\", ";
			$queryInsert .= "\"\", ";
			$queryInsert .= "\"\", ";
			$queryInsert .= "\"\", ";
			$queryInsert .= "\"\", ";
			$queryInsert .= "\"\", ";
			$queryInsert .= "\"\"";
			$queryInsert .= ")";	
			$mysqli_resultZ = mysqli_query($mysqli_link, $queryInsert);	
		}
	}
	
/////////////////////////////////////////////////////////// Run Clients Match Via Name

	$query = "SELECT * FROM foaf_fbtee_person ORDER BY ID ASC";
	$mysqli_result = mysqli_query($mysqli_link, $query);
	while($row = mysqli_fetch_row($mysqli_result)) {
		$personCounter = $row[0];
		$personUUID = $row[1];
		$personCode = $row[2];
		$personName = $row[3];
		$recordUUID = guidv4();
		$queryA = "SELECT ";
		$queryA .= "clients.client_code, ";
		$queryA .= "clients.client_name ";
		$queryA .= "FROM clients ";
		$queryA .= "WHERE clients.client_name = \"$personName\" ";
		$queryA .= "OR clients.client_name LIKE \"$personName %\" ";
		$queryA .= "OR clients.client_name LIKE \"$personName,%\" ";
		$queryA .= "OR clients.client_name LIKE \"%& $personName\" ";
		$queryA .= "ORDER BY ";
		$queryA .= "clients.client_name ASC ";
		$mysqli_resultA = mysqli_query($mysqli_link, $queryA);
		while($rowA = mysqli_fetch_row($mysqli_resultA)) {
			if((trim($personName) == trim("$rowA[1]"))) {
				$exactMatch = "Y";				
			}
			$foundCurrent = "";
			$newQuery = "SELECT * FROM foaf_fbtee_agent WHERE person_code = \"$personCode\"; ";
			$mysqli_resultNQ = mysqli_query($mysqli_link, $newQuery);
			while($rowNQ = mysqli_fetch_row($mysqli_resultNQ)) {
				$currentType = $rowNQ[8];
				$foundCurrent = "y";
			}
			if(($foundCurrent == "y")) {
				if(($currentType == "A")) {
					$queryInsert = "INSERT INTO foaf_fbtee_agent VALUES (";
					$queryInsert .= "\"0\", ";
					$queryInsert .= "\"$recordUUID\", ";
					$queryInsert .= "\"$personUUID\", ";
					$queryInsert .= "\"$personCode\", ";
					$queryInsert .= "\"$personName\", ";
					$queryInsert .= "\"$personCounter\", ";
					$queryInsert .= "\"$rowA[1]\", ";
					$queryInsert .= "\"$rowA[0]\", ";
					$queryInsert .= "\"C\", ";
					$queryInsert .= "\"\", ";
					$queryInsert .= "\"$exactMatch\", ";
					$queryInsert .= "\"\", ";
					$queryInsert .= "\"\", ";
					$queryInsert .= "\"\", ";
					$queryInsert .= "\"\", ";
					$queryInsert .= "\"\", ";
					$queryInsert .= "\"\"";
					$queryInsert .= ")";	
					$mysqli_resultZ = mysqli_query($mysqli_link, $queryInsert);	
				} else {
					$queryZ = "UPDATE foaf_fbtee_agent ";
					$queryZ .= "SET ";
					$queryZ .= "alt_name = \"$rowA[1]\", ";
					$queryZ .= "alt_code = \"$rowA[0]\", ";
					$queryZ .= "alt_type = \"C\", ";
					$queryZ .= "string_match = \"$exactMatch\" ";
					$queryZ .= "WHERE person_code = \"$personCode\" ";
					$mysqli_resultZZ = mysqli_query($mysqli_link, $queryZ);
				}
			} else {
				$queryInsert = "INSERT INTO foaf_fbtee_agent VALUES (";
				$queryInsert .= "\"0\", ";
				$queryInsert .= "\"$recordUUID\", ";
				$queryInsert .= "\"$personUUID\", ";
				$queryInsert .= "\"$personCode\", ";
				$queryInsert .= "\"$personName\", ";
				$queryInsert .= "\"$personCounter\", ";
				$queryInsert .= "\"$rowA[1]\", ";
				$queryInsert .= "\"$rowA[0]\", ";
				$queryInsert .= "\"C\", ";
				$queryInsert .= "\"\", ";
				$queryInsert .= "\"$exactMatch\", ";
				$queryInsert .= "\"\", ";
				$queryInsert .= "\"\", ";
				$queryInsert .= "\"\", ";
				$queryInsert .= "\"\", ";
				$queryInsert .= "\"\", ";
				$queryInsert .= "\"\"";
				$queryInsert .= ")";	
				$mysqli_resultZ = mysqli_query($mysqli_link, $queryInsert);	
			}
			$exactMatch = "";						
		}
	}
	
/////////////////////////////////////////////////////////// Close	
	
	echo "Update Agents Record where Person matches Client - Done ".time()." !<br />";	
	
/////////////////////////////////////////////////////////// Run Clients Match Via IDs

	$pc = 0;
	$query = "SELECT * FROM foaf_fbtee_agent ORDER BY ID ASC";
	$mysqli_result = mysqli_query($mysqli_link, $query);
	while($row = mysqli_fetch_row($mysqli_result)) {
		$personID = $row[0];
		$personCode = $row[3];
		$personAltCode = $row[7];
		$personAltType = $row[8];
		$queryD = "SELECT * FROM clients_people WHERE person_code = \"$personCode\" ";
		$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
		while($rowD = mysqli_fetch_row($mysqli_resultD)) {
			$queryF = "SELECT partnership FROM clients WHERE client_code = \"$rowD[0]\" ";
			$mysqli_resultF = mysqli_query($mysqli_link, $queryF);
			while($rowF = mysqli_fetch_row($mysqli_resultF)) {
				if(($rowF[0] == "1")) { 
					$partnerFlag = "Y"; 
				} else { 
					$partnerFlag = ""; 
				}
			}
			if(($personAltType == "C")) {
				$clientNames = "";
				$data_source = "";
				$date_first = "";
				$date_last = "";
				$otherQuery = "SELECT ";
				$otherQuery .= "client_name, ";
				$otherQuery .= "data_source, ";
				$otherQuery .= "first_date, ";
				$otherQuery .= "last_date ";
				$otherQuery .= "FROM clients ";
				$otherQuery .= "WHERE client_code = \"$rowD[0]\" ";
				$mysqli_resultOQ = mysqli_query($mysqli_link, $otherQuery);
				while($rowOQ = mysqli_fetch_row($mysqli_resultOQ)) {
					$clientNames = "$rowOQ[0]";
					$data_source = "$rowOQ[1]";
					$date_first = "$rowOQ[2]";
					$date_last = "$rowOQ[3]";
				}
				if(($date_first != "")) {
					$date_first = preg_replace("/th/i","","$date_first");
					$date_first = preg_replace("/nd/i","","$date_first");
					$date_first = preg_replace("/rd/i","","$date_first");
					$date_first = preg_replace("/st/i","","$date_first");
					$date_first = preg_replace("/january/i","01","$date_first");
					$date_first = preg_replace("/february/i","02","$date_first");
					$date_first = preg_replace("/march/i","03","$date_first");
					$date_first = preg_replace("/april/i","04","$date_first");
					$date_first = preg_replace("/may/i","05","$date_first");
					$date_first = preg_replace("/june/i","06","$date_first");
					$date_first = preg_replace("/july/i","07","$date_first");
					$date_first = preg_replace("/august/i","08","$date_first");
					$date_first = preg_replace("/september/i","09","$date_first");
					$date_first = preg_replace("/october/i","10","$date_first");
					$date_first = preg_replace("/november/i","11","$date_first");
					$date_first = preg_replace("/december/i","12","$date_first");
					$date_firsts = explode(" ", $date_first);
					$count = count($date_firsts);
					if(($count == 3)) {
						if(($date_firsts[0] < 10)) {
							$date_firsts[0] = "0".$date_firsts[0];	
						}
						$date_first = $date_firsts[2]."-".$date_firsts[1]."-".$date_firsts[0];
					}
					if(($count == 2)) {
						$date_first = $date_firsts[1]."-".$date_firsts[0];
					}
					if(($count == 1)) {
						$date_first = $date_firsts[0];
					}
				}
				if(($date_last != "")) {
					$date_last = preg_replace("/th/i","","$date_last");
					$date_last = preg_replace("/nd/i","","$date_last");
					$date_last = preg_replace("/rd/i","","$date_last");
					$date_last = preg_replace("/st/i","","$date_last");
					$date_last = preg_replace("/january/i","01","$date_last");
					$date_last = preg_replace("/february/i","02","$date_last");
					$date_last = preg_replace("/march/i","03","$date_last");
					$date_last = preg_replace("/april/i","04","$date_last");
					$date_last = preg_replace("/may/i","05","$date_last");
					$date_last = preg_replace("/june/i","06","$date_last");
					$date_last = preg_replace("/july/i","07","$date_last");
					$date_last = preg_replace("/august/i","08","$date_last");
					$date_last = preg_replace("/september/i","09","$date_last");
					$date_last = preg_replace("/october/i","10","$date_last");
					$date_last = preg_replace("/november/i","11","$date_last");
					$date_last = preg_replace("/december/i","12","$date_last");
					$date_lasts = explode(" ", $date_last);
					$count = count($date_lasts);
					if(($count == 3)) {
						if(($date_lasts[0] < 10)) {
							$date_lasts[0] = "0".$date_lasts[0];	
						}
						$date_last = $date_lasts[2]."-".$date_lasts[1]."-".$date_lasts[0];
					}
					if(($count == 2)) {
						$date_last = $date_lasts[1]."-".$date_lasts[0];
					}
					if(($count == 1)) {
						$date_last = $date_lasts[0];
					}
				}
				$queryZ = "UPDATE foaf_fbtee_agent ";
				$queryZ .= "SET ";
				$queryZ .= "alt_name = \"$clientNames\", ";
				$queryZ .= "alt_code = \"$rowD[0]\", ";
				$queryZ .= "alt_type = \"C\", ";
				$queryZ .= "client_match = \"Y\", ";
				$queryZ .= "data_source = \"$data_source\", ";
				$queryZ .= "date_first = \"$date_first\", ";
				$queryZ .= "date_last = \"$date_last\", ";
				$queryZ .= "partnership = \"$partnerFlag\" ";
				$queryZ .= "WHERE person_code = \"$personCode\" AND ";
				$queryZ .= "ID = \"$personID\"; ";
				$mysqli_resultZZ = mysqli_query($mysqli_link, $queryZ);
			}
			if(($personAltType == "")) {
				$clientNames = "";
				$data_source = "";
				$date_first = "";
				$date_last = "";
				$otherQuery = "SELECT ";
				$otherQuery .= "client_name, ";
				$otherQuery .= "data_source, ";
				$otherQuery .= "first_date, ";
				$otherQuery .= "last_date ";
				$otherQuery .= "FROM clients ";
				$otherQuery .= "WHERE client_code = \"$rowD[0]\" ";
				$mysqli_resultOQ = mysqli_query($mysqli_link, $otherQuery);
				while($rowOQ = mysqli_fetch_row($mysqli_resultOQ)) {
					$clientNames = "$rowOQ[0]";
					$data_source = "$rowOQ[1]";
					$date_first = "$rowOQ[2]";
					$date_last = "$rowOQ[3]";
				}
				if(($date_first != "")) {
					$date_first = preg_replace("/th/i","","$date_first");
					$date_first = preg_replace("/nd/i","","$date_first");
					$date_first = preg_replace("/rd/i","","$date_first");
					$date_first = preg_replace("/st/i","","$date_first");
					$date_first = preg_replace("/january/i","01","$date_first");
					$date_first = preg_replace("/february/i","02","$date_first");
					$date_first = preg_replace("/march/i","03","$date_first");
					$date_first = preg_replace("/april/i","04","$date_first");
					$date_first = preg_replace("/may/i","05","$date_first");
					$date_first = preg_replace("/june/i","06","$date_first");
					$date_first = preg_replace("/july/i","07","$date_first");
					$date_first = preg_replace("/august/i","08","$date_first");
					$date_first = preg_replace("/september/i","09","$date_first");
					$date_first = preg_replace("/october/i","10","$date_first");
					$date_first = preg_replace("/november/i","11","$date_first");
					$date_first = preg_replace("/december/i","12","$date_first");
					$date_firsts = explode(" ", $date_first);
					$count = count($date_firsts);
					if(($count == 3)) {
						if(($date_firsts[0] < 10)) {
							$date_firsts[0] = "0".$date_firsts[0];	
						}
						$date_first = $date_firsts[2]."-".$date_firsts[1]."-".$date_firsts[0];
					}
					if(($count == 2)) {
						$date_first = $date_firsts[1]."-".$date_firsts[0];
					}
					if(($count == 1)) {
						$date_first = $date_firsts[0];
					}
				}
				if(($date_last != "")) {
					$date_last = preg_replace("/th/i","","$date_last");
					$date_last = preg_replace("/nd/i","","$date_last");
					$date_last = preg_replace("/rd/i","","$date_last");
					$date_last = preg_replace("/st/i","","$date_last");
					$date_last = preg_replace("/january/i","01","$date_last");
					$date_last = preg_replace("/february/i","02","$date_last");
					$date_last = preg_replace("/march/i","03","$date_last");
					$date_last = preg_replace("/april/i","04","$date_last");
					$date_last = preg_replace("/may/i","05","$date_last");
					$date_last = preg_replace("/june/i","06","$date_last");
					$date_last = preg_replace("/july/i","07","$date_last");
					$date_last = preg_replace("/august/i","08","$date_last");
					$date_last = preg_replace("/september/i","09","$date_last");
					$date_last = preg_replace("/october/i","10","$date_last");
					$date_last = preg_replace("/november/i","11","$date_last");
					$date_last = preg_replace("/december/i","12","$date_last");
					$date_lasts = explode(" ", $date_last);
					$count = count($date_lasts);
					if(($count == 3)) {
						if(($date_lasts[0] < 10)) {
							$date_lasts[0] = "0".$date_lasts[0];	
						}
						$date_last = $date_lasts[2]."-".$date_lasts[1]."-".$date_lasts[0];
					}
					if(($count == 2)) {
						$date_last = $date_lasts[1]."-".$date_lasts[0];
					}
					if(($count == 1)) {
						$date_last = $date_lasts[0];
					}
				}
				$queryZ = "UPDATE foaf_fbtee_agent ";
				$queryZ .= "SET ";
				$queryZ .= "alt_name = \"$clientNames\", ";
				$queryZ .= "alt_code = \"$rowD[0]\", ";
				$queryZ .= "alt_type = \"C\", ";
				$queryZ .= "client_match = \"Y\", ";
				$queryZ .= "data_source = \"$data_source\", ";
				$queryZ .= "date_first = \"$date_first\", ";
				$queryZ .= "date_last = \"$date_last\", ";
				$queryZ .= "partnership = \"$partnerFlag\" ";
				$queryZ .= "WHERE person_code = \"$personCode\" AND ";
				$queryZ .= "ID = \"$personID\"; ";
				$mysqli_resultZZ = mysqli_query($mysqli_link, $queryZ);
			}
			if(($personAltType != "C") && ($personAltType != "")) {
				$pc++;	
			}
		}
	}
	
/////////////////////////////////////////////////////////// Close	
	
	echo "Update Agent Records where there is a Client ID match - Done ".time()." !<br />";	
	echo "$pc mismatches!<br />";	
	
/////////////////////////////////////////////////////////// Run Professions Match via People and Client IDs

	if(($doProfessionsMatch == "y")) {
		$query = "SELECT * FROM foaf_fbtee_agent ORDER BY ID ASC";
		$mysqli_result = mysqli_query($mysqli_link, $query);
		while($row = mysqli_fetch_row($mysqli_result)) {
			$personID = $row[0];
			$person_code = $row[3];
			$queryA = "SELECT ";
			$queryA .= "people_professions.person_code, ";
			$queryA .= "people_professions.profession_code ";
			$queryA .= "FROM people_professions ";
			$queryA .= "WHERE people_professions.person_code = \"$person_code\" ";
			$mysqli_resultA = mysqli_query($mysqli_link, $queryA);
			while($rowA = mysqli_fetch_row($mysqli_resultA)) {
				$professionCode = "$rowA[1]";
				if($professionCode != "") {
					$queryB = "SELECT ";
					$queryB .= "professions.profession_code, ";
					$queryB .= "professions.translated_profession ";
					$queryB .= "FROM professions ";
					$queryB .= "WHERE professions.profession_code = \"$professionCode\" ";
					$mysqli_resultB = mysqli_query($mysqli_link, $queryB);
					while($rowB = mysqli_fetch_row($mysqli_resultB)) {
						$professionName = "$rowB[1]";
						if($professionName != "") {
							$queryZ = "UPDATE foaf_fbtee_agent ";
							$queryZ .= "SET professions_people = \"$professionName\" ";
							$queryZ .= "WHERE ID = \"$personID\" AND person_code = \"$person_code\" ";	
							$mysqli_resultZ = mysqli_query($mysqli_link, $queryZ);
						}
					}
				}
			}
		}
		echo "Update Agent Records where there is a Person Profession Code match - Done ".time()." !<br />";
		$queryC = "SELECT * FROM foaf_fbtee_agent WHERE alt_type = \"C\" ORDER BY ID ASC";
		$mysqli_resultC = mysqli_query($mysqli_link, $queryC);
		while($rowC = mysqli_fetch_row($mysqli_resultC)) {
			$personID = $rowC[0];
			$alt_code = $rowC[7];
			$queryF = "SELECT ";
			$queryF .= "clients_professions.client_code, ";
			$queryF .= "clients_professions.profession_code ";
			$queryF .= "FROM clients_professions ";
			$queryF .= "WHERE clients_professions.client_code = \"$alt_code\" ";
			$mysqli_resultF = mysqli_query($mysqli_link, $queryF);
			while($rowF = mysqli_fetch_row($mysqli_resultF)) {
				$professionCode = "$rowF[1]";
				if($professionCode != "") {
					$queryD = "SELECT ";
					$queryD .= "professions.profession_code, ";
					$queryD .= "professions.translated_profession ";
					$queryD .= "FROM professions ";
					$queryD .= "WHERE professions.profession_code = \"$professionCode\" ";
					$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
					while($rowD = mysqli_fetch_row($mysqli_resultD)) {
						$professionName = "$rowD[1]";
						if($professionName != "") {
							$queryQ = "UPDATE foaf_fbtee_agent ";
							$queryQ .= "SET professions_client = \"$professionName\" ";
							$queryQ .= "WHERE ID = \"$personID\" AND alt_code = \"$alt_code\" ";	
							$mysqli_resultQ = mysqli_query($mysqli_link, $queryQ);
						}
					}
				}
			}
		}
		echo "Update Agent Records where there is a Client Profession Code match - Done ".time()." !<br />";	
	}
	
/////////////////////////////////////////////////////////// Finish

	include("./foaf.dbdisconnect.php");

?> 