<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php
$htmlAfterField = '';
$arr_flds = array(
    'listserial' => Label::getLabel('LBL_Sr_no.', $adminLangId) ,	
    'event_id' => Label::getLabel('LBL_Event_Id', $adminLangId) ,
    'event_name' => Label::getLabel('LBL_Event_Name', $adminLangId) ,
    'event_ticket_url' => Label::getLabel('LBL_Event_URL', $adminLangId) ,
    'event_start_time' => Label::getLabel('LBL_Event_Start_Time', $adminLangId) ,
    'event_end_time' => Label::getLabel('LBL_Event_end_Time', $adminLangId) ,
    'event_agenda_schedule' => Label::getLabel('LBL_View_Agenda_Schedule', $adminLangId) ,
    'event_and_agenda_active' => Label::getLabel('LBL_Status', $adminLangId) ,
    'action' => Label::getLabel('LBL_Action', $adminLangId) ,
);
$tbl = new HtmlElement('table', array(
    'width' => '100%',
    'class' => 'table table-responsive table--hovered'
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
    $tr->setAttribute("id", $row['event_and_agenda_id']);
    
    foreach ($arr_flds as $key => $val)
    {
        $td = $tr->appendElement('td');
        
        switch ($key)
        {
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
                        "onclick" => "editTestimonialAgendaFormNew(" . $row['event_and_agenda_id'] . ")",
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
            case 'event_and_agenda_active':
                $active = "";
                $statusAct = '';
                if ($row['event_and_agenda_active'] == applicationConstants::YES && $canEdit === true)
                {
                    $active = 'checked';
                    $statusAct = 'inactiveStatus(this)';
                }
                if ($row['event_and_agenda_active'] == applicationConstants::NO && $canEdit === true)
                {
                    $active = '';
                    $statusAct = 'activeStatus(this)';
                }
                $statusClass = ($canEdit === false) ? 'disabled' : '';
                $str = '<label class="statustab -txt-uppercase">                 
                     <input ' . $active . ' type="checkbox" id="switch' . $row['event_and_agenda_id'] . '" value="' . $row['event_and_agenda_id'] . '" onclick="' . $statusAct . '" class="switch-labels status_' . $row['event_and_agenda_id'] . '"/>
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
                        "onclick" => "editTestimonialFormNew(" . $row['event_and_agenda_id'] . ")"
                    ) , Label::getLabel('LBL_Edit', $adminLangId) , true);
                    $innerLiDelete = $innerUl->appendElement("li");
                    $innerLiDelete->appendElement('a', array(
                        'href' => 'javascript:void(0)',
                        'class' => 'button small green',
                        'title' => Label::getLabel('LBL_Delete', $adminLangId) ,
                        "onclick" => "deleteRecord(" . $row['event_and_agenda_id'] . ")"
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

