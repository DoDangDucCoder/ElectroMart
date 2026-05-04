-- Legacy archive table sample import.
-- Do not use this file for normal setup, current storefront runtime, or production bootstrap.
-- Prefer Laravel migrations + seeders. Import this only for archive comparison/recovery after a DB backup.

-- Table structure for table `tbl_icons`
CREATE TABLE IF NOT EXISTS `tbl_icons` (
  `id_icons` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `image` varchar(100) NOT NULL,
  `link` varchar(255) NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_icons`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert data for tbl_icons
INSERT INTO `tbl_icons` (`id_icons`, `name`, `image`, `link`, `category`) VALUES
(15, 'Đối tác công nghệ A', 'slider168.jpg', '#', 'doitac'),
(16, 'Đối tác công nghệ B', 'slider168.jpg', '#', 'doitac'),
(19, 'Đối tác viễn thông A', 'slider168.jpg', '#', 'doitac'),
(20, 'Đối tác viễn thông B', 'slider168.jpg', '#', 'doitac'),
(21, 'Đối tác phân phối', 'slider168.jpg', '#', 'doitac');

-- Table structure for table `tbl_infomation`
CREATE TABLE IF NOT EXISTS `tbl_infomation` (
  `info_id` int(11) NOT NULL,
  `info_contact` mediumtext NOT NULL,
  `info_map` text NOT NULL,
  `info_logo` varchar(255) NOT NULL,
  `slogan_logo` varchar(255) NOT NULL,
  `info_fanpage` text NOT NULL,
  PRIMARY KEY (`info_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert data for tbl_infomation
INSERT INTO `tbl_infomation` (`info_id`, `info_contact`, `info_map`, `info_logo`, `slogan_logo`, `info_fanpage`) VALUES
(1, '<ol><li><strong>Địa chỉ: 283/47 Cách Mạng Tháng Tám, Phường 12, Quận 10, Tp HCM</strong></li><li><strong>Số điện thoại: 123456789 MrPhú - 093254125 MrsLan</strong></li><li><strong>Fanpage: Laptop Khóa Vàng</strong></li></ol>', '<iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d1959.630754815143!2d106.701961!3d10.791272!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x317529487b889ad5%3A0xd6124d130d0ed8ae!2zTcOheSBUw61uaCBLaG_DoSBWw6BuZw!5e0!3m2!1sen!2sus!4v1743061464615!5m2!1sen!2sus" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>', 'laptop-symbol-azy3.png', 'Đảm bảo - Uy tín - Chất lượng - Giá rẻ cho mọi khách hàng dùng dịch vụ của chúng tôi.', '<div id="fb-root"></div><script async defer crossorigin="anonymous" src="https://connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v22.0&appId=6125793717446054"></script><div class="fb-page" data-href="https://www.facebook.com/khoavangquantanbinh" data-tabs="message" data-width="" data-height="" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"><blockquote cite="https://www.facebook.com/khoavangquantanbinh" class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/khoavangquantanbinh">Laptop Khóa Vàng</a></blockquote></div>');

-- Table structure for table `tbl_slider`
CREATE TABLE IF NOT EXISTS `tbl_slider` (
  `slider_id` int(11) NOT NULL,
  `slider_name` varchar(100) NOT NULL,
  `slider_image` varchar(255) NOT NULL,
  `slider_status` int(11) NOT NULL,
  `slider_desc` varchar(255) NOT NULL,
  PRIMARY KEY (`slider_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert sample slider data
INSERT INTO `tbl_slider` (`slider_id`, `slider_name`, `slider_image`, `slider_status`, `slider_desc`) VALUES
(1, 'Slider 1', 'slider1.jpg', 1, 'Main slider'),
(2, 'Slider 2', 'slider2.jpg', 1, 'Secondary slider');

-- Table structure for table `tbl_category_post`
CREATE TABLE IF NOT EXISTS `tbl_category_post` (
  `cate_post_id` int(11) NOT NULL,
  `cate_post_name` varchar(100) NOT NULL,
  `cate_post_slug` varchar(100) NOT NULL,
  `cate_post_desc` text NOT NULL,
  `cate_post_status` int(11) NOT NULL,
  PRIMARY KEY (`cate_post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert sample category post data
INSERT INTO `tbl_category_post` (`cate_post_id`, `cate_post_name`, `cate_post_slug`, `cate_post_desc`, `cate_post_status`) VALUES
(1, 'Tin tức', 'tin-tuc', 'Danh mục tin tức', 1),
(2, 'Khuyến mãi', 'khuyen-mai', 'Danh mục khuyến mãi', 1);

-- Table structure for table `tbl_posts`
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert sample post data
INSERT INTO `tbl_posts` (`post_id`, `post_title`, `post_slug`, `post_desc`, `post_content`, `post_meta_desc`, `post_meta_keywords`, `post_status`, `post_image`, `cate_post_id`) VALUES
(1, 'Bài viết mẫu', 'bai-viet-mau', 'Mô tả bài viết mẫu', 'Nội dung bài viết mẫu', 'Meta description', 'meta, keywords', 1, 'post1.jpg', 1);

-- Table structure for table `tbl_video`
CREATE TABLE IF NOT EXISTS `tbl_video` (
  `video_id` int(11) NOT NULL,
  `video_title` varchar(255) NOT NULL,
  `video_slug` varchar(255) NOT NULL,
  `video_link` varchar(255) NOT NULL,
  `video_image` varchar(255) NOT NULL,
  `video_desc` text NOT NULL,
  `video_status` int(11) NOT NULL,
  PRIMARY KEY (`video_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert sample video data
INSERT INTO `tbl_video` (`video_id`, `video_title`, `video_slug`, `video_link`, `video_image`, `video_desc`, `video_status`) VALUES
(1, 'Video mẫu', 'video-mau', 'https://youtube.com/watch?v=example', 'video1.jpg', 'Mô tả video mẫu', 1);
