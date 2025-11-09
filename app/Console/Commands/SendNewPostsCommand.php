<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Website;
use App\Models\Post;
use App\Models\Subscription;
use App\Models\PostDelivery;
use App\Jobs\SendPostEmailJob; 

class SendNewPostsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'posts:send-new {--since=72}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Queue emails for posts not yet sent to subscribers';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int {
        $since = now()->subHours((int)$this->option('since'));

        Website::orderBy('id')->chunkById(100, function($websites) use ($since){
        foreach ($websites as $website) {
            $posts = Post::where('website_id',$website->id)
            ->where('created_at','>=',$since)
            ->orderBy('id')->get(['id']);

            if ($posts->isEmpty()) continue;

            Subscription::where('website_id',$website->id)
            ->orderBy('id')
            ->chunkById(1000, function($subs) use ($posts){
                foreach ($subs as $sub) {
                foreach ($posts as $post) {
                    $exists = PostDelivery::where('post_id',$post->id)
                    ->where('user_id',$sub->user_id)->exists();
                    if (!$exists) {
                    SendPostEmailJob::dispatch($post->id, $sub->user_id)
                        ->onQueue('emails');
                    }
                }
                }
            });
        }
        });

        $this->info('Dispatch done');
        return Command::SUCCESS;
    }
}
