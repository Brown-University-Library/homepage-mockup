<?php 

// set string variables
$upcoming_events = "" ;
$categories = "" ;

// URI of the events feed, get 90 days worth of events
$url = "http://events.brown.edu/webcache/v1.0/rssDays/90/list-rss/%28catuid%3D%2700149399-257a645c-0125-94544dff-00002b0e%27%29.rss" ;
// http://events.brown.edu/webcache/v1.0/rssDays/90/list-rss/%28catuid%3D%2700149399-257a645c-0125-94544dff-00002b0e%27%29.rss
// http://events.brown.edu/webcache/v1.0/rssDays/90/list-rss/no--filter.rss

// load the text of the feed into a variable and turn it into a simpleXML object
$xml = simplexml_load_file($url) ;

// iterate through the items in the object, pulling out relevant information
for($i = 0; $i < 10; $i++){
	$title = $xml->channel->item[$i]->title ;
	$link = $xml->channel->item[$i]->link ;
	$pubDate = rtrim($xml->channel->item[$i]->pubDate, "\ UT") ;
	$categories = $xml->channel->item[$i]->category ; 

    // convert the pubDate string to a timestamp
    $event_unix_timestamp_utc = strtotime( $pubDate ) ;

    // set the timezone of the timestamp to UTC, to be sure
    $original_timestamp = date( 'Y-m-d H:i:s', $event_unix_timestamp_utc ) ;
    $utc = new DateTimeZone('UTC') ;
    $datetime = new DateTime($original_timestamp, $utc) ;

    // identify a new timezone as New York
    $target_timezone = new DateTimeZone('America/New_York') ;
    
    // set the new timezone on the timestamp 
    $datetime->setTimeZone($target_timezone) ;
    
    // make the timestamp pretty again
    $display_date = $datetime->format('F j \a\t g:ia') ;
    $event_unix_timestamp_eastern = $datetime->format('U') ;
    	
	// read each of the categories in the item 
	foreach ($xml->channel->item[$i]->category as $category) {
            $categories .= "<br />A category is " . $category ;
    }

    // We don't want "Ongoing" events, and we don't want events that have happened in the past. If none of the categories is "Ongoing", and if the event is in the future, add it to the display
    // Future improvements : make the event remain up for a half hour after it begins
    if (strpos($categories,'Ongoing') === false && $event_unix_timestamp_eastern > time() && $count < 3) 
    {
        $upcoming_events .= "$display_date:&nbsp;<a href='$link'>$title</a>&nbsp;&nbsp;&bull;&nbsp;&nbsp;";
        ++$count ;
    }
    
}

// take the final pipe and spaces off the end of the events list
$upcoming_events = rtrim($upcoming_events, "&nbsp;&nbsp;&bull;&nbsp;&nbsp;") ;

// add a link to the full library calendar
$upcoming_events .= "&nbsp;&nbsp;&bull; &nbsp;&nbsp;<a href=\"http://brown.libcal.com/\">see&nbsp;all&nbsp;events-></a>" ;

?>

<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Library Home Page</title>

<link rel="stylesheet" type="text/css" href="css/style.css" media="screen" />
<link rel="stylesheet" href="css/foundation.css" />
<link href='https://fonts.googleapis.com/css?family=Work+Sans:400,100,200,300' rel='stylesheet' type='text/css' />

</head>
<body id="type-c">


<div id="wrap">    
<div style="width: 100%;" id="libnav">
	    <a href="http://library.brown.edu/borrowing/">Borrow&nbsp;&amp;&nbsp;Renew</a> 
	    <a href="http://library.brown.edu/eresources/">Articles,&nbsp;Journals,&nbsp;&amp;&nbsp;Databases</a> 
	    <a href="http://library.brown.edu/about/specialists.php">Research&nbsp;Help</a> 
	    <a href="http://library.brown.edu/libweb/hours.php">Hours,&nbsp;Locations,&nbsp;&amp; Events</a> 
	    <a href="http://library.brown.edu/libweb/askalib.php">Ask&nbsp;a&nbsp;Question&nbsp;Now</a> 
	    <a href="http://library.brown.edu/libweb/proxy.php">Off‐Campus&nbsp;Access</a> 
	    <a href="https://josiah.brown.edu/patroninfo">MyJosiah&nbsp;Account</a> 
	</div>

	<div id="title_bar">
	    <a href="http://www.brown.edu/"><img src="images/header_shield.png" style="height: 67px; margin-left :10px; margin-right: 10px; float: left;" alt="Brown University Homepage" title="Brown University Homepage"></a> 		

	    <a href="http://library.brown.edu/"><img style="height: 27px; margin-top: 22px;" src="images/Brown_University_Library_header_text.png" alt="Brown University Library"></a> 
	</div>
