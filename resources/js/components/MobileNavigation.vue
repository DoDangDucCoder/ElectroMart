<template>
  <div class="mobile-nav">
    <div class="mobile-header">
      <button @click="toggleMenu" class="menu-toggle" :class="{ active: isMenuOpen }">
        <span></span>
        <span></span>
        <span></span>
      </button>
      
      <div class="logo">
        <router-link to="/">
          <img src="/images/logo.png" alt="Laravel Shop" />
        </router-link>
      </div>
      
      <div class="header-actions">
        <button @click="toggleSearch" class="search-toggle">
          <i class="fas fa-search"></i>
        </button>
        <button @click="toggleCart" class="cart-toggle">
          <i class="fas fa-shopping-cart"></i>
          <span v-if="cartCount > 0" class="cart-badge">{{ cartCount }}</span>
        </button>
      </div>
    </div>
    
    <div class="mobile-search" v-if="showSearch">
      <search-bar @search="handleSearch" />
    </div>
    
    <div class="mobile-menu" :class="{ active: isMenuOpen }">
      <div class="menu-overlay" @click="closeMenu"></div>
      
      <div class="menu-content">
        <div class="menu-header">
          <h3>Menu</h3>
          <button @click="closeMenu" class="close-menu">
            <i class="fas fa-times"></i>
          </button>
        </div>
        
        <nav class="menu-nav">
          <ul>
            <li>
              <router-link to="/" @click="closeMenu">
                <i class="fas fa-home"></i>
                Home
              </router-link>
            </li>
            <li>
              <router-link to="/products" @click="closeMenu">
                <i class="fas fa-box"></i>
                Products
              </router-link>
            </li>
            <li>
              <router-link to="/categories" @click="closeMenu">
                <i class="fas fa-tags"></i>
                Categories
              </router-link>
            </li>
            <li>
              <router-link to="/brands" @click="closeMenu">
                <i class="fas fa-award"></i>
                Brands
              </router-link>
            </li>
            <li>
              <router-link to="/blog" @click="closeMenu">
                <i class="fas fa-blog"></i>
                Blog
              </router-link>
            </li>
            <li>
              <router-link to="/contact" @click="closeMenu">
                <i class="fas fa-envelope"></i>
                Contact
              </router-link>
            </li>
          </ul>
        </nav>
        
        <div class="menu-footer">
          <div v-if="isAuthenticated" class="user-section">
            <div class="user-info">
              <i class="fas fa-user"></i>
              <span>{{ user.name }}</span>
            </div>
            <div class="user-actions">
              <router-link to="/profile" @click="closeMenu">Profile</router-link>
              <router-link to="/orders" @click="closeMenu">Orders</router-link>
              <button @click="logout" class="btn-logout">Logout</button>
            </div>
          </div>
          <div v-else class="auth-section">
            <router-link to="/login" @click="closeMenu" class="btn btn-primary">
              Login
            </router-link>
            <router-link to="/register" @click="closeMenu" class="btn btn-outline-primary">
              Register
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useCartStore } from '@/stores/cart'
import SearchBar from './SearchBar.vue'

export default {
  name: 'MobileNavigation',
  components: {
    SearchBar
  },
  emits: ['toggle-cart'],
  setup(props, { emit }) {
    const router = useRouter()
    const authStore = useAuthStore()
    const cartStore = useCartStore()
    
    const isMenuOpen = ref(false)
    const showSearch = ref(false)
    
    const isAuthenticated = computed(() => authStore.isAuthenticated)
    const user = computed(() => authStore.user)
    const cartCount = computed(() => cartStore.totalItems)
    
    const toggleMenu = () => {
      isMenuOpen.value = !isMenuOpen.value
      if (isMenuOpen.value) {
        document.body.style.overflow = 'hidden'
      } else {
        document.body.style.overflow = ''
      }
    }
    
    const closeMenu = () => {
      isMenuOpen.value = false
      document.body.style.overflow = ''
    }
    
    const toggleSearch = () => {
      showSearch.value = !showSearch.value
    }
    
    const toggleCart = () => {
      emit('toggle-cart')
    }
    
    const handleSearch = (searchData) => {
      showSearch.value = false
      router.push({
        name: 'products',
        query: searchData
      })
    }
    
    const logout = async () => {
      try {
        await authStore.logout()
        closeMenu()
        router.push('/')
      } catch (error) {
        console.error('Logout failed:', error)
      }
    }
    
    return {
      isMenuOpen,
      showSearch,
      isAuthenticated,
      user,
      cartCount,
      toggleMenu,
      closeMenu,
      toggleSearch,
      toggleCart,
      handleSearch,
      logout
    }
  }
}
</script>

