<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kho ảnh CKEditor</title>
    <style>
        :root {
            --browser-bg: #f4f7fb;
            --browser-surface: #ffffff;
            --browser-border: #d8e1ee;
            --browser-text: #19324a;
            --browser-muted: #65778c;
            --browser-accent: #0b84fe;
            --browser-accent-soft: #e7f2ff;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: "Segoe UI", Arial, sans-serif;
            background: var(--browser-bg);
            color: var(--browser-text);
        }

        .browser-shell {
            max-width: 1080px;
            margin: 0 auto;
            padding: 24px;
        }

        .browser-hero {
            display: flex;
            justify-content: space-between;
            gap: 24px;
            align-items: flex-start;
            margin-bottom: 24px;
        }

        .browser-hero h1 {
            margin: 0 0 8px;
            font-size: 28px;
            line-height: 1.2;
        }

        .browser-hero p {
            margin: 0;
            color: var(--browser-muted);
            max-width: 680px;
            line-height: 1.6;
        }

        .browser-stat {
            min-width: 180px;
            padding: 16px 18px;
            border: 1px solid var(--browser-border);
            border-radius: 16px;
            background: var(--browser-surface);
            box-shadow: 0 10px 24px rgba(18, 38, 63, 0.06);
        }

        .browser-stat strong {
            display: block;
            font-size: 28px;
            margin-bottom: 4px;
        }

        .browser-stat span {
            color: var(--browser-muted);
            font-size: 14px;
        }

        .browser-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 16px;
        }

        .browser-card {
            width: 100%;
            border: 1px solid var(--browser-border);
            border-radius: 18px;
            background: var(--browser-surface);
            padding: 14px;
            text-align: left;
            box-shadow: 0 10px 24px rgba(18, 38, 63, 0.06);
            transition: transform .16s ease, border-color .16s ease, box-shadow .16s ease;
            cursor: pointer;
        }

        .browser-card:hover,
        .browser-card:focus-visible {
            transform: translateY(-2px);
            border-color: var(--browser-accent);
            box-shadow: 0 16px 28px rgba(11, 132, 254, 0.12);
            outline: none;
        }

        .browser-preview {
            aspect-ratio: 4 / 3;
            border-radius: 12px;
            overflow: hidden;
            background: #eef3f9;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
        }

        .browser-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .browser-card h2 {
            margin: 0 0 8px;
            font-size: 15px;
            line-height: 1.4;
            overflow-wrap: anywhere;
        }

        .browser-meta {
            margin: 0;
            padding: 0;
            list-style: none;
            display: grid;
            gap: 6px;
            color: var(--browser-muted);
            font-size: 13px;
        }

        .browser-help {
            margin: 0 0 20px;
            padding: 14px 16px;
            border-radius: 14px;
            border: 1px solid var(--browser-border);
            background: var(--browser-accent-soft);
            color: var(--browser-text);
        }

        .browser-empty {
            padding: 40px 24px;
            border: 1px dashed var(--browser-border);
            border-radius: 18px;
            background: var(--browser-surface);
            text-align: center;
            color: var(--browser-muted);
        }

        @media (max-width: 640px) {
            .browser-shell {
                padding: 16px;
            }

            .browser-hero {
                flex-direction: column;
            }

            .browser-stat {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <main class="browser-shell" data-file-browser data-func-num="{{ $funcNum }}">
        <section class="browser-hero">
            <div>
                <h1>Kho ảnh CKEditor</h1>
                <p>Chọn một ảnh để chèn trực tiếp vào nội dung. Ảnh mới tải lên sẽ xuất hiện trong danh sách này.</p>
            </div>
            <div class="browser-stat">
                <strong>{{ $files->count() }}</strong>
                <span>Ảnh đang sẵn sàng</span>
            </div>
        </section>

        <p class="browser-help">Bấm vào ảnh muốn dùng. Cửa sổ này sẽ tự đóng sau khi chèn thành công.</p>

        @if ($files->isEmpty())
            <div class="browser-empty">
                Chưa có ảnh nào trong thư mục `uploads/ckeditor`. Hãy tải ảnh lên từ trình soạn thảo trước.
            </div>
        @else
            <section class="browser-grid" id="image_list">
                @foreach ($files as $file)
                    <button type="button" class="browser-card" data-file-url="{{ $file['url'] }}">
                        <div class="browser-preview">
                            <img src="{{ $file['url'] }}" alt="{{ $file['name'] }}">
                        </div>
                        <h2>{{ $file['name'] }}</h2>
                        <ul class="browser-meta">
                            <li>Kích thước: {{ $file['size_label'] }}</li>
                            <li>Cập nhật: {{ $file['updated_label'] }}</li>
                        </ul>
                    </button>
                @endforeach
            </section>
        @endif
    </main>

    <script>
        (function() {
            var browserShell = document.querySelector('[data-file-browser]');

            if (!browserShell) {
                return;
            }

            var funcNum = Number(browserShell.getAttribute('data-func-num') || 0);

            browserShell.addEventListener('click', function(event) {
                var button = event.target.closest('[data-file-url]');

                if (!button) {
                    return;
                }

                var fileUrl = button.getAttribute('data-file-url');

                if (window.opener && window.opener.CKEDITOR && window.opener.CKEDITOR.tools) {
                    window.opener.CKEDITOR.tools.callFunction(funcNum, fileUrl);
                    window.close();
                }
            });
        })();
    </script>
</body>

</html>
