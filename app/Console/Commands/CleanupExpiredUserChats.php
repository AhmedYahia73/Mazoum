<?php

namespace App\Console\Commands;

use App\Models\UserChat;
use App\Traits\imageTrait;
use Illuminate\Console\Command;

class CleanupExpiredUserChats extends Command
{
    use imageTrait;

    protected $signature = 'user-chat:cleanup-expired';

    protected $description = 'Delete expired temporary UserChat messages and their media files';

    public function handle()
    {
        $expired = UserChat::whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->get();

        $count = $expired->count();
        foreach ($expired as $chat) {
            $this->deleteImage($chat->image);
            $this->deleteImage($chat->voice);
            $chat->delete();
        }

        $this->info("Cleaned up {$count} expired UserChat messages.");

        return 0;
    }
}