<style scoped>
.mobile-nav {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  z-index: 1000;
  background: white;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.mobile-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 12px 16px;
  height: 60px;
}

.menu-toggle {
  display: flex;
  flex-direction: column;
  justify-content: space-around;
  width: 30px;
  height: 30px;
  background: transparent;
  border: none;
  cursor: pointer;
  padding: 0;
}

.menu-toggle span {
  width: 100%;
  height: 3px;
  background: #333;
  border-radius: 2px;
  transition: all 0.3s ease;
  transform-origin: center;
}

.menu-toggle.active span:nth-child(1) {
  transform: rotate(45deg) translate(6px, 6px);
}

.menu-toggle.active span:nth-child(2) {
  opacity: 0;
}

.menu-toggle.active span:nth-child(3) {
  transform: rotate(-45deg) translate(6px, -6px);
}

.logo img {
  height: 32px;
  width: auto;
}

.header-actions {
  display: flex;
  align-items: center;
  gap: 12px;
}

.search-toggle,
.cart-toggle {
  width: 40px;
  height: 40px;
  border: none;
  background: transparent;
  color: #333;
  font-size: 1.2rem;
  cursor: pointer;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
  position: relative;
}

.search-toggle:hover,
.cart-toggle:hover {
  background: #f8f9fa;
  color: #007bff;
}

.cart-badge {
  position: absolute;
  top: -2px;
  right: -2px;
  background: #dc3545;
  color: white;
  font-size: 0.7rem;
  font-weight: 600;
  padding: 2px 6px;
  border-radius: 10px;
  min-width: 18px;
  height: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.mobile-search {
  padding: 12px 16px;
  background: #f8f9fa;
  border-top: 1px solid #eee;
}

.mobile-menu {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100vh;
  z-index: 1001;
  pointer-events: none;
}

.mobile-menu.active {
  pointer-events: all;
}

.menu-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  opacity: 0;
  transition: opacity 0.3s ease;
}

.mobile-menu.active .menu-overlay {
  opacity: 1;
}

.menu-content {
  position: absolute;
  top: 0;
  right: -100%;
  width: 280px;
  height: 100%;
  background: white;
  transition: right 0.3s ease;
  display: flex;
  flex-direction: column;
}

.mobile-menu.active .menu-content {
  right: 0;
}

.menu-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px;
  border-bottom: 1px solid #eee;
}

.menu-header h3 {
  margin: 0;
  font-size: 1.25rem;
  font-weight: 600;
}

.close-menu {
  width: 30px;
  height: 30px;
  border: none;
  background: transparent;
  color: #666;
  font-size: 1.2rem;
  cursor: pointer;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.close-menu:hover {
  background: #f8f9fa;
  color: #333;
}

.menu-nav {
  flex: 1;
  padding: 20px 0;
}

.menu-nav ul {
  list-style: none;
  margin: 0;
  padding: 0;
}

.menu-nav li {
  margin: 0;
}

.menu-nav a {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 16px 20px;
  color: #333;
  text-decoration: none;
  font-weight: 500;
  transition: all 0.2s ease;
  border-left: 3px solid transparent;
}

.menu-nav a:hover,
.menu-nav a.router-link-active {
  background: #f8f9fa;
  color: #007bff;
  border-left-color: #007bff;
}

.menu-nav i {
  width: 20px;
  text-align: center;
}

.menu-footer {
  padding: 20px;
  border-top: 1px solid #eee;
}

.user-section {
  text-align: center;
}

.user-info {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  margin-bottom: 15px;
  padding: 12px;
  background: #f8f9fa;
  border-radius: 8px;
}

.user-info i {
  color: #007bff;
}

.user-actions {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.user-actions a,
.btn-logout {
  padding: 10px 16px;
  text-decoration: none;
  border-radius: 6px;
  font-size: 0.9rem;
  transition: all 0.2s ease;
}

.user-actions a {
  color: #333;
  background: #f8f9fa;
}

.user-actions a:hover {
  background: #e9ecef;
  color: #007bff;
}

.btn-logout {
  background: #dc3545;
  color: white;
  border: none;
  cursor: pointer;
}

.btn-logout:hover {
  background: #c82333;
}

.auth-section {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.auth-section .btn {
  width: 100%;
  justify-content: center;
}

@media (min-width: 769px) {
  .mobile-nav {
    display: none;
  }
}
</style>
