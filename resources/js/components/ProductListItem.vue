<template>
  <div class="product-list-item">
    <div class="item-image">
      <img 
        :src="product.thumbnail_url || '/images/placeholder.jpg'" 
        :alt="product.name"
        @error="handleImageError"
      />
      <div class="item-badges">
        <span v-if="product.is_featured" class="badge badge-featured">Nổi bật</span>
        <span v-if="hasSale" class="badge badge-sale">
          {{ saleBadgeText }}
        </span>
      </div>
    </div>
    
    <div class="item-content">
      <div class="item-header">
        <h5 class="item-name">
          <a :href="productUrl" class="text-decoration-none">
            {{ product.name }}
          </a>
        </h5>
        
        <div class="item-rating" v-if="product.average_rating">
          <div class="stars">
            <i 
              v-for="star in 5" 
              :key="star"
              :class="star <= product.average_rating ? 'fas fa-star text-warning' : 'far fa-star text-muted'"
            ></i>
          </div>
          <span class="rating-count">({{ product.reviews_count }})</span>
        </div>
      </div>
      
      <div class="item-description" v-if="product.description">
        <p>{{ product.description.substring(0, 150) }}{{ product.description.length > 150 ? '...' : '' }}</p>
      </div>
      
      <div class="item-specs" v-if="product.specifications && product.specifications.length > 0">
        <div class="spec-item" v-for="spec in product.specifications.slice(0, 3)" :key="spec.name">
          <span class="spec-name">{{ spec.name }}:</span>
          <span class="spec-value">{{ spec.value }}</span>
        </div>
      </div>
      
      <div class="item-footer">
        <div class="item-price">
          <span v-if="hasSale" class="current-price">
            {{ formatPrice(displayPrice) }}
          </span>
          <span v-if="hasSale" class="original-price">
            {{ formatPrice(product.price) }}
          </span>
          <span v-else class="current-price">
            {{ formatPrice(displayPrice) }}
          </span>
        </div>
        <div v-if="hasSale && product.sale_explanation" class="item-sale-note">
          {{ product.sale_explanation }}
        </div>
        
        <div class="item-stock" v-if="!product.is_in_stock">
          <span class="text-danger">Hết hàng</span>
        </div>
        <div class="item-stock" v-else-if="product.is_low_stock">
          <span class="text-warning">Chỉ còn {{ product.stock_quantity }} sản phẩm</span>
        </div>
        
        <div class="item-actions">
          <button 
            @click="addToCart" 
            class="btn btn-primary"
            :disabled="!product.is_in_stock"
          >
            <i class="fas fa-shopping-cart"></i>
            Thêm vào giỏ
          </button>
          
          <button @click="toggleWishlist" class="btn btn-outline-secondary">
            <i :class="isInWishlist ? 'fas fa-heart text-danger' : 'far fa-heart'"></i>
          </button>
          
          <button @click="quickView" class="btn btn-outline-secondary">
            <i class="fas fa-eye"></i>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { computed } from 'vue'
import { useToast } from 'vue-toastification'
import { useCartStore } from '@/stores/cart'
import { useWishlistStore } from '@/stores/wishlist'

