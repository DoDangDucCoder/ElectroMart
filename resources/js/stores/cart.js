import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useCartStore = defineStore('cart', () => {
  const items = ref([])
  const shipping_fee = ref(0)
  const discount_amount = ref(0)
  
  const totalItems = computed(() => 
    items.value.reduce((total, item) => total + item.quantity, 0)
  )
  
  const subtotal = computed(() => 
    items.value.reduce((total, item) => total + (item.price * item.quantity), 0)
  )
  
  const total = computed(() => 
    subtotal.value + shipping_fee.value - discount_amount.value
  )
  
  const addItem = async (productId, quantity = 1) => {
    try {
      const response = await fetch('/api/v1/cart/add', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${localStorage.getItem('token')}`
        },
        body: JSON.stringify({
          product_id: productId,
          quantity: quantity
        })
      })
      
      const data = await response.json()
      
      if (data.success) {
        await fetchCart()
      } else {
        throw new Error(data.message)
      }
    } catch (error) {
      console.error('Không thể thêm sản phẩm vào giỏ hàng:', error)
      throw error
    }
  }
  
  const updateItem = async (itemId, quantity) => {
    try {
      const response = await fetch(`/api/v1/cart/update/${itemId}`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${localStorage.getItem('token')}`
        },
        body: JSON.stringify({ quantity })
      })
      
      const data = await response.json()
      
      if (data.success) {
        await fetchCart()
      } else {
        throw new Error(data.message)
      }
    } catch (error) {
      console.error('Không thể cập nhật sản phẩm trong giỏ hàng:', error)
      throw error
    }
  }
  
  const removeItem = async (itemId) => {
    try {
      const response = await fetch(`/api/v1/cart/remove/${itemId}`, {
        method: 'DELETE',
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`
        }
      })
      
      const data = await response.json()
      
      if (data.success) {
        await fetchCart()
      } else {
        throw new Error(data.message)
      }
    } catch (error) {
      console.error('Không thể xóa sản phẩm khỏi giỏ hàng:', error)
      throw error
    }
  }
  
  const clearCart = async () => {
    try {
      const response = await fetch('/api/v1/cart/clear', {
        method: 'DELETE',
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`
        }
      })
      
      const data = await response.json()
      
      if (data.success) {
        items.value = []
        shipping_fee.value = 0
        discount_amount.value = 0
      } else {
        throw new Error(data.message)
      }
    } catch (error) {
      console.error('Không thể xóa giỏ hàng:', error)
      throw error
    }
  }
  
  const fetchCart = async () => {
    try {
      const response = await fetch('/api/v1/cart', {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`
        }
      })
      
      const data = await response.json()
      
      if (data.success) {
        items.value = data.data.items || []
        shipping_fee.value = data.data.shipping_fee || 0
        discount_amount.value = data.data.discount_amount || 0
      }
    } catch (error) {
      console.error('Không thể tải giỏ hàng:', error)
    }
  }
  
  return {
    items,
    shipping_fee,
    discount_amount,
    totalItems,
    subtotal,
    total,
    addItem,
    updateItem,
    removeItem,
    clearCart,
    fetchCart
  }
})
