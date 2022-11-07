<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php $layoutDirection = CommonHelper::getLayoutDirection(); ?>
<?php 
    $contactFrm->setFormTagAttribute('class', 'form form--normal');
    $captchaFld = $contactFrm->getField('htmlNote');
    $captchaFld->htmlBeforeField = '<div class="field-set">
               <div class="caption-wraper"><label class="field_label"></label></div>
               <div class="field-wraper">
                   <div class="field_cover">';
    $captchaFld->htmlAfterField = '</div></div></div>';
    $contactFrm->setFormTagAttribute('action', CommonHelper::generateUrl('home', 'contactSubmit'));
    $contactFrm->developerTags['colClassPrefix'] = 'col-md-';
    $contactFrm->developerTags['fld_default_col'] = 12;

?>
<style>
    .desktop-timer {
        display: none;
    }
    .desktop-active{
        font-size: 20px;
        color: red;
        display:block !important;
        
    }
</style>
<section class="section section--slideshow">
    <section class="main-slider"> 
  <div class="item youtube">
      <?php 
        $videoId = explode("?v=", $slide);
        $videoUrl = "https://www.youtube.com/embed/.$videoId[1]";
      ?>
     <iframe class="embed-player slide-media" width="980" allow="autoplay" height="520" src="<?php echo $videoUrl; ?>?enablejsapi=1&autoplay=1&mute=1&version=3&controls=0&fs=0&iv_load_policy=3&rel=0&showinfo=0&loop=1&playlist=<?php echo  $videoId[1]; ?>&start=1&autoplay=1&muted=1" frameborder="0" allowfullscreen></iframe> 

  <!--  <iframe class="embed-player slide-media" width="980" height="520" src="<?php //echo $videoUrl; ?>?autoplay=1&loop=1&playlist=<?php //echo  $videoId[1]; ?>&mute=1" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>-->
 
    
  </div>  
 </section>
    <div class="slideshow-content contact-box-wrp">
        <!-- <h1><?php //echo Label::getLabel('LBL_Slider_Title_Text',$siteLangId); ?></h1>
        <p><?php //echo Label::getLabel('LBL_Slider_Description_Text',$siteLangId); ?></p> -->

        <div class="slider-contents-outer">
            <div class="slider-contents">
                <h1><?php echo Label::getLabel('LBL_HomepageBanner_Title_Text',$siteLangId); ?></h1>
            </div>
            <div class="slider-imgs">
                <img src="https://ubuntutalks.com/image/editor-image/1649423563-img1.png" alt="Ubuntu Talks logo" />
            </div>
        </div>
            <div>   
            <?php
            
            $timer_array=array();
            $userTimezone = MyDate::getUserTimeZone();
            $curDateTime = MyDate::convertTimeFromSystemToUserTimezone('Y-m-d H:i:s', date('Y-m-d H:i:s'), true, $userTimezone);
            $curDateTimeUnix = strtotime($curDateTime);
            $myDate = new myDate();
            $myDate->setMonthAndweekName($siteLangId);
          
            foreach ($finaldata as $catId => $faqDetails) {
                foreach ($faqDetails as $ques) {
                    echo "<p style='display:none;' id=counter>".$ques['faq_title']."</p>";
                ?>
                <div class="timer-column">
                    <h3 id="timer">Next free trial starts in<span class="time-on-word"></span>&nbsp;</h3>
                    <div class="timer-row">
                        <!-- <p id="days"></p>
                        <p id="hours"></p>
                        <p id="mins"></p>
                        <p id="secs"></p>
                        <h2 id="end"></h2> -->
                        <?php
                          foreach ($classesList as $grpcls_start_datetime=> $class) {
                            foreach($class as $timer){
                                $price=(float)$timer['grpcls_entry_fee'];
                               
                                if($price==0){
                                  
                                   $startDateTime = MyDate::convertTimeFromSystemToUserTimezone('Y-m-d H:i:s', $timer['grpcls_start_datetime'], true, $userTimezone);
                                   $endDateTime = MyDate::convertTimeFromSystemToUserTimezone('Y-m-d H:i:s', $timer['grpcls_end_datetime'], true, $userTimezone);
                                   $startDateTimeUnix = strtotime($startDateTime);
                                   array_push($timer_array,$timer['grpcls_start_datetime']);
                               ?>
                               
                                <div class="timer__controls countdowntimer-js timer-js desktop-timer" id="timer-<?php echo $timer['grpcls_id']; ?>" data-startTime="<?php echo date('Y/m/d H:i:s', $curDateTimeUnix); ?>" data-endTime="<?php echo date('Y/m/d H:i:s', $startDateTimeUnix); ?>">
                               </div>
                               <?php
                            }
                        }
                           }
                        ?>
                    </div>
                    </div>
            </div>
            <?php
                }
            }
            // $timer_array=array();
            // $userTimezone = MyDate::getUserTimeZone();
            // $curDateTime = MyDate::convertTimeFromSystemToUserTimezone('Y-m-d H:i:s', date('Y-m-d H:i:s'), true, $userTimezone);
            // $curDateTimeUnix = strtotime($curDateTime);
            // $myDate = new myDate();
            // $myDate->setMonthAndweekName($siteLangId);
            // foreach ($classesList as $grpcls_start_datetime=> $class) {
            //      foreach($class as $timer){
            //             $startDateTime = MyDate::convertTimeFromSystemToUserTimezone('Y-m-d H:i:s', $timer['grpcls_start_datetime'], true, $userTimezone);
            //             $endDateTime = MyDate::convertTimeFromSystemToUserTimezone('Y-m-d H:i:s', $timer['grpcls_end_datetime'], true, $userTimezone);
            //             $startDateTimeUnix = strtotime($startDateTime);
            //             array_push($timer_array,$timer['grpcls_start_datetime']);
            //         ?>
                    
                      <!-- <div class="timer__controls countdowntimer-js timer-js desktop-timer" id="timer-<?php echo $timer['grpcls_id']; ?>" data-startTime="<?php echo date('Y/m/d H:i:s', $curDateTimeUnix); ?>" data-endTime="<?php echo date('Y/m/d H:i:s', $startDateTimeUnix); ?>">
                     </div> -->
                     <?php
            //      }
            //     }
        ?> 

         
        <!-- code for countdown  -->
        <!-- <div class="column" style="display: inline-flex;">
            <p id="days"></p>
            <p id="hours"></p>
            <p id="mins"></p>
            <p id="secs"></p>
            <h2 id="end"></h2>
        </div> -->
         <!-- complete code  for countdown  -->

         <div class="container container--narrow" style="width: 100% !important;">
            <p class="Question-text"><?php echo Label::getLabel('LBL_Homepage_Description_Text',$siteLangId); ?></p>
                <div class="row">
                    <div class="col-md-12" style="margin:0 auto !important; padding: 0 !important;">
                        <div class="contact-form contact-wrp">
                            <?php echo $contactFrm->getFormTag() ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="field-set">
                                        <div class="caption-wraper">
                                            <label class="field_label"><?php echo Label::getLabel('LBL_First_Name', $siteLangId) ?></label>
                                        </div>
                                        <div class="field-wraper">
                                            <div class="field_cover">
                                                <?php echo $contactFrm->getFieldHTML('name'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="field-set">
                                        <div class="caption-wraper">
                                            <label class="field_label"><?php echo Label::getLabel('LBL_Last_Name', $siteLangId) ?></label>
                                        </div>
                                        <div class="field-wraper">
                                            <div class="field_cover">
                                                <?php echo $contactFrm->getFieldHTML('lname'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="field-set">
                                        <div class="caption-wraper">
                                            <label class="field_label"><?php echo Label::getLabel('LBL_Email', $siteLangId); ?></label>
                                        </div>
                                        <div class="field-wraper">
                                            <div class="field_cover">
                                                <?php echo $contactFrm->getFieldHTML('email'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="field-set m-0">
                                        <div class="field-wraper">
                                            <div class="field_cover book-now-btn">
                                                <?php echo $contactFrm->getFieldHTML('btn_submit'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php echo $contactFrm->getExternalJS(); ?>
                            </form>
                        </div>
                    </div>
                </div>
            </div>


            
        <!-- <div class="slideshow__form">
        <form method="POST" class="form" action="<?php echo CommonHelper::generateFullUrl('Teachers','languages'); ?>" name="homeSearchForm" id="homeSearchForm" >
                <div class="slideshow-input">
                    <svg class="icon icon--search">
                        <use xlink:href="<?php echo CONF_WEBROOT_URL.'images/sprite.yo-coach.svg#search'; ?>"></use>
                    </svg>
                    <input type="text" name="language" placeholder="<?php echo Label::getLabel('LBL_I_am_learning...',$siteLangId); ?>">
                    <input type="hidden" name="teachLangId">
                    <input type="hidden" name="teachLangSlug">
                </div>
                <button class="btn btn--secondary btn--large btn--block"><?php echo Label::getLabel('LBL_Search_for_teachers',$siteLangId); ?></button>
            </form>
        </div>
        <div class="tags-inline">
            <b><?php echo Label::getLabel("LBL_Popular:",$siteLangId) ?></b>
            <ul>
                <?php
                $lastElment = end($allLanguages);
                foreach ($allLanguages as $langId => $langDetails) {
                    if ($lastElment['tlanguage_id'] != $langDetails['tlanguage_id']) {
                        $langDetails['tlanguage_name'] = $langDetails['tlanguage_name'] . ", ";
                    }
                ?>
                    <li class="tags-inline__item"><a href="<?php echo CommonHelper::generateUrl('teachers', 'languages', [$langDetails['tlanguage_slug']]); ?>"><?php echo $langDetails['tlanguage_name']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div> -->
</section> 
<script>
$(window).scroll(function () {
  //set scroll position in session storage
  sessionStorage.scrollPos = $(window).scrollTop();
});
$(document).ready(function(){          
 if($('.teacher__name').text()!==''){
     $('html, body').animate({
           scrollTop:sessionStorage.scrollPos
         }, 1000);
 }else{
     sessionStorage.scrollPos=0
 }
 });


//     // The data/time we want to countdown to
//     var daytrials = document. getElementById("counter"). innerHTML;
//     var dayTimer= parseInt(daytrials) ;
//     var today = new Date();
//     var tomorrow;
//     var timeleft=-1;
//     if((localStorage.getItem("lastTimer")!=dayTimer) || timeleft<0){
//         localStorage.setItem("lastTimer", dayTimer);
//         var today = new Date();
//         var tomorrow = new Date();
//         tomorrow.setDate(today.getDate()+dayTimer);
//         localStorage.setItem("lastDate", tomorrow);
//     }
//     else{
//         tomorrow=localStorage.getItem("lastDate" );
//     }
      
//     var countDownDate = new Date(tomorrow).getTime();

//     // Run myfunc every second
//     var myfunc = setInterval(function() {
//     var d = new Date();
//     var now = new Date().getTime();
//     timeleft = countDownDate - now;
        
//     // Calculating the days, hours, minutes and seconds left
//     var days = Math.floor(timeleft / (1000 * 60 * 60 * 24));
//     var hours = 24 - d.getHours();
//     var minutes = 60 - d.getMinutes();
//     if((minutes + '').length == 1){
//         minutes = '0' + minutes;
//     }
//   var seconds = 60 - d.getSeconds();
//   if((seconds + '').length == 1){
//         seconds = '0' + seconds;
//   }
   
//     // Result is output to the specific element
//     document.getElementById("days").innerHTML = days + ""
//     document.getElementById("hours").innerHTML = hours + "" 
//     document.getElementById("mins").innerHTML = minutes + "" 
//     document.getElementById("secs").innerHTML = seconds + "" 
        
//     // Display the message when countdown is over
//     if (timeleft < 0) {
//         clearInterval(myfunc);
//         myfunc();
//     }
//     }, 1000);
    </script>
    <script>
        var allTimer=document.getElementsByClassName('countdowntimer-js');
        for (let i = 0; i < allTimer.length; i++) {
            if(allTimer[i].innerHTML>"00:00:00:00"){
                allTimer[i].classList.remove("desktop-timer");
            allTimer[i].classList.add("desktop-active");
            i=allTimer.length+1;
            break;
            }
         }

    jQuery(document).ready(function () {
        var allTimer=document.getElementsByClassName('countdowntimer-js');
        if(allTimer.length>0){
        allTimer[0].classList.remove("desktop-timer");
            allTimer[0].classList.add("desktop-active"); 
        }
        else{
            $("h3#timer").html("<span class='trials_not_available'>Free trials are not available</span>");
        }
        $('.desktop-active').each(function (i) {
            var test = $(this);
            $(this).countdowntimer({
                startDate: $(this).attr('data-startTime'),
                dateAndTime: $(this).attr('data-endTime'),
                size: "sm",
            });
        });
        if(allTimer.length>0){
        setInterval(function () {
                     var active=document.getElementsByClassName('desktop-active');
     
        // for (let i = 0; i < allTimer.length; i++) {
          
            var ele=active[0].id;
            var ele1=document.getElementById(ele);
            var timerText=ele1.innerHTML;
            var timerTextArray = timerText.split(":");
            var newId = ele.split("-");
                var a=parseInt(newId[1]+1)
                var ne="timer"+"-"+newId[1];
                
            if(timerTextArray[0]=="00" && timerTextArray[1]=="00" && timerTextArray[2]=="00" && timerTextArray[3]=="00")
            {
                var l= localStorage.getItem("lastID");            
                var ne="timer"+"-"+l;
                var newIdele1=document.getElementById(ne);
                newIdele1.classList.remove("desktop-timer");
                newIdele1.classList.add("desktop-active"); 
                ele1.classList.remove("desktop-active");
                ele1.classList.add("desktop-timer"); 
                               
            }
            else{
                var newId = ele.split("-");
                var a=parseInt(newId[1]+1);
                localStorage.setItem("lastID", a);
                
            }
        //  }
        },1000);
    }
       
    });
</script>