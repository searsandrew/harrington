<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<section class="w-full">
    <pre>{{ request()->route()?->getName() }}</pre>
    <x-layouts.pages.posts />
</section>
