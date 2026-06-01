<script>
    (function() {
        function parseCurrency(value) {
            if (!value) {
                return 0;
            }

            const normalized = String(value).replace(/[^0-9]/g, '');
            return normalized ? parseFloat(normalized) : 0;
        }

        function parseDecimal(value) {
            if (!value) {
                return 0;
            }

            const normalized = String(value).replace(/[^0-9.,]/g, '').replace(',', '.');
            return normalized ? parseFloat(normalized) || 0 : 0;
        }

        function formatCurrency(value) {
            return new Intl.NumberFormat('vi-VN').format(Math.max(0, value || 0));
        }

        function buildExplanation(price, saleType, saleValue, finalPrice) {
            if (!saleType || !saleValue || saleValue <= 0) {
                return 'Không áp dụng sale cho sản phẩm này.';
            }

            if (saleType === 'percent') {
                return 'Giá sau giảm = ' + formatCurrency(price) + 'đ - ' + saleValue + '% = ' + formatCurrency(finalPrice) + 'đ';
            }

            return 'Giá sau giảm = ' + formatCurrency(price) + 'đ - ' + formatCurrency(saleValue) + 'đ = ' + formatCurrency(finalPrice) + 'đ';
        }

        function updatePricingPreview() {
            const priceInput = document.getElementById('product_price');
            const costInput = document.getElementById('price_cost');
            const saleTypeInput = document.getElementById('sale_type');
            const saleValueInput = document.getElementById('sale_value');
            const salePricePreview = document.getElementById('sale_price_preview');
            const profitPreview = document.getElementById('profit_preview');
            const explanation = document.getElementById('sale_explanation_preview');
            const profitExplanation = document.getElementById('profit_explanation_preview');

            if (!priceInput || !saleTypeInput || !saleValueInput || !salePricePreview || !profitPreview || !explanation) {
                return;
            }

            const price = parseCurrency(priceInput.value);
            const cost = parseCurrency(costInput ? costInput.value : 0);
            const saleType = saleTypeInput.value || '';
            const saleValue = saleType === 'percent' ? parseDecimal(saleValueInput.value) : parseCurrency(saleValueInput.value);

            let saleAmount = 0;

            if (saleType === 'percent' && saleValue > 0) {
                saleAmount = Math.min(price, (price * Math.min(100, saleValue)) / 100);
            } else if (saleType === 'fixed' && saleValue > 0) {
                saleAmount = Math.min(price, saleValue);
            }

            const finalPrice = Math.max(0, price - saleAmount);
            const profit = finalPrice - cost;

            salePricePreview.value = formatCurrency(finalPrice);
            profitPreview.value = formatCurrency(profit);
            explanation.textContent = buildExplanation(price, saleType, saleValue, finalPrice);

            if (profitExplanation) {
                if (cost <= 0) {
                    profitExplanation.textContent = 'Giá nhập là bắt buộc để xác định vốn và lợi nhuận.';
                    profitExplanation.style.color = '#b91c1c';
                } else if (profit <= 0) {
                    profitExplanation.textContent = 'Cảnh báo: giá nhập đang lớn hơn hoặc bằng giá sau sale, sản phẩm sẽ không có lãi.';
                    profitExplanation.style.color = '#b91c1c';
                } else {
                    profitExplanation.textContent = 'Lãi dự kiến = ' + formatCurrency(finalPrice) + 'đ - ' + formatCurrency(cost) + 'đ = ' + formatCurrency(profit) + 'đ.';
                    profitExplanation.style.color = '';
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            ['product_price', 'price_cost', 'sale_type', 'sale_value'].forEach(function(id) {
                const element = document.getElementById(id);
                if (!element) {
                    return;
                }

                element.addEventListener('input', updatePricingPreview);
                element.addEventListener('change', updatePricingPreview);
            });

            updatePricingPreview();
        });
    })();
</script>
