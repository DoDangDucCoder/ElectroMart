<!-- Modal so sanh -->
<div class="modal fade" id="sosanh" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">{{ config('storefront.labels.compare_products', 'So sánh sản phẩm') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-striped" id="row_compare">
                    <thead>
                        <tr>
                            <th scope="col">Sản phẩm</th>
                            <th scope="col">Giá bán</th>
                            <th scope="col">Hình ảnh</th>
                            <th scope="col">Gợi ý</th>
                            <th scope="col">Chi tiết</th>
                            <th scope="col">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>



                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="openCompareDetail()">So sánh chi tiết</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>

            </div>
        </div>
    </div>
</div>
