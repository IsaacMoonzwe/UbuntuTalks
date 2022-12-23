<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<style>
    .pagination li:hover {
        cursor: pointer;
    }

    .number-of-records {
        padding: 15px;
        width: 20%;
    }

    ul.pagination li span {
        width: 35px;
        line-height: 32px;
        text-align: center;
        background: #f7f7f7;
        height: 35px;
        display: block;
        font-size: 14px;
        color: #333;
        border: 1px solid #ececec;
        border-radius: 50%;
        font-weight: 600;
        position: relative;
        overflow: hidden;
    }

    .pagination-container {
        width: 100%;
        padding: 20px;
    }

    ul.pagination li {
        margin-left: 10px;
    }

    ul.pagination li.active span {
        background: #3bc4e7;
        border-color: #3bc4e7 !important;
        color: #fff;
    }

    i.ion-eye.icon {
        font-size: 1.9em;
        color: rgb(102, 102, 102, 102);
    }
</style>

<section>
    <div class="sectionbody">
        <div class="tablewrap">
            <div class="number-of-records">
                <select class="form-control" name="state" id="maxRows">
                    <option value="5000">Show ALL Rows</option>
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="70">70</option>
                    <option value="100">100</option>
                </select>
            </div>
            <div id="lessonListing">
                <table id="table-id" width="100%" class="table table-responsive table-striped table-class">
                    <thead>
                        <tr>
                            <th><?php echo Label::getLabel('LBL_Order_Id', $adminLangId); ?></th>
                            <th><?php echo Label::getLabel('LBL_User_Name', $adminLangId); ?></th>
                            <th><?php echo Label::getLabel('LBL_User_Email', $adminLangId); ?></th>
                            <th><?php echo Label::getLabel('LBL_Payment_Type', $adminLangId); ?></th>
                            <th><?php echo Label::getLabel('LBL_Amount', $adminLangId); ?></th>
                            <th><?php echo Label::getLabel('LBL_Date', $adminLangId); ?></th>
                            <th><?php echo Label::getLabel('LBL_Coupon_Code', $adminLangId); ?></th>
                            <th><?php echo Label::getLabel('LBL_Details', $adminLangId); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        foreach ($TransactionHistoryInformationCategoriesList as $value) {
                        ?>
                            <tr>
                                <td><?php echo $value['opayment_order_id']; ?></td>
                                <td><?php echo $value['user_first_name'] . " " . $value['user_last_name']; ?></td>
                                <td><?php echo $value['user_email']; ?></td>
                                <td><?php echo $value['opayment_method']; ?></td>
                                <td><?php echo $value['opayment_amount']; ?></td>
                                <td><?php echo $value['opayment_date']; ?></td>
                                <td>
                                    <?php
                                    if (empty($value['order_discount_coupon_code'])) {
                                        echo Label::getLabel('LBL_Not_Using', $adminLangId);
                                    } else {
                                        echo $value['order_discount_coupon_code'];
                                    }

                                    ?>
                                </td>
                                <td><a href="javascript:void(0)" class="button small green" title="View Order Detail" onclick="viewDetail('<?php echo $value['opayment_order_id']; ?>');"><i class="ion-eye icon"></i></a></td>
                            </tr>
                        <?php } ?>
                    </tbody>

                </table>
                <?php
                if (!empty($TransactionHistoryInformationCategoriesList)) {
                } else {
                ?>
                    <h3 style="text-align:center;padding-top:15px;">No Records Found..!!</h3>
                <?php } ?>
                <form name="frmPurchaseLessonSearchPaging"><input type="hidden" name="page" value=""></form>
            </div>
        </div>
    </div>
    <div class='pagination-container'>
        <nav>
            <ul class="pagination">
                <li data-page="prev">
                    <span>
                        <i class="ion-ios-arrow-left"></i> <span class="sr-only">(current)
                        </span></span>
                </li>
                <li data-page="next" id="prev">
                    <span> <i class="ion-ios-arrow-right"></i> <span class="sr-only">(current)</span></span>
                </li>
            </ul>
        </nav>
    </div>
</section>






