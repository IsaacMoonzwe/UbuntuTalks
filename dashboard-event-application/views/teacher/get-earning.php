<span class="-gap"></span><div class="box__body"><h3 class="-color-secondary"><?php echo CommonHelper::displayMoneyFormat($earningData['earning']); ?></h3> <?php
    if ($earningData['fromDate']) {
        echo $earningData['fromDate'] . ' - ' . $earningData['toDate'];
    }
    ?></div>