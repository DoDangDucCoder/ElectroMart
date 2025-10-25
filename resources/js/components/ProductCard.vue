<template>
  <div class="product-card">
    <div class="product-image-container">
      <img 
        :src="product.thumbnail_url || '/images/placeholder.jpg'" 
        :alt="product.name"
        class="product-image"
        @error="handleImageError"
      />
      <div class="product-badges">
        <span v-if="product.is_featured" class="badge badge-featured">Featured</span>
        <span v-if="product.sale_price" class="badge badge-sale">
          {{ calculateDiscountPercentage() }}% OFF
        </span>
      </div>
      <div class="product-actions">
        <button @click="addToCart" class="btn btn-primary btn-sm" :disabled="!product.is_in_stock">
          <i class="fas fa-shopping-cart"></i>
        </button>
        <button @click="toggleWishlist" class="btn btn-outline-secondary btn-sm">
          <i :class="isInWishlist ? 'fas fa-heart text-danger' : 'far fa-heart'"></i>
        </button>
        <button @click="quickView" class="btn btn-outline-secondary btn-sm">
          <i class="fas fa-eye"></i>
        </button>
      </div>
    </div>
    
    <div class="product-info">
      <h5 class="product-name">
        <router-link :to="`/products/${product.slug}`" class="text-decoration-none">
          {{ product.name }}
        </router-link>
      </h5>
      
      <div class="product-rating" v-if="product.average_rating">
        <div class="stars">
          <i 
            v-for="star in 5" 
            :key="star"
            :class="star <= product.average_rating ? 'fas fa-star text-warning' : 'far fa-star text-muted'"
          ></i>
        </div>
        <span class="rating-count">({{ product.reviews_count }})</span>
      </div>
      
      <div class="product-price">
        <span v-if="product.sale_price" class="current-price">
          {{ formatPrice(product.sale_price) }}
        </span>
        <span v-if="product.sale_price" class="original-price">
          {{ formatPrice(product.price) }}
        </span>
        <span v-else class="current-price">
          {{ formatPrice(product.price) }}
        </span>
      </div>
      
      <div class="product-stock" v-if="!product.is_in_stock">
        <span class="text-danger">Out of Stock</span>
      </div>
      <div class="product-stock" v-else-if="product.is_low_stock">
        <span class="text-warning">Only {{ product.stock_quantity }} left</span>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed } from 'vue'
import { useCartStore } from '@/stores/cart'
import { useWishlistStore } from '@/stores/wishlist'

export default {
  name: 'ProductCard',
  props: {
    product: {
      type: Object,
      required: true
    }
  },
  setup(props) {
    const cartStore = useCartStore()
    const wishlistStore = useWishlistStore()
    
    const isInWishlist = computed(() => 
      wishlistStore.items.some(item => item.id === props.product.id)
    )
    
    const calculateDiscountPercentage = () => {
      if (!props.product.sale_price) return 0
      return Math.round(((props.product.price - props.product.sale_price) / props.product.price) * 100)
    }
    
    const formatPrice = (price) => {
      return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
      }).format(price)
    }
    
    const addToCart = async () => {
      try {
        await cartStore.addItem(props.product.id, 1)
        // Show success notification
        this.$toast.success('Product added to cart!')
      } catch (error) {
        this.$toast.error('Failed to add product to cart')
      }
    }
    
    const toggleWishlist = () => {
      if (isInWishlist.value) {
        wishlistStore.removeItem(props.product.id)
        this.$toast.info('Removed from wishlist')
      } else {
        wishlistStore.addItem(props.product)
        this.$toast.success('Added to wishlist')
      }
    }
    
    const quickView = () => {
      // Emit event to parent component
      this.$emit('quick-view', props.product)
    }
    
    const handleImageError = (event) => {
      event.target.src = '/images/placeholder.jpg'
    }
    
    return {
      isInWishlist,
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
.product-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
  overflow: hidden;
  height: 100%;
}

.product-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.product-image-container {
  position: relative;
  overflow: hidden;
  aspect-ratio: 1;
}

.product-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.product-card:hover .product-image {
  transform: scale(1.05);
}

.product-badges {
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

.product-actions {
  position: absolute;
  top: 10px;
  right: 10px;
  opacity: 0;
  transition: opacity 0.3s ease;
  z-index: 2;
}

.product-card:hover .product-actions {
  opacity: 1;
}

.product-actions .btn {
  margin-left: 5px;
  border-radius: 50%;
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.product-info {
  padding: 16px;
}

.product-name {
  font-size: 1rem;
  font-weight: 600;
  margin-bottom: 8px;
  line-height: 1.4;
}

.product-name a {
  color: #333;
  transition: color 0.3s ease;
}

.product-name a:hover {
  color: #007bff;
}

.product-rating {
  display: flex;
  align-items: center;
  margin-bottom: 8px;
}

.stars {
  margin-right: 8px;
}

.rating-count {
  font-size: 0.875rem;
  color: #666;
}

.product-price {
  margin-bottom: 8px;
}

.current-price {
  font-size: 1.125rem;
  font-weight: 700;
  color: #e74c3c;
}

.original-price {
  font-size: 0.875rem;
  color: #999;
  text-decoration: line-through;
  margin-left: 8px;
}

.product-stock {
  font-size: 0.875rem;
  font-weight: 500;
}

@media (max-width: 768px) {
  .product-card {
    margin-bottom: 16px;
  }
  
  .product-info {
    padding: 12px;
  }
  
  .product-name {
    font-size: 0.9rem;
  }
  
  .current-price {
    font-size: 1rem;
  }
}
</style>
