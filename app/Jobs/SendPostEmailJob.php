<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Post;
use App\Models\User;
use App\Models\PostDelivery;
use App\Mail\NewPostMail;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendPostEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    public $tries = 3;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public int $postId, public int $userId)
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $post = Post::with('website')->findOrFail($this->postId);
        $user = User::findOrFail($this->userId);

        
        try {
        PostDelivery::create([
            'post_id'=>$post->id,'user_id'=>$user->id,
            'sent_at'=>now(),'status'=>'sent'
        ]);
        } catch (\Illuminate\Database\QueryException $e) {
        return;
        }

        Mail::to($user->email)->send(new NewPostMail($post));
    }

    public function failed(Throwable $e){
    PostDelivery::updateOrCreate(
      ['post_id'=>$this->postId,'user_id'=>$this->userId],
      ['status'=>'failed','last_error'=>$e->getMessage(),'sent_at'=>now()]
    );
  }
}
