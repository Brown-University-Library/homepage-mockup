<!DOCTYPE html>
<?php 
// FIXME Fix Typekit font flash


// URI of the events feed, get 90 days worth of events
$url = "http://events.brown.edu/webcache/v1.0/rssDays/90/list-rss/%28catuid%3D%2700149399-257a645c-0125-94544dff-00002b0e%27%29.rss" ;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_TIMEOUT, 2); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
$feed = curl_exec($ch);
curl_close($ch);

if ($feed !='') {
	// load the text of the feed into a variable and turn it into a simpleXML object
	$xml = simplexml_load_string($feed) ;

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
		// FIXME Future improvements : make the event remain up for a half hour after it begins
		if (strpos($categories,'Ongoing') === false && $event_unix_timestamp_eastern > time() && $count < 3) 
		{
			$upcoming_events .= "$display_date:&nbsp;<a href='$link'>$title</a>&nbsp;&nbsp;&bull;&nbsp;&nbsp;";
			++$count ;
		}   
	}

	// take the final pipe and spaces off the end of the events list
	$upcoming_events = rtrim($upcoming_events, "&nbsp;&nbsp;&bull;&nbsp;&nbsp;") ;

	// add a link to the full library calendar
	$upcoming_events .= "&nbsp;&nbsp;&bull; &nbsp;&nbsp;<a href=\"http://brown.libcal.com/\" id=\"see_all_events\">see&nbsp;all&nbsp;events&nbsp;&raquo;</a> <span style=\"font-size : .8em ; \">|</span></a> <a href=\"http://library.brown.edu/exhibits/\" id=\"see_all_exhibits\">see current exhibits&nbsp;&raquo;</a>" ;
}


/* end calendar event retrieval /*

/* begin librarian head-shot retrieval*/
require ('/var/www/common/guest_homepage_connect.php');

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
// if (!$conn) {
//     die("Connection failed: " . mysqli_connect_error());
// }
// keep the die commented out except for error checking -- if the db dies, the home page dies

$sql = "SELECT selector, portrait, staffid, firstname, lastname
	FROM library.staff WHERE selector='Y' and portrait NOT LIKE '' and active='y' ORDER BY RAND() LIMIT 3";

    $result = mysqli_query($conn, $sql);

    while($row = mysqli_fetch_assoc($result)) {
        $selector_display_block .= "<a href='http://library.brown.edu/sr/profile.php?id=" . $row["staffid"] . "'><img src='/gateway/portraits/" . $row["portrait"] . "' alt='" . $row["firstname"] . " " . $row["lastname"] . "' /></a>";
    }
?>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Brown University Library</title>
	<link rel="stylesheet" type="text/css" href="/common/css/bootstrap/bootstrap.min.css" />
	<link rel="stylesheet" type="text/css" href="/common/css/bootstrap/style.css" />
	<link rel="stylesheet" type="text/css" href="/common/css/icomoon.css" />
	<script>
	  (function(d) {
	    var config = {
	      kitId: 'ojl0emw',
	      scriptTimeout: 3000,
	      async: true
	    },
	    h=d.documentElement,t=setTimeout(function(){h.className=h.className.replace(/\bwf-loading\b/g,"")+" wf-inactive";},config.scriptTimeout),tk=d.createElement("script"),f=false,s=d.getElementsByTagName("script")[0],a;h.className+=" wf-loading";tk.src='https://use.typekit.net/'+config.kitId+'.js';tk.async=true;tk.onload=tk.onreadystatechange=function(){a=this.readyState;if(f||a&&a!="complete"&&a!="loaded")return;f=true;clearTimeout(t);try{Typekit.load(config)}catch(e){}};s.parentNode.insertBefore(tk,s)
	  })(document);
	</script>

	<style type="text/css">
		@media (max-width:775px) {
		    #main_search_box {
		        background-position: left -200px top 0;
		    }   
		}
		
		::-webkit-input-placeholder {
		   color: #000 ;
		}
		
		::-moz-placeholder {
		   color: #000;  
		}
		
		:-ms-input-placeholder {  
		   color: #000;  
		}
		
		@media print {
		    a[href]:after { 
		        content: none ; 
		    }
		        
		    #main_search_box { 
		        display : none ; 
		    }
		}
		
	</style>
	<script type="text/javascript">
	setTimeout(function(){var a=document.createElement("script");
	var b=document.getElementsByTagName("script")[0];
	a.src=document.location.protocol+"//script.crazyegg.com/pages/scripts/0027/2813.js?"+Math.floor(new Date().getTime()/3600000);
	a.async=true;a.type="text/javascript";b.parentNode.insertBefore(a,b)}, 1);
	</script>
