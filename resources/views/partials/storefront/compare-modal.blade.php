<style>
    @media (max-width: 767px) {
        #sosanh .modal-dialog {
            width: calc(100% - 20px);
            margin: 10px auto;
        }

        #sosanh .modal-header,
        #sosanh .modal-footer {
            padding: 12px 14px !important;
        }

        #sosanh #row_compare th,
        #sosanh #row_compare td {
            padding: 10px 12px !important;
            white-space: nowrap;
        }
    }
</style>

<!-- Modal so sanh -->
<div class="modal fade" id="sosanh" tabindex="-1" role="dialog" aria-labelledby="shopwCompareTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content" style="border-radius: 20px; overflow: hidden; border: none; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">
            <div class="modal-header" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 20px 24px;">
                <h5 class="modal-title" id="shopwCompareTitle" style="font-weight: 800; color: #0f172a; font-size: 18px;">{{ config('storefront.labels.compare_products', 'So sánh sản phẩm') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Đóng" style="opacity: 0.5;">
                    <span aria-hidden="true" style="font-size: 28px;">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="table" id="row_compare" style="margin-bottom: 0;">
                        <thead style="background: #f1f5f9;">
                            <tr>
                                <th scope="col" style="border: none; padding: 15px 24px; color: #64748b; font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em;">Sản phẩm</th>
                                <th scope="col" style="border: none; padding: 15px 24px; color: #64748b; font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em;">Giá bán</th>
                                <th scope="col" style="border: none; padding: 15px 24px; color: #64748b; font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em;">Hình ảnh</th>
                                <th scope="col" style="border: none; padding: 15px 24px; color: #64748b; font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em;">Trạng thái</th>
                                <th scope="col" style="border: none; padding: 15px 24px; color: #64748b; font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em;">Chi tiết</th>
                                <th scope="col" style="border: none; padding: 15px 24px; color: #64748b; font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em;">Xóa</th>
                            </tr>
                        </thead>
                    <tbody>



                    </tbody>
                </table>
            </div>
            <div class="modal-footer" style="background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 16px 24px;">
                <button type="button" class="btn btn-primary" onclick="openCompareDetail()" style="border-radius: 10px; font-weight: 800; padding: 10px 25px; background: #00bcd4; border: none; box-shadow: 0 4px 12px rgba(0, 188, 212, 0.25);">So sánh chi tiết</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 10px; font-weight: 700; padding: 10px 20px; background: #fff; border: 1px solid #e2e8f0; color: #64748b;">Đóng</button>
            </div>
        </div>
    </div>
</div>
