<?php

class PriceSlabsController extends AdminBaseController
{

    public function __construct($action)
    {
        parent::__construct($action);
        $this->objPrivilege->canViewPriceSlab();
    }

    public function index()
    {
        $canEdit = $this->objPrivilege->canEditPriceSlab($this->admin_id, true);
        $this->set("canEdit", $canEdit);
        $this->_template->render();
    }

    public function search()
    {
        $pagesize = FatApp::getConfig('CONF_ADMIN_PAGESIZE', FatUtility::VAR_INT, 10);
        $page = FatApp::getPostedData('page', FatUtility::VAR_INT, 1);
        $postedData = FatApp::getPostedData();
        $searchObject = PriceSlab::getSearchObject(false);
        $searchObject->addOrder('prislab_active', 'desc');
        $searchObject->addOrder('prislab_id', 'desc');
        $searchObject->setPageNumber($page);
        $searchObject->setPageSize($pagesize);
        $records = FatApp::getDb()->fetchAll($searchObject->getResultSet());
        $canEdit = $this->objPrivilege->canEditPriceSlab($this->admin_id, true);
        $this->set('pageCount', $searchObject->pages());
        $this->set("canEdit", $canEdit);
        $this->set("records", $records);
        $this->set('recordCount', $searchObject->recordCount());
        $this->set('postedData', $postedData);
        $this->set('page', $page);
        $this->set('pageSize', $pagesize);
        $this->_template->render(false, false);
    }

    public function form(int $slabId = 0)
    {
        $form = $this->getForm($slabId);
        if (0 < $slabId) {
            $data = PriceSlab::getAttributesById($slabId, array(
                        'prislab_id',
                        'prislab_min',
                        'prislab_max'
            ));
            if ($data === false) {
                FatUtility::dieWithError($this->str_invalid_request);
            }
            $form->fill($data);
        }
        $this->set('psId', $slabId);
        $this->set('form', $form);
        $this->_template->render(false, false);
    }

    public function setup()
    {
        $this->objPrivilege->canEditPriceSlab();
        $frm = $this->getForm();
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $psId = $post['prislab_id'];
        unset($post['prislab_id']);
        $priceSlab = new PriceSlab($psId);
        // if ($priceSlab->isSlapCollapse($post['prislab_min'], $post['prislab_max'])) {
        //     FatUtility::dieJsonError(Label::getLabel('LBL_YOUR_SLOT_IS_COLLAPSE_WITH_OTHER_SLOTS'));
        // }
        $priceSlab->assignValues($post);
        if (!$priceSlab->saveSlab($post['prislab_min'], $post['prislab_max'])) {
            FatUtility::dieJsonError($priceSlab->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_SLOT_SAVE_SUCCESSFULLY'));
    }

    public function changeStatus()
    {
        $this->objPrivilege->canEditPriceSlab();
        $psId = FatApp::getPostedData('psId', FatUtility::VAR_INT, 0);
        $status = FatApp::getPostedData('status', FatUtility::VAR_INT, 0);
        $slabData = PriceSlab::getAttributesById($psId, array(
                    'prislab_id',
                    'prislab_active'
        ));
        if ($slabData == false) {
            FatUtility::dieJsonError($this->str_invalid_request);
        }
        $PriceSlab = new PriceSlab($psId);
        if (!$PriceSlab->changeStatus($status)) {
            Message::addErrorMessage($PriceSlab->getError());
            FatUtility::dieWithError(Message::getHtml());
        }
        FatUtility::dieJsonSuccess($this->str_update_record);
    }

    private function getForm(int $psId = 0): object
    {
        $form = new Form('PriceSlabFrm');
        $form->addHiddenField('', 'prislab_id', $psId);
        $minField = $form->addRequiredField(Label::getLabel('LBL_Min', $this->adminLangId), 'prislab_min');
        $minField->requirements()->setIntPositive();
        $minField->requirements()->setRange(1, 9999);
        $maxField = $form->addRequiredField(Label::getLabel('LBL_max', $this->adminLangId), 'prislab_max');
        $maxField->requirements()->setIntPositive();
        $maxField->requirements()->setRange(2, 9999);
        $form->addRequiredField(Label::getLabel('LBL_Title', $langId), 'pfname', '');
        $minField->requirements()->setCompareWith('prislab_max', 'lt', Label::getLabel('LBL_max', $this->adminLangId));
        $maxField->requirements()->setCompareWith('prislab_min', 'gt', Label::getLabel('LBL_min', $this->adminLangId));
        $form->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes', $this->adminLangId));
        return $form;
    }

}
