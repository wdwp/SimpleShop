<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
{foreach from=$items item=entry}
<url>
  <loc>{$entry->detail_url}</loc>
  <lastmod>{$smarty.now|date_format:'%Y-%m-%d'}</lastmod>
  <changefreq>weekly</changefreq>
  <priority>0.6</priority>
</url>{/foreach}
</urlset>