</head>
<body>
<div class="screen_reader">
	<h1>
		Brown University Library
	</h1>
	<a href="#main_search_box">Skip to search box</a> <a href="#calendar_bar">Skip to events</a> <a href="#main_content">Skip to main content</a> 
</div>
<?php $universal_header = file_get_contents('https://library.brown.edu/includes/universal_header_include.html'); ?>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12" id="navbar_container">
			<nav class="navbar navbar-default" role="navigation">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1"> <span class="sr-only">Toggle navigation</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span> </button> 
				</div>
				<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
<?php echo $universal_header ; ?>
				</div>
			</nav>
		</div>
	</div>
	<div class="row" id="banner_bar">
		<div class="col-md-3 logo">
			<a href="http://www.brown.edu/"><img alt="Brown University Library -- link to Brown Home Page" src="https://library.brown.edu/common/images/wordmark_for_dark_background.png" /></a> 
		</div>
		<div class="col-md-9 banner_text">
			<div class="icon-clock icon">
				<h2 style="position : relative ; top : -.15em ; ">
					&nbsp;&nbsp;Today's Hours
				</h2>
			</div>
			<br />
<?php echo file_get_contents('https://library.brown.edu/includes/hours_homepage_2016.php') ; ?>
			<a href="https://library.brown.edu/libweb/hours.php" id="hours_more">More hours and locations</a> 
		</div>
	</div>
	<div class="row">
	<!-- change "display none" to "display block" to make the emergency bar display-->
		<div class="col-md-12" id="emergency_bar" style="display : none
	 ; font-size : 1.3em ; padding : 10px ; ">

		</div>
	</div>
	<div class="row" id="main_search_box_and_description">

<!-- Change URL below for new image -->
<!-- IMPORTANT: check image at phone resolution to see if the offset needs changing. The offset is in the style element in the header of this page -->

			<div class="col-md-9" id="main_search_box" style="background-image : url('https://library.brown.edu/redesign/images/banner/20170511citations2.jpg') ; " title="Need help with citations?">
	<!-- Change value of "top" value in the div below to look good over picture, but NO MORE THAN 80% -->
				<div id="search_form" style="top : 80% ; left : 50% ; width : 40% ; border : 0px solid gold ; ">
					<form method="get" action="https://search.library.brown.edu/" name="searchForm" id="searchForm" class="search">
						<input name="utf8" type="hidden" value="&#x2713;"  />
						<div id="searchForm_lookfor_container">
							<label for="searchForm_lookfor" class="screen_reader">Search library resources</label>
							<input id="searchForm_lookfor" type="text" name="q" placeholder="Search library resources" onfocus="if (value =='Search library resources'){value =''}" onblur="if (value ==''){value='Search library resources'}" style="background-color : #efefef ; height : 51px ; border : 4px solid #FFC72C ; " />
							<button id="search_button"><span class="icon-search"></span><span class="screen_reader">Search</span></button> 
						</div>
					</form>
				</div>
			</div>
			<div class="col-md-3" id="main_search_description"> 
			
	<!-- Change caption below to match image -->
				
			<h3 style="font-weight : bold ; text-align : center ; font-size : 1em ; ">
			Citation Management Help
			</h3>
			Collecting citations for bibliographies can be a lot of work. See how <a href="http://libguides.brown.edu/citations">the Library can help make the process a whole lot easier</a>.
			</div>	
						
		</div>
	</div>
	<div class="row">
		<div class="col-md-12" id="calendar_bar">
			<p>
				<span class="icon-calendar" aria-hidden="true"> </span> <strong>Upcoming Events: </strong> 
<?php echo $upcoming_events ; ?>
			</p>
		</div>
	</div>
	<div class="row" id="main_content">
		<div class="col-md-3" id="get_help">
			<div class="icon-bubbles4 icon">
				<h2>
					Get Help
				</h2>
			</div>
			<div id="helppix">
<?php echo $selector_display_block ; ?>
				<p id="meet_libs">
					<a href="http://library.brown.edu/about/specialists.php?sort=selector">Learn more about our librarians</a>
				</p>
				<p id="have_a_question">
					Have a question about <strong>research</strong>, <strong>finding materials</strong>, or <strong>anything else</strong>?
				</p>
			</div>
			<div>
				<ul id="help_links">
