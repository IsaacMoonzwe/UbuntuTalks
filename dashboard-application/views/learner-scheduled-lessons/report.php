<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$agendafrm->setFormTagAttribute('class', 'web_form form_horizontal');
$agendafrm->setFormTagAttribute('onsubmit', 'agendaSetupTestimonial(this); return(false);');
$agendafrm->developerTags['colClassPrefix'] = 'col-md-';
$agendafrm->developerTags['fld_default_col'] = 6;
?>
<style>
  .section{ padding: 0px !important; }
  .save-button input[type="submit"] {
    background: #333;
    display: -webkit-inline-box;
    display: -ms-inline-flexbox;
    display: inline-flex;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    -webkit-box-pack: center;
    -ms-flex-pack: center;
    justify-content: center;
    padding: 0 var(--padding-6);
    position: relative;
    cursor: pointer;
    border: none;
    height: 2.8rem;
    line-height: 2.8rem;
    color: var(--color-white);
    font-weight: 500;
    -webkit-transition: .3s all ease-in-out;
    -o-transition: .3s all ease-in-out;
    transition: .3s all ease-in-out;
    white-space: nowrap;
    border: 1px solid transparent;
}
</style>
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
            <!-- <form method="post" action="<?php //echo CommonHelper::generateUrl('LearnerScheduledLessons', 'Reportissue');
                                        ?>">
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
            </form> -->
            <section class="section">
              <div class="sectionhead">
              </div>
              <div class="sectionbody space">
                <div class="row">
                  <div class="col-sm-12">
                    <div class="tabs_nav_container responsive flat">
                      <div class="tabs_panel_wrap">
                        <div class="tabs_panel">
                          <?php echo $agendafrm->getFormTag() ?>
                          <div class="col-md-12">
                            <div class="field-set">
                              <div class="caption-wraper">
                                <label class="field_label"><?php echo Label::getLabel('LBL_Email_Address:', $siteLangId) ?></label>
                              </div>
                              <div class="field-wraper">
                                <div class="field_cover">
                                <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email" value="admin@ubuntutalks.com" readonly="readonly">
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="col-md-12">
                            <div class="field-set">
                              <div class="caption-wraper">
                                <label class="field_label"><?php echo Label::getLabel('LBL_Comments:', $siteLangId) ?></label>
                              </div>
                              <div class="field-wraper">
                                <div class="field_cover">
                                  <?php echo $agendafrm->getFieldHTML('report_comments_information'); ?>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="row save-button">
                          <div class="col-md-12">
                            <div class="field-set">
                              <div class="field-wraper">
                                <div class="field_cover">
                                  <?php echo $agendafrm->getFieldHTML('report_comments_id'); ?>
                                  <?php echo $agendafrm->getFieldHTML('user_id'); ?>
                                  <?php echo $agendafrm->getFieldHTML('btn_submit'); ?>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
            </section>
          </div>
        </div>
      </div>
    </div>
  </div>


  <script type="text/javascript">
    $(document).ready(function() {
      var buttonAdd = $("#add-button");
      var buttonRemove = $("#remove-button");
      var className = ".dynamic-field";
      var count = 0;
      var field = "";
      var maxFields = 50;

      function totalFields() {
        return $(className).length;
      }

      function addNewField() {
        count = totalFields() + 1;
        field = $("#dynamic-field-1").clone();
        field.attr("id", "dynamic-field-" + count);
        field.children("label").text("Field " + count);
        field.find("input").val("");
        $(className + ":last").after($(field));
        jQuery('#agenda_start_time,#agenda_end_time').each(function() {
          var dateType = $(this).attr('data-type');
          $(this).datetimepicker({
            format: 'Y-m-d H:i',
            onClose: function(date) {

              onChangeDateTime(date, dateType);
            },
          });
        });

      }


      function removeLastField() {
        if (totalFields() > 1) {
          $(className + ":last").remove();
        }
      }

      function enableButtonRemove() {
        if (totalFields() === 2) {
          buttonRemove.removeAttr("disabled");
          buttonRemove.addClass("shadow-sm");
        }
      }

      function disableButtonRemove() {
        if (totalFields() === 1) {
          buttonRemove.attr("disabled", "disabled");
          buttonRemove.removeClass("shadow-sm");
        }
      }

      function disableButtonAdd() {
        if (totalFields() === maxFields) {
          buttonAdd.attr("disabled", "disabled");
          buttonAdd.removeClass("shadow-sm");
        }
      }

      function enableButtonAdd() {
        if (totalFields() === (maxFields - 1)) {
          buttonAdd.removeAttr("disabled");
          buttonAdd.addClass("shadow-sm");
        }
      }

      buttonAdd.click(function() {
        addNewField();
        enableButtonRemove();
        disableButtonAdd();
      });

      buttonRemove.click(function() {
        removeLastField();
        disableButtonRemove();
        enableButtonAdd();
      });
    });
    var startTime = '';
    var endTime = '';
  </script>