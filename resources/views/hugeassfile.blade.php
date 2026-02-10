<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('meta_description', 'Default description')">

    <title>{{ config('app.name') }} - @yield('title', 'Welcome')</title>

    {{-- Asset compilation --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Livewire Styles --}}
    @livewireStyles

    {{-- Stack for additional styles --}}
    @stack('styles')

    {{-- Inline style example --}}
    <style>
        .custom-class {
            color: {{ $customColor ?? '#000' }};
        }
    </style>
</head>

<body class="@yield('body-class', 'bg-gray-100')">

    {{-- ============================================
        BLADE COMMENTS (not rendered in HTML)
    ============================================ --}}

    {{-- Single line comment --}}

    {{--
        Multi-line comment
        These are stripped out during compilation
    --}}


    {{-- ============================================
        DISPLAYING DATA
    ============================================ --}}

    <header>
        {{-- Escaped output (safe from XSS) --}}
        <h1>{{ $pageTitle }}</h1>

        {{-- Unescaped output (BE CAREFUL!) --}}
        <div class="html-content">{!! $trustedHtmlContent !!}</div>

        {{-- Display with default value --}}
        <p>{{ $optionalText ?? 'Default text if variable is undefined' }}</p>

        {{-- Blade echo with JavaScript frameworks (Vue/Alpine) --}}
        <span>@{{ vuejsVariable }}</span>

        {{-- Verbatim blocks (useful for Vue/Alpine) --}}
        @verbatim
            <div class="vue-component">
                {{ message }}
                <span v-for="item in items">{{ item }}</span>
            </div>
        @endverbatim
    </header>


    {{-- ============================================
        BLADE DIRECTIVES - CONDITIONALS
    ============================================ --}}

    <section class="conditionals">
        {{-- Basic if/else --}}
        @if ($userRole === 'admin')
            <div class="admin-panel">{{ __('Admin Access Granted') }}</div>
        @elseif ($userRole === 'editor')
            <div class="editor-panel">{{ __('Editor Access') }}</div>
        @else
            <div class="user-panel">{{ __('Standard User') }}</div>
        @endif

        {{-- Unless (inverse of if) --}}
        @unless ($isBlocked)
            <p>{{ __('You can access this content') }}</p>
        @endunless

        {{-- Isset and empty --}}
        @isset($userName)
            <p>{{ __('User:') }} {{ $userName }}</p>
        @endisset

        @empty($posts)
            <p>{{ __('No posts available') }}</p>
        @endempty

        {{-- Authentication directives --}}
        @auth
            <a href="/dashboard">{{ __('Dashboard') }}</a>
        @endauth

        @guest
            <a href="/login">{{ __('Login') }}</a>
        @endguest

        {{-- Specific guard authentication --}}
        @auth('admin')
            <a href="/admin">{{ __('Admin Panel') }}</a>
        @endauth

        {{-- Environment checks --}}
        @production
            <script src="https://analytics.example.com/script.js"></script>
        @endproduction

        @env('local')
            <div class="debug-bar">{{ __('Debug Mode') }}</div>
        @endenv

        @env(['staging', 'production'])
            <link rel="stylesheet" href="compiled.css">
        @endenv

        {{-- HasSection --}}
        @hasSection('navigation')
            <nav>@yield('navigation')</nav>
        @else
            <nav>{{ __('Default Navigation') }}</nav>
        @endhasSection

        {{-- Section missing --}}
        @sectionMissing('sidebar')
            <aside>{{ __('Default Sidebar') }}</aside>
        @endif
    </section>


    {{-- ============================================
        AUTHORIZATION DIRECTIVES
    ============================================ --}}

    <section class="authorization">
        @can('update', $post)
            <button>{{ __('Edit Post') }}</button>
        @elsecan('delete', $post)
            <button>{{ __('Delete Post') }}</button>
        @else
            <p>{{ __('No permissions') }}</p>
        @endcan

        @cannot('delete', $post)
            <p>{{ __('You cannot delete this post') }}</p>
        @endcannot

        @canany(['update', 'delete'], $post)
            <div class="post-actions">{{ __('Post Actions Available') }}</div>
        @endcanany
    </section>


    {{-- ============================================
        LOOPS
    ============================================ --}}

    <section class="loops">
        <h2>{{ __('Posts') }}</h2>

        {{-- Foreach loop --}}
        @foreach ($posts as $post)
            <article class="post">
                <h3>{{ $post->title }}</h3>
                <p>{{ $post->excerpt }}</p>

                {{-- Loop variable --}}
                <small>
                    {{ __('Item') }} {{ $loop->iteration }} {{ __('of') }} {{ $loop->count }}
                    @if ($loop->first)
                        (First)
                    @endif
                    @if ($loop->last)
                        (Last)
                    @endif
                    Depth: {{ $loop->depth }}
                    @if ($loop->parent)
                        Parent iteration: {{ $loop->parent->iteration }}
                    @endif
                </small>
            </article>
        @endforeach

        {{-- Forelse (foreach with empty fallback) --}}
        @forelse ($comments as $comment)
            <div class="comment">{{ $comment->body }}</div>
        @empty
            <p>{{ __('No comments yet. Be the first to comment!') }}</p>
        @endforelse

        {{-- For loop --}}
        @for ($i = 0; $i < 10; $i++)
            <span>{{ $i }}</span>
        @endfor

        {{-- While loop --}}
        @php $counter = 0; @endphp
        @while ($counter < 5)
            <div>{{ __('Count:') }} {{ $counter }}</div>
            @php $counter++; @endphp
        @endwhile

        {{-- Loop control --}}
        @foreach ($users as $user)
            @if ($user->isBanned())
                @continue
            @endif

            <div>{{ $user->name }}</div>

            @if ($user->isAdmin())
                @break
            @endif
        @endforeach

        {{-- Continue/break with conditions --}}
        @foreach ($items as $item)
            @continue($item->hidden)
            @break($loop->iteration > 10)

            <div>{{ $item->name }}</div>
        @endforeach
    </section>


    {{-- ============================================
        BLADE COMPONENTS
    ============================================ --}}

    <section class="components">
        {{-- Anonymous component --}}
        <x-alert type="success" message="Operation completed!" />

        {{-- Component with slot --}}
        <x-card title="User Profile">
            <p>{{ __('This content goes into the default slot') }}</p>
        </x-card>

        {{-- Component with named slots --}}
        <x-modal>
            <x-slot:title>
                {{ __('Confirm Action') }}
            </x-slot>

            <x-slot:body>
                {{ __('Are you sure you want to proceed?') }}
            </x-slot>

            <x-slot:footer>
                <button>{{ __('Cancel') }}</button>
                <button>{{ __('Confirm') }}</button>
            </x-slot>
        </x-modal>

        {{-- Component with attributes --}}
        <x-button type="submit" color="primary" ::class="{ 'opacity-50': processing }" wire:click="save">
            {{ __('Save Changes') }}
        </x-button>

        {{-- Dynamic component --}}
        <x-dynamic-component :component="$componentName" :data="$componentData" />

        {{-- Component with merged attributes --}}
        <x-input name="email" type="email" class="custom-input" required />
    </section>


    {{-- ============================================
        LIVEWIRE COMPONENTS
    ============================================ --}}

    <section class="livewire-section">
        <h2>{{ __('Livewire Components') }}</h2>

        {{-- Basic Livewire component --}}
        @livewire('counter')

        {{-- Livewire with parameters --}}
        @livewire('show-post', ['postId' => $post->id])

        {{-- Livewire with key (for lists) --}}
        @foreach ($items as $item)
            @livewire('item-component', ['item' => $item], key($item->id))
        @endforeach

        {{-- Livewire tag syntax --}}
        <livewire:search-bar placeholder="Search..." />

        <livewire:user-profile :user="$currentUser" />

        {{-- Full-page Livewire component with layout --}}
        {{-- This would typically be in its own file --}}
        <div>
            <!-- Livewire content with wire directives -->
            <input type="text" wire:model.live="searchTerm">
            <button wire:click="search">{{ __('Search') }}</button>
            <div wire:loading>{{ __('Searching...') }}</div>
            <div wire:loading.remove>{{ __('Results ready') }}</div>

            <!-- Wire directives examples -->
            <form wire:submit.prevent="save">
                <input type="text" wire:model.defer="name">
                <input type="email" wire:model.blur="email">
                <input type="number" wire:model.debounce.500ms="quantity">
                <button type="submit" wire:loading.attr="disabled">{{ __('Save') }}</button>
            </form>

            <!-- Wire target -->
            <button wire:click="delete" wire:loading.class="opacity-50" wire:target="delete">
                {{ __('Delete') }}
            </button>

            <!-- Wire poll -->
            <div wire:poll.5s>
                {{ __('Updated every 5 seconds:') }} {{ now() }}
            </div>

            <!-- Wire ignore -->
            <div wire:ignore>
                <select id="select2-dropdown">
                    <!-- Third-party JS library that Livewire should ignore -->
                </select>
            </div>
        </div>
    </section>


    {{-- ============================================
        SUBVIEWS & INCLUDES
    ============================================ --}}

    <section class="includes">
        {{-- Include a subview --}}
        @include('partials.header')

        {{-- Include with data --}}
        @include('partials.sidebar', ['menuItems' => $menuItems])

        {{-- Include if exists --}}
        @includeIf('partials.analytics')

        {{-- Include when condition is true --}}
        @includeWhen($isAdmin, 'partials.admin-tools')

        {{-- Include unless condition is true --}}
        @includeUnless($isGuest, 'partials.user-menu')

        {{-- Include first existing view --}}
        @includeFirst(['partials.custom-header', 'partials.default-header'])

        {{-- Each (loop include) --}}
        @each('partials.product-card', $products, 'product', 'partials.no-products')
    </section>


    {{-- ============================================
        STACKS & ONCE
    ============================================ --}}

    @once
        {{-- This will only be rendered once even if included multiple times --}}
        <script src="/js/only-load-once.js"></script>
    @endonce

    @push('scripts')
        <script>
            console.log('Pushed to scripts stack');
        </script>
    @endpush

    @prepend('scripts')
        <script>
            console.log('Prepended to scripts stack');
        </script>
    @endprepend


    {{-- ============================================
        SERVICE INJECTION
    ============================================ --}}

    @inject('metrics', 'App\Services\MetricsService')

    <div class="metrics">
        <p>{{ __('Total Users:') }} {{ $metrics->getTotalUsers() }}</p>
        <p>{{ __('Active Sessions:') }} {{ $metrics->getActiveSessions() }}</p>
    </div>


    {{-- ============================================
        PHP BLOCKS
    ============================================ --}}

    @php
        $calculatedValue = $price * $quantity;
        $discount = $calculatedValue > 100 ? 10 : 0;
        $finalPrice = $calculatedValue - $discount;
    @endphp

    <div class="price-summary">
        <p>{{ __('Subtotal: $') }}{{ number_format($calculatedValue, 2) }}</p>
        <p>{{ __('Discount: $') }}{{ number_format($discount, 2) }}</p>
        <p>{{ __('Total: $') }}{{ number_format($finalPrice, 2) }}</p>
    </div>


    {{-- ============================================
        FORMS & CSRF
    ============================================ --}}

    <section class="forms">
        <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Method spoofing for PUT/PATCH/DELETE --}}
            @method('PUT')

            <div class="form-group">
                <label for="title">{{ __('Title') }}</label>
                <input type="text" id="title" name="title" value="{{ old('title', $post->title ?? '') }}"
                    class="@error('title') is-invalid @enderror">

                {{-- Display validation errors --}}
                @error('title')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="category">{{ __('Category') }}</label>
                <select name="category_id" id="category">
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id', $post->category_id ?? null) == $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="featured" value="1" @checked(old('featured', $post->featured ?? false))>
                    {{ __('Featured Post') }}
                </label>
            </div>

            <div class="form-group">
                <label>{{ __('Status') }}</label>
                <label>
                    <input type="radio" name="status" value="draft" @checked(old('status', $post->status ?? 'draft') === 'draft')>
                    {{ __('Draft') }}
                </label>
                <label>
                    <input type="radio" name="status" value="published" @checked(old('status', $post->status ?? 'draft') === 'published')>
                    {{ __('Published') }}
                </label>
            </div>

            <div class="form-group">
                <label for="body">{{ __('Content') }}</label>
                <textarea name="body" id="body" rows="10" @disabled($isReadOnly ?? false) @readonly($isArchived ?? false) @required>{{ old('body', $post->body ?? '') }}</textarea>
            </div>

            <button type="submit">{{ __('Submit') }}</button>
        </form>

        {{-- All validation errors --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </section>


    {{-- ============================================
        TRANSLATIONS
    ============================================ --}}

    <section class="translations">
        {{-- Lang directive --}}
        <h2>@lang('messages.welcome')</h2>

        {{-- Translation with parameters --}}
        <p>{{ __('messages.greeting', ['name' => $userName]) }}</p>

        {{-- Choice/pluralization --}}
        <p>{{ trans_choice('messages.items', $itemCount) }}</p>

        {{-- JSON translations --}}
        <p>{{ __('Welcome Back') }}</p>
    </section>


    {{-- ============================================
        ASSETS & URLS
    ============================================ --}}

    <section class="assets">
        {{-- Asset URLs --}}
        <img src="{{ asset('images/logo.png') }}" alt="Logo">
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">

        {{-- Named routes --}}
        <a href="{{ route('posts.index') }}">{{ __('All Posts') }}</a>
        <a href="{{ route('posts.show', $post) }}">{{ __('View Post') }}</a>
        <a href="{{ route('posts.edit', ['post' => $post->id, 'source' => 'dashboard']) }}">{{ __('Edit') }}</a>

        {{-- URL generation --}}
        <a href="{{ url('/about') }}">{{ __('About') }}</a>
        <a href="{{ secure_url('/checkout') }}">{{ __('Checkout') }}</a>

        {{-- Current URL checks --}}
        <li class="@if (request()->routeIs('posts.*')) active @endif">{{ __('Posts') }}</li>
        <li class="{{ request()->is('admin/*') ? 'active' : '' }}">{{ __('Admin') }}</li>
    </section>


    {{-- ============================================
        CUSTOM BLADE DIRECTIVES (Examples)
    ============================================ --}}

    <section class="custom-directives">
        {{-- Example custom directives you might define --}}
        @datetime($post->created_at)

        @currency($product->price)

        @role('admin')
            <div>{{ __('Admin-only content') }}</div>
        @endrole
    </section>


    {{-- ============================================
        HTML5 SEMANTIC ELEMENTS
    ============================================ --}}

    <main>
        <article>
            <header>
                <h1>{{ __('Article Title') }}</h1>
                <time datetime="2024-01-15">{{ __('January 15, 2024') }}</time>
            </header>

            <section>
                <h2>{{ __('Section Heading') }}</h2>
                <p>{{ __('Content here...') }}</p>

                <figure>
                    <img src="image.jpg" alt="Description">
                    <figcaption>{{ __('Image caption') }}</figcaption>
                </figure>
            </section>

            <aside>
                <h3>{{ __('Related Information') }}</h3>
                <p>{{ __('Sidebar content') }}</p>
            </aside>

            <footer>
                <p>{{ __('Article footer') }}</p>
            </footer>
        </article>
    </main>

    <footer class="site-footer">
        <nav>
            <a href="/privacy">{{ __('Privacy') }}</a>
            <a href="/terms">{{ __('Terms') }}</a>
        </nav>
        <p>{{ __('&copy;') }} {{ date('Y') }} {{ config('app.name') }}</p>
    </footer>


    {{-- ============================================
        SCRIPTS
    ============================================ --}}

    {{-- Livewire Scripts --}}
    @livewireScripts

    {{-- Stack for additional scripts --}}
    @stack('scripts')

    <script>
        // JavaScript can access Blade variables
        const appConfig = @json($config);
        const users = @json($users);
        const isAuthenticated = {{ auth()->check() ? 'true' : 'false' }};
    </script>

</body>

</html>
