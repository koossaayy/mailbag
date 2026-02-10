<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="theme-color" content="{{ $themeColor ?? '#ffffff' }}">
    <meta name="robots" content="{{ $robotsMeta ?? 'index, follow' }}">

    {{-- Open Graph Meta Tags --}}
    <meta property="og:title" content="{{ $ogTitle ?? config('app.name') }}">
    <meta property="og:description" content="{{ $ogDescription ?? '' }}">
    <meta property="og:image" content="{{ $ogImage ?? asset('images/default-share.jpg') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $twitterTitle ?? ($ogTitle ?? config('app.name')) }}">

    <title>{{ $title ?? config('app.name') }}</title>

    {{-- Preconnect for performance --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="dns-prefetch" href="//cdn.example.com">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    {{-- Custom fonts stack --}}
    @stack('fonts')

    {{-- Conditional styles --}}
    @if ($darkMode ?? false)
        <link rel="stylesheet" href="{{ asset('css/dark-theme.css') }}">
    @endif

    <style>
        :root {
            --primary-color: {{ $primaryColor ?? '#3490dc' }};
            --secondary-color: {{ $secondaryColor ?? '#ffed4e' }};
        }
    </style>
</head>

<body class="{{ $bodyClass ?? '' }}" data-user-id="{{ auth()->id() }}">

    {{-- ============================================
        ADVANCED CONDITIONALS & SWITCHES
    ============================================ --}}

    <section class="advanced-conditionals">
        {{-- Switch statement (Laravel 9+) --}}
        @switch($userRole)
            @case('admin')
                <div class="admin-dashboard">
                    <h1>{{ __('Admin Dashboard') }}</h1>
                    @include('admin.widgets')
                </div>
            @break

            @case('moderator')
            @case('editor')
                <div class="content-management">
                    <h1>{{ __('Content Management') }}</h1>
                    @include('editor.tools')
                </div>
            @break

            @case('subscriber')
                <div class="subscriber-area">
                    <h1>{{ __('Premium Content') }}</h1>
                </div>
            @break

            @default
                <div class="public-view">
                    <h1>{{ __('Welcome') }}</h1>
                </div>
        @endswitch

        {{-- Complex nested conditions --}}
        @if (auth()->check() && auth()->user()->isVerified())
            @if (auth()->user()->hasSubscription())
                @if (auth()->user()->subscription->isActive())
                    <div class="premium-content">
                        <h2>{{ __('Premium Features') }}</h2>
                        @if (auth()->user()->subscription->plan === 'enterprise')
                            @include('features.enterprise')
                        @else
                            @include('features.standard')
                        @endif
                    </div>
                @else
                    <div class="subscription-expired">
                        <p>{{ __('Your subscription has expired.') }} <a href="{{ route('renew') }}">{{ __('Renew now') }}</a></p>
                    </div>
                @endif
            @else
                <div class="upgrade-prompt">
                    <h3>{{ __('Unlock Premium Features') }}</h3>
                    <a href="{{ route('subscribe') }}" class="btn-premium">{{ __('Upgrade Now') }}</a>
                </div>
            @endif
        @elseif(auth()->check())
            <div class="verify-email-notice">
                <p>{{ __('Please verify your email to access all features.') }}</p>
            </div>
        @else
            <div class="auth-prompt">
                <a href="{{ route('login') }}">{{ __('Login') }}</a> {{ __('or') }}
                <a href="{{ route('register') }}">{{ __('Sign Up') }}</a>
            </div>
        @endif

        {{-- Production/Environment specific --}}
        @production
            <!-- Google Analytics -->
            <script async src="https://www.googletagmanager.com/gtag/js?id=GA_MEASUREMENT_ID"></script>
            <script>
                window.dataLayer = window.dataLayer || [];

                function gtag() {
                    dataLayer.push(arguments);
                }
                gtag('js', new Date());
                gtag('config', 'GA_MEASUREMENT_ID');
            </script>
        @endproduction

        @env('local')
            <div class="debug-toolbar">
                <strong>{{ __('Local Development Mode') }}</strong>
                <ul>
                    <li>{{ __('Route:') }} {{ Route::currentRouteName() }}</li>
                    <li>{{ __('Memory:') }} {{ round(memory_get_usage() / 1024 / 1024, 2) }} {{ __('MB') }}</li>
                    <li>{{ __('Queries:') }} {{ count(DB::getQueryLog()) }}</li>
                </ul>
            </div>
        @endenv
    </section>


    {{-- ============================================
        ADVANCED LOOPS & ITERATIONS
    ============================================ --}}

    <section class="advanced-loops">
        <h2>{{ __('Product Categories') }}</h2>

        {{-- Nested foreach with loop variables --}}
        @foreach ($categories as $category)
            <div class="category depth-{{ $loop->depth }}">
                <h3>
                    {{ $category->name }}
                    <span class="badge">{{ $loop->iteration }}/{{ $loop->count }}</span>
                </h3>

                @if ($loop->first)
                    <p class="featured">{{ __('⭐ Featured Category') }}</p>
                @endif

                {{-- Nested products --}}
                @if ($category->products->isNotEmpty())
                    <div class="products">
                        @foreach ($category->products as $product)
                            <div class="product">
                                <h4>{{ $product->name }}</h4>
                                <p class="price">${{ number_format($product->price, 2) }}</p>

                                {{-- Access parent loop --}}
                                <small>
                                    {{ __('Category:') }} {{ $loop->parent->iteration }}{{ __(', Product:') }} {{ $loop->iteration }}
                                    @if ($loop->parent->last && $loop->last)
                                        <strong>{{ __('(Last product in last category)') }}</strong>
                                    @endif
                                </small>

                                {{-- Odd/Even styling --}}
                                <div class="{{ $loop->even ? 'bg-gray' : 'bg-white' }}">
                                    {{ $product->description }}
                                </div>
                            </div>

                            {{-- Add separator between products except last --}}
                            @if (!$loop->last)
                                <hr class="product-divider">
                            @endif
                        @endforeach
                    </div>
                @else
                    <p class="text-muted">{{ __('No products in this category') }}</p>
                @endif

                {{-- Remaining items indicator --}}
                @if (!$loop->last)
                    <small class="text-info">{{ $loop->remaining }} {{ __('more categories') }}</small>
                @endif
            </div>
        @endforeach

        {{-- Advanced forelse with complex empty state --}}
        @forelse($recentOrders as $order)
            <div class="order-card">
                <div class="order-header">
                    <span class="order-id">#{{ $order->id }}</span>
                    <span class="order-date">{{ $order->created_at->diffForHumans() }}</span>
                </div>

                <div class="order-items">
                    @foreach ($order->items as $item)
                        <div class="item">
                            <img src="{{ $item->product->thumbnail }}" alt="{{ $item->product->name }}">
                            <span>{{ $item->product->name }}</span>
                            <span>{{ __('Qty:') }} {{ $item->quantity }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="order-total">
                    {{ __('Total: $') }}{{ number_format($order->total, 2) }}
                </div>
            </div>
        @empty
            <div class="empty-state">
                <svg class="icon-empty-cart" width="100" height="100">
                    <circle cx="50" cy="50" r="40" stroke="#ccc" fill="none" stroke-width="2" />
                </svg>
                <h3>{{ __('No Orders Yet') }}</h3>
                <p>{{ __('Start shopping to see your orders here!') }}</p>
                <a href="{{ route('shop') }}" class="btn btn-primary">{{ __('Browse Products') }}</a>
            </div>
        @endforelse

        {{-- Break and continue with conditions --}}
        <div class="search-results">
            @foreach ($searchResults as $result)
                {{-- Skip hidden items --}}
                @continue($result->hidden)

                {{-- Skip items user doesn't have access to --}}
                @continue(!auth()->user()->can('view', $result))

                <div class="result-item">
                    <h4>{{ $result->title }}</h4>
                    <p>{{ Str::limit($result->content, 150) }}</p>
                </div>

                {{-- Stop after showing 10 results --}}
                @break($loop->iteration >= 10)
            @endforeach
        </div>
    </section>


    {{-- ============================================
        ADVANCED COMPONENTS & SLOTS
    ============================================ --}}

    <section class="advanced-components">
        {{-- Component with multiple named slots and attributes --}}
        <x-layouts.card title="Dashboard Overview" :collapsible="true" :expanded="$isExpanded ?? false" class="shadow-lg"
            x-data="{ open: true }">
            {{-- Default slot --}}
            <p>Main content goes here</p>

            {{-- Named slot with attributes --}}
            <x-slot:header class="bg-gradient">
                <h2>Custom Header Content</h2>
                <button>Action</button>
            </x-slot>

            {{-- Footer slot --}}
            <x-slot:footer>
                <button wire:click="save">Save</button>
                <button wire:click="cancel">Cancel</button>
            </x-slot>

            {{-- Sidebar slot (optional) --}}
            @if ($showSidebar ?? false)
                <x-slot:sidebar>
                    <ul class="sidebar-menu">
                        <li>Menu Item 1</li>
                        <li>Menu Item 2</li>
                    </ul>
                </x-slot>
            @endif
        </x-layouts.card>

        {{-- Component with spread attributes --}}
        <x-button ::class="{ 'opacity-50': loading }" wire:loading.attr="disabled" x-on:click="handleClick"
            {{ $attributes->merge(['class' => 'btn-default']) }}>
            Click Me
        </x-button>

        {{-- Dynamic components based on condition --}}
        <x-dynamic-component :component="$user->isAdmin() ? 'admin-panel' : 'user-panel'" :user="$user" :permissions="$permissions" />

        {{-- Inline component --}}
        <x-alert type="warning" dismissible>
            <x-slot:icon>
                <svg><!-- Warning icon --></svg>
            </x-slot>

            <strong>Warning!</strong> This action cannot be undone.
        </x-alert>

        {{-- Components in loops with keys --}}
        @foreach ($notifications as $notification)
            <x-notification :notification="$notification" :key="'notification-' . $notification->id" wire:key="notification-{{ $notification->id }}" />
        @endforeach
    </section>


    {{-- ============================================
        ADVANCED LIVEWIRE PATTERNS
    ============================================ --}}

    <section class="advanced-livewire">
        <h2>Advanced Livewire Examples</h2>

        {{-- Full-featured Livewire form --}}
        <div class="livewire-form">
            <form wire:submit.prevent="save">
                {{-- Real-time validation --}}
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" wire:model.live.debounce.500ms="username"
                        wire:loading.class="opacity-50" wire:target="username">
                    @error('username')
                        <span class="error">{{ $message }}</span>
                    @enderror

                    <div wire:loading wire:target="username" class="text-sm text-gray-500">
                        Checking availability...
                    </div>
                </div>

                {{-- Blur validation --}}
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" wire:model.blur="email">
                    @error('email')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Deferred binding for performance --}}
                <div class="form-group">
                    <label for="bio">Bio</label>
                    <textarea id="bio" wire:model.defer="bio" rows="5"></textarea>
                </div>

                {{-- File upload with preview --}}
                <div class="form-group">
                    <label for="avatar">Avatar</label>
                    <input type="file" id="avatar" wire:model="avatar" accept="image/*">

                    <div wire:loading wire:target="avatar">
                        Uploading...
                    </div>

                    @if ($avatar)
                        <div class="preview">
                            <img src="{{ $avatar->temporaryUrl() }}" alt="Preview">
                        </div>
                    @endif

                    @error('avatar')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Submit button with loading state --}}
                <button type="submit" wire:loading.attr="disabled" wire:target="save"
                    wire:loading.class="opacity-50 cursor-not-allowed">
                    <span wire:loading.remove wire:target="save">Save Profile</span>
                    <span wire:loading wire:target="save">Saving...</span>
                </button>
            </form>
        </div>

        {{-- Livewire polling --}}
        <div wire:poll.5s="refreshStats">
            <h3>Live Statistics</h3>
            <p>Active Users: <strong wire:loading.class="animate-pulse">{{ $activeUsers }}</strong></p>
            <p>Orders Today: <strong>{{ $ordersToday }}</strong></p>
            <small class="text-muted">Updates every 5 seconds</small>
        </div>

        {{-- Conditional polling --}}
        <div wire:poll.visible.2s="loadMorePosts">
            <p>Polling only when visible in viewport</p>
        </div>

        {{-- Stop polling after condition --}}
        <div wire:poll.5s="checkJobStatus" @if ($jobCompleted) wire:poll.stop @endif>
            <p>Job Status: {{ $jobStatus }}</p>
        </div>

        {{-- Livewire with Alpine.js --}}
        <div x-data="{ open: false, search: '' }" wire:ignore.self>
            <button @click="open = !open">Toggle Dropdown</button>

            <div x-show="open" x-transition>
                <input type="text" x-model="search" @input.debounce.500ms="$wire.search = search"
                    placeholder="Search...">

                <div wire:loading wire:target="search">
                    Searching...
                </div>

                <ul>
                    @foreach ($results as $result)
                        <li wire:click="selectItem({{ $result->id }})">
                            {{ $result->name }}
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        {{-- Lazy loading Livewire component --}}
        <div>
            @livewire('heavy-component', ['lazy' => true])
        </div>

        {{-- Livewire modals pattern --}}
        <div>
            <button wire:click="$dispatch('open-modal', { name: 'delete-confirmation' })">
                Delete
            </button>

            <x-modal name="delete-confirmation" wire:model="showDeleteModal">
                <h3>Confirm Deletion</h3>
                <p>Are you sure you want to delete this item?</p>

                <button wire:click="delete">Confirm</button>
                <button wire:click="$dispatch('close-modal')">Cancel</button>
            </x-modal>
        </div>

        {{-- Dirty state tracking --}}
        <div>
            <input type="text" wire:model="title">

            <button wire:click="save" wire:dirty.class="border-yellow-500" wire:dirty.attr="data-unsaved=true">
                <span wire:dirty>Save Changes *</span>
                <span wire:dirty.remove>Saved</span>
            </button>
        </div>

        {{-- Offline state --}}
        <div wire:offline>
            <div class="offline-banner">
                You are currently offline. Changes will sync when reconnected.
            </div>
        </div>
    </section>


    {{-- ============================================
        BLADE COMPONENT EXAMPLES
    ============================================ --}}

    <section class="blade-component-patterns">
        {{-- Attribute bags --}}
        <x-input name="search" type="text" class="custom-class" placeholder="Search..." required autofocus
            data-action="search" ::class="{ 'border-red-500': hasError }" />

        {{-- Conditional attributes --}}
        <x-button :disabled="$formInvalid" :loading="$isProcessing" :variant="$isPrimary ? 'primary' : 'secondary'">
            Submit
        </x-button>

        {{-- Components with scoped slots --}}
        <x-table>
            <x-slot:header>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </x-slot>

            @foreach ($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <x-dropdown>
                            <x-slot:trigger>
                                <button>Actions</button>
                            </x-slot>

                            <x-dropdown.item href="{{ route('users.edit', $user) }}">
                                Edit
                            </x-dropdown.item>
                            <x-dropdown.item wire:click="delete({{ $user->id }})">
                                Delete
                            </x-dropdown.item>
                        </x-dropdown>
                    </td>
                </tr>
            @endforeach
        </x-table>
    </section>


    {{-- ============================================
        ADVANCED FORMS & VALIDATION
    ============================================ --}}

    <section class="advanced-forms">
        <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @honeypot

            {{-- Dynamic field arrays --}}
            <div class="variants" x-data="{ variants: [''] }">
                <template x-for="(variant, index) in variants" :key="index">
                    <div class="variant-input">
                        <input type="text" :name="'variants[' + index + '][name]'" placeholder="Variant name">
                        <input type="number" :name="'variants[' + index + '][price]'" placeholder="Price">
                        <button type="button" @click="variants.splice(index, 1)">Remove</button>
                    </div>
                </template>
                <button type="button" @click="variants.push('')">Add Variant</button>
            </div>

            {{-- Conditional fields --}}
            <div x-data="{ shippingType: 'standard' }">
                <select x-model="shippingType" name="shipping_type">
                    <option value="standard">Standard</option>
                    <option value="express">Express</option>
                    <option value="pickup">Pickup</option>
                </select>

                <div x-show="shippingType !== 'pickup'" x-transition>
                    <input type="text" name="shipping_address" placeholder="Shipping Address"
                        :required="shippingType !== 'pickup'">
                </div>

                <div x-show="shippingType === 'pickup'" x-transition>
                    <select name="pickup_location">
                        <option>Location 1</option>
                        <option>Location 2</option>
                    </select>
                </div>
            </div>

            {{-- File inputs with multiple files --}}
            <div class="form-group">
                <label for="images">Upload Images (Max 5)</label>
                <input type="file" id="images" name="images[]" multiple
                    accept="image/jpeg,image/png,image/webp" max="5">
                @error('images')
                    <span class="error">{{ $message }}</span>
                @enderror
                @error('images.*')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            {{-- Custom validation messages --}}
            <div class="form-group">
                <input type="email" name="email" value="{{ old('email') }}">
                @error('email')
                    <div class="error-message">
                        @if ($message === 'The email field is required.')
                            Please provide your email address
                        @elseif($message === 'The email must be a valid email address.')
                            That doesn't look like a valid email
                        @else
                            {{ $message }}
                        @endif
                    </div>
                @enderror
            </div>

            <button type="submit">Submit</button>
        </form>
    </section>


    {{-- ============================================
        JSON & JAVASCRIPT INTEGRATION
    ============================================ --}}

    <section class="js-integration">
        <div id="app" data-config='@json($appConfig)'>
            {{-- React/Vue mounting point --}}
        </div>

        <script>
            // Blade to JavaScript data passing
            window.Laravel = {
                csrfToken: '{{ csrf_token() }}',
                locale: '{{ app()->getLocale() }}',
                user: @json(auth()->user()),
                permissions: @json(auth()->user()?->permissions ?? []),
                config: {
                    apiUrl: '{{ config('services.api.url') }}',
                    pusherKey: '{{ config('broadcasting.connections.pusher.key') }}',
                    stripeKey: '{{ config('services.stripe.key') }}'
                },
                routes: {
                    login: '{{ route('login') }}',
                    dashboard: '{{ route('dashboard') }}',
                    api: {
                        posts: '{{ route('api.posts.index') }}',
                        upload: '{{ route('api.upload') }}'
                    }
                },
                trans: @json(__('messages')),
                features: {
                    notifications: {{ config('features.notifications') ? 'true' : 'false' }},
                    realtime: {{ config('features.realtime') ? 'true' : 'false' }}
                }
            };

            // Using Blade variables in JS
            const products = @json(
                $products->map(fn($p) => [
                        'id' => $p->id,
                        'name' => $p->name,
                        'price' => $p->price,
                        'inStock' => $p->stock > 0,
                    ]));

            // Handling null/undefined
            const userData = @json($user ?? null);
            const settings = @json($settings ?? ['theme' => 'light']);
        </script>
    </section>


    {{-- ============================================
        CUSTOM BLADE DIRECTIVES USAGE
    ============================================ --}}

    <section class="custom-directives">
        {{-- These would be registered in AppServiceProvider --}}

        {{-- Date formatting --}}
        @date($post->published_at)
        @datetime($event->starts_at)

        {{-- Currency --}}
        @money($product->price)
        @money($product->price, 'EUR')

        {{-- Icon helper --}}
        @icon('heroicon-o-user', 'w-6 h-6')

        {{-- Role-based directives --}}
        @admin
            <a href="/admin">Admin Panel</a>
        @endadmin

        @moderator
            <a href="/moderate">Moderate Content</a>
        @endmoderator

        {{-- Feature flags --}}
        @feature('new-editor')
            <x-new-editor />
        @else
            <x-legacy-editor />
        @endfeature

        {{-- Custom auth checks --}}
        @subscribed
            <div class="premium-content">Premium Features</div>
        @endsubscribed

        @verified
            <button>Post Comment</button>
        @else
            <p>Please verify your email to comment</p>
        @endverified
    </section>


    {{-- ============================================
        PERFORMANCE OPTIMIZATION PATTERNS
    ============================================ --}}

    <section class="performance-patterns">
        {{-- Lazy load images --}}
        <img src="{{ asset('images/placeholder.jpg') }}" data-src="{{ $post->image_url }}" loading="lazy"
            alt="{{ $post->title }}">

        {{-- Preload critical resources --}}
        @once
            <link rel="preload" href="{{ asset('fonts/main.woff2') }}" as="font" type="font/woff2" crossorigin>
        @endonce

        {{-- Fragment caching pattern --}}
        @cache('popular-posts', 3600)
            <div class="popular-posts">
                @foreach ($popularPosts as $post)
                    <article>{{ $post->title }}</article>
                @endforeach
            </div>
        @endcache

        {{-- Conditional script loading --}}
        @if ($needsCharts)
            @once
                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            @endonce
        @endif
    </section>


    {{-- ============================================
        ACCESSIBILITY PATTERNS
    ============================================ --}}

    <section class="accessibility" role="region" aria-labelledby="main-heading">
        <h2 id="main-heading">{{ $heading }}</h2>

        {{-- Skip to content --}}
        <a href="#main-content" class="skip-to-content">Skip to main content</a>

        {{-- ARIA live regions --}}
        <div role="status" aria-live="polite" aria-atomic="true" wire:loading>
            Loading...
        </div>

        {{-- Form accessibility --}}
        <form>
            <div class="form-group">
                <label for="email-input">Email Address</label>
                <input type="email" id="email-input" name="email" aria-describedby="email-help"
                    aria-required="true" @error('email') aria-invalid="true" @enderror>
                <small id="email-help">We'll never share your email</small>
                @error('email')
                    <span role="alert" class="error">{{ $message }}</span>
                @enderror
            </div>
        </form>

        {{-- Button accessibility --}}
        <button type="button" aria-label="Close dialog" aria-expanded="{{ $isOpen ? 'true' : 'false' }}"
            @if ($disabled) aria-disabled="true" @endif>
            <span aria-hidden="true">&times;</span>
        </button>
    </section>


    {{-- ============================================
        THIRD-PARTY INTEGRATIONS
    ============================================ --}}

    <section class="integrations">
        {{-- Alpine.js integration --}}
        <div x-data="{
            count: 0,
            message: '{{ $message }}',
            items: @js($items),
            config: @js($config)
        }" x-init="console.log('Component initialized')">
            <button @click="count++" x-text="count"></button>

            <template x-for="item in items" :key="item.id">
                <div x-text="item.name"></div>
            </template>
        </div>

        {{-- Turbo/Hotwire --}}
        <div data-turbo="false">
            {{-- This content won't be processed by Turbo --}}
        </div>

        <a href="{{ route('posts.show', $post) }}" data-turbo-frame="content">
            {{ $post->title }}
        </a>
    </section>


    {{-- ============================================
        YIELD & SECTION INHERITANCE
    ============================================ --}}

    @yield('before-content')

    <main id="main-content">
        @yield('content')

        @section('sidebar')
            <aside class="sidebar">
                <h3>{{ __('Default Sidebar') }}</h3>
                <p>{{ __('This can be overridden in child views') }}</p>
            </aside>
        @show
    </main>

    @yield('after-content')


    {{-- ============================================
        FOOTER & SCRIPTS
    ============================================ --}}

    <footer class="site-footer">
        <div class="container">
            <div class="footer-links">
                @foreach ($footerLinks as $section => $links)
                    <div class="link-section">
                        <h4>{{ $section }}</h4>
                        <ul>
                            @foreach ($links as $link)
                                <li>
                                    <a href="{{ $link['url'] }}">{{ $link['title'] }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>

            <div class="footer-bottom">
                <p>{{ __('&copy;') }} {{ date('Y') }} {{ config('app.name') }}{{ __('. All rights reserved.') }}</p>

                {{-- Social media links --}}
                <div class="social-links">
                    @isset($socialLinks)
                        @foreach ($socialLinks as $platform => $url)
                            <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
                                aria-label="{{ ucfirst($platform) }}">
                                @icon("social-{$platform}")
                            </a>
                        @endforeach
                    @endisset
                </div>
            </div>
        </div>
    </footer>

    {{-- Scripts stack (filled from child views) --}}
    @stack('modals')

    @livewireScripts

    @stack('scripts')

    {{-- Deferred scripts --}}
    @once
        <script defer src="{{ asset('js/utilities.js') }}"></script>
    @endonce

    {{-- Inline scripts --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Flash message auto-hide
            @if (session('success'))
                setTimeout(() => {
                    document.querySelector('.flash-message')?.remove();
                }, 5000);
            @endif

            // Initialize tooltips, dropdowns, etc.
            window.initializeComponents();
        });

        // Livewire hooks
        document.addEventListener('livewire:init', () => {
            Livewire.on('notification', (event) => {
                alert(event.message);
            });
        });
    </script>

    @yield('custom-scripts')

</body>

</html>
