<?php

class SitemapController extends AdminBaseController
{

    public function generate()
    {
        $this->startSitemapXml();
        $urls = Sitemap::getUrls($this->adminLangId);
        foreach ($urls as $url) {
            foreach ($url as $val) {
                $this->writeSitemapUrl($val['url'], $val['frequency']);
            }
        }
        $this->endSitemapXml();
        $this->writeSitemapIndex();
        Message::addMessage(Label::getLabel('MSG_Sitemap_has_been_updated_successfully', $this->adminLangId));
        CommonHelper::redirectUserReferer();
    }

    private function startSitemapXml()
    {
        ob_start();
        echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    }

    private function writeSitemapUrl($url, $freq)
    {
        static $sitemap_i;
        $sitemap_i++;
        if ($sitemap_i > 2000) {
            $sitemap_i = 1;
            $this->endSitemapXml();
            $this->startSitemapXml();
        }
        echo "<url>
                <loc>" . $url . "</loc>
                <lastmod>" . date('Y-m-d') . "</lastmod>
                <changefreq>weekly</changefreq>
                <priority>0.8</priority>
            </url>";
        echo "\n";
    }

    private function endSitemapXml()
    {
        global $sitemapListInc;
        $sitemapListInc++;
        echo '</urlset>' . "\n";
        $contents = ob_get_clean();
        $rs = '';
        CommonHelper::writeFile('sitemap/list_' . $sitemapListInc . '.xml', $contents, $rs);
    }

    private function writeSitemapIndex()
    {
        global $sitemapListInc;
        ob_start();
        echo "<?xml version='1.0' encoding='UTF-8'?>
		<sitemapindex xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' xsi:schemaLocation='http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd' xmlns='http://www.sitemaps.org/schemas/sitemap/0.9'>\n";
        for ($i = 1; $i <= $sitemapListInc; $i++) {
            echo "<sitemap><loc>" . CommonHelper::generateFullUrl('', '', [], CONF_WEBROOT_FRONT_URL) . "/sitemap/list_" . $i . ".xml</loc></sitemap>\n";
        }
        echo "</sitemapindex>";
        $contents = ob_get_clean();
        $rs = '';
        CommonHelper::writeFile('sitemap.xml', $contents, $rs);
    }

}
