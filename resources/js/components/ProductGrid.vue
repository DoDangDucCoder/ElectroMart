<template>
  <div class="product-grid">
    <div class="grid-header" v-if="showHeader">
      <div class="grid-title">
        <h3>{{ title }}</h3>
        <p v-if="subtitle" class="text-muted">{{ subtitle }}</p>
      </div>
      <div class="grid-actions" v-if="showViewToggle">
        <div class="view-toggle">
          <button 
            @click="viewMode = 'grid'"
            :class="{ active: viewMode === 'grid' }"
            class="btn-view"
            title="Xem dạng lưới"
          >
            <i class="fas fa-th"></i>
          </button>
          <button 
            @click="viewMode = 'list'"
            :class="{ active: viewMode === 'list' }"
            class="btn-view"
            title="Xem dạng danh sách"
          >
            <i class="fas fa-list"></i>
          </button>
        </div>
      </div>
    </div>
    
    <div class="grid-content">
      <div 
        v-if="loading" 
        class="loading-state"
      >
        <div class="spinner"></div>
        <p>Đang tải sản phẩm...</p>
      </div>
      
      <div 
        v-else-if="products.length === 0" 
        class="empty-state"
      >
        <div class="empty-icon">
          <i class="fas fa-box-open"></i>
        </div>
        <h4>Không tìm thấy sản phẩm</h4>
        <p>{{ emptyMessage || 'Hãy thử điều chỉnh từ khóa tìm kiếm hoặc bộ lọc.' }}</p>
        <button @click="$emit('refresh')" class="btn btn-primary">
          Tải lại
        </button>
      </div>
      
      <div 
        v-else
        :class="[
          'products-container',
          viewMode === 'grid' ? 'grid-view' : 'list-view'
        ]"
      >
        <div 
          v-for="product in products" 
          :key="product.id"
          :class="[
            'product-item',
            viewMode === 'grid' ? 'product-card' : 'product-list-item'
          ]"
        >
          <ProductCard 
            v-if="viewMode === 'grid'"
            :product="product"
            @quick-view="$emit('quick-view', $event)"
          />
          
          <ProductListItem 
            v-else
            :product="product"
            @quick-view="$emit('quick-view', $event)"
          />
        </div>
      </div>
    </div>
    
    <div class="grid-footer" v-if="showPagination && pagination">
      <Pagination 
        :current-page="pagination.current_page"
        :last-page="pagination.last_page"
        :total="pagination.total"
        @page-change="$emit('page-change', $event)"
      />
    </div>
  </div>
</template>

<script>
import { ref, computed } from 'vue'
import ProductCard from './ProductCard.vue'
import ProductListItem from './ProductListItem.vue'
import Pagination from './Pagination.vue'

export default {
  name: 'ProductGrid',
  components: {
    ProductCard,
    ProductListItem,
    Pagination
  },
  props: {
    products: {
      type: Array,
      default: () => []
    },
    loading: {
      type: Boolean,
      default: false
    },
    title: {
      type: String,
      default: 'Sản phẩm'
    },
    subtitle: {
      type: String,
      default: ''
    },
    showHeader: {
      type: Boolean,
      default: true
    },
    showViewToggle: {
      type: Boolean,
      default: true
    },
    showPagination: {
      type: Boolean,
      default: true
    },
    pagination: {
      type: Object,
      default: null
    },
    emptyMessage: {
      type: String,
      default: ''
    }
  },
  emits: ['quick-view', 'page-change', 'refresh'],
  setup() {
    const viewMode = ref('grid')
    
    return {
      viewMode
    }
  }
}
</script>

<style scoped>
.product-grid {
  width: 100%;
}

.grid-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 30px;
}

.grid-title h3 {
  margin: 0 0 5px 0;
  font-size: 1.5rem;
  font-weight: 700;
  color: #333;
}

.grid-actions {
  display: flex;
  align-items: center;
  gap: 15px;
}

.view-toggle {
  display: flex;
  background: #f8f9fa;
  border-radius: 8px;
  padding: 4px;
}

.btn-view {
  padding: 8px 12px;
  border: none;
  background: transparent;
  color: #666;
  cursor: pointer;
  border-radius: 6px;
  transition: all 0.2s ease;
}

.btn-view:hover {
  background: #e9ecef;
  color: #333;
}

.btn-view.active {
  background: #007bff;
  color: white;
}

.grid-content {
  min-height: 200px;
}

.loading-state,
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px 20px;
  text-align: center;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #f3f3f3;
  border-top: 4px solid #007bff;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 20px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.empty-icon {
  font-size: 4rem;
  color: #ccc;
  margin-bottom: 20px;
}

.empty-state h4 {
  color: #666;
  margin-bottom: 10px;
}

.empty-state p {
  color: #999;
  margin-bottom: 20px;
}

.products-container {
  display: grid;
  gap: 20px;
}

.grid-view {
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
}

.list-view {
  grid-template-columns: 1fr;
}

.product-item {
  transition: transform 0.2s ease;
}

.product-item:hover {
  transform: translateY(-2px);
}

.grid-footer {
  margin-top: 40px;
  display: flex;
  justify-content: center;
}

@media (max-width: 768px) {
  .grid-header {
    flex-direction: column;
    gap: 15px;
  }
  
  .grid-view {
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 15px;
  }
  
  .grid-title h3 {
    font-size: 1.25rem;
  }
}

@media (max-width: 480px) {
  .grid-view {
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 10px;
  }
  
  .view-toggle {
    align-self: flex-start;
  }
}
</style>
