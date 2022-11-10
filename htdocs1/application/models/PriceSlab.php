<?php

class PriceSlab extends MyAppModel
{

    const DB_TBL = 'tbl_pricing_slabs';
    const DB_TBL_PREFIX = 'prislab_';

    public function __construct(int $id = 0)
    {
        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $id);
    }

    public function saveSlab(int $min, int $max): bool
    {
        $this->assignValues([
            'prislab_min' => $min,
            'prislab_max' => $max,
            'prislab_active' => applicationConstants::YES,
        ]);
        return $this->save([], $this->getFlds());
    }

    public static function getSearchObject(bool $activeOnly = false): SearchBase
    {
        $searchBase = new SearchBase(self::DB_TBL, 'ps');
        if ($activeOnly) {
            $searchBase->addCondition('prislab_active', '=', applicationConstants::ACTIVE);
        }
        return $searchBase;
    }

    public function getAllSlabs(bool $activeOnly = true, array $attr = ['ps.*']): array
    {
        $searchObject = self::getSearchObject($activeOnly);
        if (!empty($attr)) {
            $searchObject->addMultipleFields($attr);
        }
        $searchObject->doNotLimitRecords();
        return FatApp::getDb()->fetchAll($searchObject->getResultSet());
    }

    public function isSlapCollapse(int $min, int $max): bool
    {
        $searchObject = PriceSlab::getSearchObject();
        $searchObject->doNotCalculateRecords();
        $searchObject->addCondition('prislab_max', '>=', $min);
        $searchObject->addCondition('prislab_min', '<=', $max);
        $searchObject->addCondition('prislab_id', '!=', $this->mainTableRecordId);
        $searchObject->setPageSize(1);
        $slabData = FatApp::getDb()->fetch($searchObject->getResultSet());
        return (!empty($slabData));
    }

    public static function getMinAndMaxSlab(): array
    {
        $searchObject = PriceSlab::getSearchObject();
        $searchObject->doNotCalculateRecords();
        $searchObject->setPageSize(1);
        $searchObject->addCondition('prislab_active', '=', applicationConstants::ACTIVE);
        $searchObject->addMultipleFields([
            'min(prislab_min) as minSlab',
            'max(prislab_max) as maxSlab'
        ]);
        $minAndMaxSlab = FatApp::getDb()->fetch($searchObject->getResultSet());
        if (!empty($minAndMaxSlab)) {
            return $minAndMaxSlab;
        }
        return [
            'minSlab' => 0,
            'maxSlab' => 0
        ];
    }

}
