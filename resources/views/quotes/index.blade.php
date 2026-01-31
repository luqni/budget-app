@extends('layouts.app')

@section('content')
<style>
    .quote-card {
        transition: transform 0.2s, box-shadow 0.2s;
        border-left: 4px solid;
    }
    .quote-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
    }
    .category-tab {
        cursor: pointer;
        transition: all 0.3s;
        border-bottom: 3px solid transparent;
    }
    .category-tab.active {
        border-bottom-color: currentColor;
        font-weight: 700;
    }
    .category-tab:hover {
        opacity: 0.8;
    }
</style>

<div class="container py-4" style="padding-bottom: 100px;">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">📚 Renungan Harian</h4>
            <p class="text-muted small mb-0">Kumpulan inspirasi keuangan Islami</p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-primary btn-sm rounded-pill">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Category Tabs -->
    <div class="bg-white rounded-3 shadow-sm p-3 mb-4">
        <div class="d-flex gap-3 overflow-auto pb-2" style="scrollbar-width: none;">
            <a href="{{ route('quotes.index', ['category' => 'all']) }}" 
               class="category-tab text-decoration-none {{ $category === 'all' ? 'active' : '' }}" 
               style="color: #6b7280; white-space: nowrap;">
                <i class="bi bi-grid-fill"></i> Semua ({{ $categoryCounts['all'] }})
            </a>
            <a href="{{ route('quotes.index', ['category' => 'quran']) }}" 
               class="category-tab text-decoration-none {{ $category === 'quran' ? 'active' : '' }}" 
               style="color: #10b981; white-space: nowrap;">
                <i class="bi bi-book-fill"></i> Al-Quran ({{ $categoryCounts['quran'] }})
            </a>
            <a href="{{ route('quotes.index', ['category' => 'hadits']) }}" 
               class="category-tab text-decoration-none {{ $category === 'hadits' ? 'active' : '' }}" 
               style="color: #3b82f6; white-space: nowrap;">
                <i class="bi bi-chat-quote-fill"></i> Hadits ({{ $categoryCounts['hadits'] }})
            </a>
            <a href="{{ route('quotes.index', ['category' => 'ulama']) }}" 
               class="category-tab text-decoration-none {{ $category === 'ulama' ? 'active' : '' }}" 
               style="color: #8b5cf6; white-space: nowrap;">
                <i class="bi bi-person-fill"></i> Ulama ({{ $categoryCounts['ulama'] }})
            </a>
            <a href="{{ route('quotes.index', ['category' => 'tips']) }}" 
               class="category-tab text-decoration-none {{ $category === 'tips' ? 'active' : '' }}" 
               style="color: #f59e0b; white-space: nowrap;">
                <i class="bi bi-lightbulb-fill"></i> Tips ({{ $categoryCounts['tips'] }})
            </a>
        </div>
    </div>

    <!-- Quotes List -->
    <div class="row g-3">
        @forelse($quotes as $quote)
            @php
                $categoryConfig = [
                    'quran' => ['icon' => 'book-fill', 'color' => '#10b981', 'label' => 'Al-Quran'],
                    'hadits' => ['icon' => 'chat-quote-fill', 'color' => '#3b82f6', 'label' => 'Hadits'],
                    'ulama' => ['icon' => 'person-fill', 'color' => '#8b5cf6', 'label' => 'Ulama'],
                    'tips' => ['icon' => 'lightbulb-fill', 'color' => '#f59e0b', 'label' => 'Tips']
                ];
                $config = $categoryConfig[$quote->category] ?? $categoryConfig['tips'];
            @endphp
            
            <div class="col-12">
                <div class="card quote-card border-0 shadow-sm" style="border-left-color: {{ $config['color'] }} !important;">
                    <div class="card-body p-3">
                        <!-- Category Badge -->
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge rounded-pill px-2 py-1" style="background-color: {{ $config['color'] }}; font-size: 0.7rem;">
                                <i class="bi bi-{{ $config['icon'] }}"></i> {{ $config['label'] }}
                            </span>
                            @if($quote->is_active_for_date === now()->format('Y-m-d'))
                                <span class="badge bg-success rounded-pill px-2 py-1" style="font-size: 0.7rem;">
                                    <i class="bi bi-star-fill"></i> Hari Ini
                                </span>
                            @endif
                        </div>
                        
                        <!-- Quote Content -->
                        <blockquote class="mb-2">
                            <p class="mb-0 text-dark" style="line-height: 1.7; font-size: 0.95rem;">
                                "{{ $quote->content }}"
                            </p>
                        </blockquote>
                        
                        <!-- Source -->
                        <footer class="blockquote-footer mb-0 mt-2">
                            <i class="bi bi-{{ $config['icon'] }}" style="color: {{ $config['color'] }};"></i>
                            <cite title="Source">{{ $quote->source }}</cite>
                        </footer>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="bi bi-inbox fs-1 text-muted opacity-50"></i>
                    <p class="text-muted mt-2">Belum ada kutipan di kategori ini.</p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($quotes->hasPages())
        <div class="mt-4">
            {{ $quotes->appends(['category' => $category])->links() }}
        </div>
    @endif
</div>
@endsection
