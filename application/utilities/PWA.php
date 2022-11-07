<?php

class PWA
{

    public static function orientationArr(int $langId): array
    {
        return [
            'portrait' => Label::getLabel('PWALBL_Portrait', $langId),
            'landscape' => Label::getLabel('PWALBL_Landscape', $langId),
        ];
    }

    public static function displayArr(int $langId): array
    {
        return [
            'fullscreen' => Label::getLabel('PWALBL_Full_Screen', $langId),
            'standalone' => Label::getLabel('PWALBL_Standalone', $langId),
            'minimal-ui' => Label::getLabel('PWALBL_Minimal_UI', $langId),
            'browser' => Label::getLabel('PWALBL_Browser', $langId)
        ];
    }

}
