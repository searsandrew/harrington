<?php

use App\Models\Post;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Volt\Component;

new class extends Component {
    public ?string $search = null;
    public string $sortBy = 'created_at';
    public string $sortDirection = 'desc';

    public function getSortBy(): string
    {
        return match ($this->sortBy) {
            'created_at' => __('Date Created'),
            'updated_at' => __('Latest Activity'),
            'title' => __('Post Title')
        };
    }

    public function getSortDirection(): string
    {
        return match ($this->sortDirection) {
            'desc' => 'chevron-double-down',
            'asc' => 'chevron-double-up'
        };
    }

    public function setSortDirection(): string
    {
        if ($this->sortDirection === 'asc') {
            $this->sortDirection = 'desc';
        } else {
            $this->sortDirection = 'asc';
        }

        return $this->getSortDirection();
    }

    public function setSortBy(): string
    {
        return $this->getSortBy();
    }

    public function getPosts(): LengthAwarePaginator
    {
        $sortMap = [
            'created_at' => 'posts.created_at',
            'updated_at' => 'posts.updated_at',
            'title' => 'posts.title',
        ];

        $column = $sortMap[$this->sortBy] ?? 'posts.created_at';
        $direction = $this->sortDirection === 'asc' ? 'asc' : 'desc';

        $q = Post::query()
            ->where('posts.is_published', false)
            ->whereNull('posts.published_at');

        $term = trim((string)$this->search);
        if ($term !== '') {
            $q->where(function ($qq) use ($term) {
                $qq->where('posts.title', 'ilike', "%{$term}%")
                    ->orWhere('posts.subtitle', 'ilike', "%{$term}%")
                    ->orWhere('posts.content', 'ilike', "%{$term}%");
            });
        }

        if ($column === 'posts.title') {
            $q->orderByRaw('LOWER(posts.title) ' . $direction);
        } else {
            $q->orderBy($column, $direction);
        }
        $q->orderBy('posts.id', 'desc');

        return $q->paginate(15); // @todo set user preference for pagination
    }
}; ?>

<x-layouts.pages.posts>
    <div class="flex flex-row space-x-3 justify-center">
        <div class="flex flex-auto">
            <flux:input icon="magnifying-glass" wire:model.live.debounce="search" :placeholder="__('Search posts...')"/>
        </div>
        <flux:button icon="funnel">{{ __('Filter') }}</flux:button>
        <flux:button.group>
            <flux:button :icon="$this->getSortDirection()" wire:click="setSortDirection()"
                         class="!cursor-pointer">{{ $this->getSortBy() }}</flux:button>
            <flux:dropdown>
                <flux:button icon:trailing="chevron-down"/>
                <flux:menu>
                    <flux:menu.radio.group wire:click="setSortBy()" wire:model="sortBy">
                        <flux:menu.radio value="created_at">{{ __('Date created') }}</flux:menu.radio>
                        <flux:menu.radio value="updated_at">{{ __('Latest activity') }}</flux:menu.radio>
                        <flux:menu.radio value="title">{{ __('Post name') }}</flux:menu.radio>
                    </flux:menu.radio.group>
                </flux:menu>
            </flux:dropdown>
        </flux:button.group>
    </div>
    <flux:card>
        @forelse($this->getPosts() as $post)

        @empty
            <div class="flex flex-col items-center justify-center text-center space-y-3 w-full pt-5 pb-10">
                @if($search != '')
                    <x-svgs.not-found class="h-64"/>
                    <flux:heading size="lg">{{ __('Not found') }}</flux:heading>
                    <flux:text>{{ __('Sorry, we could not find any matching posts.') }}</flux:text>
                @else
                    <x-svgs.ideas class="h-64"/>
                    <flux:heading size="lg">{{ __('No draft posts') }}</flux:heading>
                    <flux:text>{{ __('Create a new post to get started.') }}</flux:text>
                    <span>
                        <flux:button :href="route('post.create')" wire:navigate>{{ __('Create Post') }}</flux:button>
                    </span>
                @endif

            </div>
        @endforelse
    </flux:card>
</x-layouts.pages.posts>