<!-- LibraryH3lp chat link. First li below shows up if someone is logged into chat and online. Second one shows up if no one is logged in and online. -->
					<li class="libraryh3lp" style="display: none;"> <a href="#" onclick="window.open('https://us.libraryh3lp.com/chat/bulchat@chat.libraryh3lp.com?css=https://library.brown.edu/common/css/libraryh3lp_widget.css', 'chat', 'resizable=1,width=350,height=380'); return false;"> Ask a Librarian Now&nbsp;&nbsp;<span class="icon-bubble" id="chat_bubble_icon" title="Live chat is online"></span> </a> </li>
<!--
					<li class="libraryh3lp" style="display: none;"></li>
-->
<!-- end LibraryH3lp chat link -->
					<li><a href="http://library.brown.edu/libweb/askalib.php">Email, Text, or Phone a Librarian</a></li>
					<li><a href="http://library.brown.edu/info/borrowing/">Learn about Borrowing</a></li>
					<li><a href="http://libguides.brown.edu/">Find an Online Subject Guide</a></li>
					<li><a href="http://library.brown.edu/workshops/">Explore and Register for Workshops</a></li>
					<li><a href="http://library.brown.edu/about/specialists.php">Connect with a Subject Librarian</a></li>
					<li><a href="http://library.brown.edu/cds/">Consult on a Digital Scholarly Project</a></li>
					<li><a href="https://library.brown.edu/reserves/">Request Course Reserves <span style="font-size : .7em ; ">(<span style="font-variant : small-caps ; ">ocra</span>)</span></a></li>
					<li><a href="http://libguides.brown.edu/diy">Get Started Using the Library</a> </li>
				</ul>
			</div>
		</div>
		<div class="col-md-9" id="non_help_container">
			<div class="col-md-12" id="find_in_get">
				<div class="col-md-5" id="find">
					<div class="col-md-12 pad">
						<div class="icon-search icon">
							<h2>
								Find Library Resources
							</h2>
						</div>
						<div class="col-md-12 shell">
							<div id="find_inner">
								<div class="col-md-7">
									<a href="https://search.library.brown.edu">Josiah&nbsp;<span style="font-size : .7em ; ">(Library catalog)</span></a><br />
									<a href="http://josiah.brown.edu">Classic&nbsp;Josiah</a><br />
									<a href="http://library.brown.edu/info/borrowing#other">Interlibrary Loan</a><br />
									<a href="http://repository.library.brown.edu">Brown&nbsp;Digital Repository</a><br />
								</div>
								<div class="col-md-5">
									<a href="http://rl3tp7zf5x.search.serialssolutions.com/">Journals</a><br />
									<a href="http://rl3tp7zf5x.search.serialssolutions.com/?SS_Page=refiner&amp;SS_RefinerEditable=yes">Articles</a><br />
									<a href="http://libguides.brown.edu/az.php">Databases</a><br />
									<a href="http://www.worldcat.org.revproxy.brown.edu/">WorldCat</a> <span style="font-size : .7em ;">(<a href="https://login.revproxy.brown.edu/login?url=http://newfirstsearch.oclc.org/dbname=WorldCat;done=http://library.brown.edu/;FSIP">Advanced</a>)</span><br />
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-4" id="in">
					<div class="col-md-12 pad">
						<div class="icon-office icon">
							<h2>
								In the Libraries
							</h2>
						</div>
						<div class="col-md-12 shell">
							<div id="in_inner">
								<div class="col-md-6">
									<a href="http://library.brown.edu/libweb/hours.php">Places & Hours</a><br />
									<a href="http://library.brown.edu/reserves/">Course Reserves</a><br />
									<a href="http://library.brown.edu/info/computers_technology">Computers</a><br />
									<a href="http://library.brown.edu/calendar/">Events</a><br />
								</div>
								<div class="col-md-6">
									<a href="http://library.brown.edu/create/digitalstudio/">Digital Studio</a><br />
									<a href="http://library.brown.edu/collatoz/">Collections</a><br />
									<a href="http://library.brown.edu/about/stafflist.php">People</a><br />
									<a href="http://library.brown.edu/info/coffee_snacks">Coffee &amp; Snacks</a><br />
