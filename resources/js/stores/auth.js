import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const token = ref(localStorage.getItem('token'))
  const isAuthenticated = computed(() => !!token.value && !!user.value)
  
  const login = async (credentials) => {
    try {
      const response = await fetch('/api/v1/auth/login', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(credentials)
      })
      
      const data = await response.json()
      
      if (data.success) {
        user.value = data.data.user
        token.value = data.data.token
        localStorage.setItem('token', token.value)
        return data
      } else {
        throw new Error(data.message)
      }
    } catch (error) {
      console.error('Đăng nhập thất bại:', error)
      throw error
    }
  }
  
  const register = async (userData) => {
    try {
      const response = await fetch('/api/v1/auth/register', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(userData)
      })
      
      const data = await response.json()
      
      if (data.success) {
        user.value = data.data.user
        token.value = data.data.token
        localStorage.setItem('token', token.value)
        return data
      } else {
        throw new Error(data.message)
      }
    } catch (error) {
      console.error('Đăng ký thất bại:', error)
      throw error
    }
  }
  
  const logout = async () => {
    try {
      if (token.value) {
        await fetch('/api/v1/auth/logout', {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${token.value}`
          }
        })
      }
    } catch (error) {
      console.error('Lỗi đăng xuất:', error)
    } finally {
      user.value = null
      token.value = null
      localStorage.removeItem('token')
    }
  }
  
  const fetchUser = async () => {
    try {
      if (!token.value) return
      
      const response = await fetch('/api/v1/user', {
        headers: {
          'Authorization': `Bearer ${token.value}`
        }
      })
      
      const data = await response.json()
      
      if (data.success) {
        user.value = data.data
      } else {
        throw new Error(data.message)
      }
    } catch (error) {
      console.error('Không thể tải thông tin người dùng:', error)
      logout()
    }
  }
  
  const updateProfile = async (profileData) => {
    try {
      const response = await fetch('/api/v1/user', {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${token.value}`
        },
        body: JSON.stringify(profileData)
      })
      
      const data = await response.json()
      
      if (data.success) {
        user.value = data.data
        return data
      } else {
        throw new Error(data.message)
      }
    } catch (error) {
      console.error('Không thể cập nhật hồ sơ:', error)
      throw error
    }
  }
  
  return {
    user,
    token,
    isAuthenticated,
    login,
    register,
    logout,
    fetchUser,
    updateProfile
  }
})
