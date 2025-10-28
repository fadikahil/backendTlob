<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration adds CASCADE delete to all foreign keys referencing users and items tables.
     * This ensures that when a user or item is deleted, all related records are automatically deleted.
     */
    public function up(): void
    {
        // ============================================================================
        // FOREIGN KEYS REFERENCING USERS TABLE (26 foreign keys)
        // ============================================================================

        // 1. block_users table - 2 foreign keys
        if (Schema::hasTable('block_users')) {
            Schema::table('block_users', function (Blueprint $table) {
                // Drop existing foreign keys
                $table->dropForeign(['user_id']);
                $table->dropForeign(['blocked_user_id']);
            });

            Schema::table('block_users', function (Blueprint $table) {
                // Recreate with CASCADE
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('blocked_user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        // 2. chats table - sender_id
        if (Schema::hasTable('chats')) {
            Schema::table('chats', function (Blueprint $table) {
                $table->dropForeign(['sender_id']);
            });

            Schema::table('chats', function (Blueprint $table) {
                $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        // 3. favourites table - user_id
        if (Schema::hasTable('favourites')) {
            Schema::table('favourites', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });

            Schema::table('favourites', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        // 4. featured_users table - user_id
        if (Schema::hasTable('featured_users')) {
            Schema::table('featured_users', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });

            Schema::table('featured_users', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        // 5. items table - 2 foreign keys (user_id, sold_to)
        if (Schema::hasTable('items')) {
            Schema::table('items', function (Blueprint $table) {
                $table->dropForeign(['user_id']);

                // Check if sold_to foreign key exists
                try {
                    $table->dropForeign(['sold_to']);
                } catch (\Exception $e) {
                    // Foreign key might not exist
                }
            });

            Schema::table('items', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

                // Only add sold_to if column exists
                if (Schema::hasColumn('items', 'sold_to')) {
                    $table->foreign('sold_to')->references('id')->on('users')->onDelete('cascade');
                }
            });
        }

        // 6. item_offers table - 2 foreign keys (buyer_id, seller_id)
        if (Schema::hasTable('item_offers')) {
            Schema::table('item_offers', function (Blueprint $table) {
                $table->dropForeign(['buyer_id']);
                $table->dropForeign(['seller_id']);
            });

            Schema::table('item_offers', function (Blueprint $table) {
                $table->foreign('buyer_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('seller_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        // 7. payment_transactions table - user_id
        if (Schema::hasTable('payment_transactions')) {
            Schema::table('payment_transactions', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });

            Schema::table('payment_transactions', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        // 8. receipts table - user_id
        if (Schema::hasTable('receipts')) {
            Schema::table('receipts', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });

            Schema::table('receipts', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        // 9. seller_ratings table - 2 foreign keys (buyer_id, seller_id)
        if (Schema::hasTable('seller_ratings')) {
            Schema::table('seller_ratings', function (Blueprint $table) {
                $table->dropForeign(['buyer_id']);
                $table->dropForeign(['seller_id']);
            });

            Schema::table('seller_ratings', function (Blueprint $table) {
                $table->foreign('buyer_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('seller_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        // 10. service_reviews table - 2 foreign keys (user_id, reviewer_id)
        if (Schema::hasTable('service_reviews')) {
            Schema::table('service_reviews', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropForeign(['reviewer_id']);
            });

            Schema::table('service_reviews', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('reviewer_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        // 11. social_logins table - user_id
        if (Schema::hasTable('social_logins')) {
            Schema::table('social_logins', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });

            Schema::table('social_logins', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        // 12. user_audience_relations table - organization_id (note: user_id was replaced with user_email)
        if (Schema::hasTable('user_audience_relations')) {
            // Check if organization_id foreign key exists
            $constraint = DB::select("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'user_audience_relations' AND COLUMN_NAME = 'organization_id' AND CONSTRAINT_NAME != 'PRIMARY'");

            if (!empty($constraint)) {
                Schema::table('user_audience_relations', function (Blueprint $table) {
                    $table->dropForeign(['organization_id']);
                });
            }

            Schema::table('user_audience_relations', function (Blueprint $table) {
                $table->foreign('organization_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        // 13. user_claims table - user_id
        if (Schema::hasTable('user_claims')) {
            Schema::table('user_claims', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });

            Schema::table('user_claims', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        // 14. user_fcm_tokens table - user_id
        if (Schema::hasTable('user_fcm_tokens')) {
            Schema::table('user_fcm_tokens', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });

            Schema::table('user_fcm_tokens', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        // 15. user_purchased_packages table - user_id
        if (Schema::hasTable('user_purchased_packages')) {
            Schema::table('user_purchased_packages', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });

            Schema::table('user_purchased_packages', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        // 16. user_reports table - user_id
        if (Schema::hasTable('user_reports')) {
            Schema::table('user_reports', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });

            Schema::table('user_reports', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        // 17. user_reviews table - 2 foreign keys (user_id, reviewer_id)
        if (Schema::hasTable('user_reviews')) {
            Schema::table('user_reviews', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropForeign(['reviewer_id']);
            });

            Schema::table('user_reviews', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('reviewer_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        // 18. user_scores table - user_id
        if (Schema::hasTable('user_scores')) {
            Schema::table('user_scores', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });

            Schema::table('user_scores', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        // 19. verification_field_values table - user_id
        if (Schema::hasTable('verification_field_values')) {
            Schema::table('verification_field_values', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });

            Schema::table('verification_field_values', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        // 20. verification_requests table - user_id
        if (Schema::hasTable('verification_requests')) {
            Schema::table('verification_requests', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });

            Schema::table('verification_requests', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        // ============================================================================
        // FOREIGN KEYS REFERENCING ITEMS TABLE (11 foreign keys)
        // ============================================================================

        // 1. favourites table - item_id
        if (Schema::hasTable('favourites')) {
            Schema::table('favourites', function (Blueprint $table) {
                $table->dropForeign(['item_id']);
            });

            Schema::table('favourites', function (Blueprint $table) {
                $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            });
        }

        // 2. featured_items table - item_id
        if (Schema::hasTable('featured_items')) {
            Schema::table('featured_items', function (Blueprint $table) {
                $table->dropForeign(['item_id']);
            });

            Schema::table('featured_items', function (Blueprint $table) {
                $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            });
        }

        // 3. item_custom_field_values table - item_id
        if (Schema::hasTable('item_custom_field_values')) {
            Schema::table('item_custom_field_values', function (Blueprint $table) {
                $table->dropForeign(['item_id']);
            });

            Schema::table('item_custom_field_values', function (Blueprint $table) {
                $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            });
        }

        // 4. item_images table - item_id
        if (Schema::hasTable('item_images')) {
            Schema::table('item_images', function (Blueprint $table) {
                $table->dropForeign(['item_id']);
            });

            Schema::table('item_images', function (Blueprint $table) {
                $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            });
        }

        // 5. item_offers table - item_id
        if (Schema::hasTable('item_offers')) {
            Schema::table('item_offers', function (Blueprint $table) {
                $table->dropForeign(['item_id']);
            });

            Schema::table('item_offers', function (Blueprint $table) {
                $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            });
        }

        // 6. notifications table - item_id
        if (Schema::hasTable('notifications')) {
            Schema::table('notifications', function (Blueprint $table) {
                try {
                    $table->dropForeign(['item_id']);
                } catch (\Exception $e) {
                    // Foreign key might not exist
                }
            });

            Schema::table('notifications', function (Blueprint $table) {
                if (Schema::hasColumn('notifications', 'item_id')) {
                    $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
                }
            });
        }

        // 7. receipts table - item_id
        if (Schema::hasTable('receipts')) {
            Schema::table('receipts', function (Blueprint $table) {
                $table->dropForeign(['item_id']);
            });

            Schema::table('receipts', function (Blueprint $table) {
                $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            });
        }

        // 8. seller_ratings table - item_id
        if (Schema::hasTable('seller_ratings')) {
            Schema::table('seller_ratings', function (Blueprint $table) {
                $table->dropForeign(['item_id']);
            });

            Schema::table('seller_ratings', function (Blueprint $table) {
                $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            });
        }

        // 9. service_reviews table - service_id (references items.id)
        if (Schema::hasTable('service_reviews')) {
            Schema::table('service_reviews', function (Blueprint $table) {
                $table->dropForeign(['service_id']);
            });

            Schema::table('service_reviews', function (Blueprint $table) {
                $table->foreign('service_id')->references('id')->on('items')->onDelete('cascade');
            });
        }

        // 10. user_claims table - item_id
        if (Schema::hasTable('user_claims')) {
            Schema::table('user_claims', function (Blueprint $table) {
                $table->dropForeign(['item_id']);
            });

            Schema::table('user_claims', function (Blueprint $table) {
                $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            });
        }

        // 11. user_reports table - item_id
        if (Schema::hasTable('user_reports')) {
            Schema::table('user_reports', function (Blueprint $table) {
                $table->dropForeign(['item_id']);
            });

            Schema::table('user_reports', function (Blueprint $table) {
                $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * This will revert all foreign keys back to their original state (RESTRICT or NO ACTION).
     * Use with caution - this may cause deletion failures if there are related records.
     */
    public function down(): void
    {
        // ============================================================================
        // REVERT FOREIGN KEYS REFERENCING USERS TABLE
        // ============================================================================

        if (Schema::hasTable('block_users')) {
            Schema::table('block_users', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropForeign(['blocked_user_id']);
            });
            Schema::table('block_users', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
                $table->foreign('blocked_user_id')->references('id')->on('users')->onDelete('restrict');
            });
        }

        if (Schema::hasTable('chats')) {
            Schema::table('chats', function (Blueprint $table) {
                $table->dropForeign(['sender_id']);
            });
            Schema::table('chats', function (Blueprint $table) {
                $table->foreign('sender_id')->references('id')->on('users')->onDelete('restrict');
            });
        }

        if (Schema::hasTable('favourites')) {
            Schema::table('favourites', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });
            Schema::table('favourites', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            });
        }

        if (Schema::hasTable('featured_users')) {
            Schema::table('featured_users', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });
            Schema::table('featured_users', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            });
        }

        if (Schema::hasTable('items')) {
            Schema::table('items', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                try {
                    $table->dropForeign(['sold_to']);
                } catch (\Exception $e) {
                    // Ignore if doesn't exist
                }
            });
            Schema::table('items', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
                if (Schema::hasColumn('items', 'sold_to')) {
                    $table->foreign('sold_to')->references('id')->on('users')->onDelete('restrict');
                }
            });
        }

        if (Schema::hasTable('item_offers')) {
            Schema::table('item_offers', function (Blueprint $table) {
                $table->dropForeign(['buyer_id']);
                $table->dropForeign(['seller_id']);
            });
            Schema::table('item_offers', function (Blueprint $table) {
                $table->foreign('buyer_id')->references('id')->on('users')->onDelete('restrict');
                $table->foreign('seller_id')->references('id')->on('users')->onDelete('restrict');
            });
        }

        if (Schema::hasTable('payment_transactions')) {
            Schema::table('payment_transactions', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });
            Schema::table('payment_transactions', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            });
        }

        if (Schema::hasTable('receipts')) {
            Schema::table('receipts', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropForeign(['item_id']);
            });
            Schema::table('receipts', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
                $table->foreign('item_id')->references('id')->on('items')->onDelete('restrict');
            });
        }

        if (Schema::hasTable('seller_ratings')) {
            Schema::table('seller_ratings', function (Blueprint $table) {
                $table->dropForeign(['buyer_id']);
                $table->dropForeign(['seller_id']);
                $table->dropForeign(['item_id']);
            });
            Schema::table('seller_ratings', function (Blueprint $table) {
                $table->foreign('buyer_id')->references('id')->on('users')->onDelete('restrict');
                $table->foreign('seller_id')->references('id')->on('users')->onDelete('restrict');
                $table->foreign('item_id')->references('id')->on('items')->onDelete('restrict');
            });
        }

        if (Schema::hasTable('service_reviews')) {
            Schema::table('service_reviews', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropForeign(['reviewer_id']);
                $table->dropForeign(['service_id']);
            });
            Schema::table('service_reviews', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
                $table->foreign('reviewer_id')->references('id')->on('users')->onDelete('restrict');
                $table->foreign('service_id')->references('id')->on('items')->onDelete('restrict');
            });
        }

        if (Schema::hasTable('social_logins')) {
            Schema::table('social_logins', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });
            Schema::table('social_logins', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            });
        }

        if (Schema::hasTable('user_audience_relations')) {
            Schema::table('user_audience_relations', function (Blueprint $table) {
                $table->dropForeign(['organization_id']);
            });
            Schema::table('user_audience_relations', function (Blueprint $table) {
                $table->foreign('organization_id')->references('id')->on('users')->onDelete('restrict');
            });
        }

        if (Schema::hasTable('user_claims')) {
            Schema::table('user_claims', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropForeign(['item_id']);
            });
            Schema::table('user_claims', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
                $table->foreign('item_id')->references('id')->on('items')->onDelete('restrict');
            });
        }

        if (Schema::hasTable('user_fcm_tokens')) {
            Schema::table('user_fcm_tokens', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });
            Schema::table('user_fcm_tokens', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            });
        }

        if (Schema::hasTable('user_purchased_packages')) {
            Schema::table('user_purchased_packages', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });
            Schema::table('user_purchased_packages', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            });
        }

        if (Schema::hasTable('user_reports')) {
            Schema::table('user_reports', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropForeign(['item_id']);
            });
            Schema::table('user_reports', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
                $table->foreign('item_id')->references('id')->on('items')->onDelete('restrict');
            });
        }

        if (Schema::hasTable('user_reviews')) {
            Schema::table('user_reviews', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropForeign(['reviewer_id']);
            });
            Schema::table('user_reviews', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
                $table->foreign('reviewer_id')->references('id')->on('users')->onDelete('restrict');
            });
        }

        if (Schema::hasTable('user_scores')) {
            Schema::table('user_scores', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });
            Schema::table('user_scores', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            });
        }

        if (Schema::hasTable('verification_field_values')) {
            Schema::table('verification_field_values', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });
            Schema::table('verification_field_values', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            });
        }

        if (Schema::hasTable('verification_requests')) {
            Schema::table('verification_requests', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });
            Schema::table('verification_requests', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            });
        }

        // ============================================================================
        // REVERT FOREIGN KEYS REFERENCING ITEMS TABLE
        // ============================================================================

        if (Schema::hasTable('favourites')) {
            Schema::table('favourites', function (Blueprint $table) {
                $table->dropForeign(['item_id']);
            });
            Schema::table('favourites', function (Blueprint $table) {
                $table->foreign('item_id')->references('id')->on('items')->onDelete('restrict');
            });
        }

        if (Schema::hasTable('featured_items')) {
            Schema::table('featured_items', function (Blueprint $table) {
                $table->dropForeign(['item_id']);
            });
            Schema::table('featured_items', function (Blueprint $table) {
                $table->foreign('item_id')->references('id')->on('items')->onDelete('restrict');
            });
        }

        if (Schema::hasTable('item_custom_field_values')) {
            Schema::table('item_custom_field_values', function (Blueprint $table) {
                $table->dropForeign(['item_id']);
            });
            Schema::table('item_custom_field_values', function (Blueprint $table) {
                $table->foreign('item_id')->references('id')->on('items')->onDelete('restrict');
            });
        }

        if (Schema::hasTable('item_images')) {
            Schema::table('item_images', function (Blueprint $table) {
                $table->dropForeign(['item_id']);
            });
            Schema::table('item_images', function (Blueprint $table) {
                $table->foreign('item_id')->references('id')->on('items')->onDelete('restrict');
            });
        }

        if (Schema::hasTable('item_offers')) {
            Schema::table('item_offers', function (Blueprint $table) {
                $table->dropForeign(['item_id']);
            });
            Schema::table('item_offers', function (Blueprint $table) {
                $table->foreign('item_id')->references('id')->on('items')->onDelete('restrict');
            });
        }

        if (Schema::hasTable('notifications')) {
            try {
                Schema::table('notifications', function (Blueprint $table) {
                    $table->dropForeign(['item_id']);
                });
                Schema::table('notifications', function (Blueprint $table) {
                    if (Schema::hasColumn('notifications', 'item_id')) {
                        $table->foreign('item_id')->references('id')->on('items')->onDelete('restrict');
                    }
                });
            } catch (\Exception $e) {
                // Ignore if doesn't exist
            }
        }

        if (Schema::hasTable('featured_items')) {
            Schema::table('featured_items', function (Blueprint $table) {
                $table->dropForeign(['item_id']);
            });
            Schema::table('featured_items', function (Blueprint $table) {
                $table->foreign('item_id')->references('id')->on('items')->onDelete('restrict');
            });
        }
    }
};
