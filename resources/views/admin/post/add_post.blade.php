@extends('admin_layout')

@section('admin_content')
    <div class="row">
        <div class="col-lg-12">
            <section class="panel">
                <header class="panel-heading">
                    Thêm bài viết
                </header>
                <div class="panel-body">
                    @if (session('message'))
                        <div class="alert alert-success">{{ session('message') }}</div>
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

                    <div class="position-center">
                        <form role="form" action="{{ url('/save-post') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="post_title">Tên bài viết</label>
                                <input type="text" name="post_title" class="form-control" id="slug"
                                    value="{{ old('post_title') }}" onkeyup="ChangeToSlug();" placeholder="Tên bài viết"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="post_slug">Slug</label>
                                <input type="text" name="post_slug" class="form-control" id="convert_slug"
                                    value="{{ old('post_slug') }}" placeholder="Slug">
                            </div>
                            <div class="form-group">
                                <label for="post_desc">Tóm tắt bài viết</label>
                                <textarea rows="6" class="form-control" name="post_desc" id="ckeditor1"
                                    placeholder="Nhập tóm tắt">{{ old('post_desc') }}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="post_content">Nội dung bài viết</label>
                                <textarea rows="8" class="form-control" name="post_content" id="ckeditor2"
                                    placeholder="Nhập nội dung">{{ old('post_content') }}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="post_meta_keywords">Meta từ khóa</label>
                                <textarea rows="3" class="form-control" name="post_meta_keywords" id="post_meta_keywords"
                                    placeholder="Nhập meta keywords">{{ old('post_meta_keywords') }}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="post_meta_desc">Meta nội dung</label>
                                <textarea rows="3" class="form-control" name="post_meta_desc" id="post_meta_desc"
                                    placeholder="Nhập meta description">{{ old('post_meta_desc') }}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="post_image">Hình ảnh bài viết</label>
                                <input type="file" name="post_image" class="form-control" id="post_image" accept="image/*">
                            </div>
                            <div class="form-group">
                                <label for="cate_post_id">Danh mục bài viết</label>
                                <select name="cate_post_id" id="cate_post_id" class="form-control input-sm m-bot15">
                                    <option value="">-- Chọn danh mục --</option>
                                    @foreach ($cate_post as $cate)
                                        <option value="{{ $cate->cate_post_id }}"
                                            {{ (string) $cate->cate_post_id === (string) old('cate_post_id') ? 'selected' : '' }}>
                                            {{ $cate->cate_post_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="post_status">Hiển thị</label>
                                <select name="post_status" id="post_status" class="form-control input-sm m-bot15">
                                    <option value="0" {{ old('post_status', '0') === '0' ? 'selected' : '' }}>Hiển thị
                                    </option>
                                    <option value="1" {{ old('post_status') === '1' ? 'selected' : '' }}>Ẩn</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-info">Thêm bài viết</button>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
