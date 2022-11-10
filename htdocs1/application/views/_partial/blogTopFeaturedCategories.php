<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
if (!empty($featuredBlogCategories)) {
    ?>
    <ul>
        <?php
        foreach ($featuredBlogCategories as $categoryId => $categoryName) {
            $childBlogCount = count(BlogPost::getBlogPostsUnderCategory($siteLangId, $categoryId));
            ?>
            <li><a href="<?php echo CommonHelper::generateUrl('Blog', 'category', array($categoryId)); ?>"><?php
                    echo $categoryName;
                    echo ($childBlogCount > 0) ? " (" . $childBlogCount . ")" : '';
                    ?></a></li>
        <?php } ?>
    </ul>
    <?php
}