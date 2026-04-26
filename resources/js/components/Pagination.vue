<template>
  <div class="pagination">
    <div class="pagination-info">
      <span>Hiển thị {{ startItem }} đến {{ endItem }} trong {{ total }} kết quả</span>
    </div>
    
    <div class="pagination-controls">
      <button 
        @click="goToPage(currentPage - 1)"
        :disabled="currentPage <= 1"
        class="btn-pagination"
      >
        <i class="fas fa-chevron-left"></i>
        Trước
      </button>
      
      <div class="page-numbers">
        <button 
          v-for="page in visiblePages" 
          :key="page"
          @click="goToPage(page)"
          :class="{ active: page === currentPage }"
          class="btn-page"
        >
          {{ page }}
        </button>
      </div>
      
      <button 
        @click="goToPage(currentPage + 1)"
        :disabled="currentPage >= lastPage"
        class="btn-pagination"
      >
        Sau
        <i class="fas fa-chevron-right"></i>
      </button>
    </div>
  </div>
</template>

<script>
import { computed } from 'vue'

export default {
  name: 'Pagination',
  props: {
    currentPage: {
      type: Number,
      required: true
    },
    lastPage: {
      type: Number,
      required: true
    },
    total: {
      type: Number,
      required: true
    },
    perPage: {
      type: Number,
      default: 12
    }
  },
  emits: ['page-change'],
  setup(props, { emit }) {
    const startItem = computed(() => {
      return (props.currentPage - 1) * props.perPage + 1
    })
    
    const endItem = computed(() => {
      const end = props.currentPage * props.perPage
      return Math.min(end, props.total)
    })
    
    const visiblePages = computed(() => {
      const pages = []
      const maxVisible = 5
      const half = Math.floor(maxVisible / 2)
      
      let start = Math.max(1, props.currentPage - half)
      let end = Math.min(props.lastPage, start + maxVisible - 1)
      
      if (end - start + 1 < maxVisible) {
        start = Math.max(1, end - maxVisible + 1)
      }
      
      for (let i = start; i <= end; i++) {
        pages.push(i)
      }
      
      return pages
    })
    
    const goToPage = (page) => {
      if (page >= 1 && page <= props.lastPage && page !== props.currentPage) {
        emit('page-change', page)
      }
    }
    
    return {
      startItem,
      endItem,
      visiblePages,
      goToPage
    }
  }
}
</script>

<style scoped>
.pagination {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 30px;
  padding: 20px 0;
}

.pagination-info {
  color: #666;
  font-size: 0.9rem;
}

.pagination-controls {
  display: flex;
  align-items: center;
  gap: 10px;
}

.btn-pagination {
  display: flex;
  align-items: center;
  gap: 5px;
  padding: 8px 16px;
  border: 1px solid #ddd;
  background: white;
  color: #333;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s ease;
  font-size: 0.9rem;
}

.btn-pagination:hover:not(:disabled) {
  background: #f8f9fa;
  border-color: #007bff;
  color: #007bff;
}

.btn-pagination:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.page-numbers {
  display: flex;
  gap: 5px;
}

.btn-page {
  width: 40px;
  height: 40px;
  border: 1px solid #ddd;
  background: white;
  color: #333;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s ease;
  font-size: 0.9rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-page:hover {
  background: #f8f9fa;
  border-color: #007bff;
  color: #007bff;
}

.btn-page.active {
  background: #007bff;
  border-color: #007bff;
  color: white;
}

@media (max-width: 768px) {
  .pagination {
    flex-direction: column;
    gap: 20px;
  }
  
  .pagination-controls {
    flex-wrap: wrap;
    justify-content: center;
  }
  
  .btn-pagination {
    padding: 6px 12px;
    font-size: 0.8rem;
  }
  
  .btn-page {
    width: 35px;
    height: 35px;
    font-size: 0.8rem;
  }
}

@media (max-width: 480px) {
  .page-numbers {
    gap: 3px;
  }
  
  .btn-page {
    width: 32px;
    height: 32px;
  }
  
  .btn-pagination {
    padding: 5px 10px;
  }
}
</style>