<script>
    getPagination('#table-id');

    function getPagination(table) {
        var lastPage = 1;
        $('#maxRows')
            .on('change', function(evt) {
                //$('.paginationprev').html('');						// reset pagination

                lastPage = 1;
                $('.pagination')
                    .find('li')
                    .slice(1, -1)
                    .remove();
                var trnum = 0; // reset tr counter
                var maxRows = parseInt($(this).val()); // get Max Rows from select option

                if (maxRows == 5000) {
                    $('.pagination').hide();
                } else {
                    $('.pagination').show();
                }

                var totalRows = $(table + ' tbody tr').length; // numbers of rows
                $(table + ' tr:gt(0)').each(function() {
                    // each TR in  table and not the header
                    trnum++; // Start Counter
                    if (trnum > maxRows) {
                        // if tr number gt maxRows

                        $(this).hide(); // fade it out
                    }
                    if (trnum <= maxRows) {
                        $(this).show();
                    } // else fade in Important in case if it ..
                }); //  was fade out to fade it in
                if (totalRows > maxRows) {
                    // if tr total rows gt max rows option
                    var pagenum = Math.ceil(totalRows / maxRows); // ceil total(rows/maxrows) to get ..
                    //	numbers of pages
                    for (var i = 1; i <= pagenum;) {
                        // for each page append pagination li
                        $('.pagination #prev')
                            .before(
                                '<li data-page="' +
                                i +
                                '">\
								  <span>' +
                                i++ +
                                '<span class="sr-only">(current)</span></span>\
								</li>'
                            )
                            .show();
                    } // end for i
                } // end if row count > max rows
                $('.pagination [data-page="1"]').addClass('active'); // add active class to the first li
                $('.pagination li').on('click', function(evt) {
                    // on click each page
                    evt.stopImmediatePropagation();
                    evt.preventDefault();
                    var pageNum = $(this).attr('data-page'); // get it's number

                    var maxRows = parseInt($('#maxRows').val()); // get Max Rows from select option

                    if (pageNum == 'prev') {
                        if (lastPage == 1) {
                            return;
                        }
                        pageNum = --lastPage;
                    }
                    if (pageNum == 'next') {
                        if (lastPage == $('.pagination li').length - 2) {
                            return;
                        }
                        pageNum = ++lastPage;
                    }

                    lastPage = pageNum;
                    var trIndex = 0; // reset tr counter
                    $('.pagination li').removeClass('active'); // remove active class from all li
                    $('.pagination [data-page="' + lastPage + '"]').addClass('active'); // add active class to the clicked
                    // $(this).addClass('active');					// add active class to the clicked
                    limitPagging();
                    $(table + ' tr:gt(0)').each(function() {
                        // each tr in table not the header
                        trIndex++; // tr index counter
                        // if tr index gt maxRows*pageNum or lt maxRows*pageNum-maxRows fade if out
                        if (
                            trIndex > maxRows * pageNum ||
                            trIndex <= maxRows * pageNum - maxRows
                        ) {
                            $(this).hide();
                        } else {
                            $(this).show();
                        } //else fade in
                    }); // end of for each tr in table
                }); // end of on click pagination list
                limitPagging();
            })
            .val(10)
            .change();

        // end of on select change

        // END OF PAGINATION
    }

    function limitPagging() {
        // alert($('.pagination li').length)

        if ($('.pagination li').length > 7) {
            if ($('.pagination li.active').attr('data-page') <= 3) {
                $('.pagination li:gt(5)').hide();
                $('.pagination li:lt(5)').show();
                $('.pagination [data-page="next"]').show();
            }
            if ($('.pagination li.active').attr('data-page') > 3) {
                $('.pagination li:gt(0)').hide();
                $('.pagination [data-page="next"]').show();
                for (let i = (parseInt($('.pagination li.active').attr('data-page')) - 2); i <= (parseInt($('.pagination li.active').attr('data-page')) + 2); i++) {
                    $('.pagination [data-page="' + i + '"]').show();

                }

            }
        }
    }

    $(function() {
        // Just to append id number for each row
        $('table tr:eq(0)').prepend('<th> ID </th>');

        var id = 0;

        $('table tr:gt(0)').each(function() {
            id++;
            $(this).prepend('<td>' + id + '</td>');
        });
    });

    //  Developed By Yasser Mas
    // yasser.mas2@gmail.com
</script>

<!--  Developed By Yasser Mas -->