import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useProductStore = defineStore('products', () => {
  const products = ref([])
  const categories = ref([])
  const brands = ref([])
  const loading = ref(false)
  const error = ref(null)
  
  const fetchProducts = async (filters = {}, page = 1) => {
    loading.value = true
    error.value = null
    
    try {
      const sanitizedFilters = Object.fromEntries(
        Object.entries(filters || {}).filter(([, value]) => (
          value !== null && value !== undefined && String(value).trim() !== ''
        ))
      )
      const queryParams = new URLSearchParams({
        page: page.toString(),
        ...sanitizedFilters
      })
      
      const response = await fetch(`/api/v1/products?${queryParams}`)
      const data = await response.json()
      
      if (data.success) {
        products.value = data.data
        return data
      } else {
        throw new Error(data.message)
      }
    } catch (err) {
      error.value = err.message
      throw err
    } finally {
      loading.value = false
    }
  }
  
  const fetchProduct = async (slug) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await fetch(`/api/v1/products/${slug}`)
      const data = await response.json()
      
      if (data.success) {
        return data.data
      } else {
        throw new Error(data.message)
      }
    } catch (err) {
      error.value = err.message
      throw err
    } finally {
      loading.value = false
    }
  }
  
  const searchProducts = async (query, limit = 20) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await fetch(`/api/v1/products/search?q=${encodeURIComponent(query)}&limit=${limit}`)
      const data = await response.json()
      
      if (data.success) {
        return data.data
      } else {
        throw new Error(data.message)
      }
    } catch (err) {
      error.value = err.message
      throw err
    } finally {
      loading.value = false
    }
  }
  
  const fetchCategories = async () => {
    try {
      const response = await fetch('/api/v1/categories')
      const data = await response.json()
      
      if (data.success) {
        categories.value = data.data
        return data.data
      } else {
        throw new Error(data.message)
      }
    } catch (err) {
      console.error('Không thể tải danh mục:', err)
      throw err
    }
  }
  
  const fetchBrands = async () => {
    try {
      const response = await fetch('/api/v1/brands')
      const data = await response.json()
      
      if (data.success) {
        brands.value = data.data
        return data.data
      } else {
        throw new Error(data.message)
      }
    } catch (err) {
      console.error('Không thể tải thương hiệu:', err)
      throw err
    }
  }
  
  const fetchLatestProducts = async (limit = 6) => {
    try {
      const response = await fetch(`/api/v1/products/latest?limit=${limit}`)
      const data = await response.json()
      
      if (data.success) {
        return data.data
      } else {
        throw new Error(data.message)
      }
    } catch (err) {
      console.error('Không thể tải sản phẩm mới nhất:', err)
      throw err
    }
  }
  
  const fetchFeaturedProducts = async (limit = 8) => {
    try {
      const response = await fetch(`/api/v1/products/featured?limit=${limit}`)
      const data = await response.json()
      
      if (data.success) {
        return data.data
      } else {
        throw new Error(data.message)
      }
    } catch (err) {
      console.error('Không thể tải sản phẩm nổi bật:', err)
      throw err
    }
  }
  
  return {
    products,
    categories,
    brands,
    loading,
    error,
    fetchProducts,
    fetchProduct,
    searchProducts,
    fetchCategories,
    fetchBrands,
    fetchLatestProducts,
    fetchFeaturedProducts
  }
})
