<?php

use App\Models\Post;
use Livewire\Attributes\Locked;
use Livewire\Volt\Component;

new class extends Component {
    #[Locked]
    public Post $post;

    public function relevantTime(): string
    {
        $published = \Carbon\Carbon::parse($this->post->published_at)->format('M jS');
        return match($this->post->status) {
            'published' => sprintf('Published %s', $published),
            'scheduled' => sprintf('Scheduled for %s', $published),
            'draft' => sprintf('Created on %s', $this->post->created_at->format('M jS')),
        };
    }
}; ?>

<item class="flex flex-row space-x-6 group p-3 items-center !cursor-pointer hover:bg-zinc-800/5 dark:hover:bg-white/10">
    <flux:avatar size="lg" color="auto" name="{{ $post->title ?? 'Untitled' }}" badge="18+" />
    <div class="flex flex-col flex-auto">
        <flux:heading>{{ $post->title ?? __('Untitled') }}</flux:heading>
        <flux:text class="text-xs">{{ $this->relevantTime() }}</flux:text>
    </div>
    <flux:dropdown position="bottom" align="end">
        <flux:button icon="ellipsis-horizontal" />
        <flux:menu>
        </flux:menu>
    </flux:dropdown>
</item>
