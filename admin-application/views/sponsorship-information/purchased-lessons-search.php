<style>
    .section {
        margin: 15px 0 15px !important;
        box-shadow: none !important
    }

    div#nav a {
        display: inline-block;
        vertical-align: top;
        margin: 20px 5px;
        width: 35px;
        line-height: 32px;
        text-align: center;
        background: #f7f7f7;
        height: 35px;
        font-size: 14px;
        color: #333;
        border: 1px solid #ececec;
        border-radius: 50%;
        font-weight: 600;
        position: relative;
        overflow: hidden;
    }

    div#nav {
        margin-left: 15px;
    }

    div#nav a.active {
        background: #3bc4e7 !important;
        border-color: #3bc4e7 !important;
        color: #fff !important;
    }
</style>
<section class="section">
    <div class="sectionbody">
        <div class="tablewrap">
            <div id="lessonListing">
                <table id="data" width="100%" class="table table-responsive">
                    <thead>
                        <tr>
                            <th><?php echo Label::getLabel('LBL_SR_No', $adminLangId); ?></th>
                            <th><?php echo Label::getLabel('LBL_User_Id', $adminLangId); ?></th>
                            <th><?php echo Label::getLabel('LBL_Visitor_Name', $adminLangId); ?></th>
                            <th><?php echo Label::getLabel('LBL_Visitor_Phone', $adminLangId); ?></th>
                            <th><?php echo Label::getLabel('LBL_Sponsorship_Event_Name', $adminLangId); ?></th>
                            <th><?php echo Label::getLabel('LBL_Sponsorship_Plan_Type', $adminLangId); ?></th>
                            <th><?php echo Label::getLabel('LBL_Sponsorship_Ending_Time', $adminLangId); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        foreach ($sponserEventData as $value) {
                        ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td><?php echo $value['sponsorship'][0]['event_user_id'];  ?></td>
                                <td><?php echo $value['sponsorship'][0]['user_first_name'] ." ".$value['sponsorship'][0]['user_last_name']; ?></td>
                                <td><?php echo $value['sponsorship'][0]['user_phone_code'] ." ".$value['sponsorship'][0]['user_phone'];  ?></td>
                                <td><?php echo $value['event_name'];  ?></td>
                                <td><?php echo $value['plan'];  ?></td>
                                <td><?php echo $value['event_ending_time'];  ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <form name="frmPurchaseLessonSearchPaging"><input type="hidden" name="page" value=""></form>
            </div>
        </div>
    </div>
</section>
<script>
    $(document).ready(function() {
        $('#data').after('<div id="nav"></div>');
        var rowsShown = 10;
        var rowsTotal = $('#data tbody tr').length;
        var numPages = rowsTotal / rowsShown;
        for (i = 0; i < numPages; i++) {
            var pageNum = i + 1;
            $('#nav').append('<a href="#" rel="' + i + '">' + pageNum + '</a> ');
        }
        $('#data tbody tr').hide();
        $('#data tbody tr').slice(0, rowsShown).show();
        $('#nav a:first').addClass('active');
        $('#nav a').bind('click', function() {
            $('#nav a').removeClass('active');
            $(this).addClass('active');
            var currPage = $(this).attr('rel');
            var startItem = currPage * rowsShown;
            var endItem = startItem + rowsShown;
            $('#data tbody tr').css('opacity', '0.0').hide().slice(startItem, endItem).
            css('display', 'table-row').animate({
                opacity: 1
            }, 300);
        });
    });
</script>