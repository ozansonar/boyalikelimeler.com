<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final class YouTubeService
{
    private const CACHE_KEY = 'youtube.channel_videos';
    private const CACHE_TTL = 21600; // 6 hours
    private const RSS_URL = 'https://www.youtube.com/feeds/videos.xml';

    public function __construct(
        private readonly SettingService $settingService,
    ) {}

    /**
     * Get latest videos from the YouTube channel via RSS feed.
     *
     * @return array<int, array{id: string, title: string, thumbnail: string, published_at: string, link: string}>
     */
    public function getChannelVideos(int $limit = 15): array
    {
        $channelId = $this->settingService->get('youtube_channel_id');

        if (empty($channelId)) {
            return [];
        }

        return Cache::remember(
            self::CACHE_KEY . '.' . $channelId,
            self::CACHE_TTL,
            fn (): array => $this->fetchVideosFromRss($channelId, $limit)
        );
    }

    /**
     * Fetch and parse YouTube RSS feed.
     *
     * @return array<int, array{id: string, title: string, thumbnail: string, published_at: string, link: string}>
     */
    private function fetchVideosFromRss(string $channelId, int $limit): array
    {
        try {
            $response = Http::timeout(10)->get(self::RSS_URL, [
                'channel_id' => $channelId,
            ]);

            if (!$response->successful()) {
                Log::warning('YouTube RSS feed fetch failed', [
                    'channel_id' => $channelId,
                    'status' => $response->status(),
                ]);

                return [];
            }

            return $this->parseRssFeed($response->body(), $limit);
        } catch (\Throwable $e) {
            Log::error('YouTube RSS feed error', [
                'channel_id' => $channelId,
                'message' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Parse RSS XML into a structured video array.
     *
     * @return array<int, array{id: string, title: string, thumbnail: string, published_at: string, link: string}>
     */
    private function parseRssFeed(string $xml, int $limit): array
    {
        $feed = @simplexml_load_string($xml);

        if ($feed === false) {
            return [];
        }

        $videos = [];
        $namespaces = $feed->getNamespaces(true);
        $entries = $feed->entry ?? [];

        foreach ($entries as $entry) {
            if (count($videos) >= $limit) {
                break;
            }

            $yt = $entry->children($namespaces['yt'] ?? 'http://www.youtube.com/xml/schemas/2015');
            $media = $entry->children($namespaces['media'] ?? 'http://search.yahoo.com/mrss/');

            $videoId = (string) ($yt->videoId ?? '');

            if (empty($videoId)) {
                continue;
            }

            $videos[] = [
                'id' => $videoId,
                'title' => (string) ($entry->title ?? ''),
                'thumbnail' => "https://img.youtube.com/vi/{$videoId}/mqdefault.jpg",
                'published_at' => (string) ($entry->published ?? ''),
                'link' => "https://www.youtube.com/watch?v={$videoId}",
            ];
        }

        return $videos;
    }

    /**
     * Clear the YouTube videos cache.
     */
    public function clearCache(): void
    {
        $channelId = $this->settingService->get('youtube_channel_id');

        if (!empty($channelId)) {
            Cache::forget(self::CACHE_KEY . '.' . $channelId);
        }
    }
}
