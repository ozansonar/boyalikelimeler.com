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
    private const API_BASE = 'https://www.googleapis.com/youtube/v3';
    private const MAX_PAGES = 5; // Maximum API pages to fetch (safety limit)
    private const SHORTS_MAX_SECONDS = 180; // YouTube allows Shorts up to 3 minutes

    public function __construct(
        private readonly SettingService $settingService,
    ) {}

    /**
     * Get latest non-Shorts videos from the YouTube channel.
     *
     * @return array<int, array{id: string, title: string, thumbnail: string, published_at: string, link: string}>
     */
    public function getChannelVideos(int $limit = 10): array
    {
        $channelId = $this->settingService->get('youtube_channel_id');
        $apiKey = $this->settingService->get('youtube_api_key');

        if (empty($channelId) || empty($apiKey)) {
            return [];
        }

        return Cache::remember(
            self::CACHE_KEY . '.' . $channelId,
            self::CACHE_TTL,
            fn (): array => $this->fetchVideosFromApi($channelId, $apiKey, $limit)
        );
    }

    /**
     * Fetch videos using YouTube Data API v3, filtering out Shorts.
     *
     * @return array<int, array{id: string, title: string, thumbnail: string, published_at: string, link: string}>
     */
    private function fetchVideosFromApi(string $channelId, string $apiKey, int $limit): array
    {
        try {
            // Convert channel ID to uploads playlist ID (UC... → UU...)
            $uploadsPlaylistId = 'UU' . substr($channelId, 2);

            $videos = [];
            $pageToken = null;

            for ($page = 0; $page < self::MAX_PAGES; $page++) {
                $playlistItems = $this->fetchPlaylistItems($uploadsPlaylistId, $apiKey, $pageToken);

                if (empty($playlistItems['items'])) {
                    break;
                }

                $videoIds = array_map(
                    fn (array $item): string => $item['contentDetails']['videoId'] ?? '',
                    $playlistItems['items']
                );
                $videoIds = array_filter($videoIds);

                if (!empty($videoIds)) {
                    $videoDetails = $this->fetchVideoDetails($videoIds, $apiKey);
                    $normalVideos = $this->filterNormalVideos($videoDetails);
                    $videos = array_merge($videos, $normalVideos);
                }

                if (count($videos) >= $limit) {
                    break;
                }

                $pageToken = $playlistItems['nextPageToken'] ?? null;

                if ($pageToken === null) {
                    break;
                }
            }

            return array_slice($videos, 0, $limit);
        } catch (\Throwable $e) {
            Log::error('YouTube API error', [
                'channel_id' => $channelId,
                'message' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Fetch items from a YouTube playlist.
     *
     * @return array{items: array, nextPageToken: ?string}
     */
    private function fetchPlaylistItems(string $playlistId, string $apiKey, ?string $pageToken = null): array
    {
        $params = [
            'part' => 'contentDetails,snippet',
            'playlistId' => $playlistId,
            'maxResults' => 50,
            'key' => $apiKey,
        ];

        if ($pageToken !== null) {
            $params['pageToken'] = $pageToken;
        }

        $response = Http::timeout(10)->get(self::API_BASE . '/playlistItems', $params);

        if (!$response->successful()) {
            Log::warning('YouTube playlistItems API failed', [
                'playlist_id' => $playlistId,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return ['items' => [], 'nextPageToken' => null];
        }

        $data = $response->json();

        return [
            'items' => $data['items'] ?? [],
            'nextPageToken' => $data['nextPageToken'] ?? null,
        ];
    }

    /**
     * Fetch video details (duration) for given video IDs.
     *
     * @param  array<string> $videoIds
     * @return array<int, array{id: string, title: string, thumbnail: string, published_at: string, link: string, duration_seconds: int}>
     */
    private function fetchVideoDetails(array $videoIds, string $apiKey): array
    {
        $response = Http::timeout(10)->get(self::API_BASE . '/videos', [
            'part' => 'contentDetails,snippet,liveStreamingDetails',
            'id' => implode(',', $videoIds),
            'key' => $apiKey,
        ]);

        if (!$response->successful()) {
            Log::warning('YouTube videos API failed', [
                'status' => $response->status(),
            ]);

            return [];
        }

        $items = $response->json('items') ?? [];
        $videos = [];

        foreach ($items as $item) {
            $videoId = $item['id'] ?? '';
            $duration = $item['contentDetails']['duration'] ?? 'PT0S';
            $liveBroadcast = $item['snippet']['liveBroadcastContent'] ?? 'none';
            $title = $item['snippet']['title'] ?? '';

            $videos[] = [
                'id' => $videoId,
                'title' => $title,
                'thumbnail' => "https://img.youtube.com/vi/{$videoId}/mqdefault.jpg",
                'published_at' => $item['snippet']['publishedAt'] ?? '',
                'link' => "https://www.youtube.com/watch?v={$videoId}",
                'duration_seconds' => $this->parseDuration($duration),
                'is_live' => $liveBroadcast !== 'none',
                'has_live_details' => isset($item['liveStreamingDetails']),
                'is_shorts_tag' => $this->hasShortsTag($title, $item['snippet']['description'] ?? ''),
            ];
        }

        return $videos;
    }

    /**
     * Filter out Shorts, live streams and upcoming broadcasts.
     *
     * @param  array<int, array> $videos
     * @return array<int, array{id: string, title: string, thumbnail: string, published_at: string, link: string}>
     */
    private function filterNormalVideos(array $videos): array
    {
        $normal = [];

        foreach ($videos as $video) {
            // Skip live streams and upcoming broadcasts
            if ($video['is_live'] || $video['has_live_details']) {
                continue;
            }

            // Skip Shorts: duration check OR #shorts tag in title/description
            if ($video['duration_seconds'] <= self::SHORTS_MAX_SECONDS || $video['is_shorts_tag']) {
                continue;
            }

            unset($video['duration_seconds'], $video['is_live'], $video['has_live_details'], $video['is_shorts_tag']);
            $normal[] = $video;
        }

        return $normal;
    }

    /**
     * Check if title or description contains #shorts hashtag.
     */
    private function hasShortsTag(string $title, string $description): bool
    {
        $text = mb_strtolower($title . ' ' . $description);

        return str_contains($text, '#shorts') || str_contains($text, '#short');
    }

    /**
     * Parse ISO 8601 duration (PT1H2M3S) to seconds.
     */
    private function parseDuration(string $duration): int
    {
        try {
            $interval = new \DateInterval($duration);

            return ($interval->h * 3600) + ($interval->i * 60) + $interval->s;
        } catch (\Throwable) {
            return 0;
        }
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
