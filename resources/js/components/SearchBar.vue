<template>
  <div class="search-bar">
    <div class="search-container">
      <div class="search-input-wrapper">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search products..."
          class="search-input"
          @input="handleSearch"
          @focus="showSuggestions = true"
          @blur="hideSuggestions"
        />
        <button @click="performSearch" class="search-button">
          <i class="fas fa-search"></i>
        </button>
      </div>
      
      <div class="search-suggestions" v-if="showSuggestions && suggestions.length > 0">
        <div class="suggestion-categories">
          <h6 class="suggestion-title">Categories</h6>
          <div class="suggestion-items">
            <div 
              v-for="category in suggestions.slice(0, 3)" 
              :key="category.id"
              @click="selectSuggestion(category)"
              class="suggestion-item"
            >
              <i class="fas fa-tag"></i>
              {{ category.name }}
            </div>
          </div>
        </div>
        
        <div class="suggestion-products">
          <h6 class="suggestion-title">Products</h6>
          <div class="suggestion-items">
            <div 
              v-for="product in suggestions.slice(3, 6)" 
              :key="product.id"
              @click="selectSuggestion(product)"
              class="suggestion-item"
            >
              <img 
                :src="product.thumbnail_url || '/images/placeholder.jpg'" 
                :alt="product.name"
                class="suggestion-image"
              />
              <div class="suggestion-content">
                <div class="suggestion-name">{{ product.name }}</div>
                <div class="suggestion-price">{{ formatPrice(product.final_price) }}</div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="suggestion-footer">
          <button @click="viewAllResults" class="btn btn-link btn-sm">
            View all results for "{{ searchQuery }}"
          </button>
        </div>
      </div>
    </div>
    
    <div class="search-filters" v-if="showFilters">
      <div class="filter-group">
        <label>Category:</label>
        <select v-model="filters.category" @change="applyFilters">
          <option value="">All Categories</option>
          <option v-for="category in categories" :key="category.id" :value="category.slug">
            {{ category.name }}
          </option>
        </select>
      </div>
      
      <div class="filter-group">
        <label>Brand:</label>
        <select v-model="filters.brand" @change="applyFilters">
          <option value="">All Brands</option>
          <option v-for="brand in brands" :key="brand.id" :value="brand.slug">
            {{ brand.name }}
          </option>
        </select>
      </div>
      
      <div class="filter-group">
        <label>Price Range:</label>
        <div class="price-range">
          <input 
            v-model.number="filters.min_price" 
            type="number" 
            placeholder="Min"
            @change="applyFilters"
          />
          <span>-</span>
          <input 
            v-model.number="filters.max_price" 
            type="number" 
            placeholder="Max"
            @change="applyFilters"
          />
        </div>
      </div>
      
      <div class="filter-group">
        <label>Sort by:</label>
        <select v-model="filters.sort" @change="applyFilters">
          <option value="">Default</option>
          <option value="price_asc">Price: Low to High</option>
          <option value="price_desc">Price: High to Low</option>
          <option value="name_asc">Name: A to Z</option>
          <option value="name_desc">Name: Z to A</option>
          <option value="created_at_desc">Newest First</option>
        </select>
      </div>
      
      <button @click="clearFilters" class="btn btn-outline-secondary btn-sm">
        Clear Filters
      </button>
    </div>
  </div>
</template>

<script>
import { ref, reactive, computed, watch } from 'vue'
import { useRouter } from 'vue-router'
import { debounce } from 'lodash-es'

