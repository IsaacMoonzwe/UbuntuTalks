<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<!-- [ PAGE ========= -->
<!-- <main class="page"> -->
<div class="container container--fixed">
    <div class="page__head">
        <h1>Support Help</h1>
    </div>
    <div class="page__body">
            <div class="page-content">
                 <div class="wallet-box page-container margin-top-5 margin-bottom-5 padding-8">
                    <div class="row justify-content-between align-items-center">
                      <div class="col-sm-12">
                        <form method="post" action="<?php echo CommonHelper::generateUrl('LearnerScheduledLessons', 'Reportissue'); ?>">
                          <div class="form-group">
                            <label for="exampleInputEmail1">Email Address:</label>
                            <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email" value="admin@ubuntutalks.com" readonly="readonly">
                            <input type="hidden" name="type" value="report">
                            <small id="emailHelp" class="form-text text-muted">Your email is secured and not shared..</small>
                          </div>
                          <br>
                          <div class="form-group">
                            <label for="exampleInputPassword1">Comment:</label>
                            <textarea class="form-control" name="comment"></textarea>
                          </div>
                          
                          <button type="submit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>
        </div>
    </div>
</div>
  