<?php

use Illuminate\Support\Facades\DB;

// Seed basic locations to satisfy foreign key constraints
$cities = [
    ['id' => 1, 'name' => 'TP. Hồ Chí Minh', 'type' => 'city', 'parent_id' => null],
    ['id' => 2, 'name' => 'TP. Hà Nội', 'type' => 'city', 'parent_id' => null],
    ['id' => 3, 'name' => 'Đà Nẵng', 'type' => 'city', 'parent_id' => null],
    ['id' => 4, 'name' => 'Cần Thơ', 'type' => 'city', 'parent_id' => null],
    ['id' => 5, 'name' => 'Hải Phòng', 'type' => 'city', 'parent_id' => null],
];

DB::table('locations')->insertOrIgnore($cities);

// Seed some provinces (districts) for TP. HCM (ID: 1)
$provinces = [
    ['id' => 101, 'name' => 'Quận 1', 'type' => 'province', 'parent_id' => 1],
    ['id' => 102, 'name' => 'Quận 3', 'type' => 'province', 'parent_id' => 1],
    ['id' => 103, 'name' => 'Quận Tân Bình', 'type' => 'province', 'parent_id' => 1],
];

DB::table('locations')->insertOrIgnore($provinces);

// Seed some wards for Quận 1 (ID: 101)
$wards = [
    ['id' => 1001, 'name' => 'Phường Bến Nghé', 'type' => 'ward', 'parent_id' => 101],
    ['id' => 1002, 'name' => 'Phường Bến Thành', 'type' => 'ward', 'parent_id' => 101],
    ['id' => 1003, 'name' => 'Phường 1', 'type' => 'ward', 'parent_id' => 101],
];

DB::table('locations')->insertOrIgnore($wards);

echo "Basic locations seeded successfully!\n";
