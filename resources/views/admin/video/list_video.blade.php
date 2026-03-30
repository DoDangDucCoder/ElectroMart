@extends('admin_layout')

@section('admin_content')
    <div class="g-admin-page-shell g-admin-video-page">
        <section class="g-admin-page-hero">
            <div>
                <span class="g-admin-page-eyebrow">Nội dung / Video</span>
                <h1>Quản lý video</h1>
                <p>Quản lý video review, demo và hướng dẫn hiển thị trên website.</p>
            </div>
            <div class="g-admin-page-stats">
                <article>
                    <strong>Video</strong>
                    <span>Review, demo và hướng dẫn</span>
                </article>
                <article>
                    <strong>Cập nhật</strong>
                    <span>Thêm mới và chỉnh sửa nhanh trên cùng một màn hình</span>
                </article>
            </div>
        </section>

        @if (session('message'))
            <div class="alert alert-success">{{ session('message') }}</div>
        @endif

        <div class="g-admin-video-layout">
            <section class="g-admin-panel-card">
                <header class="g-admin-panel-head">
                    <div>
                        <strong>Thêm video mới</strong>
                        <p>Nhập tiêu đề, slug, link YouTube, mô tả và thumbnail.</p>
                    </div>
                </header>
                <div class="g-admin-panel-body">
                    <form class="g-admin-video-form">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label for="slug">Tên video</label>
                            <input type="text" name="video_title" class="form-control video_title" onkeyup="ChangeToSlug();"
                                id="slug" placeholder="Tên video">
                        </div>
                        <div class="form-group">
                            <label for="convert_slug">Slug video</label>
                            <input type="text" name="video_slug" class="form-control video_slug" id="convert_slug"
                                placeholder="Slug video">
                        </div>
                        <div class="form-group">
                            <label for="video_link_input">Link video</label>
                            <input type="text" name="link_video" class="form-control video_link" id="video_link_input"
                                placeholder="https://youtu.be/...">
                        </div>
                        <div class="form-group">
                            <label for="video_desc_input">Mô tả video</label>
                            <textarea style="resize:none" rows="8" class="form-control video_desc" name="video_desc"
                                id="video_desc_input" placeholder="Mô tả video"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="file_img_video">Hình ảnh video</label>
                            <input type="file" class="form-control" id="file_img_video" name="file" accept="image/*">
                        </div>
                        <button type="button" name="add_video" id="btn-add-video" class="btn btn-info">Thêm video</button>
                    </form>
                    <div id="notify" style="margin-top:16px;"></div>
                </div>
            </section>

            <section class="g-admin-panel-card">
                <header class="g-admin-panel-head">
                    <div>
                        <strong>Danh sách video</strong>
                        <p>Theo dõi video hiện có và thao tác chỉnh sửa ngay trong danh sách.</p>
                    </div>
                </header>
                <div class="g-admin-panel-body g-admin-video-load">
                    <div id="video_load"></div>
                </div>
            </section>
        </div>

        <div class="modal fade" id="video_modal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tên video</h5>
                    </div>
                    <div class="modal-body">
                        Nội dung video
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
