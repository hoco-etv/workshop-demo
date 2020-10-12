<?php

use yii\helpers\Url;

/**
 * @var array $items
 */

?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <?php foreach ($items as $i):
        if (is_array($i['url']) || substr($i['url'], 0, 4) == 'http'): ?>
            <url>
                <loc>
                    <?php
                    if (is_array($i['url'])) {
                        echo Url::to($i['url'], true);
                    } else if (substr($i['url'], 0, 4) == 'http') {
                        echo $i['url'];
                    }
                    ?>
                </loc>
                <changefreq><?= (isset($i['changefreq'])) ? $i['changefreq'] : 'weekly' ?></changefreq>
            </url>
        <?php endif;
    endforeach; ?>
</urlset>
