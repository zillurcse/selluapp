<template>
  <Teleport to="body">
    <Transition name="fade">
      <div v-if="isWishlistOpen" class="wishlist-overlay" @click="closeWishlist"></div>
    </Transition>
    
    <Transition name="slide">
      <div v-if="isWishlistOpen" class="wishlist-drawer">
        <div class="wishlist-header">
          <h2 class="wishlist-title">My Wishlist ({{ wishlistCount }})</h2>
          <button class="close-btn" @click="closeWishlist">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
          </button>
        </div>

        <div v-if="wishlist.length === 0" class="empty-wishlist">
          <div class="empty-icon">🖤</div>
          <p>Your wishlist is empty</p>
          <button class="btn btn-primary" @click="closeWishlist">Discover Items</button>
        </div>

        <div v-else class="wishlist-items">
          <div v-for="item in wishlist" :key="item.id" class="wishlist-item group relative">
            <div class="item-img glass cursor-pointer" @click="navigateToProduct(item)">
              <img v-if="item.image_url" :src="item.image_url" :alt="item.name" />
              <img v-else-if="item.image" :src="item.image" :alt="item.name" />
              <div v-else class="flex h-full w-full items-center justify-center bg-gray-50 text-4xl">{{ item.emoji || '📦' }}</div>
            </div>
            
            <div class="item-details">
              <div class="item-info">
                <h3 class="item-name cursor-pointer" @click="navigateToProduct(item)">{{ item.name }}</h3>
                <p class="item-price">৳{{ item.sale_price || item.price }}</p>
              </div>
              <div class="item-actions">
                <button class="add-to-cart-btn" @click="handleAddToCart(item)">
                  <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                  Add to Cart
                </button>
                <button class="remove-btn" @click="removeFromWishlist(item.id)">Remove</button>
              </div>
            </div>
          </div>
        </div>

        <div class="wishlist-footer">
          <button class="btn btn-outline w-full" @click="navigateTo('/account')">
            View Full Wishlist
          </button>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { toast } from 'vue-sonner'

const { wishlist, isWishlistOpen, closeWishlist, removeFromWishlist, wishlistCount } = useWishlist()
const { addToCart } = useCart()

const handleAddToCart = (item) => {
  addToCart(item, 1)
  toast.success(`${item.name} added to cart!`)
}

const navigateToProduct = (item) => {
  closeWishlist()
  navigateTo(`/product/${item.slug || item.id}`)
}
</script>

<style scoped>
.wishlist-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.4);
  backdrop-filter: blur(4px);
  z-index: 1000;
}

.wishlist-drawer {
  position: fixed;
  top: 0;
  right: 0;
  bottom: 0;
  width: 100%;
  max-width: 450px;
  background: var(--background);
  z-index: 1001;
  display: flex;
  flex-direction: column;
  box-shadow: -20px 0 40px rgba(0,0,0,0.1);
}

.wishlist-header {
  padding: 1.5rem 2rem;
  border-bottom: 1px solid var(--border);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.wishlist-title {
  font-size: 1.25rem;
  font-weight: 700;
}

.close-btn {
  color: var(--muted-foreground);
  padding: 0.5rem;
  cursor: pointer;
  background: transparent;
  border: none;
}

.wishlist-items {
  flex: 1;
  overflow-y: auto;
  padding: 2rem;
}

.wishlist-item {
  display: flex;
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.item-img {
  width: 100px;
  height: 120px;
  border-radius: var(--radius);
  overflow: hidden;
  flex-shrink: 0;
  background: var(--muted);
}

.item-img img {
  width: 100%;
  height: 100%;
  object-fit: contain;
}

.item-details {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  padding-bottom: 0.5rem;
}

.item-name {
  font-size: 1.1rem;
  font-weight: 600;
  margin-bottom: 0.25rem;
  display: -webkit-box;
  line-clamp: 2;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.item-price {
  color: var(--muted-foreground);
  font-weight: 500;
}

.item-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 1rem;
}

.add-to-cart-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.85rem;
  font-weight: 600;
  color: var(--background);
  background: var(--foreground);
  padding: 0.5rem 1rem;
  border-radius: 100px;
  border: none;
  cursor: pointer;
  transition: opacity 0.2s;
}

.add-to-cart-btn:hover {
  opacity: 0.9;
}

.remove-btn {
  font-size: 0.85rem;
  color: var(--accent);
  text-decoration: underline;
  background: none;
  border: none;
  cursor: pointer;
}

.wishlist-footer {
  padding: 2rem;
  border-top: 1px solid var(--border);
}

.empty-wishlist {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 2rem;
  text-align: center;
}

.empty-icon {
  font-size: 4rem;
  margin-bottom: 1.5rem;
}

.empty-wishlist p {
  color: var(--muted-foreground);
  margin-bottom: 2rem;
}

.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: var(--radius);
  font-weight: 600;
  font-size: 0.875rem;
  line-height: 1.25rem;
  padding: 0.75rem 1.5rem;
  transition: all 0.2s;
  cursor: pointer;
}

.btn-primary {
  background-color: var(--foreground);
  color: var(--background);
  border: none;
}

.btn-primary:hover {
  opacity: 0.9;
}

.btn-outline {
  background-color: transparent;
  color: var(--foreground);
  border: 1px solid var(--border);
}

.btn-outline:hover {
  background-color: var(--accent);
}


/* Transitions */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

.slide-enter-active, .slide-leave-active {
  transition: transform 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
}
.slide-enter-from, .slide-leave-to {
  transform: translateX(100%);
}
</style>
