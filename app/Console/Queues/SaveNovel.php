<?php

namespace App\Jobs;

use App\Models\NovelDetail;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SaveNovel implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $book_id;
    protected $ele;
    public function __construct($book_id,$ele)
    {
        $this->book_id = $book_id;
        $this->ele = $ele;
    }

    public function handle() 
    {
        $nd = NovelDetail::where([
            'book_id' => $this->book_id,
            'title' => $this->ele->plaintext,
        ])->first();
   
        if (!$nd) {
            $count = NovelDetail::where('book_id',$this->book_id)->count();
            NovelDetail::create([
                'book_id' => $this->book_id,
                'catalog_id' => $count + 1,
                'title' => $this->ele->plaintext,
                'source_href' => $this->siteUrl . $this->ele->href,
            ]);
        }
    }
}