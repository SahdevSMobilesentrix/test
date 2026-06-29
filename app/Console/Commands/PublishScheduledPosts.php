<?php

namespace App\Console\Commands;

use App\Models\BlogPost;
use Illuminate\Console\Command;

class PublishScheduledPosts extends Command
{
    protected $signature = 'blogify:publish-due';

    protected $description = 'Flip scheduled posts whose publish time has arrived to published.';

    public function handle(): int
    {
        $due = BlogPost::due()->get();

        foreach ($due as $post) {
            $post->update(['status' => 'published']);
            $this->line("📣 Published: {$post->title}");
        }

        $this->info("✅ {$due->count()} post(s) published.");

        return self::SUCCESS;
    }
}
