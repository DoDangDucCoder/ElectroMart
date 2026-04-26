<?php

namespace Tests\Unit;

use App\Application\DataMigration\Services\SqlDumpSplitter;
use Tests\TestCase;

class SqlDumpSplitterTest extends TestCase
{
    public function test_it_splits_statements_without_breaking_semicolons_inside_strings_and_comments(): void
    {
        $sql = <<<'SQL'
-- header comment
CREATE TABLE `tbl_posts` (`post_id` integer NOT NULL, `post_content` text NOT NULL);
INSERT INTO `tbl_posts` (`post_id`, `post_content`) VALUES (1, '<p>Hello; world &nbsp;</p>');
/* block comment ; with semicolon */
INSERT INTO `tbl_infomation` (`info_id`, `info_contact`) VALUES (1, '<div>Line 1; Line 2</div>');
INSERT INTO `tbl_videos` (`video_id`, `video_desc`) VALUES (1, 'Text with ''quotes'' and ; semicolon');
SQL;

        $statements = (new SqlDumpSplitter())->split($sql);

        $this->assertCount(4, $statements);
        $this->assertStringContainsString('CREATE TABLE `tbl_posts`', $statements[0]);
        $this->assertStringContainsString('<p>Hello; world &nbsp;</p>', $statements[1]);
        $this->assertStringContainsString('<div>Line 1; Line 2</div>', $statements[2]);
        $this->assertStringContainsString("''quotes''", $statements[3]);
    }
}
