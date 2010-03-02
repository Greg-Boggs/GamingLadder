<?php
// this file produces a elo/game graph using the pChart classes (which uses GD library)
// documentation to pChart can be found at http://pchart.sourceforge.net/

// include the pChart classes      
include("pChart/pData.class");   
include("pChart/pChart.class");   


// to avoid the useless generation of the image for each page call, we have to check some things first 

// get the date of the last game the player had played and which wasnt contested or withdrawn
$lastgamedate = "";
$sql = "SELECT reported_on FROM $gamestable WHERE withdrawn = 0 AND contested_by_loser = 0 AND winner = '$_GET[name]' OR loser = '$_GET[name]'  ORDER BY reported_on DESC LIMIT 1";
$result = mysql_query($sql,$db);
	while ($row = mysql_fetch_array($result)) {
		$lastgamedate = $row['reported_on'];
	}

//provide the path for a possible already existent image
$filename = "pChart/img/".$_GET['name'].".png";


// if the image already exists and it was created after the last game skip the image creating part and just display the already existent file 

if (file_exists($filename) && date ("Y-m-d H:i:s'.", filemtime($filename)) > $lastgamedate)
      { ?> <img src="pChart/img/<?echo $_GET['name']?>.png"> <?php }
    
else {
//if no image was found or image is older than last game create a new graph

//create the object for the data
$imagedata = new pData;

//get all games where the displayed player did participate, except those which where contested or withdrawn
$sql ="SELECT * FROM $gamestable WHERE withdrawn = 0 AND contested_by_loser = 0 AND (loser = '".$_GET['name']."' OR winner = '".$_GET['name']."')";
$result = mysql_query($sql,$db);

//now populate $imagedata for each game with the values for elo

	//add a first point with the base-rating-elo value set up in the config ("BASE_RATING")
	$imagedata->AddPoint(BASE_RATING,"elo");
	//to limit x-axis labels and ticks we need to keep track of how many games are added (maybe there is a better way to read the number, count() etc)
	$numberofgames = 0;
	//now add all the other values, if he was the winner its stored in "winner_elo" if not "loser_elo"
	while ($row = mysql_fetch_array($result)) { 
		//player was the winner
		if ($row[0] == $_GET['name']) 
		    {
		      $imagedata->AddPoint($row["winner_elo"],"elo");
		      $numberofgames = $numberofgames + 1;	
		    }
		//player was the loser
		if ($row[1] == $_GET['name']) 
		    {
		      $imagedata->AddPoint($row["loser_elo"],"elo");
		      $numberofgames = $numberofgames + 1;
		    }
	}


//after we get all the data the imagecreation begins
//be careful with the font/image folders, path begins by root, otherwise it will throw errors

//adding the data to graph and setting up the axis/unit names
 $imagedata->AddAllSeries();   
 $imagedata->SetAbsciseLabelSerie();   
 $imagedata->SetYAxisName("Elo");
 $imagedata->SetYAxisUnit("points");
 $imagedata->SetXAxisName("Games");

//calulate the $SkipLabels used in drawScale to display only around 10 Labels/Ticks (avoiding too much labels)
 $skiplabels = round($numberofgames/10); 
 if ($skiplabels < 1) {$skiplabels = 1;} //avoid division through zero
  
// Initialise the graph   
 $image = new pChart(800,230);
 $image->setFontProperties("pChart/Fonts/tahoma.ttf",8);
 $image->setColorPalette(231, 217, 190,0);  //set the line color 
 $image->setGraphArea(100,30,780,190);   
 $image->drawGraphArea(255,255,255,FALSE);
 $image->drawScale($imagedata->GetData(),$imagedata->GetDataDescription(),SCALE_NORMAL,100,100,100,TRUE,0,2,FALSE,$skiplabels);   
  
 // Draw the 1500 elo line   
 $image->setFontProperties("pChart/Fonts/tahoma.ttf",6);   
 $image->drawTreshold(1500,100,100,100,TRUE,FALSE,5); 
 
 // Draw the line graph
 $image->drawLineGraph($imagedata->GetData(),$imagedata->GetDataDescription());   
 $image->drawPlotGraph($imagedata->GetData(),$imagedata->GetDataDescription(),1,1,100,100,100);   
  
 // Finish the graph   
 $image->setFontProperties("pChart/Fonts/tahoma.ttf",10);   
 $image->Render("pChart/img/".$_GET['name'].".png");

//and finally...display the new graph
?> <img src="pChart/img/<?echo $_GET['name']?>.png">  <?php 
}

?>