<!--<a href="" class="morelink">More&nbsp;&raquo;</a><br />-->
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-3" id="get">
					<div class="col-md-12 pad">
						<div class="icon-accessibility icon">
							<h2>
								Get access
							</h2>
						</div>
						<div class="col-md-12 shell">
							<div class="col-md-12" id="get_inner">
								<a href="http://library.brown.edu/libweb/proxy.php">Off-Campus Access</a><br />
								<a href="http://library.brown.edu/hay/specol.php">Special Collections</a><br />
								<a href="http://library.brown.edu/libweb/visitors.php">Info for Guests</a><br />
								<a href="http://library.brown.edu/borrowing/borrowdirect.php">BorrowDirect</a> 
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-12" id="cluster_news_pix">
				<div class="col-md-5" id="cluster_pix">
					<div class="col-md-12" id="cluster">
<!-- 
						<div id="cluster_info">
							<a href="http://library.brown.edu/services/clusters/lib_mobile/" class="icon-info"><span class="screen_reader">Computer availability for all Library spaces</span></a> 
						</div>
 -->
						<div class="icon-display icon">
							<h2>
								Computer Availability
							</h2>
						</div>
						<div id="cluster_availability">
<?php include('/var/www/html/libweb/cluster_counts/key_server_counts.php'); ?>
						</div>
					</div>
					<div class="col-md-12" id="pix">
						<div class="col-md-12" id="pix_inner">
						
						
						<!-- CENTER BOX CONTENT to be updated weekly -->
							
							<h3>
							    <a href="http://library.brown.edu/eresources/nytimes.php">New York Times</a>
							</h3>
							<a href="http://library.brown.edu/eresources/nytimes.php"><img style="width : 40% ; " src="http://library.brown.edu/redesign/images/banner/20170329NYTlogo.jpg" alt="The New York Times" /></a>Did you know that Brown students, faculty, and staff are eligible for free access to the New York Times? <a href="http://library.brown.edu/eresources/nytimes.php">Learn how to activate your Academic Pass</a>.
						</div>
					</div>
				</div>
				<div class="col-md-7" id="news">
					<div class="col-md-12" id="news_inner">
						<div class="icon-quotes-left icon">
							<h2>
								<a href="https://blogs.brown.edu/libnews/" style="color : #333 ; ">News from the Libraries</a>
							</h2>
						</div>
						<div id="news_content">
<?php
                                //adapted from http://bavotasan.com/2010/display-rss-feed-with-php/
                                $rss = new DOMDocument();
                                $rss->load('https://blogs.brown.edu/libnews/feed/');
                                $feed = array();
                                foreach ($rss->getElementsByTagName('item') as $node) {
                                    $item = array ( 
                                        'title' => $node->getElementsByTagName('title')->item(0)->nodeValue,
                                        'desc' => $node->getElementsByTagName('description')->item(0)->nodeValue,
                                        'link' => $node->getElementsByTagName('link')->item(0)->nodeValue,
                                        );
                                    array_push($feed, $item);
                                }
                                $limit = 2;
                                for($x=0;$x<$limit;$x++) {
                                    $title = str_replace(' & ', ' &amp; ', $feed[$x]['title']);
                                    $link = $feed[$x]['link'];
                                    $description = $feed[$x]['desc'];
                                    $new_description = preg_replace('/Continue reading <span class=\"meta-nav\">\&\#8594\;<\/span>/', 'more &raquo;<br /><br />', $description) ;
                                    $date = date('l F d, Y', strtotime($feed[$x]['date']));
                                    echo '<p><strong><a href="'.$link.'">'.$title.'</a></strong><br />';
                                    echo '<p><!--img src="" style="float : right ; width : 70px ; height : 70px ; margin-left : 10px ; margin-bottom : 10px ; "/-->'.$new_description.'</p>';
                                }
                            ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php include("/var/www/html/includes/universal_footer_include_bootstrap.html") ; ?>
</div>
<script src="/common/js/bootstrap/jquery.min.js">
</script>
<script src="/common/js/bootstrap/bootstrap.min.js">
</script>
<script src="/common/js/bootstrap/scripts.js">
</script>
<!--chat code-->
<script type="text/javascript">
	
	     (function() {
	       var x = document.createElement("script"); x.type = "text/javascript"; x.async = true;
	       x.src = (document.location.protocol === "https:" ? "https://" : "http://") + "us.libraryh3lp.com/js/libraryh3lp.js?multi,poll";
	       var y = document.getElementsByTagName("script")[0]; y.parentNode.insertBefore(x, y);
	     })();
</script>
<!-- End chat widget -->
<!-- Google analytics -->
<script type="text/javascript">
	var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
	document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
	try {
	var pageTracker = _gat._getTracker("UA-3203647-3");
	pageTracker._trackPageview();
	} catch(err) {}
</script>
</body>
</html>
