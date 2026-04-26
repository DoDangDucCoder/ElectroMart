import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useWishlistStore = defineStore('wishlist', () => {
  const items = ref([])
  
  const addItem = (product) => {
    const existingItem = items.value.find(item => item.id === product.id)
    if (!existingItem) {
      items.value.push(product)
      saveToLocalStorage()
    }
  }
  
  const removeItem = (productId) => {
    const index = items.value.findIndex(item => item.id === productId)
    if (index > -1) {
      items.value.splice(index, 1)
      saveToLocalStorage()
    }
  }
  
  const isInWishlist = (productId) => {
    return items.value.some(item => item.id === productId)
  }
  
  const clearWishlist = () => {
    items.value = []
    saveToLocalStorage()
  }
  
  const saveToLocalStorage = () => {
    localStorage.setItem('wishlist', JSON.stringify(items.value))
  }
  
  const loadFromLocalStorage = () => {
    const saved = localStorage.getItem('wishlist')
    if (saved) {
      try {
        items.value = JSON.parse(saved)
      } catch (error) {
        console.error('Không thể tải danh sách yêu thích từ localStorage:', error)
        items.value = []
      }
    }
  }
  
  // Tải dữ liệu từ localStorage khi khởi tạo store
  loadFromLocalStorage()
  
  return {
    items,
    addItem,
    removeItem,
    isInWishlist,
    clearWishlist
  }
})
