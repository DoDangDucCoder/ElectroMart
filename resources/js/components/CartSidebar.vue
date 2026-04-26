<template>
  <div class="cart-sidebar" :class="{ 'show': isOpen }">
    <div class="cart-overlay" @click="closeCart"></div>
    
    <div class="cart-content">
      <div class="cart-header">
        <h5 class="cart-title">
          <i class="fas fa-shopping-cart"></i>
          Giỏ hàng ({{ totalItems }})
        </h5>
        <button @click="closeCart" class="btn-close">
          <i class="fas fa-times"></i>
        </button>
      </div>
      
      <div class="cart-body" v-if="items.length > 0">
        <div class="cart-items">
          <div 
            v-for="item in items" 
            :key="item.id"
            class="cart-item"
          >
            <div class="item-image">
              <img 
                :src="item.product_image || '/images/placeholder.jpg'" 
                :alt="item.product_name"
                @error="handleImageError"
              />
            </div>
            
            <div class="item-details">
              <h6 class="item-name">{{ item.product_name }}</h6>
              <div class="item-price">{{ formatPrice(item.price) }}</div>
              <div class="item-quantity">
                <button 
                  @click="updateQuantity(item.id, item.quantity - 1)"
                  class="btn-quantity"
                  :disabled="item.quantity <= 1"
                >
                  <i class="fas fa-minus"></i>
                </button>
                <span class="quantity">{{ item.quantity }}</span>
                <button 
                  @click="updateQuantity(item.id, item.quantity + 1)"
                  class="btn-quantity"
                >
                  <i class="fas fa-plus"></i>
                </button>
              </div>
            </div>
            
            <div class="item-actions">
              <div class="item-subtotal">{{ formatPrice(item.subtotal) }}</div>
              <button 
                @click="removeItem(item.id)"
                class="btn-remove"
            title="Xóa sản phẩm"
              >
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </div>
        </div>
        
        <div class="cart-summary">
          <div class="summary-row">
            <span>Tạm tính:</span>
            <span>{{ formatPrice(subtotal) }}</span>
          </div>
          <div class="summary-row" v-if="shipping_fee > 0">
            <span>Phí vận chuyển:</span>
            <span>{{ formatPrice(shipping_fee) }}</span>
          </div>
          <div class="summary-row" v-if="discount_amount > 0">
            <span>Giảm giá:</span>
            <span class="text-success">-{{ formatPrice(discount_amount) }}</span>
          </div>
          <div class="summary-row total">
            <span>Tổng cộng:</span>
            <span>{{ formatPrice(total) }}</span>
          </div>
        </div>
      </div>
      
      <div class="cart-empty" v-else>
        <div class="empty-icon">
          <i class="fas fa-shopping-cart"></i>
        </div>
        <p>Giỏ hàng của bạn đang trống</p>
        <button @click="closeCart" class="btn btn-primary">
          Tiếp tục mua sắm
        </button>
      </div>
      
      <div class="cart-footer" v-if="items.length > 0">
        <button @click="viewCart" class="btn btn-outline-primary btn-block">
          Xem giỏ hàng
        </button>
        <button @click="checkout" class="btn btn-primary btn-block">
          Thanh toán
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { computed } from 'vue'
import { useCartStore } from '@/stores/cart'

export default {
  name: 'CartSidebar',
  props: {
    isOpen: {
      type: Boolean,
      default: false
    }
  },
  emits: ['close'],
  setup(props, { emit }) {
    const cartStore = useCartStore()
    
    const items = computed(() => cartStore.items)
    const totalItems = computed(() => cartStore.totalItems)
    const subtotal = computed(() => cartStore.subtotal)
    const shipping_fee = computed(() => cartStore.shipping_fee)
    const discount_amount = computed(() => cartStore.discount_amount)
    const total = computed(() => cartStore.total)
    
    const closeCart = () => {
      emit('close')
    }
    
    const updateQuantity = async (itemId, newQuantity) => {
      if (newQuantity < 1) return
      
      try {
        await cartStore.updateItem(itemId, newQuantity)
      } catch (error) {
        console.error('Không thể cập nhật số lượng:', error)
      }
    }
    
    const removeItem = async (itemId) => {
      try {
        await cartStore.removeItem(itemId)
      } catch (error) {
        console.error('Không thể xóa sản phẩm:', error)
      }
    }
    
    const viewCart = () => {
      closeCart()
      window.location.assign('/gio-hang')
    }
    
    const checkout = () => {
      closeCart()
      window.location.assign('/checkout')
    }
    
    const formatPrice = (price) => {
      return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
      }).format(price)
    }
    
    const handleImageError = (event) => {
      event.target.src = '/images/placeholder.jpg'
    }
    
    return {
      items,
      totalItems,
      subtotal,
      shipping_fee,
      discount_amount,
      total,
      closeCart,
      updateQuantity,
      removeItem,
      viewCart,
      checkout,
      formatPrice,
      handleImageError
    }
  }
}
</script>

