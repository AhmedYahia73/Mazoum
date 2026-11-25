<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdvancedFeaturesToUserChatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_chats', function (Blueprint $table) {
            $table->string('client_msg_id', 64)->nullable()->after('id');

            $table->boolean('is_delivered')->default(false)->after('voice');
            $table->timestamp('delivered_at')->nullable()->after('is_delivered');
            $table->timestamp('admin_read_at')->nullable()->after('is_admin_read');
            $table->timestamp('user_read_at')->nullable()->after('is_user_read');

            $table->foreignId('reply_to_id')
                ->nullable()
                ->after('admin_id')
                ->constrained('user_chats')
                ->onDelete('set null');

            $table->boolean('is_forwarded')->default(false)->after('reply_to_id');
            $table->foreignId('forwarded_from_id')
                ->nullable()
                ->after('is_forwarded')
                ->constrained('user_chats')
                ->onDelete('set null');

            $table->boolean('is_edited')->default(false)->after('msg');
            $table->timestamp('edited_at')->nullable()->after('is_edited');

            $table->boolean('deleted_for_user')->default(false)->after('user_read_at');
            $table->boolean('deleted_for_admin')->default(false)->after('deleted_for_user');
            $table->timestamp('deleted_for_everyone_at')->nullable()->after('deleted_for_admin');
            $table->string('deleted_by_type', 20)->nullable()->after('deleted_for_everyone_at');
            $table->unsignedBigInteger('deleted_by_id')->nullable()->after('deleted_by_type');

            $table->boolean('is_pinned')->default(false)->after('deleted_by_id');
            $table->timestamp('pinned_at')->nullable()->after('is_pinned');
            $table->string('pinned_by_type', 20)->nullable()->after('pinned_at');
            $table->unsignedBigInteger('pinned_by_id')->nullable()->after('pinned_by_type');

            $table->timestamp('expires_at')->nullable()->after('pinned_by_id');

            $table->index(['user_id', 'client_msg_id']);
            $table->index(['user_id', 'is_pinned']);
            $table->index(['user_id', 'is_delivered']);
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_chats', function (Blueprint $table) {
            $table->dropForeign(['reply_to_id']);
            $table->dropForeign(['forwarded_from_id']);
            $table->dropIndex(['user_id', 'client_msg_id']);
            $table->dropIndex(['user_id', 'is_pinned']);
            $table->dropIndex(['user_id', 'is_delivered']);
            $table->dropIndex(['expires_at']);

            $table->dropColumn([
                'client_msg_id',
                'is_delivered',
                'delivered_at',
                'admin_read_at',
                'user_read_at',
                'reply_to_id',
                'is_forwarded',
                'forwarded_from_id',
                'is_edited',
                'edited_at',
                'deleted_for_user',
                'deleted_for_admin',
                'deleted_for_everyone_at',
                'deleted_by_type',
                'deleted_by_id',
                'is_pinned',
                'pinned_at',
                'pinned_by_type',
                'pinned_by_id',
                'expires_at',
            ]);
        });
    }
}
