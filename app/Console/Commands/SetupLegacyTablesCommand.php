<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SetupLegacyTablesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:legacy-tables';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup essential legacy tables for immediate functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Setting up legacy tables...');

        $tables = [
            'tbl_slider' => "
                CREATE TABLE IF NOT EXISTS `tbl_slider` (
                    `slider_id` int(11) NOT NULL,
                    `slider_name` varchar(100) NOT NULL,
                    `slider_image` varchar(255) NOT NULL,
                    `slider_status` int(11) NOT NULL,
                    `slider_desc` varchar(255) NOT NULL,
                    PRIMARY KEY (`slider_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
            ",
            'tbl_category_post' => "
                CREATE TABLE IF NOT EXISTS `tbl_category_post` (
                    `cate_post_id` int(11) NOT NULL,
                    `cate_post_name` varchar(100) NOT NULL,
                    `cate_post_slug` varchar(100) NOT NULL,
                    `cate_post_desc` text NOT NULL,
                    `cate_post_status` int(11) NOT NULL,
                    PRIMARY KEY (`cate_post_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
            ",
            'tbl_posts' => "
                CREATE TABLE IF NOT EXISTS `tbl_posts` (
                    `post_id` int(11) NOT NULL,
                    `post_title` varchar(255) NOT NULL,
                    `post_slug` varchar(255) NOT NULL,
                    `post_desc` text NOT NULL,
                    `post_content` longtext NOT NULL,
                    `post_meta_desc` varchar(255) NOT NULL,
                    `post_meta_keywords` varchar(255) NOT NULL,
                    `post_status` int(11) NOT NULL,
                    `post_image` varchar(255) NOT NULL,
                    `cate_post_id` int(11) NOT NULL,
                    `created_at` timestamp NULL DEFAULT NULL,
                    `updated_at` timestamp NULL DEFAULT NULL,
                    PRIMARY KEY (`post_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
            ",
            'tbl_video' => "
                CREATE TABLE IF NOT EXISTS `tbl_video` (
                    `video_id` int(11) NOT NULL,
                    `video_title` varchar(255) NOT NULL,
                    `video_slug` varchar(255) NOT NULL,
                    `video_link` varchar(255) NOT NULL,
                    `video_image` varchar(255) NOT NULL,
                    `video_desc` text NOT NULL,
                    `video_status` int(11) NOT NULL,
                    PRIMARY KEY (`video_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
            "
        ];

        foreach ($tables as $tableName => $sql) {
            if (!DB::getSchemaBuilder()->hasTable($tableName)) {
                DB::statement($sql);
                $this->info("Created table: $tableName");
            } else {
                $this->line("Table already exists: $tableName");
            }
        }

        // Insert sample data for tbl_slider
        if (DB::table('tbl_slider')->count() == 0) {
            DB::table('tbl_slider')->insert([
                ['slider_id' => 1, 'slider_name' => 'Slider 1', 'slider_image' => 'slider1.jpg', 'slider_status' => 1, 'slider_desc' => 'Main slider'],
                ['slider_id' => 2, 'slider_name' => 'Slider 2', 'slider_image' => 'slider2.jpg', 'slider_status' => 1, 'slider_desc' => 'Secondary slider']
            ]);
            $this->info("Inserted sample data for tbl_slider");
        }

        // Insert sample data for tbl_category_post
        if (DB::table('tbl_category_post')->count() == 0) {
            DB::table('tbl_category_post')->insert([
                ['cate_post_id' => 1, 'cate_post_name' => 'Tin tức', 'cate_post_slug' => 'tin-tuc', 'cate_post_desc' => 'Danh mục tin tức', 'cate_post_status' => 1],
                ['cate_post_id' => 2, 'cate_post_name' => 'Khuyến mãi', 'cate_post_slug' => 'khuyen-mai', 'cate_post_desc' => 'Danh mục khuyến mãi', 'cate_post_status' => 1]
            ]);
            $this->info("Inserted sample data for tbl_category_post");
        }

        // Insert sample data for tbl_posts
        if (DB::table('tbl_posts')->count() == 0) {
            DB::table('tbl_posts')->insert([
                [
                    'post_id' => 1, 
                    'post_title' => 'Bài viết mẫu', 
                    'post_slug' => 'bai-viet-mau', 
                    'post_desc' => 'Mô tả bài viết mẫu', 
                    'post_content' => 'Nội dung bài viết mẫu', 
                    'post_meta_desc' => 'Meta description', 
                    'post_meta_keywords' => 'meta, keywords', 
                    'post_status' => 1, 
                    'post_image' => 'post1.jpg', 
                    'cate_post_id' => 1
                ]
            ]);
            $this->info("Inserted sample data for tbl_posts");
        }

        // Insert sample data for tbl_video
        if (DB::table('tbl_video')->count() == 0) {
            DB::table('tbl_video')->insert([
                [
                    'video_id' => 1, 
                    'video_title' => 'Video mẫu', 
                    'video_slug' => 'video-mau', 
                    'video_link' => 'https://youtube.com/watch?v=example', 
                    'video_image' => 'video1.jpg', 
                    'video_desc' => 'Mô tả video mẫu', 
                    'video_status' => 1
                ]
            ]);
            $this->info("Inserted sample data for tbl_video");
        }

        $this->info('Legacy tables setup completed!');
        return 0;
    }
}
