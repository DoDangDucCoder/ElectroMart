@extends('admin_layout')
@section('admin_content')
    <div class="table-agile-info">
        <div class="panel panel-default">
            <div class="panel-heading">
                Liệt kê bài viết
            </div>
            {{--   <div class="row w3-res-tb">
      <div class="col-sm-5 m-b-xs">
        <select class="input-sm form-control w-sm inline v-middle">
          <option value="0">Bulk action</option>
          <option value="1">Delete selected</option>
          <option value="2">Bulk edit</option>
          <option value="3">Export</option>
        </select>
        <button class="btn btn-sm btn-default">Apply</button>                
      </div>
      <div class="col-sm-4">
      </div>
      <div class="col-sm-3">
        <div class="input-group">
          <input type="text" class="input-sm form-control" placeholder="Search">
          <span class="input-group-btn">
            <button class="btn btn-sm btn-default" type="button">Go!</button>
          </span>
        </div>
      </div>
    </div> --}}
            <div class="table-responsive">
                @if (session('message'))
                    <div class="alert alert-success">{{ session('message') }}</div>
                @endif
                <form class="form-inline mb-3" method="get" action="{{ url('/all-post') }}">
                    <div class="form-group mr-2">
                        <input type="text" name="search" class="form-control" placeholder="Tìm kiếm"
                            value="{{ $filters['search'] ?? '' }}">
                    </div>
                    <div class="form-group mr-2">
                        <select name="category_id" class="form-control">
                            <option value="">-- Tất cả danh mục --</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->cate_post_id }}"
                                    {{ (string) ($filters['category_id'] ?? '') === (string) $category->cate_post_id ? 'selected' : '' }}>
                                    {{ $category->cate_post_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mr-2">
                        <select name="status" class="form-control">
                            <option value="">-- Trạng thái --</option>
                            <option value="published" {{ ($filters['status'] ?? '') === 'published' ? 'selected' : '' }}>
                                Hiển thị
                            </option>
                            <option value="draft" {{ ($filters['status'] ?? '') === 'draft' ? 'selected' : '' }}>Ẩn
                            </option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary">Lọc</button>
                </form>
                <table class="table table-striped b-t b-light" id="myTable">
                    <thead>
                        <tr>
                            <th style="width:20px;">
                                <label class="i-checks m-b-none">
                                    <input type="checkbox"><i></i>
                                </label>
                            </th>
                            <th>Tên bài viết</th>
                            <th>Hình ảnh</th>
                            <th>Slug</th>
                            <th>Mô tả bài viết</th>
                            <th>Từ khóa bài viết</th>
                            <th>Danh mục bài viết</th>
                            <th>Hiển thị</th>

                            <th style="width:30px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($all_post as $key => $post)
                            <tr>
                                <td><label class="i-checks m-b-none"><input type="checkbox" name="post[]"><i></i></label>
                                </td>
                                <td>{{ $post->post_title }}</td>
                                <td>
                                    <img src="{{ $post->featured_image_path ? asset($post->featured_image_path) : asset('frontend/images/no-image.png') }}"
                                        alt="{{ $post->post_title }}" style="height:80px;width:auto;">
                                </td>
                                <td>{{ $post->post_slug }}</td>
                                <td>{!! $post->post_desc !!}</td>
                                <td>{{ $post->post_meta_keywords }}</td>
                                <td>{{ optional($post->category)->name }}</td>
                                <td>
                                    @if ($post->post_status == 0)
                                        Hiển thị
                                    @else
                                        Ẩn
                                    @endif
                                </td>

                                <td>
                                    <a href="{{ URL::to('/edit-post/' . $post->post_id) }}" class="active styling-edit"
                                        ui-toggle-class="">
                                        <i class="fa fa-pencil-square-o text-success text-active"></i></a>
                                    <a onclick="return confirm('Bạn có chắc là muốn xóa bài viết này ko?')"
                                        href="{{ URL::to('/delete-post/' . $post->post_id) }}" class="active styling-edit"
                                        ui-toggle-class="">
                                        <i class="fa fa-times text-danger text"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <footer class="panel-footer">
                <div class="row">
                    <div class="col-sm-12 text-right">
                        {{ $all_post->appends(request()->query())->links() }}
                    </div>
                </div>
            </footer>
        </div>
    </div>
@endsection
