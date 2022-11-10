<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<section class="section section--cta tdsnesccls">
	<div class="container2">
	<div class="videloBlock-lt">
		<img src="../images/img-1.png" alt="" />
	</div>
	<div class="videloBlock-rt">
		<iframe width="100%" height="510" src="https://www.youtube.com/embed/1dcUxalZ9Yo?autoplay=1&loop=1&playlist=1dcUxalZ9Yo&mute=1" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
	<div class="container container--narrow">
		<div class="cta-content">          
			
			<h2 style="text-shadow: none !important;font-weight: bold;">We are focused on</h2>
			<div><br>
				</div>
			<h3>Providing flexible access to virtual lessons to allow you to learn anywhere at any time!</h3>
			<h3><span style="color: rgb(206, 69, 0);"></span></h3>
			<div><br>
				</div><a class="btn btn--secondary btn--large" href="https://www.ubuntutalks.com/teachers">Browse Courses</a></div></div>
	</div>
</div>
</section>

<style type="text/css">
.container2{max-width: 1220px;padding-right: 15px;display: flex;
overflow: hidden;
padding-left: 15px;
margin-right: auto;
margin-left: auto;
position: relative;}
.tdsnesccls {
  padding: 0 !important;
overflow: hidden;
position: relative;
display: flex;
}
.tdsnesccls iframe {
  height:400px !important;
  width: 100% !important;
}
.tdsnesccls .container.container--narrow {
  position: absolute;
  top: 50%;
  left: 5%;
  width: 100%; z-index: 2;
  transform: translate(0,-50%);
  -webkit-transform: translate(0,-50%);
}
.tdsnesccls h2 {
  margin: 0 !important;
}
.section--cta::before {
  background:none !important;
}

.videloBlock-lt {
  width: 50%;
  position: relative;
  z-index: 2;
  background: #fff;
  padding: 25px;
}
.videloBlock-rt {
  width: 50%;overflow: hidden;
  position: relative;
  z-index: 1;
}

@media only screen and (max-width: 1200px){
.tdsnesccls iframe {height: 320px !important;}
}

@media only screen and (max-width: 767px){
.container2{display: block;}
.tdsnesccls {display: block;}
.videloBlock-lt {width: 100%;}
.videloBlock-rt {width: 100%;overflow: hidden;}
.tdsnesccls iframe {height: 400px !important;}
.tdsnesccls .container.container--narrow {position: relative; top: 0; left: 0;transform: none;  -webkit-transform: none;}
.videloBlock-rt .cta-content {margin: 25px 0 !important;  max-width: 100% !important;  color: #000;}
.videloBlock-rt .cta-content h1 {color: rgb(175, 88, 0);}
}

@media only screen and (max-width: 600px){
.tdsnesccls .cta-content {max-width: 90% !important;}
.tdsnesccls iframe {height: 320px !important;}
}
@media only screen and (max-width: 500px){
.tdsnesccls iframe {height: 250px !important;}
}
@media only screen and (max-width: 400px){
.tdsnesccls iframe {height: 200px !important;}
}
</style>

<?php //echo html_entity_decode($browseTutorPage);
