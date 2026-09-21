<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('posts')) {
            Schema::table('posts', function (Blueprint $table) {
                if (! $this->indexExists('posts', 'posts_status_created_at_idx')) {
                    $table->index(['status', 'created_at'], 'posts_status_created_at_idx');
                }
                if (! $this->indexExists('posts', 'posts_unicode_idx')) {
                    $table->index('unicode', 'posts_unicode_idx');
                }
            });
        }

        if (Schema::hasTable('comments')) {
            Schema::table('comments', function (Blueprint $table) {
                if (! $this->indexExists('comments', 'comments_post_parent_created_idx')) {
                    $table->index(['post_id', 'parent_id', 'created_at'], 'comments_post_parent_created_idx');
                }
            });
        }

        if (Schema::hasTable('user_views')) {
            Schema::table('user_views', function (Blueprint $table) {
                if (! $this->indexExists('user_views', 'user_views_post_created_idx')) {
                    $table->index(['post_id', 'created_at'], 'user_views_post_created_idx');
                }
            });
        }

        if (Schema::hasTable('user_likes')) {
            Schema::table('user_likes', function (Blueprint $table) {
                if (! $this->indexExists('user_likes', 'user_likes_post_created_idx')) {
                    $table->index(['post_id', 'created_at'], 'user_likes_post_created_idx');
                }
            });
        }

        if (Schema::hasTable('user_comments')) {
            Schema::table('user_comments', function (Blueprint $table) {
                if (! $this->indexExists('user_comments', 'user_comments_post_created_idx')) {
                    $table->index(['post_id', 'created_at'], 'user_comments_post_created_idx');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('posts')) {
            Schema::table('posts', function (Blueprint $table) {
                if ($this->indexExists('posts', 'posts_status_created_at_idx')) {
                    $table->dropIndex('posts_status_created_at_idx');
                }
                if ($this->indexExists('posts', 'posts_unicode_idx')) {
                    $table->dropIndex('posts_unicode_idx');
                }
            });
        }

        if (Schema::hasTable('comments')) {
            Schema::table('comments', function (Blueprint $table) {
                if ($this->indexExists('comments', 'comments_post_parent_created_idx')) {
                    $table->dropIndex('comments_post_parent_created_idx');
                }
            });
        }

        if (Schema::hasTable('user_views')) {
            Schema::table('user_views', function (Blueprint $table) {
                if ($this->indexExists('user_views', 'user_views_post_created_idx')) {
                    $table->dropIndex('user_views_post_created_idx');
                }
            });
        }

        if (Schema::hasTable('user_likes')) {
            Schema::table('user_likes', function (Blueprint $table) {
                if ($this->indexExists('user_likes', 'user_likes_post_created_idx')) {
                    $table->dropIndex('user_likes_post_created_idx');
                }
            });
        }

        if (Schema::hasTable('user_comments')) {
            Schema::table('user_comments', function (Blueprint $table) {
                if ($this->indexExists('user_comments', 'user_comments_post_created_idx')) {
                    $table->dropIndex('user_comments_post_created_idx');
                }
            });
        }
    }

    private function indexExists(string $table, string $indexName): bool
    {
        if (DB::getDriverName() === 'sqlite') {
            $indexes = DB::select("PRAGMA index_list(`{$table}`)");
            foreach ($indexes as $index) {
                if (($index->name ?? null) === $indexName) {
                    return true;
                }
            }

            return false;
        }

        $rows = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);

        return count($rows) > 0;
    }
};
