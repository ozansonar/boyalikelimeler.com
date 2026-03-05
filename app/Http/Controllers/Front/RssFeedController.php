<?php

declare(strict_types=1);

namespace App\Http\Controllers\Front;

use App\Enums\LiteraryWorkStatus;
use App\Enums\PostStatus;
use App\Http\Controllers\Controller;
use App\Models\LiteraryWork;
use App\Models\Post;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class RssFeedController extends Controller
{
    private const int FEED_LIMIT = 50;
    private const int CACHE_TTL = 3600;

    public function literaryWorks(): Response
    {
        $content = Cache::remember('rss_literary_works', self::CACHE_TTL, function (): string {
            return $this->generateLiteraryWorksFeed();
        });

        return response($content, 200, [
            'Content-Type' => 'application/rss+xml; charset=utf-8',
        ]);
    }

    public function blog(): Response
    {
        $content = Cache::remember('rss_blog', self::CACHE_TTL, function (): string {
            return $this->generateBlogFeed();
        });

        return response($content, 200, [
            'Content-Type' => 'application/rss+xml; charset=utf-8',
        ]);
    }

    private function generateLiteraryWorksFeed(): string
    {
        $works = LiteraryWork::query()
            ->where('status', LiteraryWorkStatus::Approved)
            ->whereNotNull('published_at')
            ->whereHas('author', fn ($q) => $q->whereNotNull('username'))
            ->with(['author:id,name,username', 'category:id,name'])
            ->select('id', 'title', 'slug', 'excerpt', 'body', 'user_id', 'literary_category_id', 'published_at')
            ->orderByDesc('published_at')
            ->limit(self::FEED_LIMIT)
            ->get();

        $lastBuildDate = $works->first()?->published_at?->toRssString() ?? now()->toRssString();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:dc="http://purl.org/dc/elements/1.1/">' . "\n";
        $xml .= '<channel>' . "\n";
        $xml .= '  <title>Boyalı Kelimeler — İçerikler</title>' . "\n";
        $xml .= '  <link>' . $this->escape(route('literary-works.index')) . '</link>' . "\n";
        $xml .= '  <description>Boyalı Kelimeler yazarlarının en güzel edebi eserleri. Şiir, hikaye, deneme, roman ve daha fazlası.</description>' . "\n";
        $xml .= '  <language>tr</language>' . "\n";
        $xml .= '  <lastBuildDate>' . $lastBuildDate . '</lastBuildDate>' . "\n";
        $xml .= '  <atom:link href="' . $this->escape(route('feed.literary-works')) . '" rel="self" type="application/rss+xml"/>' . "\n";
        $xml .= '  <image>' . "\n";
        $xml .= '    <url>' . $this->escape(asset('images/og-cover.jpg')) . '</url>' . "\n";
        $xml .= '    <title>Boyalı Kelimeler</title>' . "\n";
        $xml .= '    <link>' . $this->escape(url('/')) . '</link>' . "\n";
        $xml .= '  </image>' . "\n";

        foreach ($works as $work) {
            $xml .= $this->buildItem(
                title: $work->title,
                link: route('literary-works.show', $work->slug),
                description: $work->excerpt ?: Str::limit(strip_tags($work->body), 300),
                author: $work->author->name,
                category: $work->category->name,
                pubDate: $work->published_at->toRssString(),
                guid: route('literary-works.show', $work->slug),
            );
        }

        $xml .= '</channel>' . "\n";
        $xml .= '</rss>';

        return $xml;
    }

    private function generateBlogFeed(): string
    {
        $posts = Post::query()
            ->where('status', PostStatus::Published)
            ->whereNotNull('published_at')
            ->with('category:id,name')
            ->select('id', 'title', 'slug', 'excerpt', 'body', 'category_id', 'published_at')
            ->orderByDesc('published_at')
            ->limit(self::FEED_LIMIT)
            ->get();

        $lastBuildDate = $posts->first()?->published_at?->toRssString() ?? now()->toRssString();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:dc="http://purl.org/dc/elements/1.1/">' . "\n";
        $xml .= '<channel>' . "\n";
        $xml .= '  <title>Boyalı Kelimeler — Blog</title>' . "\n";
        $xml .= '  <link>' . $this->escape(route('blog.index')) . '</link>' . "\n";
        $xml .= '  <description>Boyalı Kelimeler blog yazıları. Sanat, edebiyat, kültür ve etkinlik haberleri.</description>' . "\n";
        $xml .= '  <language>tr</language>' . "\n";
        $xml .= '  <lastBuildDate>' . $lastBuildDate . '</lastBuildDate>' . "\n";
        $xml .= '  <atom:link href="' . $this->escape(route('feed.blog')) . '" rel="self" type="application/rss+xml"/>' . "\n";
        $xml .= '  <image>' . "\n";
        $xml .= '    <url>' . $this->escape(asset('images/og-cover.jpg')) . '</url>' . "\n";
        $xml .= '    <title>Boyalı Kelimeler</title>' . "\n";
        $xml .= '    <link>' . $this->escape(url('/')) . '</link>' . "\n";
        $xml .= '  </image>' . "\n";

        foreach ($posts as $post) {
            $xml .= $this->buildItem(
                title: $post->title,
                link: route('blog.show', $post->slug),
                description: $post->excerpt ?: Str::limit(strip_tags((string) $post->body), 300),
                category: $post->category?->name,
                pubDate: $post->published_at->toRssString(),
                guid: route('blog.show', $post->slug),
            );
        }

        $xml .= '</channel>' . "\n";
        $xml .= '</rss>';

        return $xml;
    }

    private function buildItem(
        string $title,
        string $link,
        string $description,
        ?string $author = null,
        ?string $category = null,
        string $pubDate = '',
        string $guid = '',
    ): string {
        $xml = '  <item>' . "\n";
        $xml .= '    <title>' . $this->escape($title) . '</title>' . "\n";
        $xml .= '    <link>' . $this->escape($link) . '</link>' . "\n";
        $xml .= '    <description><![CDATA[' . $description . ']]></description>' . "\n";

        if ($author !== null) {
            $xml .= '    <dc:creator>' . $this->escape($author) . '</dc:creator>' . "\n";
        }

        if ($category !== null) {
            $xml .= '    <category>' . $this->escape($category) . '</category>' . "\n";
        }

        $xml .= '    <pubDate>' . $pubDate . '</pubDate>' . "\n";
        $xml .= '    <guid isPermaLink="true">' . $this->escape($guid) . '</guid>' . "\n";
        $xml .= '  </item>' . "\n";

        return $xml;
    }

    private function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1, 'UTF-8');
    }
}
