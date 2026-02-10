<div class="bg-white px-5 py-3 shadow-sm rounded-md">
    <div class="divide-y divide-gray-200">
        @foreach($upcomingRssFeeds as $feed)
            <div class="py-2 flex">
                <div class="flex-auto">
                    <a href="{{ route('rss.edit', ['feed' => $feed, 'campaign' => $feed->campaign]) }}" class="text-sm font-medium">{{ $feed->templateSend->name }}</a> <br>
                    <span class="text-gray-600 text-sm">{{ __('Next check') }} {{ $feed->next_review_at->diffForHumans() }}</span> <br>
                    <a href="{{ route('campaigns.show', ['campaign' => $feed->campaign]) }}" class="text-sm font-medium text-gray-600">{{ __('Campaign:') }} {{ $feed->campaign->name }}</a>
                </div>
            </div>
        @endforeach
    </div>
</div>
