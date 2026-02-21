<template>
  <footer class="footer">
    <div class="container footer-inner">
      <div class="footer-grid">
        <div class="footer-brand">
          <NuxtLink to="/" class="logo">
            <span class="logo-text">MODERN</span><span class="logo-dot">.</span>
          </NuxtLink>
          <p class="brand-desc">Curating the finest minimal essentials for your modern lifestyle. Quality over quantity, always.</p>
        </div>
        
        <div class="footer-section">
          <h4 class="footer-title">Shop</h4>
          <ul class="footer-links">
            <li><NuxtLink to="/shop/new">New Arrivals</NuxtLink></li>
            <li><NuxtLink to="/shop/best">Best Sellers</NuxtLink></li>
            <li><NuxtLink to="/shop/collections">Collections</NuxtLink></li>
          </ul>
        </div>

        <div class="footer-section">
          <h4 class="footer-title">Company</h4>
          <ul class="footer-links">
            <li><NuxtLink to="/about">About Us</NuxtLink></li>
            <li><NuxtLink to="/careers">Careers</NuxtLink></li>
            <li><NuxtLink to="/contact">Contact</NuxtLink></li>
          </ul>
        </div>

        <div class="footer-section">
          <h4 class="footer-title">Support</h4>
          <ul class="footer-links">
            <li><NuxtLink to="/shipping">Shipping</NuxtLink></li>
            <li><NuxtLink to="/returns">Returns</NuxtLink></li>
            <li><NuxtLink to="/faq">FAQ</NuxtLink></li>
          </ul>
        </div>
      </div>

      <div class="footer-bottom">
        <p>&copy; 2026 MODERN. All rights reserved.</p>
        <div class="footer-socials">
          <a href="#">Instagram</a>
          <a href="#">Twitter</a>
          <a href="#">Pinterest</a>
        </div>
      </div>
    </div>
  </footer>
</template>

<style scoped>
.footer {
  background: var(--muted);
  padding: 5rem 0 2rem;
  margin-top: 5rem;
}

.footer-grid {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr 1fr;
  gap: 4rem;
  margin-bottom: 4rem;
}

.logo {
  font-family: var(--font-heading);
  font-size: 1.5rem;
  font-weight: 700;
  display: block;
  margin-bottom: 1.5rem;
}

.logo-dot {
  color: var(--secondary);
}

.brand-desc {
  color: var(--muted-foreground);
  max-width: 300px;
  font-size: 0.95rem;
}

.footer-title {
  font-size: 1rem;
  font-weight: 600;
  margin-bottom: 1.5rem;
}

.footer-links {
  list-style: none;
}

.footer-links li {
  margin-bottom: 0.8rem;
}

.footer-links a {
  color: var(--muted-foreground);
  font-size: 0.9rem;
}

.footer-links a:hover {
  color: var(--foreground);
}

.footer-bottom {
  border-top: 1px solid var(--border);
  padding-top: 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  color: var(--muted-foreground);
  font-size: 0.85rem;
}

.footer-socials {
  display: flex;
  gap: 2rem;
}

@media (max-width: 768px) {
  .footer-grid {
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
  }
  
  .footer-brand {
    grid-column: span 2;
  }
  
  .footer-bottom {
    flex-direction: column;
    gap: 1.5rem;
    text-align: center;
  }
}
</style>
