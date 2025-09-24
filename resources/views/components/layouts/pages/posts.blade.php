<div class="flex items-start max-md:flex-col">
    <div class="flex flex-col w-full space-y-6">
        <header class="mb-6">
            <flux:heading size="xl">{{ __('Posts') }}</flux:heading>
        </header>
        <flux:separator />
        <content class="flex flex-col gap-6">
            @php($counts = Auth::user()->postTabCounts())
            <nav class="flex items-center justify-between">
                <flux:navbar>
                    <flux:navbar.item :href="route('posts.published')" :badge="$counts['published']" :current="request()->routeIs('posts.published')" wire:navigate>{{ __('Published') }}</flux:navbar.item>
                    <flux:navbar.item :href="route('posts.scheduled')" :badge="$counts['scheduled']" :current="request()->routeIs('posts.scheduled')" wire:navigate>{{ __('Scheduled') }}</flux:navbar.item>
                    <flux:navbar.item :href="route('posts.drafts')" :badge="$counts['drafts']" :current="request()->routeIs('posts.drafts')" wire:navigate>{{ __('Drafts') }}</flux:navbar.item>
                </flux:navbar>
                <flux:button variant="primary">{{ __('New Post') }}</flux:button>
            </nav>

            <div class="mt-5 w-full max-w-lg">
                {{ $slot }}
            </div>
        </content>
    </div>
</div>
