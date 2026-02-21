<template>
  <div class="container section-padding">
    <h1 class="section-title">Shop All Products</h1>
    <p class="muted-text">Explore our full range of minimalist essentials.</p>
    
    <div class="shop-grid">
      <!-- Placeholder for shop content -->
      <div v-for="i in 8" :key="i" class="product-placeholder glass animate-pulse"></div>
    </div>
  </div>
</template>

<style scoped>
.section-padding {
  padding-top: 5rem;
  padding-bottom: 5rem;
}

.muted-text {
  color: var(--muted-foreground);
  margin-bottom: 3rem;
}

.shop-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 2rem;
}

.product-placeholder {
  aspect-ratio: 1;
  border-radius: var(--radius);
  background: var(--muted);
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.5; }
}
</style>
