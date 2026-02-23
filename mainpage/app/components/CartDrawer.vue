<template>
  <Teleport to="body">
    <Transition name="fade">
      <div v-if="isCartOpen" class="cart-overlay" @click="closeCart"></div>
    </Transition>
    
    <Transition name="slide">
      <div v-if="isCartOpen" class="cart-drawer">
        <div class="cart-header">
          <h2 class="cart-title">Your Cart ({{ cartCount }})</h2>
          <button class="close-btn" @click="closeCart">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
          </button>
        </div>

        <div v-if="cart.length === 0" class="empty-cart">
          <div class="empty-icon">🛒</div>
          <p>Your cart is empty</p>
          <button class="btn btn-primary" @click="closeCart">Start Shopping</button>
        </div>

        <div v-else class="cart-items">
          <div v-for="item in cart" :key="item.id" class="cart-item">
            <div class="item-img glass">
              <img :src="item.image" :alt="item.name" />
            </div>
            <div class="item-details">
              <div class="item-info">
                <h3 class="item-name">{{ item.name }}</h3>
                <p class="item-price">${{ item.price }}</p>
              </div>
              <div class="item-actions">
                <div class="quantity-control">
                  <button @click="updateQuantity(item.id, item.quantity - 1)">-</button>
                  <span>{{ item.quantity }}</span>
                  <button @click="updateQuantity(item.id, item.quantity + 1)">+</button>
                </div>
                <button class="remove-btn" @click="removeFromCart(item.id)">Remove</button>
              </div>
            </div>
          </div>
        </div>

        <div v-if="cart.length > 0" class="cart-footer">
          <div class="total-row">
            <span>Subtotal</span>
            <span class="total-amount">${{ cartTotal.toFixed(2) }}</span>
          </div>
          <p class="shipping-note">Shipping and taxes calculated at checkout.</p>
          <NuxtLink to="/checkout" class="btn btn-primary checkout-btn" @click="closeCart">
            Checkout
          </NuxtLink>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
const { cart, isCartOpen, closeCart, removeFromCart, updateQuantity, cartTotal, cartCount } = useCart()
</script>

<style scoped>
.cart-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.4);
  backdrop-filter: blur(4px);
  z-index: 1000;
}

.cart-drawer {
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

.cart-header {
  padding: 1.5rem 2rem;
  border-bottom: 1px solid var(--border);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.cart-title {
  font-size: 1.25rem;
  font-weight: 700;
}

.close-btn {
  color: var(--muted-foreground);
  padding: 0.5rem;
}

.cart-items {
  flex: 1;
  overflow-y: auto;
  padding: 2rem;
}

.cart-item {
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
}

.item-img img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.item-details {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.item-name {
  font-size: 1.1rem;
  font-weight: 600;
  margin-bottom: 0.25rem;
}

.item-price {
  color: var(--muted-foreground);
  font-weight: 500;
}

.item-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.quantity-control {
  display: flex;
  align-items: center;
  gap: 1rem;
  border: 1px solid var(--border);
  border-radius: 100px;
  padding: 0.25rem 0.75rem;
}

.quantity-control button {
  font-size: 1.2rem;
  color: var(--muted-foreground);
}

.remove-btn {
  font-size: 0.85rem;
  color: var(--accent);
  text-decoration: underline;
}

.cart-footer {
  padding: 2rem;
  border-top: 1px solid var(--border);
}

.total-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.5rem;
  font-size: 1.25rem;
  font-weight: 700;
}

.shipping-note {
  font-size: 0.85rem;
  color: var(--muted-foreground);
  margin-bottom: 1.5rem;
}

.checkout-btn {
  width: 100%;
  text-align: center;
}

.empty-cart {
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

.empty-cart p {
  color: var(--muted-foreground);
  margin-bottom: 2rem;
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
