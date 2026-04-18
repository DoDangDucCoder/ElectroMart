<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\User;
use App\Support\AdminAccess;
use App\Support\AdminPermissions;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ContactControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_save_info_redirects_to_information_without_referer_when_table_is_missing(): void
    {
        $admin = $this->createAdminUser();

        Schema::dropIfExists('tbl_infomation');

        $this->actingAs($admin, 'web')
            ->post('/save-info', [
                'info_contact' => 'Thong tin lien he',
                'slogan_logo' => 'Khau hieu',
            ])
            ->assertRedirect('/information')
            ->assertSessionHas('message', 'Bảng thông tin website chưa sẵn sàng.');
    }

    public function test_update_info_redirects_to_information_without_referer_when_record_is_missing(): void
    {
        $admin = $this->createAdminUser();
        $this->ensureLegacyInformationTableExists();

        $this->actingAs($admin, 'web')
            ->post('/update-info/999999', [
                'info_contact' => 'Thong tin lien he',
                'slogan_logo' => 'Khau hieu',
            ])
            ->assertRedirect('/information')
            ->assertSessionHas('message', 'Không tìm thấy thông tin website cần cập nhật');
    }

    protected function createAdminUser(array $attributes = []): User
    {
        foreach (AdminPermissions::all() as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => AdminAccess::guardName(),
            ]);
        }

        $adminRole = Role::firstOrCreate([
            'name' => 'Admin',
            'guard_name' => AdminAccess::guardName(),
        ]);
        $adminRole->syncPermissions(AdminPermissions::defaultsForRole('Admin'));

        $user = User::factory()->create($attributes);
        $user->assignRole($adminRole);

        return $user;
    }

    protected function ensureLegacyInformationTableExists(): void
    {
        if (Schema::hasTable('tbl_infomation')) {
            return;
        }

        Schema::create('tbl_infomation', function (Blueprint $table) {
            $table->increments('info_id');
            $table->text('info_contact');
            $table->text('info_map')->nullable();
            $table->string('info_logo')->nullable();
            $table->string('slogan_logo')->nullable();
            $table->text('info_fanpage')->nullable();
        });
    }
}
