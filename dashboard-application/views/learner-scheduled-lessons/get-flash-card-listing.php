<div class="-padding-20">
    <table class="table">
        <tr class="-hide-mobile">
            <th><?php echo Label::getLabel('LBL_Word'); ?></th>
            <th><?php echo Label::getLabel('LBL_Definition'); ?></th>
            <th></th>
        </tr>
        <?php foreach ($flashCardData as $flashCardData) { ?>
            <tr>
                <td width="38%">
                    <span class="td__caption -hide-desktop -show-mobile"><?php echo Label::getLabel('LBL_Word'); ?></span>
                    <span class="td__data"><?php
                        echo $flashCardData['flashcard_title'];
                        echo " (" . SpokenLanguage::getLangById($flashCardData['flashcard_lang_id']) . ")";
                        ?></span>
                </td>
                <td width="38%">
                    <span class="td__caption -hide-desktop -show-mobile"><?php echo Label::getLabel('LBL_Definition'); ?></span>
                    <span class="td__data"><?php
                        echo $flashCardData['flashcard_defination'];
                        echo " (" . SpokenLanguage::getLangById($flashCardData['flashcard_defination_lang_id']) . ")";
                        ?></span>
                </td>
                <td>
                    <span class="td__caption -hide-desktop -show-mobile"><?php echo Label::getLabel('LBL_Action'); ?></span>
                    <span class="td__data">
                        <a href="javascript:void(0)" onclick="addFlashcard('<?php echo $flashCardData['flashcard_id']; ?>', '<?php echo $flashCardData['sflashcard_learner_id'] ?>', '<?php echo $flashCardData['sflashcard_teacher_id'] ?>', '<?php echo $flashCardData['sflashcard_slesson_id'] ?>')" class="btn btn--small btn--secondary  btn--action">
                            <span class="svg-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" viewBox="0 0 485.219 485.22" style="enable-background:new 0 0 485.219 485.22;" xml:space="preserve">
                                    <g>
                                        <path d="M467.476,146.438l-21.445,21.455L317.35,39.23l21.445-21.457c23.689-23.692,62.104-23.692,85.795,0l42.886,42.897   C491.133,84.349,491.133,122.748,467.476,146.438z M167.233,403.748c-5.922,5.922-5.922,15.513,0,21.436   c5.925,5.955,15.521,5.955,21.443,0L424.59,189.335l-21.469-21.457L167.233,403.748z M60,296.54c-5.925,5.927-5.925,15.514,0,21.44   c5.922,5.923,15.518,5.923,21.443,0L317.35,82.113L295.914,60.67L60,296.54z M338.767,103.54L102.881,339.421   c-11.845,11.822-11.815,31.041,0,42.886c11.85,11.846,31.038,11.901,42.914-0.032l235.886-235.837L338.767,103.54z    M145.734,446.572c-7.253-7.262-10.749-16.465-12.05-25.948c-3.083,0.476-6.188,0.919-9.36,0.919   c-16.202,0-31.419-6.333-42.881-17.795c-11.462-11.491-17.77-26.687-17.77-42.887c0-2.954,0.443-5.833,0.859-8.703   c-9.803-1.335-18.864-5.629-25.972-12.737c-0.682-0.677-0.917-1.596-1.538-2.338L0,485.216l147.748-36.986   C147.097,447.637,146.36,447.193,145.734,446.572z" fill="#FFFFFF"/>
                                    </g>
                                </svg>
                            </span>
                        </a>
                        <a href="javascript:void(0);" onclick="removeFlashcard('<?php echo $flashCardData['flashcard_id']; ?>');" class="btn btn--small btn--action">
                            <span class="svg-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve" >
                                    <g>
                                        <g>
                                            <path d="M425.298,51.358h-91.455V16.696c0-9.22-7.475-16.696-16.696-16.696H194.854c-9.22,0-16.696,7.475-16.696,16.696v34.662    H86.703c-9.22,0-16.696,7.475-16.696,16.696v51.357c0,9.22,7.475,16.696,16.696,16.696h338.593c9.22,0,16.696-7.475,16.696-16.696    V68.054C441.993,58.832,434.518,51.358,425.298,51.358z M300.45,51.358h-88.9V33.391h88.9V51.358z" fill="#FFFFFF"/>
                                        </g>
                                    </g>
                                    <g>
                                        <g>
                                            <path d="M93.192,169.497l13.844,326.516c0.378,8.937,7.735,15.988,16.68,15.988h264.568c8.945,0,16.302-7.051,16.68-15.989    l13.843-326.515H93.192z M205.53,444.105c0,9.22-7.475,16.696-16.696,16.696c-9.22,0-16.696-7.475-16.696-16.696V237.391    c0-9.22,7.475-16.696,16.696-16.696c9.22,0,16.696,7.475,16.696,16.696V444.105z M272.693,444.105    c0,9.22-7.475,16.696-16.696,16.696s-16.696-7.475-16.696-16.696V237.391c0-9.22,7.475-16.696,16.696-16.696    s16.696,7.475,16.696,16.696V444.105z M339.856,444.105c0,9.22-7.475,16.696-16.696,16.696s-16.696-7.475-16.696-16.696V237.391    c0-9.22,7.475-16.696,16.696-16.696s16.696,7.475,16.696,16.696V444.105z" fill="#FFFFFF"/>
                                        </g>
                                    </g>
                                </svg>
                            </span>
                        </a>
                    </span>
                    <a href="javascript:void(0);" onclick="viewFlashCard('<?php echo $flashCardData['flashcard_id']; ?>')" class="btn btn--small btn--action"><?php echo Label::getLabel('LBL_View'); ?></a>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>
<?php
echo FatUtility::createHiddenFormFromData($postedData, array(
    'name' => 'frmFlashcCardSearchPaging'
));
$this->includeTemplate('_partial/pagination.php', $pagingArr, false);
?>
               