export default {
  name: 'ProductListItem',
  props: {
    product: {
      type: Object,
      required: true
    }
  },
  emits: ['quick-view'],
  setup(props, { emit }) {
    const cartStore = useCartStore()
    const wishlistStore = useWishlistStore()
    const toast = useToast()
    
    const isInWishlist = computed(() => 
      wishlistStore.items.some(item => item.id === props.product.id)
    )
    
    const hasSale = computed(() =>
      Number(props.product.sale_amount || 0) > 0 || Number(props.product.sale_price || 0) > 0
    )

    const displayPrice = computed(() => props.product.final_price || props.product.sale_price || props.product.price)

    const productUrl = computed(() => `/chi-tiet/${props.product.slug}`)

    const calculateDiscountPercentage = () => {
      if (!hasSale.value || !props.product.price) return 0
      return Math.round(((props.product.price - displayPrice.value) / props.product.price) * 100)
    }

    const saleBadgeText = computed(() => {
      if (!hasSale.value) return ''

      if (props.product.sale_type === 'fixed' && props.product.sale_value) {
        return `-${formatPrice(props.product.sale_value)}`
      }

      return `-${calculateDiscountPercentage()}%`
    })
    
    const formatPrice = (price) => {
      return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
      }).format(price)
    }
    
    const addToCart = async () => {
      try {
        await cartStore.addItem(props.product.id, 1)
        toast.success('Đã thêm sản phẩm vào giỏ hàng!')
      } catch (error) {
        toast.error('Không thể thêm sản phẩm vào giỏ hàng')
      }
    }
    
    const toggleWishlist = () => {
      if (isInWishlist.value) {
        wishlistStore.removeItem(props.product.id)
        toast.info('Đã xóa khỏi danh sách yêu thích')
      } else {
        wishlistStore.addItem(props.product)
        toast.success('Đã thêm vào danh sách yêu thích')
      }
    }
    
    const quickView = () => {
      emit('quick-view', props.product)
    }
    
    const handleImageError = (event) => {
      event.target.src = '/images/placeholder.jpg'
    }
    
    return {
      isInWishlist,
      hasSale,
      displayPrice,
      productUrl,
      saleBadgeText,
      calculateDiscountPercentage,
      formatPrice,
      addToCart,
      toggleWishlist,
      quickView,
      handleImageError
    }
  }
}
</script>

<style scoped>
.product-list-item {
  display: flex;
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  transition: all 0.3s ease;
  margin-bottom: 20px;
}

.product-list-item:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.item-image {
  position: relative;
  width: 200px;
  height: 200px;
  flex-shrink: 0;
}

.item-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.item-badges {
  position: absolute;
  top: 10px;
  left: 10px;
  z-index: 2;
}

.badge {
  display: inline-block;
  padding: 4px 8px;
  font-size: 0.75rem;
  font-weight: 600;
  border-radius: 4px;
  margin-right: 5px;
}

.badge-featured {
  background: linear-gradient(45deg, #ff6b6b, #ff8e8e);
  color: white;
}

.badge-sale {
  background: linear-gradient(45deg, #4ecdc4, #44a08d);
  color: white;
}

.item-content {
  flex: 1;
  padding: 20px;
  display: flex;
  flex-direction: column;
}

.item-header {
  margin-bottom: 15px;
}

.item-name {
  margin: 0 0 8px 0;
  font-size: 1.25rem;
  font-weight: 600;
}

.item-name a {
  color: #333;
  transition: color 0.3s ease;
}

.item-name a:hover {
  color: #007bff;
}

.item-rating {
  display: flex;
  align-items: center;
}

.stars {
  margin-right: 8px;
}

.rating-count {
  font-size: 0.875rem;
  color: #666;
}

.item-description {
  margin-bottom: 15px;
  color: #666;
  line-height: 1.5;
}

.item-specs {
  margin-bottom: 20px;
}

.spec-item {
  display: flex;
  margin-bottom: 5px;
  font-size: 0.9rem;
}

.spec-name {
  font-weight: 600;
  color: #333;
  margin-right: 8px;
  min-width: 80px;
}

.spec-value {
  color: #666;
}

.item-footer {
  margin-top: auto;
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 15px;
}

.item-price {
  display: flex;
  align-items: center;
  gap: 10px;
}

.current-price {
  font-size: 1.25rem;
  font-weight: 700;
  color: #e74c3c;
}

.original-price {
  font-size: 1rem;
  color: #999;
  text-decoration: line-through;
}

.item-stock {
  font-size: 0.875rem;
  font-weight: 500;
}

.item-actions {
  display: flex;
  gap: 10px;
}

.item-actions .btn {
  display: flex;
  align-items: center;
  gap: 5px;
}

.item-sale-note {
  color: #64748b;
  font-size: 0.8rem;
  line-height: 1.5;
}

@media (max-width: 768px) {
  .product-list-item {
    flex-direction: column;
  }
  
  .item-image {
    width: 100%;
    height: 250px;
  }
  
  .item-content {
    padding: 15px;
  }
  
  .item-footer {
    flex-direction: column;
    align-items: stretch;
  }
  
  .item-actions {
    justify-content: center;
  }
}

@media (max-width: 480px) {
  .item-name {
    font-size: 1.1rem;
  }
  
  .current-price {
    font-size: 1.1rem;
  }
  
  .item-actions {
    flex-direction: column;
  }
  
  .item-actions .btn {
    justify-content: center;
  }
}
</style>
