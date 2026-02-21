<template>
  <nav class="navbar glass">
    <div class="container navbar-inner">
      <NuxtLink to="/" class="logo">
        <span class="logo-text">MODERN</span><span class="logo-dot">.</span>
      </NuxtLink>
      
      <div class="nav-links">
        <NuxtLink to="/" class="nav-link">Home</NuxtLink>
        <NuxtLink to="/shop" class="nav-link">Shop</NuxtLink>
        <NuxtLink to="/collections" class="nav-link">Collections</NuxtLink>
        <NuxtLink to="/about" class="nav-link">About</NuxtLink>
      </div>

      <div class="nav-actions">
        <button class="nav-icon-btn">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
        </button>
        <button class="nav-icon-btn cart-btn">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
          <span class="cart-count">0</span>
        </button>
      </div>
    </div>
  </nav>
</template>

<style scoped>
.navbar {
  position: sticky;
  top: 0;
  z-index: 100;
  height: 80px;
  display: flex;
  align-items: center;
}

.navbar-inner {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.logo {
  font-family: var(--font-heading);
  font-size: 1.5rem;
  font-weight: 700;
  letter-spacing: -0.05em;
  display: flex;
  align-items: center;
}

.logo-dot {
  color: var(--secondary);
}

.nav-links {
  display: flex;
  gap: 2.5rem;
}

.nav-link {
  font-size: 0.95rem;
  font-weight: 500;
  color: var(--muted-foreground);
}

.nav-link:hover, .nav-link.router-link-active {
  color: var(--foreground);
}

.nav-actions {
  display: flex;
  gap: 1.5rem;
  align-items: center;
}

.nav-icon-btn {
  color: var(--foreground);
  transition: opacity 0.2s;
  position: relative;
}

.nav-icon-btn:hover {
  opacity: 0.7;
}

.cart-btn {
  display: flex;
  align-items: center;
}

.cart-count {
  position: absolute;
  top: -8px;
  right: -8px;
  background: var(--foreground);
  color: var(--background);
  font-size: 0.7rem;
  font-weight: 700;
  width: 18px;
  height: 18px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

@media (max-width: 768px) {
  .nav-links {
    display: none;
  }
}
</style>
