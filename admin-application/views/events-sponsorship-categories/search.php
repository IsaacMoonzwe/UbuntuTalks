<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php
$htmlAfterField = '';
$arr_flds = array(
    'dragdrop' => '',
    'listserial' => Label::getLabel('LBL_Sr_no.', $adminLangId) ,
    'events_sponsorship_categories_plan_title' => Label::getLabel('LBL_events_sponsorship_categories_Plan_Title', $adminLangId) ,
    'events_sponsorship_categories_starting_date' => Label::getLabel('LBL_Starting_Date', $adminLangId) ,
    'events_sponsorship_categories_ending_date' => Label::getLabel('LBL_Ending_Date', $adminLangId) ,
    'events_sponsorship_categories_active' => Label::getLabel('LBL_Status', $adminLangId) ,
    'action' => Label::getLabel('LBL_Action', $adminLangId) ,
);
if (!$canEdit) {
    unset($arr_flds['dragdrop']);
}
$tbl = new HtmlElement('table', array(
    'width' => '100%',
    'class' => 'table table-responsive table--hovered',
    'id' => 'paymentMethod'
));
$th = $tbl->appendElement('thead')
    ->appendElement('tr');
foreach ($arr_flds as $val)
{
    $e = $th->appendElement('th', array() , $val);
}
$sr_no = 0;
foreach ($arr_listing as $sn => $row)
{
    $sr_no++;
    $tr = $tbl->appendElement('tr');
    $tr->setAttribute("id", $row['events_sponsorship_categories_id']);
    
    foreach ($arr_flds as $key => $val)
    {
        $td = $tr->appendElement('td');
        
        switch ($key)
        {
            case 'dragdrop':
                if ($row['events_sponsorship_categories_active'] == applicationConstants::ACTIVE) {
                    $td->appendElement('i', array('class' => 'ion-arrow-move icon'));
                    $td->setAttribute("class", 'dragHandle');
                }
                break;
            case  'testimonial_image':
                if (!empty($row['speaker_image'])) {
                    foreach ($row['speaker_image'] as $testimonialImg) {
                        $htmlAfterField = '<img src="' . CommonHelper::generateFullUrl('Speakers', 'image', array($testimonialImg['afile_record_id'], $testimonialImg['afile_lang_id'], 'MEDIUM')) . '?' . time() . '">';
                    }
                }
                $td->appendElement('image', array() , $htmlAfterField, true);
            break;
            case 'listserial':
                $td->appendElement('plaintext', array() , $sr_no);
            break;
            case 'event_agenda_schedule':
                $ul = $td->appendElement("ul", array(
                    "class" => "actions actions centered"
                ));
                if ($canEdit)
                {
                    $li = $ul->appendElement("li", array(
                        'class' => 'droplink'
                    ));
                    $li->appendElement('a', array(
                        'href' => 'javascript:void(0)',
                        'class' => 'button small green',
                        "onclick" => "editTestimonialAgendaFormNew(" . $row['events_sponsorship_categories_id'] . ")",
                        'title' => Label::getLabel('LBL_Edit', $adminLangId)
                    ) , '<i class="icon ion-ios-calendar-outline"></i>', true);
                    $innerDiv = $li->appendElement('div', array(
                        'class' => 'dropwrap'
                    ));
                    $innerUl = $innerDiv->appendElement('ul', array(
                        'class' => 'linksvertical'
                    ));
                }

            break;
            case 'events_sponsorship_categories_active':
                $active = "";
                $statusAct = '';
                if ($row['events_sponsorship_categories_active'] == applicationConstants::YES && $canEdit === true)
                {
                    $active = 'checked';
                    $statusAct = 'inactiveStatus(this)';
                }
                if ($row['events_sponsorship_categories_active'] == applicationConstants::NO && $canEdit === true)
                {
                    $active = '';
                    $statusAct = 'activeStatus(this)';
                }
                $statusClass = ($canEdit === false) ? 'disabled' : '';
                $str = '<label class="statustab -txt-uppercase">                 
                     <input ' . $active . ' type="checkbox" id="switch' . $row['events_sponsorship_categories_id'] . '" value="' . $row['events_sponsorship_categories_id'] . '" onclick="' . $statusAct . '" class="switch-labels status_' . $row['events_sponsorship_categories_id'] . '"/>
                    <i class="switch-handles ' . $statusClass . '"></i></label>';
                $td->appendElement('plaintext', array() , $str, true);
            break;
            case 'action':
                $ul = $td->appendElement("ul", array(
                    "class" => "actions actions centered"
                ));
                if ($canEdit)
                {
                    $li = $ul->appendElement("li", array(
                        'class' => 'droplink'
                    ));
                    $li->appendElement('a', array(
                        'href' => 'javascript:void(0)',
                        'class' => 'button small green',
                        'title' => Label::getLabel('LBL_Edit', $adminLangId)
                    ) , '<i class="ion-android-more-horizontal icon"></i>', true);
                    $innerDiv = $li->appendElement('div', array(
                        'class' => 'dropwrap'
                    ));
                    $innerUl = $innerDiv->appendElement('ul', array(
                        'class' => 'linksvertical'
                    ));
                    $innerLiEdit = $innerUl->appendElement('li');
                    $innerLiEdit->appendElement('a', array(
                        'href' => 'javascript:void(0)',
                        'class' => 'button small green',
                        'title' => Label::getLabel('LBL_Edit', $adminLangId) ,
                        "onclick" => "editTestimonialFormNew(" . $row['events_sponsorship_categories_id'] . ")"
                    ) , Label::getLabel('LBL_Edit', $adminLangId) , true);
                    $innerLiDelete = $innerUl->appendElement("li");
                    $innerLiDelete->appendElement('a', array(
                        'href' => 'javascript:void(0)',
                        'class' => 'button small green',
                        'title' => Label::getLabel('LBL_Delete', $adminLangId) ,
                        "onclick" => "deleteRecord(" . $row['events_sponsorship_categories_id'] . ")"
                    ) , Label::getLabel('LBL_Delete', $adminLangId) , true);
                }
            break;
            default:
                $td->appendElement('plaintext', array() , $row[$key], true);
            break;
        }
    }
}
if (count($arr_listing) == 0)
{
    $tbl->appendElement('tr')
        ->appendElement('td', array(
        'colspan' => count($arr_flds)
    ) , Label::getLabel('LBL_No_Records_Found', $adminLangId));
}
echo $tbl->getHtml();

?>
<script>
    $(document).ready(function () {
        $('#paymentMethod').tableDnD({
            onDrop: function (table, row) {
                fcom.displayProcessing();
                var order = $.tableDnD.serialize('id');
                fcom.ajax(fcom.makeUrl('EventsSponsorshipCategories', 'updateOrder'), order, function (res) {
                    var ans = $.parseJSON(res);
                    if (ans.status == 1)
                    {
                        fcom.displaySuccessMessage(ans.msg);
                    } else {
                        fcom.displayErrorMessage(ans.msg);
                    }
                });
            },
            dragHandle: ".dragHandle",
        });
    });
</script>