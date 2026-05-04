@extends('admin_layout')
@section('admin_content')
<link rel="stylesheet" href="{{ asset('backend/css/admin-dashboard.css') }}">
<link rel="stylesheet" href="{{ asset('backend/css/admin-pages.css') }}">
<div class="adm-page">
    <div class="adm-page-title"><h1>Thêm / sửa bài viết</h1><div class="crumb"><a href="{{ url('/dashboard') }}">Trang chủ</a> › <a href="{{ route('admin.posts.management') }}">Bài viết</a> › <a href="#">Quản lý bài viết</a> › Thêm / sửa bài viết</div></div>

    <div class="adm-action-bar">
        <a href="{{ route('admin.posts.management') }}" class="adm-btn-back"><i class="fa fa-arrow-left"></i> Quay lại</a>
        <span class="spacer"></span>
        <button class="adm-btn outline"><i class="fa fa-save"></i> Lưu nháp</button>
        <button class="adm-btn outline"><i class="fa fa-eye"></i> Xem trước</button>
        <button class="adm-btn green"><i class="fa fa-check"></i> Cập nhật</button>
        <button class="adm-btn blue"><i class="fa fa-paper-plane"></i> Xuất bản</button>
        <button class="adm-btn editing"><i class="fa fa-pencil"></i> Đang chỉnh sửa</button>
    </div>

    {{-- Stats bar --}}
    <div class="adm-stats-bar">
        <div class="adm-stat-item"><div class="icon blue"><i class="fa fa-file-text-o"></i></div><div><div class="val">1.284</div><div class="lbl">Số từ — Tốt cho SEO</div></div></div>
        <div class="adm-stat-item"><div class="icon teal"><i class="fa fa-clock-o"></i></div><div><div class="val">6 phút</div><div class="lbl">Thời gian đọc — Trung bình</div></div></div>
        <div class="adm-stat-item"><div class="icon green"><i class="fa fa-tachometer"></i></div><div><div class="val">78/100</div><div class="lbl">Điểm SEO — Khá tốt</div></div></div>
        <div class="adm-stat-item"><div class="icon amber"><i class="fa fa-bookmark"></i></div><div><div class="val">Bản nháp</div><div class="lbl">Trạng thái — Chưa xuất bản</div></div></div>
    </div>

    <div class="adm-editor-layout">
        {{-- Main content --}}
        <div>
            {{-- Post info --}}
            <div class="adm-card">
                <h3><i class="fa fa-info-circle"></i> Thông tin bài viết</h3>
                <div class="adm-form-grid">
                    <div class="adm-form-group"><label>Tiêu đề bài viết <span class="req">*</span></label><input type="text" value="Hướng dẫn chọn laptop phù hợp cho sinh viên năm 2025"><div class="char-count">56/255</div></div>
                    <div class="adm-form-group"><label>Slug <span class="req">*</span></label><input type="text" value="huong-dan-chon-laptop-phu-hop-cho-sinh-vien-nam-2025"><div class="char-count">56/255</div></div>
                </div>
                <div class="adm-form-grid three-col" style="margin-top:12px">
                    <div class="adm-form-group"><label>Danh mục <span class="req">*</span></label><select><option>Hướng dẫn</option></select></div>
                    <div class="adm-form-group"><label>Loại nội dung <span class="req">*</span></label><select><option>Blog</option></select></div>
                    <div class="adm-form-group"><label>Tác giả <span class="req">*</span></label><select><option>Nguyễn Văn An</option></select></div>
                </div>
                <div class="adm-form-group span-full" style="margin-top:12px">
                    <label>Từ khóa / tags</label>
                    <div class="adm-tags">
                        <span class="adm-tag">laptop <span class="remove">&times;</span></span>
                        <span class="adm-tag">sinh viên <span class="remove">&times;</span></span>
                        <span class="adm-tag">mua laptop <span class="remove">&times;</span></span>
                        <span class="adm-tag">2025 <span class="remove">&times;</span></span>
                        <input type="text" placeholder="Nhập từ khóa và nhấn Enter..." style="border:none;outline:none;flex:1;min-width:100px;font-size:12px">
                    </div>
                </div>
                <div class="adm-form-group span-full" style="margin-top:12px">
                    <label>Mô tả ngắn</label>
                    <textarea style="height:60px">Gợi ý cách chọn laptop phù hợp nhu cầu học tập, giải trí và ngân sách dành cho sinh viên năm 2025. Tổng hợp cấu hình, thương hiệu và kinh nghiệm mua hàng.</textarea>
                    <div class="char-count">141/300</div>
                </div>
            </div>

            {{-- Rich text editor --}}
            <div class="adm-card">
                <h3><i class="fa fa-edit"></i> Nội dung bài viết</h3>
                <div class="adm-editor-area">
                    <div class="adm-editor-toolbar">
                        <select><option>Heading 2</option></select>
                        <div class="sep"></div>
                        <button title="Bold"><i class="fa fa-bold"></i></button>
                        <button title="Italic"><i class="fa fa-italic"></i></button>
                        <button title="Underline"><i class="fa fa-underline"></i></button>
                        <button title="Strikethrough"><i class="fa fa-strikethrough"></i></button>
                        <div class="sep"></div>
                        <button title="Ordered list"><i class="fa fa-list-ol"></i></button>
                        <button title="Unordered list"><i class="fa fa-list-ul"></i></button>
                        <div class="sep"></div>
                        <button title="Quote"><i class="fa fa-quote-left"></i></button>
                        <button title="Code"><i class="fa fa-code"></i></button>
                        <button title="Table"><i class="fa fa-table"></i></button>
                        <div class="sep"></div>
                        <button title="Link"><i class="fa fa-link"></i></button>
                        <button title="Image"><i class="fa fa-image"></i></button>
                        <button title="Video"><i class="fa fa-video-camera"></i></button>
                        <div class="sep"></div>
                        <button title="Undo"><i class="fa fa-undo"></i></button>
                        <button title="Redo"><i class="fa fa-repeat"></i></button>
                        <button title="More"><i class="fa fa-ellipsis-v"></i></button>
                    </div>
                    <div class="adm-editor-body">
                        <h2>1. Xác định nhu cầu sử dụng</h2>
                        <p>Trước khi chọn mua laptop, bạn cần xác định rõ nhu cầu sử dụng của mình: học tập, lập trình, thiết kế đồ họa, chỉnh sửa video hay giải trí. Mỗi nhu cầu sẽ phù hợp với cấu hình và mức giá khác nhau. Việc xác định đúng nhu cầu giúp tối ưu chi phí và đảm bảo hiệu năng.</p>
                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='500' height='120' fill='%23334155'%3E%3Crect width='500' height='120' fill='%231e293b' rx='8'/%3E%3Ctext x='250' y='65' text-anchor='middle' fill='%2394a3b8' font-size='14'%3EHình ảnh minh họa laptop%3C/text%3E%3C/svg%3E" alt="Laptop cho sinh viên">
                        <div class="tip-box"><i class="fa fa-lightbulb-o"></i> Mẹo: Hãy ưu tiên hiệu năng ổn định, pin tốt và bàn phím thoải mái để đáp ứng tốt việc học tập lâu dài.</div>
                        <h2>2. Cấu hình laptop nên ưu tiên</h2>
                        <ul>
                            <li>CPU: Intel Core i5 / AMD Ryzen 5 trở lên</li>
                            <li>RAM: 8GB ~ 16GB</li>
                            <li>Ổ cứng: SSD 256GB trở lên</li>
                            <li>Màn hình: 14" ~ 15.6" Full HD</li>
                        </ul>
                    </div>
                    <div class="adm-editor-footer">
                        <span>P > STRONG</span>
                        <span>1,284 words &nbsp; POWERED BY TINY</span>
                    </div>
                </div>
            </div>

            {{-- Content blocks --}}
            <div class="adm-card">
                <h3><i class="fa fa-th-large"></i> Khối nội dung bổ sung</h3>
                <p style="font-size:11px;color:var(--adm-muted);margin:0 0 12px">Kéo thả để sắp xếp thứ tự hiện thị các khối nội dung bên dưới</p>
                <div class="adm-content-blocks">
                    <div class="adm-content-block"><div class="cb-icon blue"><i class="fa fa-question-circle"></i></div><div class="cb-info"><div class="cb-title">FAQ liên quan</div><div class="cb-meta">3 câu hỏi</div></div><div class="cb-toggle on"></div></div>
                    <div class="adm-content-block"><div class="cb-icon amber"><i class="fa fa-picture-o"></i></div><div class="cb-info"><div class="cb-title">Banner liên kết</div><div class="cb-meta">2 banner</div></div><div class="cb-toggle on"></div></div>
                    <div class="adm-content-block"><div class="cb-icon teal"><i class="fa fa-file-text"></i></div><div class="cb-info"><div class="cb-title">Bài viết liên quan</div><div class="cb-meta">4 bài viết</div></div><div class="cb-toggle on"></div></div>
                    <div class="adm-content-block"><div class="cb-icon purple"><i class="fa fa-phone"></i></div><div class="cb-info"><div class="cb-title">CTA hỗ trợ</div><div class="cb-meta">Hiển thị form liên hệ</div></div><div class="cb-toggle"></div></div>
                </div>
                <div style="display:flex;justify-content:flex-end;margin-top:12px">
                    <button class="adm-btn outline adm-btn sm"><i class="fa fa-plus"></i> Thêm khối</button>
                </div>
            </div>

            {{-- SEO --}}
            <div class="adm-card">
                <h3><i class="fa fa-search"></i> SEO & tối ưu nội dung</h3>
                <div class="adm-form-grid three-col">
                    <div class="adm-form-group"><label>Tiêu đề SEO (meta title)</label><input type="text" value="Hướng dẫn chọn laptop phù hợp cho sinh viên 2025"><div class="char-count">55/60</div></div>
                    <div class="adm-form-group"><label>Meta description</label><input type="text" value="Gợi ý cách chọn laptop cho sinh viên 2025: cấu hình, hãng nổi bật, kinh nghiệm mua và mẹo tiết kiệm chi phí."><div class="char-count">118/160</div></div>
                    <div class="adm-form-group"><label>URL chuẩn (canonical URL)</label><div style="display:flex;align-items:center;gap:6px"><input type="text" value="https://dangduc.vn/huong-dan-chon-laptop" style="flex:1"><i class="fa fa-check-circle" style="color:var(--adm-green)"></i></div></div>
                </div>
                <div class="adm-form-grid three-col" style="margin-top:12px">
                    <div class="adm-form-group"><label>Từ khóa chính (focus keyword)</label><input type="text" value="chọn laptop sinh viên"></div>
                    <div class="adm-form-group"><label>Alt text ảnh đại diện</label><input type="text" value="Sinh viên đang chọn laptop cho học tập"></div>
                    <div class="adm-form-group"><label>Index trong Google</label><div style="display:flex;gap:8px;align-items:center"><select style="flex:1"><option>Index</option></select><div class="adm-seo-score" style="background:conic-gradient(var(--adm-teal) 0% 78%, #f1f5f9 78% 100%)"><div style="width:60px;height:60px;border-radius:50%;background:#fff;display:flex;align-items:center;justify-content:center;flex-direction:column"><span class="score-num" style="font-size:18px;font-weight:800;color:var(--adm-teal)">78<span style="font-size:11px">/100</span></span><span style="font-size:8px;color:var(--adm-muted)">Điểm SEO</span></div></div></div></div>
                </div>
                <div class="adm-seo-checks">
                    <span class="adm-seo-check"><span class="dot green"></span> Tiêu đề đủ dài</span>
                    <span class="adm-seo-check"><span class="dot green"></span> Có từ khóa chính</span>
                    <span class="adm-seo-check"><span class="dot green"></span> Có mô tả meta</span>
                    <span class="adm-seo-check"><span class="dot green"></span> Slug thân thiện</span>
                    <span class="adm-seo-check"><span class="dot amber"></span> Ảnh đại diện có alt</span>
                </div>
            </div>
        </div>

        {{-- Right sidebar --}}
        <aside>
            {{-- Publish status --}}
            <div class="adm-sidebar-card">
                <h4>Trạng thái xuất bản</h4>
                <div class="adm-publish-row"><span class="pub-label">Trạng thái</span><span class="pub-value"><select><option>Bản nháp</option><option>Công khai</option></select></span></div>
                <div class="adm-publish-row"><span class="pub-label">Ngày xuất bản</span><span class="pub-value">24/05/2025 10:00 <i class="fa fa-calendar" style="color:var(--adm-muted);margin-left:4px;cursor:pointer"></i></span></div>
                <div class="adm-publish-row"><span class="pub-label">Bài viết nổi bật</span><span class="pub-value"><div class="adm-toggle on"></div></span></div>
                <div class="adm-publish-row"><span class="pub-label">Cho phép bình luận</span><span class="pub-value"><div class="adm-toggle on"></div></span></div>
                <div class="adm-publish-row"><span class="pub-label">Ghim bài viết</span><span class="pub-value"><div class="adm-toggle"></div></span></div>
            </div>

            {{-- Featured image --}}
            <div class="adm-sidebar-card">
                <h4>Ảnh đại diện</h4>
                <div class="adm-featured-img"><img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='320' height='180' fill='%23334155'%3E%3Crect width='320' height='180' fill='%231e293b' rx='8'/%3E%3Ctext x='160' y='95' text-anchor='middle' fill='%2394a3b8' font-size='14'%3EẢnh đại diện bài viết%3C/text%3E%3C/svg%3E" alt="Featured"></div>
                <div class="adm-featured-img-actions">
                    <button class="adm-btn outline sm" style="flex:1"><i class="fa fa-upload"></i> Thay ảnh</button>
                    <button class="adm-btn outline sm" style="color:var(--adm-red);border-color:#fca5a5"><i class="fa fa-trash"></i> Xóa ảnh</button>
                </div>
            </div>

            {{-- Preview --}}
            <div class="adm-sidebar-card">
                <h4>Xem trước bài viết <a href="#">Xem trên site →</a></h4>
                <div class="adm-preview-card">
                    <div class="preview-img"><img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='70' fill='%23334155'%3E%3Crect width='100' height='70' fill='%231e293b' rx='4'/%3E%3C/svg%3E" alt="Preview"></div>
                    <div class="preview-info">
                        <div class="preview-cat">Hướng dẫn <span style="color:var(--adm-muted);font-weight:400">· 6 phút đọc</span></div>
                        <div class="preview-title">Hướng dẫn chọn laptop phù hợp cho sinh viên năm 2025</div>
                        <div class="preview-desc">Gợi ý cách chọn laptop phù hợp nhu cầu học tập, giải trí và ngân sách dành cho sinh viên năm 2025.</div>
                        <div class="preview-author"><div style="width:18px;height:18px;border-radius:50%;background:linear-gradient(135deg,#3b82f6,#2563eb);display:flex;align-items:center;justify-content:center;color:#fff;font-size:8px;font-weight:700">N</div> Nguyễn Văn An <span style="margin-left:auto">24/05/2025</span></div>
                    </div>
                </div>
            </div>

            {{-- Checklist --}}
            <div class="adm-sidebar-card">
                <h4>Checklist xuất bản <span class="adm-badge green" style="font-size:10px">5/7</span></h4>
                <ul class="adm-checklist">
                    <li><i class="fa fa-check-circle"></i> Ảnh đại diện</li>
                    <li><i class="fa fa-check-circle"></i> Tiêu đề</li>
                    <li><i class="fa fa-check-circle"></i> Mô tả ngắn</li>
                    <li><i class="fa fa-check-circle"></i> Nội dung bài viết</li>
                    <li><i class="fa fa-check-circle"></i> SEO & tối ưu</li>
                    <li><i class="fa fa-times-circle"></i> Danh mục</li>
                    <li><i class="fa fa-times-circle"></i> Tags</li>
                </ul>
            </div>

            {{-- Edit history --}}
            <div class="adm-sidebar-card">
                <h4>Lịch sử chỉnh sửa <a href="#">Xem toàn bộ lịch sử</a></h4>
                <ul class="adm-history">
                    <li><div class="h-avatar" style="background:linear-gradient(135deg,#3b82f6,#2563eb);color:#fff;font-size:9px;font-weight:700">NA</div><div class="h-info"><div class="h-name">Nguyễn Văn An</div><div class="h-action">Cập nhật nội dung bài viết</div></div><div class="h-time">24/05/2025 10:28</div></li>
                    <li><div class="h-avatar" style="background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;font-size:9px;font-weight:700">TN</div><div class="h-info"><div class="h-name">Trần Thị Bích Ngọc</div><div class="h-action">Chỉnh sửa tiêu đề và mô tả</div></div><div class="h-time">24/05/2025 09:48</div></li>
                    <li><div class="h-avatar" style="background:linear-gradient(135deg,#8b5cf6,#7c3aed);color:#fff;font-size:9px;font-weight:700">LĐ</div><div class="h-info"><div class="h-name">Lê Minh Đức</div><div class="h-action">Tạo bài viết (bản nháp)</div></div><div class="h-time">24/05/2025 09:21</div></li>
                </ul>
            </div>
        </aside>
    </div>
</div>
@endsection
