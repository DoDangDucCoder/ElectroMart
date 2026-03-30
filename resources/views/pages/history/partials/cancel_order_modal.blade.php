<div id="cancelOrderModal" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <form>
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Hủy đơn hàng <span id="cancel-order-code-preview"></span></h4>
                </div>
                <div class="modal-body">
                    <label class="shopw-order-cancel-label" for="cancel-order-reason">Lý do hủy đơn hàng</label>
                    <textarea id="cancel-order-reason" rows="5" class="lydohuydon form-control"
                        required placeholder="Vui lòng nhập lý do hủy đơn hàng..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
                    <button type="button" id="cancel-order-submit" data-order-code=""
                        class="btn btn-danger">
                        Gửi yêu cầu hủy
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

