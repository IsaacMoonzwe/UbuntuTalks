<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">

<div id="navigation">
    <?php
    $this->includeTemplate('virtual-session/navigation-menu.php', ['siteLangId' => $siteLangId]);
    ?>
</div>

<main>
    <!-- Education  -->
    <section class="education">
        <div class="education-box">
            <div class="container-width">
                <h1>Pushing higher education forward through research.</h1>
            </div>
        </div>
    </section>

    <!-- Session Titles -->

    <section class="edu-session">
        <div class="container-width">
            <div class="edu-session-txt">
                <p>Click the <b>Session Titles</b> below to view and comment on the research posters.
                    Join the Discussion via Zoom on May 10th, 2022 (4:30pm – 7pm PT).
                    <a href="https://www.zoom.us/" target="_blank"><span class="actives">(Zoom Help)</span></a></h2>
            </div>
            <div class="row">
                <?php foreach ($records as $value) {
                    if (!empty($value['speaker_image'])) {
                        foreach ($value['speaker_image'] as $testimonialImg) {
                            $htmlAfterField = '<img src="' . CommonHelper::generateFullUrl('VirtualSession', 'image', array($testimonialImg['afile_record_id'], $testimonialImg['afile_lang_id'], 'MEDIUM')) . '?' . time() . '">';
                        }
                    }
                ?>
                    <div class="col-md-4">
                        <div class="student-img">
                            <?php echo $htmlAfterField; ?>
                            <?php
                            $Startdate = explode(" ", $value['virtual_main_session_start_time']);
                            $StartDate_Convert = date("g:iA", strtotime($Startdate[1]));
                            $Enddate = explode(" ", $value['virtual_main_session_end_time']);
                            $EndDate_Convert = date("g:iA", strtotime($Enddate[1]));
                            ?>
                            <div class="session-detail">
                                <a href="<?php echo CommonHelper::generateUrl('VirtualSession', 'sessionWiseListing', [CommonHelper::htmlEntitiesDecode($value['virtual_main_session_slug'])]); ?>">

                                    <?php echo $value['virtual_main_session_title']; ?> |
                                    <?php echo $StartDate_Convert; ?> -
                                    <?php echo $EndDate_Convert; ?> |
                                    <?php echo $value['virtual_main_session_sub_title']; ?>
                                </a>

                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>

    <!-- Comments -->
    <!-- <section class="comment">
        <div class="container-width">
            <hr class="comment-border">
            <div class="comment-detail d-flexs">
                <h2>Recent Comments</h2>
            </div>
            <div class="comment-txt">
                <ul>
                    <li class="d-flexs">
                        <div>
                            <img src="http://1.gravatar.com/avatar/7638f76a5d508b888a846e8f907da410?s=48&d=mm&r=g" alt="user">
                        </div>
                        <div class="recent-txt">
                            <span href="">Zoe Corwin on <a href="" class="actives">Student Equity & Inclusion
                                    Programs and Mental Health Care</a><br>
                                May 17, 2022
                            </span>
                            <p>I really like the way you clearly organized your code list and included quotes to
                                support each point. The details…</p>
                        </div>
                    </li>
                    <li class="d-flexs">
                        <div>
                            <img src="http://1.gravatar.com/avatar/7638f76a5d508b888a846e8f907da410?s=48&d=mm&r=g" alt="user">
                        </div>
                        <div class="recent-txt">
                            <span href="">Diana Santoyo on <a href="" class="actives"> The Perspective of Abolish
                                    Greek Life Among National Pan-Hellenic Council and Multicultural Greek
                                    Organizations Collegiate Members</a><br>
                                May 11, 2022
                            </span>
                            <p>Amazing work!!! I appreciate this research. Greek life has a lot of progress to make
                                surrounding support, safety, and accountability.…</p>
                        </div>
                    </li>
                    <li class="d-flexs">
                        <div>
                            <img src="http://1.gravatar.com/avatar/7638f76a5d508b888a846e8f907da410?s=48&d=mm&r=g" alt="user">
                        </div>
                        <div class="recent-txt">
                            <span href="">Brandon Lim on<a href="" class="actives">The Perspective of Abolish Greek
                                    Life Among National Pan-Hellenic Council and Multicultural Greek Organizations
                                    Collegiate Members</a><br>
                                May 11, 2022
                            </span>
                            <p>What an important topic! I’ve read articles related to this but, this project is the
                                first research study I’ve seen…</p>
                        </div>
                    </li>
                    <li class="d-flexs">
                        <div>
                            <img src="http://1.gravatar.com/avatar/7638f76a5d508b888a846e8f907da410?s=48&d=mm&r=g" alt="user">
                        </div>
                        <div class="recent-txt">
                            <span href="">Diana Santoyo on <a href="" class="actives"> Faculty Perceptions of the
                                    Student-Athlete Academic Experience</a><br>
                                May 11, 2022
                            </span>
                            <p>My favorite athlete group! Great job!</p>
                        </div>
                    </li>
                    <li class="d-flexs">
                        <div>
                            <img src="http://1.gravatar.com/avatar/7638f76a5d508b888a846e8f907da410?s=48&d=mm&r=g" alt="user">
                        </div>
                        <div class="recent-txt">
                            <span href="">Brandon Lim on <a href="" class="actives">Student Athletes’ Mental
                                    Wellness Resources: Access and Utilization</a><br>
                                May 11, 2022
                            </span>
                            <p>Hi friends! Thank you so much for highlighting issues of access and presenting on
                                implications for practice! Great job, everyone!</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </section> -->

</main>