</div>

<div id="content-wrap">
  <div id="content">

<!--
  <div class="alertbar">
    <p>Alert: <a href="">PubMed will be down today until 4 p.m.</a></p>
  </div>
-->

<div class="row">


<div class="overlay-container">
    <img style="width:100%" src="images/dsl.png">

    <div class="overlay">
      <div class="searchbox">

     <div class="row collapse">     
      <div class="large-11 columns">
        <input type="text" placeholder="search" style="border-width : 0px ; " />
      </div>
      <div class="large-1 end columns">
        <button class="postfix" style="padding: 10px;"><i class="icon-search"></i></button></input>
      </div>
    </div>
    <div class="advanced">
        <a href="">Advanced Search</a>
      </div>

      </div> <!-- end .searchbox -->
    </div>
    
    <div class="bannercaption">
        <p>The <a href="http://library.brown.edu/dsl/">Digital Scholarship Lab</a> was designed for collaboration, flexibility, and ease of use for scholars working on data-rich and visually mediated research.</p>
    </div>
    
</div> 
           
</div>


  <div class="eventsbar">
    <p><span class="icon-calendar"> </span><strong>Upcoming Events: </strong><?php echo $upcoming_events ; ?></p>
  </div>

<div class="row underbanner">
<div class="large-9 columns">
<div class="row">
<div class="large-4 columns">
<div class="panel">
<h6>Electronic Resources</h6>
<div class="row">
  <div class="large-5 columns">
    <span class="textbox">
    <a>
    Journals<br>
    Databases<br>
    Articles<br>
    Josiah/Catalog<br>
    </a>
    </span>
  </div>
  <div class="large-5 columns">
    <span class="textbox">
    <a>
    WorldCat<br>
    Illiad<br>
    myJosiah<br>
    More-><br>
    </a>
    </span>
  </div>
  </div>
</div>
</div>
<div class="large-4 columns">
<div class="panel">
<h6>Getting Access</h6>
<div class="row">
  <div class="large-12 columns">
    <span class="textbox">
    <a>
    Borrow & Renew<br>
    Off-Campus Logins<br>
    Special Collections<br>
    Information for Guests<br>
    </a>
    </span>
  </div>
  </div>
</div>
</div>
<div class="large-4 columns">
<div class="panel">
<h6>In the Libraries</h6>
 <div class="row">
  <div class="large-6 columns">
    <span class="textbox">
    <a>
    Hours<br>
    Spaces<br>
    Computers<br>
    Events<br>
    </a>
    </span>
  </div>
  <div class="large-6 columns">
    <span class="textbox">
    <a>
    Collections<br>
    People<br>
    Coffee&c.<br>
    More -><br>
    </a>
    </span>
  </div>
  </div>
</div>
</div>
</div>

<div class="row">
<div class="large-6 columns">
<div class="panel">
<h4>News from The Libraries</h4>
<div class="row">
<div class="large-10 columns textbox">
CBS News Report Features Two Brown Alumni, Malcolm X, and Materials from the Brown Archives
</div>
<div class="large-2 columns">
<img src="http://placehold.it/100x100&text=Image">
</div>
</div>

<div class="row">
<div class="large-10 columns textbox">
Event: Open Access in the Humanities: Benefits, Challenges and Economics with Martin Paul Eve
</div>
<div class="large-2 columns">
<img src="http://placehold.it/100x100&text=Image">
</div>
</div>

<div class="row">
<div class="large-10 columns textbox">
Exhibit: Art of the Book
</div>
<div class="large-2 columns">
<img src="http://placehold.it/100x100&text=Image">
</div>
</div>

<div class="row">
<div class="large-10 columns textbox">
Event and Exhibit: Unicorn Found: Science, Literature, and the Arts
</div>
<div class="large-2 columns">
<img src="http://placehold.it/100x100&text=Image">
</div>
</div>
<hr>

<div class="row">
<div class="large-12 columns textbox">
<a href="">Subscribe to the Library Newsletter</a>
</div>
</div>
</div>
</div>

<div class="large-6 columns">
<div class="panel">
<h4>Today's Hours</h4>
<p class="textbox">Rockefeller Library: 8:30 a.m.-9:00 p.m. <br>
Friedman Study Center: 8:30 a.m.-9:00 p.m.    </p>
<a href="#" class="small button">more hours and locations -></a>
</div>