export default {
  name: 'SearchBar',
  props: {
    showFilters: {
      type: Boolean,
      default: false
    },
    categories: {
      type: Array,
      default: () => []
    },
    brands: {
      type: Array,
      default: () => []
    }
  },
  emits: ['search', 'filter'],
  setup(props, { emit }) {
    const router = useRouter()
    
    const searchQuery = ref('')
    const showSuggestions = ref(false)
    const suggestions = ref([])
    const isLoading = ref(false)
    
    const filters = reactive({
      category: '',
      brand: '',
      min_price: null,
      max_price: null,
      sort: ''
    })
    
    const performSearch = () => {
      if (!searchQuery.value.trim()) return
      
      router.push({
        name: 'products',
        query: {
          search: searchQuery.value,
          ...filters
        }
      })
      
      emit('search', {
        query: searchQuery.value,
        filters: { ...filters }
      })
      
      showSuggestions.value = false
    }
    
    const handleSearch = debounce(async () => {
      if (!searchQuery.value.trim()) {
        suggestions.value = []
        return
      }
      
      isLoading.value = true
      
      try {
        const response = await fetch(`/api/v1/products/search?q=${encodeURIComponent(searchQuery.value)}&limit=6`)
        const data = await response.json()
        
        if (data.success) {
          suggestions.value = data.data
        }
      } catch (error) {
        console.error('Search error:', error)
      } finally {
        isLoading.value = false
      }
    }, 300)
    
    const selectSuggestion = (item) => {
      if (item.slug) {
        // It's a product
        router.push(`/products/${item.slug}`)
      } else {
        // It's a category
        router.push(`/categories/${item.slug}`)
      }
      
      showSuggestions.value = false
    }
    
    const viewAllResults = () => {
      performSearch()
    }
    
    const hideSuggestions = () => {
      setTimeout(() => {
        showSuggestions.value = false
      }, 200)
    }
    
    const applyFilters = () => {
      emit('filter', { ...filters })
    }
    
    const clearFilters = () => {
      Object.keys(filters).forEach(key => {
        filters[key] = key.includes('price') ? null : ''
      })
      applyFilters()
    }
    
    const formatPrice = (price) => {
      return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
      }).format(price)
    }
    
    return {
      searchQuery,
      showSuggestions,
      suggestions,
      isLoading,
      filters,
      performSearch,
      handleSearch,
      selectSuggestion,
      viewAllResults,
      hideSuggestions,
      applyFilters,
      clearFilters,
      formatPrice
    }
  }
}
</script>

<style scoped>
.search-bar {
  position: relative;
  width: 100%;
}

.search-container {
  position: relative;
}

.search-input-wrapper {
  display: flex;
  background: white;
  border-radius: 25px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.search-input {
  flex: 1;
  padding: 12px 20px;
  border: none;
  outline: none;
  font-size: 1rem;
  background: transparent;
}

.search-input::placeholder {
  color: #999;
}

.search-button {
  padding: 12px 20px;
  background: #007bff;
  border: none;
  color: white;
  cursor: pointer;
  transition: background 0.3s ease;
}

.search-button:hover {
  background: #0056b3;
}

.search-suggestions {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  background: white;
  border-radius: 12px;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
  z-index: 1000;
  margin-top: 8px;
  max-height: 400px;
  overflow-y: auto;
}

.suggestion-categories,
.suggestion-products {
  padding: 15px;
}

.suggestion-title {
  font-size: 0.875rem;
  font-weight: 600;
  color: #666;
  margin-bottom: 10px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.suggestion-items {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.suggestion-item {
  display: flex;
  align-items: center;
  padding: 8px 12px;
  border-radius: 8px;
  cursor: pointer;
  transition: background 0.2s ease;
}

.suggestion-item:hover {
  background: #f8f9fa;
}

.suggestion-item i {
  margin-right: 10px;
  color: #007bff;
  width: 16px;
}

.suggestion-image {
  width: 40px;
  height: 40px;
  object-fit: cover;
  border-radius: 6px;
  margin-right: 12px;
}

.suggestion-content {
  flex: 1;
}

.suggestion-name {
  font-size: 0.9rem;
  font-weight: 500;
  margin-bottom: 2px;
}

.suggestion-price {
  font-size: 0.8rem;
  color: #e74c3c;
  font-weight: 600;
}

.suggestion-footer {
  padding: 15px;
  border-top: 1px solid #eee;
  text-align: center;
}

.search-filters {
  margin-top: 20px;
  padding: 20px;
  background: #f8f9fa;
  border-radius: 12px;
  display: flex;
  flex-wrap: wrap;
  gap: 15px;
  align-items: end;
}

.filter-group {
  display: flex;
  flex-direction: column;
  min-width: 150px;
}

.filter-group label {
  font-size: 0.875rem;
  font-weight: 600;
  color: #333;
  margin-bottom: 5px;
}

.filter-group select,
.filter-group input {
  padding: 8px 12px;
  border: 1px solid #ddd;
  border-radius: 6px;
  font-size: 0.9rem;
  background: white;
}

.price-range {
  display: flex;
  align-items: center;
  gap: 8px;
}

.price-range input {
  width: 80px;
}

@media (max-width: 768px) {
  .search-filters {
    flex-direction: column;
    align-items: stretch;
  }
  
  .filter-group {
    min-width: auto;
  }
  
  .price-range {
    justify-content: space-between;
  }
  
  .price-range input {
    flex: 1;
  }
}
</style>
