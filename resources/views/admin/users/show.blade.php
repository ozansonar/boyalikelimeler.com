@extends('layouts.admin')

@section('title', $user->name . ' — Kullanıcı Profili')

@section('content')

    <!-- ==================== COVER & PROFILE HEADER ==================== -->
    <div class="prf-cover">
        @if($user->cover_image)
            <img src="{{ upload_url($user->cover_image, 'lg') }}" alt="{{ $user->name }} kapak fotoğrafı" class="prf-cover-img" loading="lazy">
        @else
            <div class="prf-cover-bg"></div>
        @endif
        <div class="prf-cover-overlay"></div>
    </div>

    <div class="prf-header">
        <div class="prf-avatar-wrap">
            <div class="prf-avatar">
                @if($user->avatar)
                    <img src="{{ upload_url($user->avatar, 'thumb') }}" alt="{{ $user->name }}" class="prf-avatar-img">
                @else
                    <span>{{ mb_strtoupper(mb_substr($user->name, 0, 2)) }}</span>
                @endif
                @if($user->hasVerifiedEmail())
                    <div class="prf-avatar-status"></div>
                @endif
            </div>
        </div>

        <div class="prf-header-info">
            <div class="prf-name-row">
                <div>
                    <h2>{{ $user->name }}</h2>
                    <p class="prf-role">
                        @php
                            $roleIconMap = [
                                'super_admin' => 'bi-shield-fill-check',
                                'admin'       => 'bi-shield-fill',
                                'yazar'       => 'bi-pencil-fill',
                                'kullanici'   => 'bi-person-fill',
                            ];
                            $slug = $user->role?->slug ?? 'kullanici';
                        @endphp
                        <i class="bi {{ $roleIconMap[$slug] ?? 'bi-person-fill' }}"></i> {{ $user->role?->name ?? 'Kullanıcı' }}
                        @if($user->hasVerifiedEmail())
                            <span class="prf-verified"><i class="bi bi-patch-check-fill"></i></span>
                        @endif
                        @if($user->hasActiveGoldenPen())
                            <span class="usr-golden-pen-badge" title="Altın Kalem"><i class="bi bi-pen-fill"></i></span>
                        @endif
                    </p>
                </div>
                <div class="prf-header-actions">
                    <a href="{{ route('admin.users.edit', $user) }}" class="prf-btn prf-btn-primary"><i class="bi bi-pencil-square"></i> Düzenle</a>
                    <a href="{{ route('admin.users.index') }}" class="prf-btn prf-btn-ghost"><i class="bi bi-arrow-left"></i> Listeye Dön</a>
                </div>
            </div>

            @if($user->bio)
                <p class="prf-bio">{{ $user->bio }}</p>
            @endif

            <div class="prf-meta">
                @if($user->email)
                    <span><i class="bi bi-envelope"></i> {{ $user->email }}</span>
                @endif
                @if($user->location)
                    <span><i class="bi bi-geo-alt"></i> {{ $user->location }}</span>
                @endif
                @if($user->website)
                    <span><i class="bi bi-link-45deg"></i> <a href="{{ $user->website }}" target="_blank" rel="noopener">{{ parse_url($user->website, PHP_URL_HOST) }}</a></span>
                @endif
                <span><i class="bi bi-calendar3"></i> {{ $user->created_at->translatedFormat('F Y') }}'den beri üye</span>
            </div>

            @if($user->instagram || $user->twitter || $user->youtube || $user->tiktok || $user->spotify)
                <div class="prf-socials">
                    @if($user->instagram)
                        <a href="https://instagram.com/{{ $user->instagram }}" target="_blank" rel="noopener" class="prf-social-btn sc-instagram"><i class="bi bi-instagram"></i></a>
                    @endif
                    @if($user->twitter)
                        <a href="https://x.com/{{ $user->twitter }}" target="_blank" rel="noopener" class="prf-social-btn sc-twitter"><i class="bi bi-twitter-x"></i></a>
                    @endif
                    @if($user->youtube)
                        <a href="https://youtube.com/{{ $user->youtube }}" target="_blank" rel="noopener" class="prf-social-btn sc-youtube"><i class="bi bi-youtube"></i></a>
                    @endif
                    @if($user->tiktok)
                        <a href="https://tiktok.com/@{{ $user->tiktok }}" target="_blank" rel="noopener" class="prf-social-btn sc-tiktok"><i class="bi bi-tiktok"></i></a>
                    @endif
                    @if($user->spotify)
                        <a href="https://open.spotify.com/user/{{ $user->spotify }}" target="_blank" rel="noopener" class="prf-social-btn sc-spotify"><i class="bi bi-spotify"></i></a>
                    @endif
                </div>
            @endif
        </div>

        <!-- Stats -->
        <div class="prf-stats-bar">
            <div class="prf-stat">
                <span class="prf-stat-val">{{ number_format($postStats->total ?? 0) }}</span>
                <span class="prf-stat-label">Yazı</span>
            </div>
            <div class="prf-stat">
                <span class="prf-stat-val">{{ number_format($literaryWorkStats->total ?? 0) }}</span>
                <span class="prf-stat-label">Eser</span>
            </div>
            <div class="prf-stat">
                <span class="prf-stat-val">{{ number_format($commentCount) }}</span>
                <span class="prf-stat-label">Yorum</span>
            </div>
            <div class="prf-stat">
                <span class="prf-stat-val">{{ number_format(($postStats->total_views ?? 0) + ($literaryWorkStats->total_views ?? 0)) }}</span>
                <span class="prf-stat-label">Okunma</span>
            </div>
        </div>
    </div>

    <!-- ==================== PROFILE TABS ==================== -->
    <div class="prf-tabs">
        <button class="prf-tab active" onclick="switchProfileTab(this,'prf-overview')">Genel Bakış</button>
        <button class="prf-tab" onclick="switchProfileTab(this,'prf-posts')">Yazılar</button>
        <button class="prf-tab" onclick="switchProfileTab(this,'prf-comments')">Yorumlar</button>
        <button class="prf-tab" onclick="switchProfileTab(this,'prf-activity')">Aktivite</button>
    </div>

    <!-- ==================== TAB: GENEL BAKIŞ ==================== -->
    <div class="prf-tab-content active" id="prf-overview">
        <div class="row g-4">

            <!-- Sol Kolon -->
            <div class="col-lg-4">

                <!-- Hakkında -->
                <div class="prf-card">
                    <div class="prf-card-title"><i class="bi bi-person-lines-fill"></i> Hakkında</div>
                    <div class="prf-about-list">
                        <div class="prf-about-item">
                            <i class="bi bi-person"></i>
                            <div><small>Kullanıcı Adı</small><span>{{ $user->username ?? '-' }}</span></div>
                        </div>
                        <div class="prf-about-item">
                            <i class="bi bi-envelope"></i>
                            <div><small>E-posta</small><span>{{ $user->email }}</span></div>
                        </div>
                        <div class="prf-about-item">
                            <i class="bi bi-shield"></i>
                            <div><small>Rol</small><span>{{ $user->role?->name ?? '-' }}</span></div>
                        </div>
                        <div class="prf-about-item">
                            <i class="bi bi-patch-check"></i>
                            <div>
                                <small>E-posta Durumu</small>
                                @if($user->hasVerifiedEmail())
                                    <span class="text-neon-green">Doğrulanmış</span>
                                @else
                                    <span class="text-neon-orange">Doğrulanmamış</span>
                                @endif
                            </div>
                        </div>
                        @if($user->birthdate)
                            <div class="prf-about-item">
                                <i class="bi bi-cake2"></i>
                                <div><small>Doğum Tarihi</small><span>{{ $user->birthdate->format('d.m.Y') }}</span></div>
                            </div>
                        @endif
                        @if($user->gender)
                            <div class="prf-about-item">
                                <i class="bi bi-gender-ambiguous"></i>
                                <div><small>Cinsiyet</small><span>{{ $user->gender->label() }}</span></div>
                            </div>
                        @endif
                        <div class="prf-about-item">
                            <i class="bi bi-calendar3"></i>
                            <div><small>Kayıt Tarihi</small><span>{{ $user->created_at->format('d.m.Y H:i') }}</span></div>
                        </div>
                    </div>
                </div>

                <!-- İlgi Alanları -->
                @if(!empty($user->interests))
                    <div class="prf-card">
                        <div class="prf-card-title"><i class="bi bi-lightning-charge-fill"></i> İlgi Alanları</div>
                        <div class="prf-skills">
                            @php
                                $skillColors = ['sk-orange', 'sk-blue', 'sk-teal', 'sk-purple', 'sk-green', 'sk-pink', 'sk-red'];
                            @endphp
                            @foreach($user->interests as $index => $interest)
                                <span class="prf-skill-tag {{ $skillColors[$index % count($skillColors)] }}">{{ $interest }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Altın Kalem Dönemleri -->
                @if($user->goldenPenPeriods->isNotEmpty())
                    <div class="prf-card">
                        <div class="prf-card-title"><i class="bi bi-pen-fill text-neon-orange"></i> Altın Kalem Dönemleri</div>
                        <div class="prf-about-list">
                            @foreach($user->goldenPenPeriods as $period)
                                <div class="prf-about-item">
                                    <i class="bi bi-award{{ $period->starts_at <= now() && $period->ends_at >= now() ? '-fill text-neon-orange' : '' }}"></i>
                                    <div>
                                        <small>{{ $period->starts_at->format('d.m.Y') }} — {{ $period->ends_at->format('d.m.Y') }}</small>
                                        @if($period->starts_at <= now() && $period->ends_at >= now())
                                            <span class="text-neon-green">Aktif</span>
                                        @elseif($period->ends_at < now())
                                            <span class="text-clr-muted">Sona Erdi</span>
                                        @else
                                            <span class="text-neon-blue">Gelecek</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sağ Kolon -->
            <div class="col-lg-8">

                <!-- Hızlı İstatistikler -->
                <div class="row g-3 mb-4">
                    <div class="col-sm-6 col-md-3">
                        <div class="prf-quick-stat">
                            <div class="prf-qs-icon qs-clr-blue"><i class="bi bi-file-earmark-text"></i></div>
                            <div class="prf-qs-info">
                                <span class="prf-qs-val">{{ number_format($postStats->published ?? 0) }}</span>
                                <small>Yayında Yazı</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="prf-quick-stat">
                            <div class="prf-qs-icon qs-clr-green"><i class="bi bi-journal-richtext"></i></div>
                            <div class="prf-qs-info">
                                <span class="prf-qs-val">{{ number_format($literaryWorkStats->approved ?? 0) }}</span>
                                <small>Onaylı Eser</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="prf-quick-stat">
                            <div class="prf-qs-icon qs-clr-purple"><i class="bi bi-chat-left-dots"></i></div>
                            <div class="prf-qs-info">
                                <span class="prf-qs-val">{{ number_format($commentCount) }}</span>
                                <small>Yorum</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="prf-quick-stat">
                            <div class="prf-qs-icon qs-clr-orange"><i class="bi bi-eye"></i></div>
                            <div class="prf-qs-info">
                                <span class="prf-qs-val">{{ number_format(($postStats->total_views ?? 0) + ($literaryWorkStats->total_views ?? 0)) }}</span>
                                <small>Toplam Okunma</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Son Yazılar -->
                <div class="prf-card">
                    <div class="prf-card-title"><i class="bi bi-file-earmark-text"></i> Son Yazılar</div>
                    @if($posts->isEmpty())
                        <p class="text-clr-muted">Henüz yazı bulunmuyor.</p>
                    @else
                        <div class="prf-timeline">
                            @foreach($posts->take(5) as $post)
                                <div class="prf-tl-item">
                                    <div class="prf-tl-dot tl-clr-blue"><i class="bi bi-file-earmark-text"></i></div>
                                    <div class="prf-tl-content">
                                        <div class="prf-tl-header">
                                            <span><strong>{{ $post->title }}</strong></span>
                                            <small>{{ $post->created_at->diffForHumans() }}</small>
                                        </div>
                                        <p>
                                            <span class="usr-status-badge {{ $post->status->badgeClass() }}">{{ $post->status->label() }}</span>
                                            @if($post->category)
                                                <span class="text-clr-muted ms-2">{{ $post->category->name }}</span>
                                            @endif
                                            <span class="text-clr-muted ms-2"><i class="bi bi-eye"></i> {{ number_format($post->view_count) }}</span>
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Son Edebi Eserler -->
                <div class="prf-card">
                    <div class="prf-card-title"><i class="bi bi-journal-richtext"></i> Son Edebi Eserler</div>
                    @if($literaryWorks->isEmpty())
                        <p class="text-clr-muted">Henüz edebi eser bulunmuyor.</p>
                    @else
                        <div class="prf-timeline">
                            @foreach($literaryWorks->take(5) as $work)
                                <div class="prf-tl-item">
                                    <div class="prf-tl-dot tl-clr-green"><i class="bi bi-journal-richtext"></i></div>
                                    <div class="prf-tl-content">
                                        <div class="prf-tl-header">
                                            <span><strong>{{ $work->title }}</strong></span>
                                            <small>{{ $work->created_at->diffForHumans() }}</small>
                                        </div>
                                        <p>
                                            <span class="usr-status-badge {{ $work->status->badgeClass() }}">{{ $work->status->label() }}</span>
                                            @if($work->category)
                                                <span class="text-clr-muted ms-2">{{ $work->category->name }}</span>
                                            @endif
                                            <span class="text-clr-muted ms-2"><i class="bi bi-eye"></i> {{ number_format($work->view_count) }}</span>
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== TAB: YAZILAR ==================== -->
    <div class="prf-tab-content" id="prf-posts">
        <div class="prf-project-filters">
            <button class="prf-filter-btn active" onclick="filterPosts(this,'all')">Tümü</button>
            <button class="prf-filter-btn" onclick="filterPosts(this,'blog')">Blog Yazıları</button>
            <button class="prf-filter-btn" onclick="filterPosts(this,'literary')">Edebi Eserler</button>
        </div>

        <div class="row g-3" id="prf-posts-grid">
            @forelse($posts as $post)
                <div class="col-md-6 col-xl-4" data-type="blog">
                    <div class="prf-project-card">
                        <div class="prf-prj-header">
                            <div class="prf-prj-icon avatar-gradient-blue"><i class="bi bi-file-earmark-text"></i></div>
                            <span class="prf-prj-status {{ $post->status === \App\Enums\PostStatus::Published ? 'prf-prj-done' : ($post->status === \App\Enums\PostStatus::Draft ? 'prf-prj-arch' : 'prf-prj-active') }}">
                                {{ $post->status->label() }}
                            </span>
                        </div>
                        <h6>{{ $post->title }}</h6>
                        <p>{{ Str::limit($post->excerpt ?? strip_tags($post->body ?? ''), 80) }}</p>
                        <div class="prf-prj-footer">
                            <small class="text-clr-muted"><i class="bi bi-eye"></i> {{ number_format($post->view_count) }} okunma</small>
                            <small class="text-clr-muted"><i class="bi bi-calendar3"></i> {{ $post->created_at->format('d.m.Y') }}</small>
                        </div>
                    </div>
                </div>
            @empty
            @endforelse

            @forelse($literaryWorks as $work)
                <div class="col-md-6 col-xl-4" data-type="literary">
                    <div class="prf-project-card">
                        <div class="prf-prj-header">
                            <div class="prf-prj-icon avatar-gradient-green"><i class="bi bi-journal-richtext"></i></div>
                            <span class="prf-prj-status {{ $work->status === \App\Enums\LiteraryWorkStatus::Approved ? 'prf-prj-done' : ($work->status === \App\Enums\LiteraryWorkStatus::Pending ? 'prf-prj-active' : 'prf-prj-arch') }}">
                                {{ $work->status->label() }}
                            </span>
                        </div>
                        <h6>{{ $work->title }}</h6>
                        <p>{{ Str::limit($work->excerpt ?? strip_tags($work->body ?? ''), 80) }}</p>
                        <div class="prf-prj-footer">
                            <small class="text-clr-muted"><i class="bi bi-eye"></i> {{ number_format($work->view_count) }} okunma</small>
                            <small class="text-clr-muted"><i class="bi bi-calendar3"></i> {{ $work->created_at->format('d.m.Y') }}</small>
                        </div>
                    </div>
                </div>
            @empty
            @endforelse

            @if($posts->isEmpty() && $literaryWorks->isEmpty())
                <div class="col-12">
                    <div class="prf-card text-center py-4">
                        <i class="bi bi-inbox text-clr-muted" class="fs-1"></i>
                        <p class="text-clr-muted mt-2">Henüz içerik bulunmuyor.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- ==================== TAB: YORUMLAR ==================== -->
    <div class="prf-tab-content" id="prf-comments">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="prf-card">
                    <div class="prf-card-title"><i class="bi bi-chat-left-dots"></i> Yorumlar <small class="prf-card-subtitle text-clr-muted">({{ $commentCount }} yorum)</small></div>
                    @if($comments->isEmpty())
                        <p class="text-clr-muted">Henüz yorum bulunmuyor.</p>
                    @else
                        <div class="prf-activity-log">
                            @foreach($comments as $comment)
                                <div class="prf-log-item">
                                    <div class="prf-log-icon {{ $comment->is_approved ? 'lg-clr-green' : 'lg-clr-orange' }}">
                                        <i class="bi {{ $comment->is_approved ? 'bi-check-circle-fill' : 'bi-clock-fill' }}"></i>
                                    </div>
                                    <div class="prf-log-body">
                                        <span>
                                            <strong>{{ $comment->contentTypeLabel() }}:</strong> {{ Str::limit($comment->contentTitle(), 40) }}
                                            <br><small class="text-clr-muted">{{ Str::limit($comment->body, 100) }}</small>
                                        </span>
                                        <small>{{ $comment->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-lg-4">
                <div class="prf-card">
                    <div class="prf-card-title"><i class="bi bi-bar-chart"></i> Yorum İstatistikleri</div>
                    <div class="prf-about-list">
                        @php
                            $approvedComments = $comments->where('is_approved', true)->count();
                            $pendingComments = $comments->where('is_approved', false)->count();
                        @endphp
                        <div class="prf-about-item">
                            <i class="bi bi-chat-left-dots text-neon-blue"></i>
                            <div><small>Toplam</small><span>{{ $commentCount }}</span></div>
                        </div>
                        <div class="prf-about-item">
                            <i class="bi bi-check-circle text-neon-green"></i>
                            <div><small>Onaylı</small><span>{{ $approvedComments }}</span></div>
                        </div>
                        <div class="prf-about-item">
                            <i class="bi bi-clock text-neon-orange"></i>
                            <div><small>Beklemede</small><span>{{ $pendingComments }}</span></div>
                        </div>
                    </div>
                </div>

                @if($comments->count() > 0)
                    <div class="prf-card">
                        <div class="prf-card-title"><i class="bi bi-star-fill text-neon-orange"></i> Ortalama Puan</div>
                        @php
                            $ratedComments = $comments->filter(fn($c) => $c->rating > 0);
                            $avgRating = $ratedComments->isNotEmpty() ? round($ratedComments->avg('rating'), 1) : null;
                        @endphp
                        @if($avgRating)
                            <div class="text-center py-2">
                                <span class="prf-stat-val">{{ $avgRating }}</span>
                                <div class="mt-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="bi bi-star{{ $i <= round($avgRating) ? '-fill text-neon-orange' : ' text-clr-muted' }}"></i>
                                    @endfor
                                </div>
                                <small class="text-clr-muted">{{ $ratedComments->count() }} değerlendirme</small>
                            </div>
                        @else
                            <p class="text-clr-muted text-center">Henüz puan verilmemiş.</p>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- ==================== TAB: AKTİVİTE ==================== -->
    <div class="prf-tab-content" id="prf-activity">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="prf-card">
                    <div class="prf-card-title"><i class="bi bi-journal-text"></i> Aktivite Günlüğü</div>
                    <div class="prf-activity-log">
                        @php
                            $activities = collect();

                            foreach ($posts as $post) {
                                $activities->push([
                                    'type'   => 'post',
                                    'icon'   => 'bi-file-earmark-text',
                                    'color'  => 'lg-clr-blue',
                                    'title'  => $post->title,
                                    'suffix' => 'başlıklı yazı oluşturuldu',
                                    'badge'  => $post->status->label(),
                                    'date'   => $post->created_at,
                                ]);
                            }

                            foreach ($literaryWorks as $work) {
                                $activities->push([
                                    'type'   => 'literary',
                                    'icon'   => 'bi-journal-richtext',
                                    'color'  => 'lg-clr-green',
                                    'title'  => $work->title,
                                    'suffix' => 'edebi eseri eklendi',
                                    'badge'  => $work->status->label(),
                                    'date'   => $work->created_at,
                                ]);
                            }

                            foreach ($comments as $comment) {
                                $activities->push([
                                    'type'   => 'comment',
                                    'icon'   => 'bi-chat-left-dots-fill',
                                    'color'  => 'lg-clr-purple',
                                    'title'  => $comment->contentTitle(),
                                    'suffix' => 'içeriğine yorum yapıldı',
                                    'badge'  => $comment->is_approved ? 'Onaylı' : 'Beklemede',
                                    'date'   => $comment->created_at,
                                ]);
                            }

                            $activities = $activities->sortByDesc('date');
                            $groupedActivities = $activities->groupBy(fn($a) => $a['date']->format('Y-m-d'));
                        @endphp

                        @forelse($groupedActivities as $date => $dayActivities)
                            <div class="prf-log-date">{{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}</div>
                            @foreach($dayActivities as $activity)
                                <div class="prf-log-item">
                                    <div class="prf-log-icon {{ $activity['color'] }}"><i class="bi {{ $activity['icon'] }}"></i></div>
                                    <div class="prf-log-body">
                                        <span><strong>{{ $activity['title'] }}</strong> {{ $activity['suffix'] }} <small class="text-clr-muted">({{ $activity['badge'] }})</small></span>
                                        <small>{{ $activity['date']->format('H:i') }}</small>
                                    </div>
                                </div>
                            @endforeach
                        @empty
                            <p class="text-clr-muted">Henüz aktivite bulunmuyor.</p>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <!-- Özet -->
                <div class="prf-card">
                    <div class="prf-card-title"><i class="bi bi-trophy-fill"></i> Özet</div>
                    <div class="prf-achievements">
                        <div class="prf-ach-item {{ $goldenPenCount > 0 ? 'prf-ach-unlocked' : '' }}">
                            <div class="prf-ach-icon ach-clr-orange"><i class="bi bi-pen-fill"></i></div>
                            <div><span>Altın Kalem</span><small>{{ $goldenPenCount }} dönem</small></div>
                        </div>
                        <div class="prf-ach-item {{ ($postStats->published ?? 0) >= 10 ? 'prf-ach-unlocked' : '' }}">
                            <div class="prf-ach-icon ach-clr-blue"><i class="bi bi-file-earmark-text-fill"></i></div>
                            <div><span>Üretken Yazar</span><small>{{ $postStats->published ?? 0 }} yayında yazı</small></div>
                        </div>
                        <div class="prf-ach-item {{ ($literaryWorkStats->approved ?? 0) >= 5 ? 'prf-ach-unlocked' : '' }}">
                            <div class="prf-ach-icon ach-clr-green"><i class="bi bi-journal-richtext"></i></div>
                            <div><span>Edebi Ruh</span><small>{{ $literaryWorkStats->approved ?? 0 }} onaylı eser</small></div>
                        </div>
                        <div class="prf-ach-item {{ $commentCount >= 20 ? 'prf-ach-unlocked' : '' }}">
                            <div class="prf-ach-icon ach-clr-purple"><i class="bi bi-chat-left-dots-fill"></i></div>
                            <div><span>Aktif Katılımcı</span><small>{{ $commentCount }} yorum</small></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script src="{{ asset('assets/admin/js/user-profile.js') }}?v={{ filemtime(public_path('assets/admin/js/user-profile.js')) }}"></script>
@endpush
