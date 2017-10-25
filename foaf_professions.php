	<?php

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

///////////////////////////////////////////////////////////

		$query = "SELECT * FROM foaf_agent ORDER BY ID ASC";
		$mysqli_result = mysqli_query($mysqli_link, $query);
		while($row = mysqli_fetch_row($mysqli_result)) {
			$personID = $row[0];
			$person_code = $row[3];
			$queryA = "SELECT ";
			$queryA .= "people_professions.person_code, ";
			$queryA .= "people_professions.profession_code ";
			$queryA .= "FROM people_professions ";
			$queryA .= "WHERE people_professions.person_code = \"$row[3]\" ";
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
							$queryZ = "UPDATE foaf_agent ";
							$queryZ .= "SET people_professions = \"$professionName\" ";
							$queryZ .= "WHERE ID = \"$personID\" AND person_code = \"$person_code\" ";	
							$mysqli_resultZ = mysqli_query($mysqli_link, $queryZ);
						}
					}
				}
			}
		}

/////////////////////////////////////////////////////////// Close	

	echo "Update Agent Records where there is a Person Profession Code match - Done ".time()." !<br />";

///////////////////////////////////////////////////////////

$queryC = "SELECT * FROM foaf_agent ORDER BY ID ASC";
		$mysqli_resultC = mysqli_query($mysqli_link, $queryC);
		while($rowC = mysqli_fetch_row($mysqli_resultC)) {
			$personID = $rowC[0];
			$alt_code = $rowC[9];
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
							$queryQ = "UPDATE foaf_agent ";
							$queryQ .= "SET client_professions = \"$professionName\" ";
							$queryQ .= "WHERE ID = \"$personID\" AND alt_code = \"$alt_code\" ";	
							$mysqli_resultQ = mysqli_query($mysqli_link, $queryQ);
						}
					}
				}
			}
		}
			
/////////////////////////////////////////////////////////// Close	

	echo "Update Agent Records where there is a Client Profession Code match - Done ".time()." !<br />";	

/////////////////////////////////////////////////////////// Finish

	include("./foaf.dbdisconnect.php");
		
	?>