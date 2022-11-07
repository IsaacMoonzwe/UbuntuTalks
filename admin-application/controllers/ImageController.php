<?php

class ImageController extends FatController
{

    public function default_action()
    {
        exit(Label::getLabel('LBL_Invalid_Request!!', CommonHelper::getLangId()));
    }

    public function siteAdminLogo($lang_id = 0, $sizeType = '')
    {
        $lang_id = FatUtility::int($lang_id);
        $file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_ADMIN_LOGO, 0, 0, $lang_id);
        $image_name = isset($file_row['afile_physical_path']) ? $file_row['afile_physical_path'] : '';
        $default_image = '';
        AttachedFile::displayImage($image_name, 0, 0, $default_image, '', ImageResize::IMG_RESIZE_RESET_DIMENSIONS);
    }

}
