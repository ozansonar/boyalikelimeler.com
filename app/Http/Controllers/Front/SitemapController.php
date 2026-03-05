<?php

declare(strict_types=1);

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\LiteraryCategory;
use App\Models\LiteraryWork;
use App\Models\Page;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $content = Cache::remember('sitemap_xml', 3600, function (): string {
            return $this->generateSitemap();
        });

        return response($content, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
        ]);
    }

    private function generateSitemap(): string
    {
        $urls = [];

        // Static pages
        $urls[] = $this->url(url('/'), now()->format('Y-m-d'), 'daily', '1.0');
        $urls[] = $this->url(route('literary-works.index'), now()->format('Y-m-d'), 'daily', '0.9');
        $urls[] = $this->url(route('blog.index'), now()->format('Y-m-d'), 'daily', '0.8');
        $urls[] = $this->url(route('authors.index'), now()->format('Y-m-d'), 'weekly', '0.7');
        $urls[] = $this->url(route('contact.show'), now()->format('Y-m-d'), 'monthly', '0.5');

        // Literary works
        LiteraryWork::query()
            ->where('status', 'approved')
            ->whereNotNull('published_at')
            ->whereHas('author', fn ($q) => $q->whereNotNull('username'))
            ->select('slug', 'updated_at')
            ->orderByDesc('published_at')
            ->chunk(500, function ($works) use (&$urls): void {
                foreach ($works as $work) {
                    $urls[] = $this->url(
                        route('literary-works.show', $work->slug),
                        $work->updated_at->format('Y-m-d'),
                        'weekly',
                        '0.8'
                    );
                }
            });

        // Blog posts
        Post::query()
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->select('slug', 'updated_at')
            ->orderByDesc('published_at')
            ->chunk(500, function ($posts) use (&$urls): void {
                foreach ($posts as $post) {
                    $urls[] = $this->url(
                        route('blog.show', $post->slug),
                        $post->updated_at->format('Y-m-d'),
                        'weekly',
                        '0.7'
                    );
                }
            });

        // Literary categories
        LiteraryCategory::query()
            ->where('is_active', true)
            ->select('slug', 'updated_at')
            ->each(function ($category) use (&$urls): void {
                $urls[] = $this->url(
                    route('literary-works.index', ['kategori' => $category->slug]),
                    $category->updated_at->format('Y-m-d'),
                    'weekly',
                    '0.6'
                );
            });

        // Static pages
        Page::query()
            ->where('is_active', true)
            ->select('slug', 'updated_at')
            ->each(function ($page) use (&$urls): void {
                $urls[] = $this->url(
                    route('page.show', $page->slug),
                    $page->updated_at->format('Y-m-d'),
                    'monthly',
                    '0.5'
                );
            });

        // Author profiles
        User::query()
            ->whereNotNull('username')
            ->whereHas('role', fn ($q) => $q->where('slug', 'yazar'))
            ->select('username', 'updated_at')
            ->each(function ($user) use (&$urls): void {
                $urls[] = $this->url(
                    route('profile.show', $user->username),
                    $user->updated_at->format('Y-m-d'),
                    'weekly',
                    '0.6'
                );
            });

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $entry) {
            $xml .= '  <url>' . "\n";
            $xml .= '    <loc>' . htmlspecialchars($entry['loc'], ENT_XML1, 'UTF-8') . '</loc>' . "\n";
            $xml .= '    <lastmod>' . $entry['lastmod'] . '</lastmod>' . "\n";
            $xml .= '    <changefreq>' . $entry['changefreq'] . '</changefreq>' . "\n";
            $xml .= '    <priority>' . $entry['priority'] . '</priority>' . "\n";
            $xml .= '  </url>' . "\n";
        }

        $xml .= '</urlset>';

        return $xml;
    }

    /**
     * @return array{loc: string, lastmod: string, changefreq: string, priority: string}
     */
    private function url(string $loc, string $lastmod, string $changefreq, string $priority): array
    {
        return compact('loc', 'lastmod', 'changefreq', 'priority');
    }
}
