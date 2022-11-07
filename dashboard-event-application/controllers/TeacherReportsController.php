<?php

class TeacherReportsController extends TeacherBaseController
{

    public function index()
    {
        FatUtility::exitWithErrorCode(404);
    }

    public function getStatisticalData()
    {
        $reportSearchForm = $this->reportSearchForm($this->siteLangId);
        $post = $reportSearchForm->getFormDataFromArray(FatApp::getPostedData());
        if (!$post) {
            Message::addErrorMessage(Label::getLabel('MSG_ERROR_INVALID_ACCESS', $this->siteLangId));
            FatUtility::dieJsonError(Message::getHtml());
        }
        $durationField = $reportSearchForm->getField('duration_type');
        $durationTypeArray = $durationField->options;
        $statObj = new Statistics(UserAuthentication::getLoggedUserId());
        $responseArray = [
            'earningData' => [],
            'soldLessons' => [],
            'graphData' => []
        ];
        foreach ($post['report_type'] as $value) {
            if ($value == Statistics::REPORT_EARNING) {
                $responseArray['earningData'] = $statObj->getEarning($post['duration_type'], true);
                $responseArray['earningData']['earning'] = CommonHelper::displayMoneyFormat($responseArray['earningData']['earning']);
            }
            if ($value == Statistics::REPORT_EARNING) {
                $responseArray['soldLessons'] = $statObj->getSoldLessons($post['duration_type'], true);
            }
        }
        if ($post['forGraph']) {
            $responseArray = $this->formatGraphArray($responseArray, $durationTypeArray, $post['duration_type']);
        }
        FatUtility::dieJsonSuccess($responseArray);
    }

    private function formatGraphArray(array $responseArray, array $durationTypeArray, int $durationType): array
    {
        $userTimezone = MyDate::getUserTimeZone();
        $systemTimezone = MyDate::getTimeZone();
        $fromDate = MyDate::changeDateTimezone($responseArray['earningData']['fromDate'], $systemTimezone, $userTimezone);
        $toDate = MyDate::changeDateTimezone($responseArray['earningData']['toDate'], $systemTimezone, $userTimezone);
        $earningLabel = Label::getLabel('Lbl_Earning');
        $lessonSoldLabel = Label::getLabel('Lbl_LESSONS_SOLD');
        $graphArray['column'] = [
            'durationType' => $durationTypeArray[$durationType] . ' ' . $fromDate . ' - ' . $toDate,
            'earningLabel' => $earningLabel,
            'lessonSoldLabel' => $lessonSoldLabel,
        ];
        $graphArray['rowData'] = [];
        $rowData = [];
        if (!empty($responseArray['earningData']['earningData'])) {
            foreach ($responseArray['earningData']['earningData'] as $key => $value) {
                $date = $this->getDateFormat($durationType, $value['order_date_added'], $userTimezone);
                $rowData[$key] = [
                    $date,
                    CommonHelper::displayMoneyFormat($value['earning'], true, false, false),
                    $earningLabel . " " . CommonHelper::displayMoneyFormat($value['earning']),
                    0,
                    $lessonSoldLabel . " 0"
                ];
            }
        }
        if (!empty($responseArray['soldLessons']['lessonData'])) {
            foreach ($responseArray['soldLessons']['lessonData'] as $key => $value) {
                if (array_key_exists($key, $rowData)) {
                    $rowData[$key][3] = $value['lessonCount'];
                    $rowData[$key][4] = $lessonSoldLabel . " " . $value['lessonCount'];
                } else {
                    $date = $this->getDateFormat($durationType, $value['order_date_added'], $userTimezone);
                    $rowData[$key] = [
                        $date,
                        0,
                        $earningLabel . " " . CommonHelper::displayMoneyFormat(0),
                        $value['lessonCount'],
                        $lessonSoldLabel . " " . $value['lessonCount']
                    ];
                }
            }
        }
        $graphArray['rowData'] = array_values($rowData);
        $responseArray['graphData'] = $graphArray;
        return $responseArray;
    }

    private function getDateFormat(int $durationType, string $date, string $userTimezone): string
    {
        switch ($durationType) {
            case Statistics::TYPE_TODAY:
                $date = MyDate::convertTimeFromSystemToUserTimezone("h:i A", $date, true, $userTimezone);
                break;
            case Statistics::TYPE_THIS_YEAR:
            case Statistics::TYPE_LAST_YEAR:
                $date = MyDate::convertTimeFromSystemToUserTimezone("M-Y", $date, true, $userTimezone);
                break;
            default:
                $date = MyDate::convertTimeFromSystemToUserTimezone("Y-m-d", $date, true, $userTimezone);
                break;
        }
        return $date;
    }

}
