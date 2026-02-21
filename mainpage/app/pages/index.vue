<template>
  <div class="home-page">
    <!-- Hero Section -->
    <section class="hero animate-fade-in">
      <div class="container hero-container">
        <div class="hero-content">
          <span class="badge">New Arrival</span>
          <h1 class="hero-title">Elegance in <br /><span class="text-gradient">Simplicity</span>.</h1>
          <p class="hero-subtitle">Discover our curated collection of minimalist designs for the modern home and lifestyle. Crafted with precision, designed for you.</p>
          <div class="hero-actions">
            <NuxtLink to="/shop" class="btn btn-primary">Shop Collection</NuxtLink>
            <NuxtLink to="/about" class="btn btn-outline">Our Story</NuxtLink>
          </div>
        </div>
        <div class="hero-image-wrapper">
          <img src="~/assets/img/hero.png" alt="Featured Product" class="hero-image" />
        </div>
      </div>
    </section>

    <!-- Featured Categories -->
    <section class="categories container section-padding">
      <div class="section-header">
        <h2 class="section-title">Shop by Category</h2>
        <NuxtLink to="/categories" class="text-link">View all</NuxtLink>
      </div>
      <div class="category-grid">
        <div class="category-card glass">
          <h3 class="category-name">Furniture</h3>
          <p class="category-items">12 Items</p>
        </div>
        <div class="category-card glass">
          <h3 class="category-name">Accessories</h3>
          <p class="category-items">24 Items</p>
        </div>
        <div class="category-card glass">
          <h3 class="category-name">Lifestyle</h3>
          <p class="category-items">18 Items</p>
        </div>
      </div>
    </section>

    <!-- Featured Products -->
    <section class="featured-products container section-padding">
      <div class="section-header">
        <h2 class="section-title">Featured Products</h2>
        <NuxtLink to="/shop" class="text-link">Shop All</NuxtLink>
      </div>
      <div class="product-grid">
        <div v-for="product in products" :key="product.id" class="product-card">
          <div class="product-image-container glass">
            <img :src="product.image" :alt="product.name" class="product-image" />
            <button class="add-to-cart-overlay">Add to Cart</button>
          </div>
          <div class="product-info">
            <h3 class="product-name">{{ product.name }}</h3>
            <p class="product-price">${{ product.price }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Newsletter -->
    <section class="newsletter container section-padding">
      <div class="newsletter-card glass">
        <div class="newsletter-content">
          <h2 class="newsletter-title">Stay Updated</h2>
          <p class="newsletter-desc">Subscribe to our newsletter and be the first to know about new collections and exclusive offers.</p>
          <form class="newsletter-form" @submit.prevent>
            <input type="email" placeholder="Enter your email" class="newsletter-input" />
            <button type="submit" class="btn btn-primary">Subscribe</button>
          </form>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import watchImg from '~/assets/img/watch.png'
import headphonesImg from '~/assets/img/headphones.png'

const products = [
  { id: 1, name: 'Minimalist Timepiece', price: '250.00', image: watchImg },
  { id: 2, name: 'Pure Wireless Headphones', price: '320.00', image: headphonesImg },
  { id: 3, name: 'Sleek Smart Watch', price: '190.00', image: watchImg },
  { id: 4, name: 'Studio Headphones Noir', price: '450.00', image: headphonesImg },
]
</script>

<style scoped>
.section-padding {
  padding-top: 5rem;
  padding-bottom: 5rem;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  margin-bottom: 2.5rem;
}

.section-title {
  font-size: 2rem;
  font-weight: 600;
}

.text-link {
  color: var(--muted-foreground);
  font-weight: 500;
  border-bottom: 1px solid transparent;
}

.text-link:hover {
  color: var(--foreground);
  border-bottom-color: var(--foreground);
}

/* Hero */
.hero {
  min-height: calc(100vh - 80px);
  display: flex;
  align-items: center;
  padding-bottom: 2rem;
}

.hero-container {
  display: grid;
  grid-template-columns: 1fr 1fr;
  align-items: center;
  gap: 4rem;
}

.badge {
  display: inline-block;
  padding: 0.4rem 1rem;
  background: var(--muted);
  border-radius: 100px;
  font-size: 0.8rem;
  font-weight: 600;
  margin-bottom: 1.5rem;
  text-transform: uppercase;
  letter-spacing: 0.1em;
}

.hero-title {
  font-size: clamp(3rem, 6vw, 5rem);
  line-height: 1.1;
  margin-bottom: 1.5rem;
  letter-spacing: -0.04em;
}

.text-gradient {
  background: linear-gradient(135deg, var(--secondary), var(--accent));
  -webkit-background-clip: text;
  background-clip: text;
  -webkit-text-fill-color: transparent;
}

.hero-subtitle {
  font-size: 1.15rem;
  color: var(--muted-foreground);
  max-width: 500px;
  margin-bottom: 2.5rem;
}

.hero-actions {
  display: flex;
  gap: 1rem;
}

.btn {
  padding: 1rem 2rem;
  border-radius: var(--radius);
  font-weight: 600;
  transition: all 0.3s ease;
  display: inline-block;
}

.btn-primary {
  background: var(--foreground);
  color: var(--background);
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}

.btn-outline {
  border: 1px solid var(--border);
  color: var(--foreground);
}

.btn-outline:hover {
  background: var(--muted);
}

.hero-image-wrapper {
  position: relative;
}

.hero-image {
  width: 100%;
  height: auto;
  border-radius: var(--radius);
  object-fit: cover;
  box-shadow: 0 20px 40px rgba(0,0,0,0.1);
}

/* Categories */
.category-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 2rem;
}