<div class="panel">
<h4>Celebration Box</h4>
</div>

<div class="panel">
<h4>Innovations</h4>
<div class="row">
<div class="large-4 columns textbox">
Center for Digital Scholarship<br>
Brown Digital Repository
</div>
<div class="large-4 columns textbox">
Medical Connections<br>
Digital Scholarship Lab
</div>
<div class="large-3 columns textbox">
Digital Studio<br>
Digital Studio
</div>
</div>
</div>
</div>




</div>
</div>
<div class="large-3 panel columns">
<h4>Get Help</h4>
<div class="row">
<div class="large-8 columns">
<ul class="textbox">
  <li>Ask a Librarian Now</li>
  <li>Find a Subject Research Guide</li>
  <li>Locate a Book or Article</li>
  <li>Request Course Reserves</li>
  <li>Attend a Workshop</li>
  <li>Get Help with Citations</li>
  <li>Request an Interlibrary Loan</li>
  <li>Connect with a Personal Librarian</li>
  <li>Get Started Using the Library</li>
</ul>
</div>
<div class="large-4 columns">
<img src="http://placehold.it/100x100&text=Image">
<p class="gethelp">Bonnie Buzzel<br>
Senior Knowledge Systems Librarian<br><br>
<a href="">Meet More Librarians</a>
</p>
</div>

</div>
</div>
</div>
 
<footer class="row">
<div class="large-12 columns">
<div class="row">
<div class="large-8 columns">
  <div class="row">
  <div class="large-4 columns">
  <h5>About the Library</h5>
  <ul class="textbox">
    <li><a href="">Overview and Leadership</a></li>
    <li><a href="">Friends of the Library</a></li>
    <li><a href="">Library Staff</a></li>
    <li><a href="">Alumni / Giving</a></li>
    <li><a href="">Policies</a></li>
    <li><a href="">Blogs</a></li>
    <li><a href="">Jobs</a></li>
  </ul>
  </div>
  <div class="large-4 columns">
  <h5>Resources for:</h5>
  <ul class="textbox">
    <li><a href="">Undergraduate Students</a></li>
    <li><a href="">Graduate Students</a></li>
    <li><a href="">Faculty</a></li>
    <li><a href="">Alumni</a></li>
    <li><a href="">Visiting Scholars</a></li>
    <li><a href="">Researchers</a></li>
    <li><a href="http://library.brown.edu/intranet">Library Staff</a></li>
  </ul>
  </div>
  <div class="large-4 columns">
  <h5>Materials by Type</h5>
  <ul class="textbox">
    <li><a href="">Newspapers</a></li>
    <li><a href="">Video and Audio</a></li>
    <li><a href="">Books and ebooks</a></li>
    <li><a href="">Journals and Articles</a></li>
    <li><a href="">Theses and Dissertations</a></li>
    <li><a href="">Special Collections</a></li>
    <li><a href="">Entertainment DVDs</a></li>
    <li><a href="">Computers and Technology</a></li>
  </ul>
  </div>
  </div>

</div>

<div class="large-4 columns">
<ul class="right">
<h6><a href="http://library.brown.edu/libweb/atoz.php">Library A-Z</a></h6>
<li>Brown University Library<br>10 Prospect Street/Box A<br>Providence, RI 02912 USA</li>
<li><a href="mailto:libweb@brown.edu">libweb@brown.edu</a></li>
<li>401-863-2165</li>
</ul>
<ul class="inline-list right">
<li><a href="http://library.brown.edu/gateway/lrg.php?id=67&task=home"><img src="http://library.brown.edu/images/fdlp_logo_32x29.gif"></a></li>
<li><a href="http://library.brown.edu/socialwall/"><img src="http://library.brown.edu/img_2011/socialwall_footer_icon.png"></a></li>
<li><a href="http://library.brown.edu/enewsletters/"><img src="http://library.brown.edu/img_2011/enewsletter_footer_icon.png"></a></li>
<li><a href="http://library.brown.edu/about/public_feeds.php"><img src="http://library.brown.edu/img_2011/RSS_footer_icon.png"></a></li>
<li><a href="http://mobul.boopsie.com/"><img src="http://library.brown.edu/img_2011/moBUL_icon.png"></a></li>
</ul>
</div>
</div>
</div>
</footer>



  </div> <!-- end #content -->
</div> <!-- end #content-wrap -->


</body>
</html>