<style scoped>
.cart-sidebar {
  position: fixed;
  top: 0;
  right: -100%;
  width: 100%;
  max-width: 400px;
  height: 100vh;
  z-index: 1050;
  transition: right 0.3s ease;
}

.cart-sidebar.show {
  right: 0;
}

.cart-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background: rgba(0, 0, 0, 0.5);
  z-index: -1;
}

.cart-content {
  background: white;
  height: 100%;
  display: flex;
  flex-direction: column;
  box-shadow: -2px 0 10px rgba(0, 0, 0, 0.1);
}

.cart-header {
  padding: 20px;
  border-bottom: 1px solid #eee;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.cart-title {
  margin: 0;
  font-size: 1.25rem;
  font-weight: 600;
}

.cart-title i {
  margin-right: 8px;
  color: #007bff;
}

.btn-close {
  background: none;
  border: none;
  font-size: 1.25rem;
  color: #666;
  cursor: pointer;
  padding: 0;
  width: 30px;
  height: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-close:hover {
  color: #333;
}

.cart-body {
  flex: 1;
  overflow-y: auto;
  padding: 20px;
}

.cart-items {
  margin-bottom: 20px;
}

.cart-item {
  display: flex;
  align-items: center;
  padding: 15px 0;
  border-bottom: 1px solid #f0f0f0;
}

.cart-item:last-child {
  border-bottom: none;
}

.item-image {
  width: 60px;
  height: 60px;
  margin-right: 15px;
  border-radius: 8px;
  overflow: hidden;
}

.item-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.item-details {
  flex: 1;
  margin-right: 15px;
}

.item-name {
  font-size: 0.9rem;
  font-weight: 600;
  margin-bottom: 5px;
  line-height: 1.3;
}

.item-price {
  font-size: 0.85rem;
  color: #666;
  margin-bottom: 8px;
}

.item-quantity {
  display: flex;
  align-items: center;
  gap: 8px;
}

.btn-quantity {
  width: 24px;
  height: 24px;
  border: 1px solid #ddd;
  background: white;
  border-radius: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.75rem;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-quantity:hover:not(:disabled) {
  background: #f8f9fa;
  border-color: #007bff;
}

.btn-quantity:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.quantity {
  font-size: 0.9rem;
  font-weight: 600;
  min-width: 20px;
  text-align: center;
}

.item-actions {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 8px;
}

.item-subtotal {
  font-size: 0.9rem;
  font-weight: 600;
  color: #e74c3c;
}

.btn-remove {
  background: none;
  border: none;
  color: #dc3545;
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  transition: background 0.2s ease;
}

.btn-remove:hover {
  background: #f8d7da;
}

.cart-summary {
  background: #f8f9fa;
  padding: 15px;
  border-radius: 8px;
  margin-bottom: 20px;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 8px;
  font-size: 0.9rem;
}

.summary-row.total {
  font-weight: 700;
  font-size: 1rem;
  border-top: 1px solid #dee2e6;
  padding-top: 8px;
  margin-top: 8px;
}

.cart-empty {
  text-align: center;
  padding: 40px 20px;
}

.empty-icon {
  font-size: 3rem;
  color: #ccc;
  margin-bottom: 20px;
}

.cart-empty p {
  color: #666;
  margin-bottom: 20px;
}

.cart-footer {
  padding: 20px;
  border-top: 1px solid #eee;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.btn-block {
  width: 100%;
}

@media (max-width: 480px) {
  .cart-sidebar {
    max-width: 100%;
  }
  
  .cart-item {
    padding: 10px 0;
  }
  
  .item-image {
    width: 50px;
    height: 50px;
  }
  
  .item-name {
    font-size: 0.85rem;
  }
}
</style>