.category-card {
  padding: 3rem 2rem;
  border-radius: var(--radius);
  text-align: center;
  transition: transform 0.3s ease;
  cursor: pointer;
}

.category-card:hover {
  transform: translateY(-10px);
}

.category-name {
  font-size: 1.5rem;
  margin-bottom: 0.5rem;
}

.category-items {
  color: var(--muted-foreground);
  font-size: 0.9rem;
}

/* Products */
.product-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 2rem;
}

.product-image-container {
  aspect-ratio: 1;
  border-radius: var(--radius);
  overflow: hidden;
  position: relative;
  margin-bottom: 1.2rem;
}

.product-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.product-card:hover .product-image {
  transform: scale(1.05);
}

.add-to-cart-overlay {
  position: absolute;
  bottom: 1rem;
  left: 1rem;
  right: 1rem;
  background: var(--background);
  color: var(--foreground);
  padding: 0.8rem;
  border-radius: calc(var(--radius) / 2);
  font-weight: 600;
  transform: translateY(150%);
  transition: transform 0.3s ease;
  font-size: 0.9rem;
}

.product-card:hover .add-to-cart-overlay {
  transform: translateY(0);
}

.product-name {
  font-size: 1.1rem;
  margin-bottom: 0.4rem;
}

.product-price {
  font-weight: 600;
  color: var(--muted-foreground);
}

/* Newsletter */
.newsletter-card {
  padding: 5rem;
  border-radius: 2rem;
  text-align: center;
}

.newsletter-content {
  max-width: 600px;
  margin: 0 auto;
}

.newsletter-title {
  font-size: 2.5rem;
  margin-bottom: 1rem;
}

.newsletter-desc {
  color: var(--muted-foreground);
  margin-bottom: 2.5rem;
}

.newsletter-form {
  display: flex;
  gap: 1rem;
}

.newsletter-input {
  flex: 1;
  padding: 1rem 1.5rem;
  border-radius: var(--radius);
  border: 1px solid var(--border);
  background: var(--background);
  font-family: inherit;
  outline: none;
}

.newsletter-input:focus {
  border-color: var(--secondary);
}

@media (max-width: 992px) {
  .hero-container {
    grid-template-columns: 1fr;
    text-align: center;
  }
  
  .hero-subtitle {
    margin-left: auto;
    margin-right: auto;
  }
  
  .hero-actions {
    justify-content: center;
  }
}

@media (max-width: 768px) {
  .category-grid {
    grid-template-columns: 1fr;
  }
  
  .newsletter-card {
    padding: 3rem 1.5rem;
  }
  
  .newsletter-form {
    flex-direction: column;
  }
}
</style>
