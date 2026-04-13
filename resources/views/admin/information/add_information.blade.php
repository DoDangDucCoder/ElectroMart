@extends('admin_layout')

@section('admin_content')
    @php
        $primaryContact = $contact->first();
        $formAction = $primaryContact ? url('/update-info/' . $primaryContact->info_id) : url('/save-info');
    @endphp

    <div class="g-admin-page-shell g-admin-site-info">
        <section class="g-admin-page-hero">
            <div>
                <span class="g-admin-page-eyebrow">Hệ thống / Website</span>
                <h1>Thông tin website</h1>
                <p>Quản lý thông tin liên hệ, logo, nút mạng xã hội và danh sách đối tác hiển thị trên website.</p>
            </div>
            <div class="g-admin-page-stats">
                <article>
                    <strong>{{ $primaryContact ? '1' : '0' }}</strong>
                    <span>Hồ sơ website</span>
                </article>
                <article>
                    <strong>Mạng xã hội</strong>
                    <span>Nút nổi và liên kết nhanh</span>
                </article>
                <article>
                    <strong>Đối tác</strong>
                    <span>Logo đối tác hiển thị</span>
                </article>
            </div>
        </section>

        @if (session('message'))
            <div class="alert alert-success">{{ session('message') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div id="site_info_notice" class="alert alert-info" hidden></div>

        <div class="g-admin-panel-grid two-col">
            <section class="g-admin-panel-card">
                <header class="g-admin-panel-head">
                    <div>
                        <strong>Hồ sơ liên hệ và branding</strong>
                        <p>Cập nhật thông tin liên hệ, bản đồ, fanpage và logo chính.</p>
                    </div>
                    <span class="g-admin-status-chip">{{ $primaryContact ? 'Đã cấu hình' : 'Chưa có dữ liệu' }}</span>
                </header>
                <div class="g-admin-panel-body">
                    <form class="g-admin-site-info-form" action="{{ $formAction }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="ckeditor">Thông tin liên hệ</label>
                            <textarea style="resize:none" rows="8" class="form-control" name="info_contact" id="ckeditor"
                                data-validation="length" data-validation-length="min5"
                                data-validation-error-msg="Làm ơn điền ít nhất 5 ký tự">{{ old('info_contact', $primaryContact->info_contact ?? '') }}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="info_map">Bản đồ</label>
                            <textarea style="resize:none" rows="8" class="form-control" name="info_map" id="info_map"
                                data-validation="length" data-validation-length="min5"
                                data-validation-error-msg="Làm ơn điền ít nhất 5 ký tự">{{ old('info_map', $primaryContact->info_map ?? '') }}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="info_fanpage">Fanpage</label>
                            <textarea style="resize:none" rows="8" class="form-control" name="info_fanpage" id="info_fanpage"
                                data-validation="length" data-validation-length="min5"
                                data-validation-error-msg="Làm ơn điền ít nhất 5 ký tự">{{ old('info_fanpage', $primaryContact->info_fanpage ?? '') }}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="info_image">Hình ảnh logo</label>
                            <input type="file" name="info_image" class="form-control" id="info_image">
                            @if (! empty($primaryContact?->info_logo))
                                <div class="g-admin-surface" style="margin-top:12px;">
                                    <h3>Logo hiện tại</h3>
                                    <img src="{{ url('uploads/contact/' . $primaryContact->info_logo) }}" height="100" width="200" alt="Logo hiện tại">
                                </div>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="slogan_logo">Slogan logo</label>
                            <input type="text" name="slogan_logo" value="{{ old('slogan_logo', $primaryContact->slogan_logo ?? '') }}"
                                class="form-control" id="slogan_logo">
                        </div>
                        <button type="submit" class="btn btn-info">{{ $primaryContact ? 'Cập nhật thông tin' : 'Lưu thông tin' }}</button>
                    </form>
                </div>
            </section>

            <section class="g-admin-panel-card">
                <header class="g-admin-panel-head">
                    <div>
                        <strong>Lưu ý khi cập nhật</strong>
                        <p>Một số điểm cần kiểm tra khi thay đổi thông tin hiển thị trên website.</p>
                    </div>
                </header>
                <div class="g-admin-panel-body">
                    <div class="g-admin-surface">
                        <h3>Những phần liên quan</h3>
                        <ul class="g-admin-note-list">
                            <li>Thông tin liên hệ sẽ xuất hiện trên trang liên hệ của website.</li>
                            <li>Logo hiện tại đang được dùng ở phần đầu và cuối trang ngoài storefront.</li>
                            <li>Fanpage và bản đồ nên dùng mã nhúng ổn định để tránh lỗi hiển thị.</li>
                        </ul>
                    </div>
                </div>
            </section>
        </div>

        <div class="g-admin-panel-grid two-col">
            <section class="g-admin-panel-card">
                <header class="g-admin-panel-head">
                    <div>
                        <strong>Nút mạng xã hội</strong>
                        <p>Quản lý các nút nổi và liên kết nhanh hiển thị trên website.</p>
                    </div>
                </header>
                <div class="g-admin-panel-body">
                    <form role="form" id="form-nut" class="g-admin-site-info-form" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label for="name">Tên nút</label>
                            <input type="text" name="name" id="name" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="link">Link nút</label>
                            <input type="text" name="link" id="link" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="image_nut">Hình ảnh nút</label>
                            <input type="file" name="info_image" class="form-control" id="image_nut">
                        </div>
                        <button type="button" class="btn btn-info add-nut">Thêm nút</button>
                    </form>
                    <div class="g-admin-site-info-list" style="margin-top:18px;">
                        <div id="list_nut"></div>
                    </div>
                </div>
            </section>

            <section class="g-admin-panel-card">
                <header class="g-admin-panel-head">
                    <div>
                        <strong>Đối tác hiển thị</strong>
                        <p>Quản lý logo và liên kết của các đối tác hiển thị trên website.</p>
                    </div>
                </header>
                <div class="g-admin-panel-body">
                    <form role="form" class="g-admin-site-info-form" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label for="name_doitac">Tên đối tác</label>
                            <input type="text" name="name" id="name_doitac" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="link_doitac">Link đối tác</label>
                            <input type="text" name="link" id="link_doitac" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="image_doitac">Hình ảnh đối tác</label>
                            <input type="file" name="info_image" class="form-control" id="image_doitac">
                        </div>
                        <button type="button" class="btn btn-info add-doitac">Thêm đối tác</button>
                    </form>
                    <div class="g-admin-site-info-list" style="margin-top:18px;">
                        <div id="list_doitac"></div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
